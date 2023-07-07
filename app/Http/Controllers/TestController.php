<?php

namespace App\Http\Controllers;

use App\Models\Category\Category;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQR;

class TestController extends Controller
{


    public function index()
    {


        $product = Product::find(1);

        $qrUrl = route('filament.resources.product.view',['record' => $product->id]);


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
