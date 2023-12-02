<?php

namespace App\Http\Controllers\Api\Order;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Localization\Address;
use App\Models\Order\Order;
use App\Models\Payment\PaymentProvider;
use App\Services\OrderService\OrderConfirmService;
use App\Services\OrderService\OrderCreationService;
use App\Services\OrderService\Return\OrderReturnRefundService;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;
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

    public ShippingService $shippingService;

    /**
     * @param PaymentService $paymentService
     * @param ShippingService $shippingService
     */
    public function __construct(PaymentService $paymentService,ShippingService $shippingService)
    {

        $this->middleware('auth:customer')->except('captureCallback', 'verifyPayment', 'confirmPayment');
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;
    }





    public function placeOrder(OrderStoreRequest $request, Cart $cart): JsonResponse|RedirectResponse
    {
        $validate = $request->validated();

        // Check If Coupon Presence (Voucher)

        if (isset($validate['coupon']) && !empty($validate['coupon']))
        {
            // Apply Coupon In Cart
            $cart->addCoupon($validate['coupon']);
        }


        $paymentProvider = PaymentProvider::firstWhere('id', $validate['payment_provider_id']);
        // Validate Payment Method
        if (is_null($paymentProvider)) {
            return response()->json(['status' => false, 'message' => 'payment service does not exist'], 422);
        }
        if (!$paymentProvider->status) {
            return response()->json(['status' => false, 'message' => 'please choose another payment service'], 422);
        }



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
        $paymentProviderService = $this->paymentService->provider($paymentProvider->code)->getProvider();

        $orderCreationService = new OrderCreationService($paymentProviderService,$cart);
        $uuid = $this->generateUniqueID();
        if (is_null($uuid))
        {
            return response()->json([
                'success' => true,
                'message' => 'unable to generate unique order id, try again!',
            ],409);
        }
        $orderCreationService->checkout($uuid,$shippingAddress,$billingAddress);


        // redirect instead json response

//            return (!app()->runningInConsole() && !is_null($paymentProviderService->getModel())) ? response()->json([
//                'success' => true,
//                'message' => 'order placed successfully',
//                'payment_provider' => [
//                    'name' => $paymentProviderService->getModel()->name,
//                    'code' => $paymentProviderService->getModel()->code,
//                ],
//                'order' => [
//                    'uuid' => $orderCreationService->getOrder()->uuid,
//                    'status' => $orderCreationService->getOrder()->status,
//                ],
//                'redirect' => ($orderCreationService->isCod) ?
//                    config('app.client_url').'/orders/'. $orderCreationService->getOrder()->uuid : route('payment.visit', ['payment' => $orderCreationService->getOrder()->payment->receipt]),
//            ], 200) : ['success' => true, 'message' => 'order placed successfully', 'payment' => $orderCreationService->getOrder()->payment()];


        // return Application Checkout link Route

            return redirect()->to(($orderCreationService->isCod) ?
                config('app.client_url').'/orders/'. $orderCreationService->getOrder()->uuid :
                route('payment.visit', ['payment' => $orderCreationService->getOrder()->payment->receipt]));






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




    public function captureCallback()
    {

    }







    public function returnOrder(Order $order,Request $request): JsonResponse
    {
        $givenSku = isset($request->product_sku) ? $request->product_sku : null;
        $newReturnService = new OrderReturnRefundService($order,$this->paymentService,$this->shippingService,$givenSku);
        if ($newReturnService->return())
        {
            return response()->json(['message' => 'Product returned successfully']);
        }else{
            return response()->json(['error' => $newReturnService->getError()]);
        }
    }

































    protected function generateUniqueID() {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);
            $id = $prefix . $random;
            $attempt++;
        } while (Order::where('uuid', $id)->exists() && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            //throw new Exception('Unable to generate unique ID');
            return null;
        }

        return $id;
    }






}
