<?php

namespace App\Services\CartService\Support;

use App\Models\Events\EventPromo;
use App\Models\Events\Events;
use App\Models\Events\EventTicket;
use App\Services\MoneyServices\Money;
use Illuminate\Support\Collection;

class CartCalculatorService
{
    protected ?Events $events = null;
    protected ?EventPromo $eventPromo = null;
    protected ?Collection $tickets = null;
    protected bool $discountAlreadyApplied = false;
    protected bool $validCoupon = false;
    protected array $ticketBag = [];


    public static function make(?Events $events): static
    {
        $instance = new static();
        $instance->events = $events;
        return $instance;
    }

    public function tickets($tickets):static
    {
        $this->tickets = $tickets;
        return $this;
    }


    public function setEventPromoModel(?EventPromo $eventPromo = null,bool $isValid = false): static
    {
        $this->eventPromo = $eventPromo;
        $this->validCoupon = $isValid;
        return $this;
    }


    public function get():array
    {
        $this->calculateCartTickets();

        // Calculate All Ticket Sums...
        $totalBaseAmount = new Money();
        $totalDiscountAmount = new Money();
        $totalTaxAmount = new Money();
        $totalNetAmount = new Money();

        // dd($this->ticketBag);

        foreach ($this->ticketBag as $ticket) {
            $totalBaseAmount->add($ticket['total_base_amount']);
            $totalDiscountAmount->add($ticket['total_discount_amount']);
            $totalTaxAmount->add($ticket['total_tax_amount']);
            $totalNetAmount->add($ticket['net_total']);
        }

        // Prepare For Meta
        // return Data
        return [
            'subTotal' => $totalBaseAmount,
            'discount' => $totalDiscountAmount,
            'tax' => $totalTaxAmount,
            'amount' => $totalNetAmount,
            'ticket' => $this->ticketBag,
        ];


    }






    protected function calculateCartTickets()
    {

        foreach ($this->tickets as $ticket)
        {
            // Calculate SubTotal Each Ticket
            $ticketPrice = new Money($ticket->price);
            $taxableAmount = $ticketPrice->multiplyOnce($ticket->tax_type->percentage())->divide(100);
            // Single Ticket Total Value = Price + Tax Amount
            $ticketTotalPriceIncludeTax = $ticketPrice->add($taxableAmount);
            // Single Ticket Total Value * Quantity
            $subTotal = $ticketTotalPriceIncludeTax->multiplyOnce($ticket->pivot->quantity);
            $totalTax = $taxableAmount->multiplyOnce($ticket->pivot->quantity);


            $isCouponValidateForThisTicket = $this->isCouponValidateForThisTicket($ticket);


            $totalDiscount = $isCouponValidateForThisTicket && !$this->discountAlreadyApplied ? $this->getTotalDiscountAmount($ticket) : (new Money());
            $discountAmount = $this->getDiscountAmount();


            // Deducted Discount Only Once Per Cart
            if (!$this->discountAlreadyApplied) {
                $affiliateCommission = !is_null($this->eventPromo) && !is_null($this->eventPromo->affiliate_id)
                    ? (new Money($ticket->price))->multiplyOnce($ticket->pivot->quantity)->multiplyOnce($this->eventPromo->affiliate_commission)->divideOnce(100)
                    : (new Money());
            } else {
                $affiliateCommission = new Money(0);
            }


            // Deducted Discount From SubTotal
            $afterDeductDiscount = new Money();
            $afterDeductDiscount->add($subTotal);
            if (!$totalDiscount->sameAs(0.00) && !$this->discountAlreadyApplied) {
                $afterDeductDiscount->subtract($totalDiscount); // This is now our Current Subtotal
                $this->discountAlreadyApplied = true;
            }

            // As Tax Already Included in Subtotal
            $netTotal = $afterDeductDiscount;


            // Fill Array Into Bag
            $this->ticketBag[] = [
                'id' => $ticket->id,
                'pivot_quantity' => $ticket->pivot->quantity,
                'total_base_amount' => $subTotal,
                'total_discount_amount' => $totalDiscount,
                'total_tax_amount' => $totalTax,
                'affiliate_commission' => $affiliateCommission,
                'net_total' => $netTotal,
                'has_tax' => !($totalTax->sameAs(0.00)),
                'ticket' => $ticket,
            ];

        }

    }


    // Calculation Related Methods

    private function isCouponValidateForThisTicket(EventTicket $ticket): bool
    {
        return (!is_null($this->eventPromo)  && $ticket->allow_promo && in_array($ticket->id, $this->eventPromo->event_ticket_ids ?? []));
    }

    private function getTotalDiscountAmount(EventTicket $ticket): Money
    {
        // Check if coupon is valid and model is not null
        if (!is_null($this->eventPromo) && $this->validCoupon && !$this->discountAlreadyApplied)
        {
            $discountAmount = new Money($this->eventPromo->discount_amount);
            return $discountAmount->multiplyOnce($ticket->pivot->quantity);
        }
        // Return zero if no valid discount
        return new Money(0);
    }


    private function getDiscountAmount(): Money
    {
        // Return the discount amount if valid and the model is not null, otherwise return zero
        if (!is_null($this->eventPromo) && $this->validCoupon && !$this->discountAlreadyApplied)
        {
            return new Money($this->eventPromo->discount_amount);
        }

        return new Money(0);
    }




}
