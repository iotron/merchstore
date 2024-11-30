<?php

namespace App\Http\Controllers\Api\Order;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderConfirmRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Services\BackupServices\OrderService\OrderConfirmService;
use App\Services\BackupServices\OrderService\Return\OrderReturnRefundService;
use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use App\Services\OrderService\OrderCreationService;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class OrderActionController extends Controller
{


    public ShippingService $shippingService;

//    public function __construct(PaymentService $paymentService, ShippingService $shippingService)
//    {
//
//        $this->middleware('auth:customer')->except('captureCallback', 'verifyPayment', 'confirmPayment');
//        $this->paymentService = $paymentService;
//        $this->shippingService = $shippingService;
//    }



    public function __construct()
    {
        $this->middleware('auth:customer')->except('captureCallback', 'verifyPayment', 'confirmOrder');
    }


    public function placeOrder(OrderStoreRequest $request, Cart $cart): JsonResponse|RedirectResponse
    {
        // Validate Request
        $validate = $request->validated();
        if ($cart->getTotalQuantity() <= 0) {
            return response()->json(['success' => false, 'message' => 'cart empty!'], 403);
        }

        // Addresses For Shipping

        // Validate Delivery Address (auth)
        $shippingAddress = auth('customer')->user()->addresses()->firstWhere('id', $validate['shipping_address_id']);
        // Validate Shipping Method
        if (is_null($shippingAddress)) {
            return response()->json(['status' => false, 'message' => 'shipping address does not exist'], 422);
        }

        // Check Shipping Is Billing
        if ($validate['shipping_is_billing']) {
            $billingAddress = $shippingAddress;
        } else {
            $billingAddress = auth('customer')->user()->addresses()->firstWhere('id', $validate['billing_address_id']);
        }

        // Validate Cart
        $cartMeta = $cart->getMeta();
        if (!empty($cartMeta['error']))
        {
            return response()->json(['success' => false, 'message' => implode(', ',$cartMeta['error'])], 403);
        }

        // Placing New Order
        if (!in_array($request->provider,[PaymentProvider::CASH,PaymentProvider::RAZORPAY]))
        {
            return response()->json(['success' => false, 'message' => 'unknown provider given'], 403);
        }

        return OrderCreationService::make()
            ->create($cart)
            ->setCartMeta($cartMeta)
            ->setProvider($request->provider)
            ->setShippingAddress($shippingAddress)
            ->setBillingAddress($billingAddress)
            ->checkout();


    }



    public function confirmOrder(Order $order,OrderConfirmRequest $request)
    {

        $order->load('payment', 'payment.provider');
        $payment = $order->payment;
        // default Laravel Razorpay Payment Provider
        $paymentVerified = LaravelRazorpay::make()->verify()->viaCallback($request);


        if ($paymentVerified && \App\Services\OrderService\OrderConfirmService::make($order)->validate())
        {
            //Send Notification To Event Manager
            //$this->notifyManagerOnSuccess($booking->event->host, 'new booking found!', 'a new booking '.$booking->uuid.' found for event - '.$booking->event->name);
            //Redirect On Success
            return redirect()->to($payment->success_url);
        }
        return redirect()->to($payment->failure_url);


    }




















    public function confirmPayment(Payment $payment, OrderConfirmRequest $request): Application|JsonResponse|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        // Found Payment Provider
        $paymentProviderModel = $this->paymentService->getAllProvidersModel()->firstWhere('id', $payment->payment_provider_id);
        $paymentProviderService = $this->paymentService->provider($paymentProviderModel->code)->getProvider();
        $paymentVerified = $paymentProviderService->verify()->verifyWith($payment, $request->validationData());

        if (! $paymentVerified || ! is_null($paymentProviderService->getError())) {
            return response()->json(['status' => false, 'message' => 'provider order id mismatch'], 403);
        }

        // Confirm This Payment And Update Order
        $orderConfirmService = new OrderConfirmService($payment);
        $orderConfirmService->confirmOrder();
        $order = $orderConfirmService->getOrder();

        if (is_null($orderConfirmService->getError())) {
            return redirect(config('app.client_url').'/cart/');
        }

        //Send Notification To Event Manager
        //$this->notifyManagerOnSuccess($order->event->manager,'new booking found!','a new booking '.$order->uuid.' found for order - '.$order->event->name);
        //Redirect On Success
        return redirect(config('app.client_url').'/orders/'.$order->uuid);

    }

    public function captureCallback()
    {

    }

    public function returnOrder(Order $order, Request $request): JsonResponse
    {
        $givenSku = isset($request->product_sku) ? $request->product_sku : null;
        $newReturnService = new OrderReturnRefundService($order, $this->paymentService, $this->shippingService, $givenSku);
        if ($newReturnService->return()) {
            return response()->json(['message' => 'Product returned successfully']);
        } else {
            return response()->json(['error' => $newReturnService->getError()]);
        }
    }

    protected function generateUniqueID()
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix
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
}
