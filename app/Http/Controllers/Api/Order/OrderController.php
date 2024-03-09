<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderIndexResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{




    public function index()
    {
        $customer = auth('customer')->user();
        $allOrders = $customer->orders()->with('orderProducts','orderProducts.product')->paginate();


        return OrderIndexResource::collection($allOrders);
    }


    public function show(Order $order)
    {
        // Order Policy customer individual check

        $order->load('billingAddress','invoices','payment','shipments','orderProducts','orderProducts.product');
        return OrderResource::make($order);
    }


    public function viewInvoice(Order $order)
    {
        $order->load('billingAddress','invoices','payment','shipments','orderProducts','orderProducts.product');


//        dd($order);

        $redirectUrl = \App\Filament\Resources\Order\OrderResource::getUrl('view',['record' => $order->id]);


        $allOrderProducts = $order->orderProducts;
        $totalOrderProductCount = $allOrderProducts->count();
        $hasTaxOnOrderProductCount = $allOrderProducts->where('tax','>',0)->count();
        $title = match (true) {
            $hasTaxOnOrderProductCount == $totalOrderProductCount => 'Tax Invoice',
            $hasTaxOnOrderProductCount < $totalOrderProductCount => 'Bill Of Supply/Tax Invoice',
            default => "Bill Of Supply",
        };

        $pdf = app('dompdf.wrapper')->loadView('invoice', ['order' => $order, 'qr' => $this->generateQRCode($redirectUrl),'title' => $title]);
        return $pdf->download();


    }


    /**
     * @param $url
     * @param int $width
     * @param int $height
     * @return string
     */
    private function generateQRCode($url, int $width = 100, int $height = 100): string
    {
        $code = QrCode::generate($url);
        return 'data:image/svg+xml;base64,' . base64_encode($code);
    }



}
