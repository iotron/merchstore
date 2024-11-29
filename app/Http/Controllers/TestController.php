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
    public function index()
    {


        $this->loginDefaultCustomer();

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
