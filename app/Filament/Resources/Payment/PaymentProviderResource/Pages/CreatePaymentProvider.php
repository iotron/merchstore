<?php

namespace App\Filament\Resources\Payment\PaymentProviderResource\Pages;

use App\Filament\Resources\Payment\PaymentProviderResource;
use App\Models\Payment\PaymentProvider;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Razorpay\Api\Api as RazorpayApi;
use Stripe\StripeClient;
class CreatePaymentProvider extends CreateRecord
{
    protected static string $resource = PaymentProviderResource::class;


    protected static bool $canCreateAnother = false;





    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->validateConfig($data))
        {
            $data['key'] = base64_encode($data['key']);
            $data['secret'] = base64_decode($data['secret']);
            return parent::mutateFormDataBeforeCreate($data);
        }else{
            $this->halt();
        }
    }


    protected function validateConfig(array $data): bool
    {

        try {

            if ($this->record->url == PaymentProvider::RAZORPAY)
            {
                $api = new RazorpayApi($data['key'],$data['secret']);
                $response = $api->payment->all();
            }

//            if ($this->record->url == PaymentProvider::STRIPE)
//            {
//                $api = new StripeClient($data['secret']);
//                $api->paymentLinks->all(['limit' => 3]);
//            }

            return true;
        }catch (\Throwable $e)
        {
            Notification::make()->title('Authentication Failed!')->body($e->getMessage())->danger()->send();
            return false;
        }

    }


}
