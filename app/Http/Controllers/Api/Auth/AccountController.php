<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Customer\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    /**
     * Customer Registration
     */
    public function register(Request $request): JsonResponse
    {

        $validator = $request->validate([
            'name' => 'bail|required',
            'email' => 'bail|required|email|unique:customers,email',
            'password' => 'bail|required',
            'c_password' => 'bail|required|same:password',
            'contact' => 'bail|nullable|numeric|min_digits:10|max_digits:15|unique:customers,contact',
            'token' => 'required|string|exists:otps,token',
        ]);

        if ($validator) {
            $otpTokenModel = Otp::firstWhere('token', $validator['token']);
            if (! $otpTokenModel->expires_at->isPast()) {
                $identifier = $otpTokenModel->identifier;
                $type = $otpTokenModel->type;

                if (($type === 'email' && $validator['email'] === $identifier) || ($type === 'contact' && $validator['contact'] === $identifier)) {
                    // Validation passed
                } else {
                    return response()->json(['success' => false, 'message' => 'Invalid token identifier'], 500);
                }

                // then do this
                $validator['password'] = bcrypt($validator['password']);
                $user = Customer::create($validator);
                Auth::guard('customer')->login($user);

                $request->session()->regenerate();

                event(new UserCreated($user));

                return response()->json([
                    'success' => true,
                    'message' => 'Register Success.',
                ], 200);

            } else {
                return response()->json(['success' => false, 'message' => 'token expire'], 500);
            }

        } else {
            return response()->json(['success' => false, 'message' => 'validator error'], 500);
        }

    }

    /**
     * @throws ValidationException
     */
    public function reset(Request $request): JsonResponse
    {

        // validate the request
        $validator = Validator::make(
            $request->all(),
            [
                'method' => 'bail|required|in:email,contact',
                'password' => 'bail|required',
                'c_password' => 'bail|required|same:password',
                'token' => 'required|string|exists:otps,token',
            ],
            // do not change the validation message, view will ask customer to register based on this
            ['identifier.exists' => 'User not found!']
        );

        // if the method is email, validate email
        $validator->sometimes('identifier', 'bail|required|email|max:100|exists:customers,email', function ($input) {
            return $input->method == 'email';
        });

        // if the method is contact, validate contact
        $validator->sometimes('identifier', 'bail|required|numeric|min_digits:10|max_digits:15|exists:customers,contact', function ($input) {
            return $input->method == 'contact';
        });

        // validate the request
        if ($validator->validate()) {
            // get validated data
            $validated = $validator->safe()->only(['method', 'identifier', 'password', 'token']);

            $otpTokenModel = Otp::firstWhere('token', $validated['token']);
            if (! $otpTokenModel->expires_at->isPast()) {

                if ($otpTokenModel->type !== $validated['method'] || $validated['identifier'] !== $otpTokenModel->identifier) {
                    return response()->json(['success' => false, 'message' => 'Invalid token identifier'], 500);
                }

                // update password
                $user = Customer::where($validated['method'], $validated['identifier'])->update(['password' => bcrypt($validated['password'])]);

                // if password updated successfully
                if ($user) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Password reset success!',
                    ], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'password reset error!'], 500);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'token expire'], 500);
            }

        }

    }

    // request token validation for reset password and register
    public function checkTokenValidity(Request $request): JsonResponse
    {
        // validate required and ulid for token field
        $validated = $request->validate([
            'token' => 'required|ulid',
        ]);

        // find the token in the database
        $tokenModel = Otp::select('identifier', 'type', 'expires_at', 'verified_at')->firstWhere('token', $validated['token']);
        if (is_null($tokenModel)) {
            // token is not found return 404 not found
            return response()->json(['success' => false, 'message' => 'token not found! Kindly reverify!'], 404);
        }

        // check if the token is verified
        if (is_null($tokenModel->verified_at)) {
            // token is not verified return 410 gone
            return response()->json(['success' => false, 'message' => 'token not verified! Kindly reverify!'], 410);
        }

        // check if the token is expired
        if ($tokenModel->expires_at->isPast()) {
            // token is expired return 410 gone
            return response()->json(['success' => false, 'message' => 'token expired! Kindly reverify!'], 410);
        }

        // token passed all the validations
        return response()->json(['success' => true, 'message' => 'token validated!', 'data' => $tokenModel], 200);
    }
}
