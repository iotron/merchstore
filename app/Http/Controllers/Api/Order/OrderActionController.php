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
use Illuminate\Http\Request;
use App\Models\Payment\Payment;
use App\Http\Requests\Order\OrderConfirmRequest;

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





    public function placeOrder(OrderStoreRequest $request, Cart $cart)
    {
        $validate = $request->validated();
        $paymentProvider = PaymentProvider::firstWhere('id', $validate['payment_method_id']);
        // Validate Payment Method
        if (is_null($paymentProvider)) {
            return response()->json(['status' => false, 'message' => 'payment service does not exist'], 422);
        }
        if (!$paymentProvider->status) {
            return response()->json(['status' => false, 'message' => 'please choose another payment service'], 422);
        }
       $this->paymentService->provider($paymentProvider->url);


        // Validate Delivery Address
        $deliveryAddress = Address::firstWhere('id', $validate['delivery_address_id']);
        // Validate Shipping Method
        if (is_null($deliveryAddress)) {
            return response()->json(['status' => false, 'message' => 'delivery address does not exist'], 422);
        }
        // Check It is Default Address
        if (!$deliveryAddress->default) {
            return response()->json(['status' => false, 'message' => 'please choose another delivery address'], 422);
        }

        // Check this Address Belongs to Active User
        $activeCustomer = auth('customer')->user();
        $activeCustomer->loadMissing('addresses');
        if (!$activeCustomer->addresses->contains($deliveryAddress))
        {
            return response()->json(['status' => false, 'message' => 'please choose another delivery address'], 422);
        }

        // Can not Place Order With Empty Cart
        if ($cart->getTotalQuantity() <= 0) {
            return response()->json(['success' => false, 'message' => 'cart empty!'], 403);
        }
        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        }
        $orderCreationService = new OrderCreationService($this->paymentService,$cart);

        return $orderCreationService->checkout($deliveryAddress);

    }




    public function confirmPayment(Payment $payment, OrderConfirmRequest $request)
    {

        $paymentVerified =
            $this->paymentService
                ->provider($payment->provider->url)
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
