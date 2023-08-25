<?php

namespace App\Http\Controllers\Api\Auth\Verification;

use App\Http\Controllers\Controller;
use App\Mail\Customer\OtpSent;
use App\Models\Customer\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EmailOTPController extends Controller
{


    /**
     * send otp to email from a register or reset form
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendOtp(Request $request): JsonResponse
    {
        // general validation rules
        $validator = Validator::make(
            $request->all(),
            [
                'type' => 'bail|required|in:register,reset',
                'email' => 'required|email|max:100',
            ],
            ['email.exists' => 'User not found!']
        );

        // check if email is unique for register and exists for reset
        $validator->sometimes('email', 'bail|unique:customers,email', function ($input) {
            return $input->type == 'register';
        });
        $validator->sometimes('email', 'bail|exists:customers,email', function ($input) {
            return $input->type == 'reset';
        });

        // validate request and get validated data
        $validator->validate();
        $validated = $validator->safe()->only(['type', 'email']);
        // generate 6 digit otp
        $sixDigit = mt_rand(100000, 999999);
        // if previous email otp exists then update else create new
        $otp = Otp::updateOrCreate(
            ['identifier' => $validated['email']],
            [
                'code' => $sixDigit,
                'type' => 'email',
                'token' => Str::ulid(),
                'expires_at' => NOW()->addMinutes(30),
                'verified_at' => null,
            ]
        );
        // send otp to email
        Mail::to($otp->identifier)->send(new OtpSent($otp));
        // return response
        return response()->json([
            'success' => true,
            'message' => "Otp sent to your email!",
        ], 200);
    }


    /**
     * verify otp from otp model
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        // validate request
        $validator =
            $request->validate([
                'otp' => 'required',
                'email' => 'required|email',
            ]);

        // get otp model
        $otpVerified = Otp::where([
            'identifier' => $validator['email'],
            'code' => $validator['otp'],
            'type' => 'email'
        ])->first();

        // check if otp model is found
        if ($otpVerified && $otpVerified->verified_at === null) {
            // update the verified_at field
            $otpVerified->update(['verified_at' => NOW()]);
            return response()->json([
                'success' => true,
                'message' => "OTP verified Successfully!",
                'token' => $otpVerified->token,
            ], 200);
        } else {
            return response()->json(['success' => false, 'message' => "Invalid OTP.",], 400);
        }
    }






}
