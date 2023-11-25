<?php

namespace App\Http\Controllers\Api\Order;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Localization\Address;
use App\Models\Payment\PaymentProvider;
use App\Services\OrderService\OrderConfirmService;
use App\Services\OrderService\OrderCreationService;
use App\Services\PaymentService\PaymentService;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Payment\Payment;
use App\Http\Requests\Order\OrderConfirmRequest;
use Illuminate\Routing\Redirector;

class OrderActionController extends Controller
{


    /**
     * @var PaymentService
     */
    public PaymentService $paymentService;

    /**
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {

        $this->middleware('auth:customer')->except('captureCallback', 'verifyPayment', 'confirmPayment');
        $this->paymentService = $paymentService;
    }





    public function placeOrder(OrderStoreRequest $request, Cart $cart): JsonResponse|array
    {
        $validate = $request->validated();

        // Check If Coupon Presence (Voucher)

        if (isset($validate['coupon']) && !empty($validate['coupon']))
        {

            // Apply Coupon In Cart
            $cart->addCoupon($validate['coupon']);
            dd($cart->getMeta());

        }





        $paymentProvider = PaymentProvider::firstWhere('id', $validate['payment_provider_id']);
        // Validate Payment Method
        if (is_null($paymentProvider)) {
            return response()->json(['status' => false, 'message' => 'payment service does not exist'], 422);
        }
        if (!$paymentProvider->status) {
            return response()->json(['status' => false, 'message' => 'please choose another payment service'], 422);
        }
       $this->paymentService->provider($paymentProvider->code);


        // Validate Delivery Address (auth)
        $shippingAddress = auth('customer')->user()->addresses()->firstWhere('id', $validate['shipping_address_id']);
        // Validate Shipping Method
        if (is_null($shippingAddress)) {
            return response()->json(['status' => false, 'message' => 'shipping address does not exist'], 422);
        }
        // Check Shipping Is Billing
        if ($validate['shipping_is_billing'])
        {
            $billingAddress = $shippingAddress;
        }else{
            $billingAddress = auth('customer')->user()->addresses()->firstWhere('id', $validate['billing_address_id']);
        }


        // Can not Place Order With Empty Cart (changed option old cart for stock)
        if ($cart->getTotalQuantity() <= 0) {
            return response()->json(['success' => false, 'message' => 'cart empty!'], 403);
        }
        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        }
        $orderCreationService = new OrderCreationService($this->paymentService,$cart);

        return $orderCreationService->checkout($shippingAddress,$billingAddress);

    }




    public function confirmPayment(Payment $payment, OrderConfirmRequest $request): Application|JsonResponse|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {

        $paymentVerified =
            $this->paymentService
                ->provider($payment->provider->code)
                ->verify()
                ->verifyWith($payment,$request->validationData());


        if ($paymentVerified && is_null($this->paymentService->provider()->getError()))
        {
            $orderConfirmService = new OrderConfirmService($payment);
            if ($orderConfirmService->updateOrder()
            ) {
                $order = $orderConfirmService->getOrder();
                if ($order->exists) {
                    //Send Notification To Event Manager
                    //$this->notifyManagerOnSuccess($order->event->manager,'new booking found!','a new booking '.$order->uuid.' found for order - '.$order->event->name);
                    //Redirect On Success
                    return redirect(config('app.client_url') . '/orders/' . $order->uuid);
                }
                return redirect(config('app.client_url') . '/cart/');
            }
            return redirect(config('app.client_url') . '/cart/');
        } else {
            return response()->json(['status' => false, 'message' => 'provider order id mismatch'], 403);
        }




    }






}
