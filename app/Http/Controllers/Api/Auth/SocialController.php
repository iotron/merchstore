<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialController extends Controller
{


    public function removeSocial(Request $request)
    {

        $validator = $request->validate([
            'social_id' => 'exists:customer_socials,social_id',
        ]);

        $customer = $request->user('customer');

        $social = $customer->socials()->firstWhere('social_id', $validator['social_id']);
        if (!is_null($social)) {
            $service = $social->service;
            $social->delete();
            return response()->json(['data' => ucfirst($service) . ' social provider removed successfully for ' . $customer->name . '.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'wrong social id provided for ' . $customer->email . '.'], 401);
        }


    }



    public function viewSocials(Request $request)
    {
        $customer = $request->user('customer');
        $allSocials =$customer->socials;
        return response()->json(['success' => true, 'data' => $allSocials->toArray()], 200);
    }




}
