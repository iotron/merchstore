<?php

namespace App\Http\Controllers;

use App\Models\Customer\Customer;
use App\Models\Filter\FilterGroup;
use App\Models\Order\Order;
use App\Services\PaymentService\PaymentService;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQR;

class TestController extends Controller
{
    public function index(PaymentService $paymentService)
    {
        //        $razorpay = $paymentService->provider('razorpay')->getProvider();
        //
        //           dd($razorpay->isOnline());
        // $order = Order::with('shipments')->first();

        //        $orderShipment = OrderShipment::with('orderProducts')->first();
        //
        //        dd($orderShipment);

        //        foreach ($order->shipments as $shipment)
        //        {
        //            $shippingService->provider('shiprocket')->order()->create($shipment);
        //        }

        //        $orderShipment = $order->shipments->first();
        //
        //
        //
        //        dd($shippingService->provider('shiprocket')->courier()->getCharge());
        //

        //
        //
        //        $order = Order::first();
        //
        //
        //        $result = $shippingService->provider()->order()->create($order->toArray());
        //
        //        dd($result);
        //
        //      //  dd($paymentService->provider('razorpay')->order()->fetch('order_N2NOkMZ3higYO2'));

        $this->loginDefaultCustomer();

    }

    public function getSampleData()
    {
        return json_decode('{
          "order_id": "224-447",
          "order_date": "2019-07-24 11:11",
          "pickup_location": "Jammu",
          "channel_id": "",
          "comment": "Reseller: M/s Goku",
          "billing_customer_name": "Naruto",
          "billing_last_name": "Uzumaki",
          "billing_address": "House 221B, Leaf Village",
          "billing_address_2": "Near Hokage House",
          "billing_city": "New Delhi",
          "billing_pincode": "110002",
          "billing_state": "Delhi",
          "billing_country": "India",
          "billing_email": "naruto@uzumaki.com",
          "billing_phone": "9876543210",
          "shipping_is_billing": true,
          "shipping_customer_name": "",
          "shipping_last_name": "",
          "shipping_address": "",
          "shipping_address_2": "",
          "shipping_city": "",
          "shipping_pincode": "",
          "shipping_country": "",
          "shipping_state": "",
          "shipping_email": "",
          "shipping_phone": "",
          "order_items": [
            {
              "name": "Kunai",
              "sku": "chakra123",
              "units": 10,
              "selling_price": "900",
              "discount": "",
              "tax": "",
              "hsn": 441122
            }
          ],
          "payment_method": "Prepaid",
          "shipping_charges": 0,
          "giftwrap_charges": 0,
          "transaction_charges": 0,
          "total_discount": 0,
          "sub_total": 9000,
          "length": 10,
          "breadth": 15,
          "height": 20,
          "weight": 2.5
        }');
    }

    protected function generateUniqueID()
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix 8
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);

            $id = $prefix.$random;
            $attempt++;
        } while (Order::where('uuid', $id)->exists() && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            //throw new Exception('Unable to generate unique ID');
            return null;
        }

        return $id;
    }

    private function loginDefaultCustomer()
    {
        $customer = Customer::firstWhere('email', 'customer@example.com');
        Auth::guard('customer')->login($customer);
        echo 'Login Successfully!';
    }

    private function getFilterDetails(int $id): array
    {
        $group = FilterGroup::where('id', $id)->with('filters.options')->first();
        $bag = [];
        foreach ($group->filters as $filter) {
            $options = $filter->options->random(random_int(1, 3))->pluck('admin_name', 'id')->toArray();
            $bag[$filter->display_name] = $options;
        }

        return $bag;

    }

    /**
     * This give error :
     * BaconQrCode Exception RuntimeException PHP 8.1.12 10.13.2
     * You need to install the imagick extension to use this back end
     *
     * @return string
     */
    private function generateQRCodeViaSimpleSoftware($url, int $width = 100, int $height = 100)
    {
        $code = SimpleQR::generate($url);

        return 'data:image/svg+xml;base64,'.base64_encode($code);
    }
}
