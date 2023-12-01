<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Models\Order\OrderShipment;
use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\ShippingService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class OrderShippedService
{


    private Order|Model $order;
    protected ?string $error = null;
    private ShippingService $shippingService;

    public function __construct(Order|Model $order, ShippingService $shippingService)
    {
        $this->order = $order;
        $this->shippingService = $shippingService;
    }

    public function send():bool
    {

        $this->order->loadMissing('shipments','shipments.shippingProvider');


        foreach ($this->order->shipments as $shipment)
        {
            $shippingProvider = $shipment->shippingProvider->code;
            if (empty($this->error))
            {
                $newOrderOnProvider = $this->shippingService->provider($shippingProvider)->order()->create($shipment);
                if (isset($newOrderOnProvider['message']))
                {
                    // has error
                    // Pickup location need to added.. in shiprocket.. Wrong Pickup location entered. Please choose one location from the data given
                    $this->error = $newOrderOnProvider['message'];
                    // Throw a Notification (Filament)
                    Notification::make()
                        ->danger()
                        ->title('OrderShipment ID:'.$shipment->id.' Failed For Shipped!')
                        ->body('Shipping Request Try To Placed With'.$shipment->shippingProvider->name)
                        ->send();
                }else{
                    // Successfully Placed Order In Shipping Provider
                    $trackingInfo =  $this->shippingService->provider($shippingProvider)->tracking()->shipment($newOrderOnProvider['shipment_id']);

                    $trackingId = null;
                    if (isset($trackingInfo[0]['tracking_data']['shipment_track'][0]['awb_code']))
                    {
                        $trackingId = $trackingInfo[0]['tracking_data']['shipment_track'][0]['awb_code'];
                    }

                    $shipmentTrackActivities = null;
                    if (isset($trackingInfo[0]['tracking_data']['shipment_track_activities'])) {
                        $shipmentTrackActivities = $trackingInfo[0]['tracking_data']['shipment_track_activities'];
                    }

                    // Fetch Order Details From Provider
                    $orderDetails = $this->shippingService->provider($shippingProvider)->order()->fetch($newOrderOnProvider['order_id']);

                    $channelID = null;
                    if ($shippingProvider == ShippingProvider::CUSTOM)
                    {
                        $channelID = $orderDetails['data']['channel_id'];

                    }else{
                        $channelID = $orderDetails[0]['data']['channel_id'];
                    }



                    $insertableData = [
                        'details' => $orderDetails,
                        'provider_order_id' => $newOrderOnProvider['order_id'],
                        'shipment_id' => $newOrderOnProvider['shipment_id'],
                        'tracking_data' => $trackingInfo,
                        'tracking_id' => $trackingId,
                        'shipment_track_activities' => $shipmentTrackActivities,
                        'provider_payment_method' => $newOrderOnProvider['payment_method'],
                        'provider_channel_id' => $channelID
                    ];

                   // dd($insertableData);



                    // Update OrderShipment Model
//                    $shipment->details = $orderDetails;
//                    $shipment->provider_order_id = $newOrderOnProvider['order_id'];
//                    $shipment->shipment_id = $newOrderOnProvider['shipment_id'];
//                    $shipment->tracking_data = $trackingInfo;
//                    $shipment->tracking_id = $trackingId;
//                    $shipment->shipment_track_activities = $shipmentTrackActivities;
//                    $shipment->provider_payment_method = $newOrderOnProvider['payment_method'];
//                    $shipment->provider_channel_id = $channelID;

                    $shipment->fill($insertableData)->save();

                    // Throw a Notification (Filament)
                    Notification::make()
                        ->success()
                        ->title('OrderShipment ID:'.$shipment->id.' Ready For Shipped!')
                        ->body('Shipping Request Placed By '.$shipment->shippingProvider->name.' Shipping Service')
                        ->send();


                }
            }

        }
        return is_null($this->error);
    }



    public function getError(): ?string
    {
        return $this->error;
    }


}
