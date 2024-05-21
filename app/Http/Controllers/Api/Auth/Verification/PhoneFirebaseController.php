<?php

namespace App\Http\Controllers\Api\Auth\Verification;

use App\Http\Controllers\Controller;
use App\Models\Customer\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PhoneFirebaseController extends Controller
{
    /**
     * Check if the contact number is already registered in the system
     *
     * @throws ValidationException
     */
    public function checkExistingContact(Request $request): JsonResponse
    {
        // STEP 1: add validation rules
        $validator = Validator::make($request->all(), [
            'type' => 'bail|required|in:register,reset',
            'contact' => 'required|numeric|min_digits:10|max_digits:10',
        ]);

        // STEP 2: if type is register, check if contact is unique
        $validator->sometimes('contact', 'bail|unique:customers,contact', function ($input) {
            return $input->type == 'register';
        });

        // STEP 3: if type is reset, check if contact exists
        $validator->sometimes('contact', 'bail|exists:customers,contact', function ($input) {
            return $input->type == 'reset';
        });

        // STEP 4: validate the request
        $validator->validate();

        return response()->json([
            'success' => true,
            'message' => 'validations passed!',
        ], 200);

    }

    public function saveVerifiedContact(Request $request): JsonResponse
    {
        // Validate the request
        $validator = $request->validate(['contact' => 'required|numeric|min_digits:10|max_digits:10']);

        // Update or create an OTP record for the contact
        $verified = Otp::updateOrCreate(['identifier' => $validator['contact']], [
            'type' => 'contact',
            'token' => Str::ulid(),
            'expires_at' => NOW()->addMinutes(30),
            'verified_at' => NOW(),
        ]);

        // If the OTP record was successfully created or updated
        if ($verified) {
            // Return a 200 response
            return response()->json([
                'message' => 'verified contact saved successfully!',
                'token' => $verified->token,
            ], 200);
        } else {
            // Return a 500 response
            return response()->json([
                'message' => 'unable to save verified contact!',
            ], 500);
        }
    }
}
