<?php

namespace App\Http\Controllers\Api\Order;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderConfirmRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Services\OrderService\OrderConfirmService;
use App\Services\OrderService\OrderCreationService;
use App\Services\OrderService\Return\OrderReturnRefundService;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class OrderActionController extends Controller
{
    public PaymentService $paymentService;

    public ShippingService $shippingService;

    public function __construct(PaymentService $paymentService, ShippingService $shippingService)
    {

        $this->middleware('auth:customer')->except('captureCallback', 'verifyPayment', 'confirmPayment');
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;
    }

    public function placeOrder(OrderStoreRequest $request, Cart $cart): JsonResponse|RedirectResponse
    {

        // Validate Request
        $validate = $request->validated();

        // Check If Coupon Presence (Voucher)
        if (isset($validate['coupon']) && ! empty($validate['coupon'])) {
            // Apply Coupon In Cart
            $cart->addCoupon($validate['coupon']);
        }

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

        // Can not Place Order With Empty Cart (changed option old cart for stock)
        if ($cart->getTotalQuantity() <= 0) {
            return response()->json(['success' => false, 'message' => 'cart empty!'], 403);
        }

        // Finish Cart Calculation
        $cartMeta = $cart->getMeta();
        // Check Cart For Errors
        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        }

        // Found Payment Provider
        $paymentProvider = $this->paymentService->getAllProvidersModel()->firstWhere('id', '=', $validate['payment_provider_id']);

        // Validate Payment Method
        if (is_null($paymentProvider)) {
            return response()->json(['status' => false, 'message' => 'payment service does not exist'], 422);
        }
        if (! $paymentProvider->status) {
            return response()->json(['status' => false, 'message' => 'please choose another payment service'], 422);
        }
        // Load Payment Provider Service
        $paymentProviderService = $this->paymentService->provider($paymentProvider->code)->getProvider();

        // Order UUID Generation
        $uuid = $this->generateUniqueID();
        if (is_null($uuid)) {
            return response()->json([
                'success' => true,
                'message' => 'unable to generate unique order id, try again!',
            ], 409);
        }

        // Order Place Process Start
        $cartCustomer = $cart->getCustomer();
        $orderCreation = new OrderCreationService($paymentProviderService, $cartCustomer, $cartMeta);
        $orderCreation->placeOrder($uuid, $shippingAddress, $billingAddress);
        // Clean Cart Attributes
        $cart->reset();

        // Return Based On Error
        if (! is_null($orderCreation->getError())) {
            // Failure
            return response()->json([
                'success' => true,
                'message' => $orderCreation->getError(),
            ], 409);
        } else {
            // Success
            //            dd('order success');
            return redirect()->to(($orderCreation->isCashOnDelivery()) ?
                config('app.client_url').'/orders/'.$orderCreation->getOrder()->uuid :
                route('payment.visit', ['payment' => $orderCreation->getPayment()->receipt]));
        }

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
