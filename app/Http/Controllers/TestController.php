<?php

namespace App\Http\Controllers;

use App\Helpers\Promotion\Sales\ProductSaleHelper;
use App\Models\Category\Category;
use App\Models\Filter\Filter;
use App\Models\Filter\FilterGroup;
use App\Models\Product\Product;
use App\Models\Promotion\Sale;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQR;

class TestController extends Controller
{


    public function index()
    {

        $rawProductData = Product::factory()->raw(['type' => Product::CONFIGURABLE]);
        $typeInstance = app(config('project.product_types.'.$rawProductData['type'].'.class'));
        $rawProductData['filter_attributes'] = $this->getFilterDetails($rawProductData['filter_group_id']);
        // Create Configurable Product
        $product = $typeInstance->create($rawProductData);

        dd($product,$product->flat);

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
