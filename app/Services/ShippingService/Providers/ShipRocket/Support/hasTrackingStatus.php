<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Support;

trait hasTrackingStatus
{
    protected function updateStatusViaCode(string|object $response): string|object
    {
        $data = json_decode($response);
        $statusMsg = $this->getMatchedStatus($data);
        $data->tracking_data->trackingMsg = $statusMsg;

        return $data;
    }

    protected function getMatchedStatus($data): ?string
    {
        return match ($data->tracking_data->track_status) {
            6 => 'Shipped',
            7 => 'Delivered',
            8 => 'Cancelled',
            9 => 'RTO Initiated',
            10 => 'RTO Delivered',
            12 => 'Lost',
            13 => 'Pickup Error',
            14 => 'RTO Acknowledged',
            15 => 'Pickup Rescheduled',
            16 => 'Cancellation Requested',
            17 => 'Out For Delivery',
            18 => 'In Transit',
            19 => 'Out For Pickup',
            20 => 'Pickup Exception',
            21 => 'Undelivered',
            22 => 'Delayed',
            23 => 'Partial_Delivered',
            24 => 'Destroyed',
            25 => 'Damaged',
            26 => 'Fulfilled',
            38 => 'Reached at Destination',
            39 => 'Misrouted',
            40 => 'RTO NDR',
            41 => 'RTO OFD',
            42 => 'Picked Up',
            43 => 'Self Fulfilled',
            44 => 'DISPOSED_OFF',
            45 => 'CANCELLED_BEFORE_DISPATCHED',
            46 => 'RTO_IN_TRANSIT',
            47 => 'QC Failed',
            48 => 'Reached Warehouse',
            49 => 'Custom Cleared',
            50 => 'In Flight',
            51 => 'Handover to Courier',
            52 => 'Shipment Booked',
            54 => 'In Transit Overseas',
            55 => 'Connection Aligned',
            56 => 'Reached Overseas Warehouse',
            57 => 'Custom Cleared Overseas',
            59 => 'Box Packing',
            default => null,
        };
    }
}
