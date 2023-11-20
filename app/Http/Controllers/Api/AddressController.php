<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\Address\AddressStoreRequest;
use App\Http\Resources\Location\AddressResource;
use App\Models\Localization\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:customer');
    }




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allAddress = auth('customer')->user()->addresses()->paginate();
        return AddressResource::collection($allAddress);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(AddressStoreRequest $request)
    {
        $newAddress = auth('customer')->user()->addresses()->create($request->validationData());
        return response()->json([
            'success' => true,
            'message' => 'Address created successfully!',
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        if ($address->addressable->email != auth('customer')->user()->email)
        {
            return response()->json([
                'success' => false,
                'message' => 'This address belongs to another customer!',
            ], 401);
        }

        return new AddressResource($address);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(AddressStoreRequest $request, Address $address)
    {
        if ($address->addressable->email != auth('customer')->user()->email)
        {
            return response()->json([
                'success' => false,
                'message' => 'This address belongs to another customer!',
            ], 401);
        }

        $address->fill($request->validationData())->save();
        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully!',
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        if ($address->addressable->email != auth('customer')->user()->email)
        {
            return response()->json([
                'success' => false,
                'message' => 'This address belongs to another customer!',
            ], 401);
        }
        $address->delete();
        return response()->json([
            'success' => true,
            'message' => 'Address remove successfully!',
        ], 201);
    }
}
