<?php

namespace App\Services\Iotron\FilamentRazorpay\Support;

use App\Services\Iotron\FilamentRazorpay\FilamentRazorpayService;
use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelStatusCast;
use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;
use App\Services\Iotron\LaravelRazorpay\Support\Contracts\LaravelRazorpayPaymentModelContract;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class FilamentRazorpayPayNowAction
{
    protected FilamentRazorpayService $filamentRazorpay;
    protected ?LaravelRazorpayPaymentModelContract $payment = null;
    protected string $name;
    protected ?Model $record = null;
    protected ?Model $bookingOn = null;
    protected ?string $relationshipName = 'payment';
    protected \Closure  $visibility;
    protected ?string $callbackUrl = null;
    protected ?string $successUrl = null;

    /**
     * RazorpayFilamentAction constructor.
     *
     * @param string $name The name of the action.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new instance of the action.
     *
     * @param string $name The name of the action.
     * @return static
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set the booking model record.
     *
     * @param Model $record The booking model record.
     * @return $this
     */
    public function record(Model $record): static
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Set the relationship name between the booking model and payment model.
     *
     * @param string $relationship The name of the relationship.
     * @return $this
     */
    public function relationship(string $relationship): static
    {
        $this->relationshipName = $relationship;
        return $this;
    }

    /**
     * Set the base model for the action (e.g., Events, Business, Product).
     *
     * @param Model|null $bookingFor The model to be booked.
     * @return $this
     */
    public function on(?Model $bookingFor = null): static
    {
        $this->bookingOn = $bookingFor;
        return $this;
    }

    /**
     * Set the visibility of the action group (creation).
     *
     * @param bool $visibility Whether the action should be visible.
     * @return $this
     */
    public function visible(\Closure $visibility): static
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Set the callback URL where the provider will return after payment.
     *
     * @param string $url The callback URL.
     * @return $this
     */
    public function callbackUrl(string $url): static
    {
        $this->callbackUrl = $url;
        return $this;
    }

    /**
     * Set the URL to redirect to after payment confirmation.
     *
     * @param string $url The success URL.
     * @return $this
     */
    public function successUrl(string $url): static
    {
        $this->successUrl = $url;
        return $this;
    }

    /**
     * Render the actions for Filament pages.
     *
     * @return array
     */
    public function render(): array
    {
        if (is_null($this->record)) {
            Notification::make()->title('Record cannot be null for the pay button')->danger()->send();
        }

        $this->record->loadMissing($this->relationshipName);
        $this->payment = $this->getPaymentRecord();

        if ($this->payment?->getType() == PaymentModelTypeCast::STANDARD)
        {
            return [];
        }
        if (is_null($this->payment))
        {
            return [
                ActionGroup::make([
                    $this->getRazorpayQRCodeRequestAction()->visible(is_null($this->payment)),
                    $this->getRazorpayPayLinkAction()->visible(is_null($this->payment)),
                ])
                    ->visible($this->visibility)
                    ->badge()
                    ->icon('heroicon-o-wallet')
                    ->label('Pay Now')
            ];
        }
        return [];
    }

    /**
     * Create the Razorpay QR Code request action.
     *
     * @return Action
     */
    protected function getRazorpayQRCodeRequestAction(): Action
    {
        return Action::make('pay_qr')
            ->label(__('Generate QR Code'))
            ->modalSubmitActionLabel(__('Generate QR'))
            ->icon('heroicon-o-qr-code')
            ->visible($this->visibility)
            ->requiresConfirmation()
            ->action(fn() => $this->processQRCodeAction());
    }

    /**
     * Create the Razorpay payment link action.
     *
     * @return Action
     */
    protected function getRazorpayPayLinkAction(): Action
    {
        return Action::make('send_link')
            ->label(__('Send Payment Link'))
            ->icon('heroicon-o-link')
            ->visible($this->visibility)
            ->requiresConfirmation()
            ->action(fn() => $this->processPayLinkAction());
    }



    /**
     * Handle QR Code payment action processing.
     *
     * @return void
     */
    private function processQRCodeAction(): void
    {
        try {
            if (is_null($this->payment)) {
                $this->payment = $this->createQRCodePayment();
            }

            if ($this->payment->getType() != PaymentModelTypeCast::QR) {
                $this->sendWrongModeNotification();
            } else {
                if (!$this->payment->isPaid()) {
                    Notification::make()
                        ->title('QR Generated Successfully')
                        ->body('QR : ' . $this->payment->details['provider']['image_url'])
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->url(route('show.provider.qr', ['payment' => $this->payment->getRazorpayGeneratedId()]), true)
                        ])
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Payment Already Received')
                        ->body('Process Abort!')
                        ->warning()
                        ->send();
                }
            }

        } catch (Throwable $e) {
            Notification::make()
                ->title('Request Failed!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Handle Payment Link action processing.
     *
     * @return void
     */
    private function processPayLinkAction(): void
    {
        try {
            if (is_null($this->payment)) {
                $this->payment = $this->createLinkPayment();
            }

            if ($this->payment->getType() != PaymentModelTypeCast::LINK) {
                $this->sendWrongModeNotification();
            } else {
                if (!$this->payment->isPaid()) {
                    Notification::make()
                        ->title('Payment Link Sent Successfully')
                        ->body('Link : ' . $this->payment->details['provider']['short_url'])
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->url($this->payment->details['provider']['short_url'], true)
                        ])
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Payment Already Received')
                        ->body('Process Abort!')
                        ->warning()
                        ->send();
                }
            }

        } catch (Throwable $e) {
            Notification::make()
                ->title('Request Failed!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Get the payment record associated with the booking.
     *
     * @return LaravelRazorpayPaymentModelContract|null
     */
    private function getPaymentRecord(): ?LaravelRazorpayPaymentModelContract
    {
        return $this->record->{$this->relationshipName};
    }

    /**
     * Create a QR Code payment record.
     *
     * @return LaravelRazorpayPaymentModelContract|null
     * @throws Throwable
     */
    private function createQRCodePayment(): ?LaravelRazorpayPaymentModelContract
    {
        $providerResponse = LaravelRazorpay::make()->qr()->create([
            "name" => is_null($this->bookingOn) ? $this->record->uuid : $this->bookingOn->name,
            "payment_amount" => $this->record->getRawOriginal('total'),
            "description" => $this->record->uuid . ' Booking',
            "close_by" => now()->addMinutes((int) config('app.booking_cleanup_time_limit')),
            "notes" => [
                "purpose" => $this->bookingOn->name . ' Booking'
            ]
        ]);

        if (!$providerResponse['success']) {
            throw_unless($providerResponse['success'], $providerResponse['error']);
            return null;
        }

        $this->payment = $this->getNewPaymentRecordWithResponse($providerResponse, PaymentModelTypeCast::QR);
        return $this->payment;
    }

    /**
     * Create a Payment Link record.
     *
     * @return LaravelRazorpayPaymentModelContract|null
     * @throws Throwable
     */
    private function createLinkPayment(): ?LaravelRazorpayPaymentModelContract
    {
        $providerResponse = LaravelRazorpay::make()->link()->create([
            'amount' => $this->record->getRawOriginal('total'),
            'currency' => config('filament-razorpay.currency'),
            'reference_id' => $this->record->uuid,
            'accept_partial' => false,
            'customer' => [
                'name' => $this->record->booking_name,
                'email' => $this->record->booking_email,
                'contact' => $this->record->booking_contact,
            ],
            'notify' => [
                'sms' => true,
                'email' => true,
            ],
            'callback_url' => $this->callbackUrl,
            'callback_method' => 'get',
        ]);

        if (!$providerResponse['success']) {
            throw_unless($providerResponse['success'], $providerResponse['error']);
            return null;
        }

        $this->payment = $this->getNewPaymentRecordWithResponse($providerResponse, PaymentModelTypeCast::LINK);
        return $this->payment;
    }

    /**
     * Create a new payment record with the response data.
     *
     * @param array $providerResponse The response data from Razorpay.
     * @param string|PaymentModelTypeCast $type The type of payment.
     * @return LaravelRazorpayPaymentModelContract
     */
    private function getNewPaymentRecordWithResponse(array $providerResponse, string|PaymentModelTypeCast $type): LaravelRazorpayPaymentModelContract
    {
        // Convert amount from paise to rupees
        $providerResponse['data']['amount'] = $providerResponse['data']['amount'] / 100;

        // Create a new payment record
        return $this->record->{$this->relationshipName}()->create(array_merge($providerResponse['data'], [
            'type' => $type,
            'payment_provider_id' => $this->record->payment_provider_id,
            'callback_url' => $this->callbackUrl,
            'success_url' => $this->successUrl,
            'failure_url' => $this->successUrl,
            'expire_at' => now()->addMinutes((int) config('app.booking_cleanup_time_limit')),
        ]));
    }

    /**
     * Send a notification indicating the wrong payment mode was chosen.
     *
     * @return void
     */
    private function sendWrongModeNotification(): void
    {
        Notification::make()
            ->title('Wrong Payment Mode Chosen')
            ->body('Process aborted!')
            ->warning()
            ->send();
    }
}
