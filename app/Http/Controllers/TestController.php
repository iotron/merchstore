<?php

namespace App\Http\Controllers;

use App\Helpers\Promotion\Sales\ProductSaleHelper;
use App\Models\Category\Category;
use App\Models\Filter\Filter;
use App\Models\Filter\FilterGroup;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\Promotion\Sale;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQR;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{


    public function index(ShippingService $shippingService)
    {

        $order = Order::first();


        $result = $shippingService->provider()->order()->create($order->toArray());

        dd($result);

      //  dd($paymentService->provider('razorpay')->order()->fetch('order_N2NOkMZ3higYO2'));


      //  $this->loginDefaultCustomer();

    }


    private function loginDefaultCustomer()
    {
        $customer = Customer::firstWhere('email','customer@example.com');
        Auth::guard('customer')->login($customer);
        echo "Login Successfully!";
    }




    private function getFilterDetails(int $id): array
    {
        $group = FilterGroup::where('id', $id)->with('filters.options')->first();
        $bag=[];
        foreach ($group->filters as $filter)
        {
            $options = $filter->options->random(random_int(1,3))->pluck('admin_name','id')->toArray();
            $bag [$filter->display_name] = $options;
        }
        return $bag;

    }







    /**
     * This give error :
     * BaconQrCode Exception RuntimeException PHP 8.1.12 10.13.2
     * You need to install the imagick extension to use this back end
     * @param $url
     * @param int $width
     * @param int $height
     * @return string
     */
    private function generateQRCodeViaSimpleSoftware($url, int $width = 100, int $height = 100)
    {
        $code = SimpleQR::generate($url);
        return 'data:image/svg+xml;base64,' . base64_encode($code);
    }




}
