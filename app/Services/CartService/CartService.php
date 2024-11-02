<?php

namespace App\Services\CartService;

use App\Models\Customer\Customer;
use App\Models\Events\EventPromo;
use App\Models\Events\Events;
use App\Models\Events\EventTicket;
use App\Services\CartService\Support\CartCalculatorService;
use App\Services\CartService\Support\CouponService;
use App\Services\MoneyServices\Money;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

class CartService
{
    protected bool $changed = false;
    protected array $errors = [];
    protected Customer|Authenticatable $customer;
    protected ?Events $events = null;
    protected ?EventPromo $eventPromo = null;
    protected ?string $couponCode = null;
    protected bool $validCoupon = false;
    protected int $totalQuantity = 0;
    public bool $requestForReLoadCustomerCartInRuntime = false;



    public function __construct(Customer|Authenticatable $customer, ?string $couponCode = null)
    {
        $this->customer = $customer;
        $this->couponCode = $couponCode;
        $this->customer->loadMissing('cart');
    }



    /**
     * GET MODEL INSTANCE OF PROPERTIES
     */

    public function getCustomer(): Customer|Authenticatable
    {
        return $this->customer;
    }


    public function getEvent()
    {
        if ($this->tickets()->count()) {
            $firstTicket = $this->tickets()->first();
            $firstTicket->loadMissing('event');
            $this->events = $firstTicket->event;
            return $this->events;
        } else {
            return null;
        }

    }


    /**
     * GENERAL METHODS
     */


    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function hasChanged(): bool
    {
        return $this->changed;
    }

    public function setError(string $msg): void
    {
        $this->errors[] = $msg;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }





    /**
     * Stock Related Methods
     */

    public function getTotalQuantity(): int
    {
        $this->totalQuantity = $this->tickets()->sum('pivot.quantity');
        return $this->totalQuantity;
    }


    public function tickets(): Collection
    {
        if (App::runningInConsole() || $this->requestForReLoadCustomerCartInRuntime) {
            return  $this->customer->fresh()->cart;
        }
        return $this->customer->cart;
    }


    public function empty(): void
    {
        $this->customer->cart()->detach();
    }

    public function isEmpty(): bool
    {
        return $this->customer->cart->sum('pivot.quantity') === 0;
    }

    public function checkStock(): void
    {
        $customerCart = App::runningInConsole() ? $this->customer->fresh()->cart : $this->customer->cart;
        $customerCart->each(function ($ticket) {
            $quantity = $ticket->minStock($ticket->pivot->quantity);
            $this->changed = $quantity != $ticket->pivot->quantity;
            if ($this->changed) {
                $ticket->pivot->update([
                    'quantity' => $quantity,
                ]);
            }
        });
    }

    public function refresh(): static
    {
        $this->requestForReLoadCustomerCartInRuntime = true;
        return $this;
    }

    public function reset(): void
    {
        // Remove Items
        $this->empty();
        // Remove Coupon
        $this->removeCoupon($this->couponCode);
    }


    /**
     * CART COUPON CURD
     * ADD REMOVE COUPON CODE FOR APPLY DISCOUNT
     * Coupon Only Added When Ticket Is Present
     */

    public function addCoupon(string $code): void
    {
        $currentEvent = $this->getEvent();
        if (is_null($currentEvent))
        {
            $this->errors[] = 'coupon code must apply after adding a ticket';
        }
        if (empty($this->errors))
        {
            $couponModel = EventPromo::with('event')->firstWhere(['event_id' => $currentEvent->id, 'code' => $code]);
            if (!$couponModel)
            {
                $this->errors[] = 'coupon code not applicable';
            }
            if ($couponModel)
            {
                $couponService = CouponService::make($couponModel);
                if (!$couponService->isValidFor($this->customer,$this->getTotalQuantity()))
                {
                    $this->errors = array_merge($this->errors,$couponService->getErrors());
                }

                // Finally
                if (empty($this->errors))
                {
                    $this->validCoupon = true;
                    $this->couponCode = $code;
                    session(['coupon' => $code]);
                    $this->eventPromo = $couponModel;
                }
            }
        }
    }

    public function removeCoupon(?string $code = null): bool
    {
        if ($this->couponCode != $code)
        {
            $this->errors[] = 'no coupon code found for remove';
        }
        // Remove Coupon
        if (empty($this->errors) && $this->couponCode == $code)
        {
            $this->validCoupon = false;
            if (session()->has('coupon')) { session()->forget('coupon');}
            $this->couponCode = null;
            $this->eventPromo = null;
            return true;
        }
        return false;
    }





    /**
     * CART ITEM CURD
     * ADD UPDATE DELETE EVENT TICKETS FROM CUSTOMER CART RELATION
     */


    public function add(int $ticketID, int $quantity): void
    {
        $cart = $this->customer->cart;
        $selectedTicket = $cart->firstWhere('id', $ticketID) ?? EventTicket::findOrFail($ticketID);
        $existEvent = $this->getEvent();
        // Validate Same Event Ticket Before Add
        if ($existEvent && $existEvent->id != $selectedTicket->event_id)
        {
            $this->setError('Complete existing event ticket booking before adding a new one.');
        }

        $maxPerBooking = $selectedTicket->max_per_booking;
        if (empty($this->errors))
        {
            if ($cart->contains('id', $ticketID)) {
                $existQuantity = $selectedTicket->pivot->quantity;
                if ($existQuantity + $quantity <= $maxPerBooking) {
                    // Update Existing Ticket Quantity in Cart
                    $this->update($ticketID, $existQuantity + $quantity);
                }else{
                    // Can throw an error or book with maximum qty per booking
                    $this->update($ticketID, $maxPerBooking);
                }
            } else {

                if ($quantity <= $maxPerBooking) {
                    // Add New Ticket to Cart
                    $this->customer->cart()->attach($ticketID, ['quantity' => $quantity]);
                }else{
                    // Can throw an error or book with maximum qty per booking
                    //$this->update($ticketID, $maxPerBooking);
                    $this->customer->cart()->attach($ticketID, ['quantity' => $maxPerBooking]);
                }
            }
        }


    }


    public function update(int $ticketID, int $quantity): void
    {
        $this->customer->cart()->updateExistingPivot($ticketID, [
            'quantity' => $quantity,
        ]);
    }

    public function delete(int $ticketID): void
    {
        if ($this->tickets()->contains('id', $ticketID)) {
            $this->customer->cart()->detach($ticketID);
        } else {
            $this->errors[] = 'ticket not found!';
        }
    }



    // Bulk Item Add And Its Relative Methods
    public function addBulk(array $tickets): void
    {
        $this->customer->cart()->syncWithoutDetaching($this->getStorePayload($tickets));
    }

    protected function getStorePayload(array $items): array
    {
        return collect($items)->keyBy('id')->map(function ($item) {
            return ['quantity' => $item['quantity'] + $this->getCurrentQuantity($item['id'])];
        })->toArray();
    }

    protected function getCurrentQuantity($ticketID): int
    {
        if ($ticket = $this->tickets()->where('id', $ticketID)->first()) {
            return $ticket->pivot->quantity;
        }

        return 0;
    }


    /**
     * Calculation Of Cart
     */


    public function getCalculatedData(): array
    {

        $currentEvent = $this->getEvent();
        if (is_null($currentEvent))
        {
            $this->setError('no ticket found!');
        }

        $data = [];

        // Validate Coupon If Present
        if ($this->couponCode)
        {
            $this->addCoupon($this->couponCode);
        }

        // Preparing Calculation
        $this->checkStock();
        // Calculation Service
        $cartCalculatorService = CartCalculatorService::make($currentEvent)->tickets($this->tickets());

        $data = [
            'subTotal' => new Money(0.00),
            'discount' => new Money(0.00),
            'tax' => new Money(0.00),
            'amount' => new Money(0.00),
            'ticket' => []
        ];

        // Check if $currentEvent exists and conditionally set $data only if it's false
        if ($currentEvent) {
            $data =  $cartCalculatorService->setEventPromoModel($this->eventPromo,$this->validCoupon)->get();
        }

        return array_merge([
            'coupon' => $this->couponCode,
            'validCoupon' => $this->validCoupon,
            'couponModel' => $this->eventPromo,
            'empty' => $this->isEmpty(),
            'changed' => $this->changed,
            'customer' => $this->getCustomer()->email,
            'quantity' => $this->getTotalQuantity(),
            'error' => $this->getErrors(),
            'events' => $this->events
        ], $data);

    }





}
