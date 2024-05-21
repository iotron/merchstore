<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile(Request $request): JsonResponse|CustomerResource
    {
        $customer = $request->user('customer') ?? null;
        if (! is_null($customer)) {
            $customer->load('socials');

            return CustomerResource::make($customer);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized!'], 401);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {

        $validator = $request->validate([
            'name' => 'bail',
            'contact' => 'bail|nullable|numeric|min_digits:10|max_digits:15|unique:customers,contact',
            'alt_contact' => 'bail|nullable|numeric|min_digits:10|max_digits:15|unique:customers,contact',
            'email' => 'bail|email|unique:customers,email',
            //            'whatsapp' => 'string',
        ]);

        if ($validator) {
            auth('customer')->user()->update($validator);

            return response()->json(['success' => true, 'data' => $validator, 'message' => 'profile update successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'User arleady exists, try another credentials!'], 400);
        }

    }

    public function setPassword(Request $request): JsonResponse
    {
        $validator = $request->validate([
            'new_password' => 'required|min:6',
            'c_password' => 'required|min:6|same:new_password',
        ]);

        auth('customer')->user()->update([
            'password' => Hash::make($validator['new_password']),
        ]);

        return response()->json(['message' => 'Password set successfully!'], 201);

    }

    public function updatePassword(Request $request): JsonResponse
    {

        $validator = $request->validate([
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6',
            'c_password' => 'required|min:6|same:new_password',
        ]);

        if (! Hash::check($validator['old_password'], auth('customer')->user()->password)) {
            return response()->json(['success' => false, 'message' => "Old Password Doesn't match!"], 401);
        } else {
            auth('customer')->user()->update([
                'password' => Hash::make($validator['new_password']),
            ]);

            return response()->json(['message' => 'Password changed successfully!'], 201);
        }

    }
}
