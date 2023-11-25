<?php

namespace App\Observers\Order;

use App\Models\Order\OrderShipment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ShipmentTrackActivityObserver
{


    /**
     * @throws ValidationException
     */
    public function creating(OrderShipment $shipment)
    {

        $validator = Validator::make($shipment->toArray(), [
            'shipment_track_activities.*.date' => 'required|date',
            'shipment_track_activities.*.activity' => 'required|string',
            'shipment_track_activities.*.location' => 'required|string',
        ]);

        if ($validator->fails()) {
            // Handle validation failure...
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }








    /**
     * @throws ValidationException
     */
    public function saving(OrderShipment $model)
    {
        $validator = Validator::make($model->toArray(), [
            'shipment_track_activities.*.date' => 'required|date',
            'shipment_track_activities.*.activity' => 'required|string',
            'shipment_track_activities.*.location' => 'required|string',
        ]);

        if ($validator->fails()) {
            // Handle validation failure...
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
