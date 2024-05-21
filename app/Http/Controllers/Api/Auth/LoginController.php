<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function loginCustomer(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'method' => 'bail|required|in:email,contact',
                'password' => 'bail|required',
            ],
            // do not change the validation message, view will ask customer to register based on this
            ['identifier.exists' => 'User not found!']
        );

        $validator->sometimes('identifier', 'bail|required|email|max:100|exists:customers,email', function ($input) {
            return $input->method == 'email';
        })->validate();
        $validator->sometimes('identifier', 'bail|required|numeric|min_digits:10|max_digits:15|exists:customers,contact', function ($input) {
            return $input->method == 'contact';
        })->validate();

        $validated = $validator->safe()->only(['method', 'identifier', 'password']);

        $credentials = [$validated['method'] => $validated['identifier'], 'password' => $validated['password']];

        if (Auth::guard('customer')->attempt($credentials)) {

            // Regenerate Session
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully!',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                // do not change the validation message, view will ask customer to register based on this
                'message' => 'Incorrect credentials!',
            ], 401);
        }
    }

    public function getSession(): JsonResponse|CustomerResource
    {
        $user = auth('customer')->user();

        // return response()->json(['data' => $user], 200);
        return CustomerResource::make($user);
    }

    /**
     * @throws AuthenticationException
     */
    public function logout(Request $request): JsonResponse
    {
        if (! auth('customer')->check()) {
            throw new AuthenticationException();
        }
        auth()->guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'You have logged out!'], 201);
    }
}
