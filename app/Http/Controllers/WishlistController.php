<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistRequest;
use App\Http\Requests\UpdateWishlistRequest;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWishlistRequest $request)
    {

        $product_id = $request->product_id;
        $customer_id = $request->user()->customer->id;

        $checkWishList = Wishlist::query()->where("product_id",$product_id)->where("customer_id",$customer_id)->get();


        if($checkWishList->count() == 0){
            $wishlist = Wishlist::create([
                'product_id' => $product_id,
                'customer_id' => $customer_id
            ]);

            return response()->json([
                "message" => "Product added to wishlist successfully",
                "status" => 200
            ]);
        }else{
            return response()->json([
                "message" => "Product already in wishlist",
                "status" => 200
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWishlistRequest $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();

        return response()->json([
            'message' => 'Wishlist deleted successfully'
        ], 204);
    }
}
