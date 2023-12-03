<?php

namespace App\Services\OrderService\Shipping;

use App\Models\Order\OrderShipment;
use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use Filament\Notifications\Notification;

class OrderShipmentShippingService
{

    protected ?string $error = null;
    protected OrderShipment $orderShipment;
    protected ShippingProviderContract $shippingProvider;

    public function __construct(OrderShipment $orderShipment, ShippingProviderContract $shippingProvider)
    {
        $this->orderShipment = $orderShipment;
        $this->shippingProvider = $shippingProvider;

    }

    public function getError(): ?string
    {
        return $this->error;
    }


    protected function isShippable(): bool
    {
        if (!is_null($this->orderShipment->weight) && !is_null($this->orderShipment->length) && !is_null($this->orderShipment->breadth) && !is_null($this->orderShipment->height))
        {
            return true;
        }
        $this->error = 'OrderShipment::'.$this->orderShipment->id.' - not fulfil this parameters "weight","length","breadth","height" ';
        return false;
    }


    public function shipped():bool
    {
        if ($this->isShippable())
        {
            $newProviderOrder = $this->shippingProvider->order()->create($this->orderShipment);

            if (!$this->isFailed($newProviderOrder))
            {
                // Fetch Full Order Details From Provider
                $orderDetails = $this->shippingProvider->order()->fetch($newProviderOrder['order_id']);
                // Fetch Full Tracking Details From Provider
                $trackingInfo =  $this->shippingProvider->tracking()->shipment($newProviderOrder['shipment_id']);

                // Get Tracking ID
                $trackingId = null;
                if (isset($trackingInfo[0]['tracking_data']['shipment_track'][0]['awb_code']))
                {
                    $trackingId = $trackingInfo[0]['tracking_data']['shipment_track'][0]['awb_code'];
                }
                // Get Tracking Activities
                $shipmentTrackActivities = null;
                if (isset($trackingInfo[0]['tracking_data']['shipment_track_activities'])) {
                    $shipmentTrackActivities = $trackingInfo[0]['tracking_data']['shipment_track_activities'];
                }

                // Fetch Order Channel Id From Provider
                $channelID = ($this->shippingProvider->getProviderName() == ShippingProvider::CUSTOM)
                    ? $orderDetails['data']['channel_id']
                    : $orderDetails[0]['data']['channel_id'];

                $insertableData = [
                    'details' => $orderDetails,
                    'provider_order_id' => $newProviderOrder['order_id'],
                    'shipment_id' => $newProviderOrder['shipment_id'],
                    'tracking_data' => $trackingInfo,
                    'tracking_id' => $trackingId,
                    'shipment_track_activities' => $shipmentTrackActivities,
                    'provider_payment_method' => $newProviderOrder['payment_method'],
                    'provider_channel_id' => $channelID,
                    'status' => OrderShipment::READYTOSHIP,
                    'last_update' => now(),
                    'shipping_provider_id' => $this->shippingProvider->getModel()->id
                ];

                $this->orderShipment->fill($insertableData)->save();
            }
        }
        return is_null($this->error);
    }


    protected function isFailed(array $newProviderOrder):bool
    {
        if (isset($newProviderOrder['message']))
        {
            // has error
            // Pickup location need to added.. in shiprocket.. Wrong Pickup location entered. Please choose one location from the data given
            $this->error = $newProviderOrder['message'];
            // Throw a Notification (Filament)
//            Notification::make()
//                ->danger()
//                ->title('OrderShipment ID:'.$this->orderShipment->id.' Failed For Shipped!')
//                ->body('Shipping Request Try To Placed With '.ucwords($this->shippingProvider->getProviderName()))
//                ->send();
        }
        return !is_null($this->error);
    }







}
