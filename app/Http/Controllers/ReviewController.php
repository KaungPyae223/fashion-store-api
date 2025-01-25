<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function ratings ($id)
    {
        $product= Product::find($id);

        $ratings = $product->review()->orderBy("id","desc")->paginate(10);

        return response()->json([
            "data" => $ratings->map(function($rating) {
                return [
                    "id" => $rating->id,
                    "name" => $rating->customer->user->name,
                    "email" => $rating->customer->user->email,
                    "review" => $rating->comment,
                    "rating" => $rating->rating,
                    "date" => $rating->created_at
                ];
            }),
           'meta' => [
                'current_page' => $ratings->currentPage(),
                'last_page' => $ratings->lastPage(),
                'total' => $ratings->total(),
            ],
        ]);
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
    public function store(StoreReviewRequest $request)
    {
        $review = Review::create([
            'rating' => $request->rating,
            'comment' => $request->comment,
            "product_id" => $request->product_id,
            'customer_id' => $request->user()->customer->id,
        ]);

        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
            'status' => 200
        ], 204);
    }

    public function averageRating ($id){

        $product = Product::find($id);

        $totalReviews = $product->review->count();

        $average_rating = round($product->review? $product->review->average('rating'):0, 1);

        function Calculate_percentage ($rating,$total) {
            if ($total === 0) {
                return 0; // Avoid division by zero
            }
            return (int) (($rating / $total) * 100);
        }

        function starCalc ($star,$product,$totalReviews) {

            $total = $product->review()->where("rating", $star)->count();

           $percentage = Calculate_percentage($total,$totalReviews);

           return [
             "total" => $total,
             "percentage" => $percentage
           ];

        }

        return response()->json([
            "total_review" => $totalReviews,
            "average_rating" => $average_rating,
            "percentage" => [
                "star5" => starCalc(5,$product,$totalReviews),
                "star4" => starCalc(4,$product,$totalReviews),
                "star3" => starCalc(3,$product,$totalReviews),
                "star2" => starCalc(2,$product,$totalReviews),
                "star1" => starCalc(1,$product,$totalReviews),
            ]
        ]);

    }
}
