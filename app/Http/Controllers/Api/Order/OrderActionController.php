<?php

namespace App\Http\Controllers\Api\Order;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Localization\Address;
use App\Models\Payment\PaymentProvider;
use App\Services\OrderService\OrderCreationService;
use App\Services\PaymentService\PaymentService;
use Illuminate\Http\Request;

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
        if (!auth('customer')->user()->addresses()->contains($deliveryAddress))
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


}
