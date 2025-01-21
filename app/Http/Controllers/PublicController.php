<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hero;
use App\Models\page;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getHeaderAds(){

        $ads = page::find(1)->ads;

        return response()->json([
            "ads" => $ads
        ],200);

    }

    public function getCarousels(){

        $hero = Hero::all()->map(function($carousel){
            return [
                "id" => $carousel->id,
                "image" => $carousel->image,
                "title" => $carousel->title,
                "sub_title" => $carousel->subtitle,
                "link" => $carousel->link,
                "link_title" => $carousel->link_title
            ];
        });

        return response()->json($hero);

    }

    public function getHomePage(Request $request){

        $hero = Hero::all()->map(function($carousel){
            return [
                "id" => $carousel->id,
                "image" => $carousel->image,
                "title" => $carousel->title,
                "sub_title" => $carousel->subtitle,
                "link" => $carousel->link,
                "link_title" => $carousel->link_title
            ];
        });

        return response()->json([
            "hero" => $hero
        ]);

    }

    public function getFilterData (Request $request, $id){

        $gender = $request->input("gender");


        $category=  Category::find($id);

        $brands = $category->product
                ->sortBy('brand.name')
                ->pluck('brand.name')
                ->unique()
                ->values();

        $size = $category->size->sortBy('size')->pluck('size')->values();

        $colors = $category->product
            ->pluck('color.color')
            ->unique()
            ->values();

            $types = $category->type;

            if ($gender && $gender !== "All") {
                $types = $types->filter(function ($type) use ($gender) {
                    return $type->gender === $gender || $type->gender === "All";
                });
            }

            $types = $types->sortBy('type')
                ->pluck('type')
                ->values();


        return response()->json([
            "brands" => ["All", ...$brands],
            "types" => ["All",...$types],
            "colors" => ["All",...$colors],
            "sizes" => ["All",...$size]
        ]);

    }

    public function getProducts(Request $request,$id){

        $gender = $request->input("gender");

        $brand = $request->input("brand");
        $type = $request->input("type");
        $color = $request->input("color");
        $size = $request->input("size");
        $maxPrice = $request->input("max_price");
        $minPrice = $request->input("min_price");

        $query = Product::query();



        $query = $query->where("is_delete",false)->where("status","public");

        if ($gender && $gender !== "All") {
            $query->where("gender", $gender)->orWhere("gender", "All");
        }

        if ($brand && $brand != "All") {
            $query->whereHas('brand', function ($q) use ($brand) {
                $q->where('name', $brand);
            });
        }

        if ($type && $type != "All") {
            $query->whereHas('type', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($size && $size != "All") {
            $query->whereHas('size', function ($q) use ($type) {
                $q->where('size', $size);
            });
        }

        if ($color && $color != "All") {
            $query->whereHas('color', function ($q) use ($color) {
                $q->where('color', $color);
            });
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }


        $products = $query->where("category_id",$id)->paginate(16);

        $data = $products->map(function($product){
            return [
                "id" => $product->id,
                "name" => $product->name,
                "cover_photo" => $product->cover_photo,
                "price" => $product->price,
                "color" => $product->color->color,

            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);

    }

    public function productDetailsData($id){

        $product = Product::find($id);

        $totalReviews = $product->review->count();
        $averageRating = $product->review->average("rating");

        $query = Product::query();

        $query = $query->where("category_id",$product->category_id)
        ->where("id","!=",$id)
        ->where("is_delete",false)
        ->where("status","public")
        ->where(function ($q) use ($product) {
            $q->where("gender","All")
            ->orWhere("gender",$product->gender);
        })
        ->where("type_id",$product->type_id)
        ->limit(6)
        ->get()
        ->map(function($product){
            return [
                "id" => $product->id,
                "name" => $product->name,
                "cover_photo" => $product->cover_photo,
                "price" => $product->price,
                "color" => $product->color->color,
            ];
        });


        return response()->json([
            "DetailsData" => [
                "id" => $product->id,
                "rating" => 5,
                "color" => $product->color->color,
                "price" => $product->price,
                "title" => $product->name,
                "cover_image" => $product->cover_photo,
                "detailsImage" => $product->productPhoto->pluck("Photo")->values(),
                "size" => $product->size->map(function ($size) {
                    return [
                        "id" => $size->id,
                        "name" => $size->size
                    ];
                }),
                "description" => $product->description
            ],
            "RelativeProducts" => $query,
            "ReviewData" => [

                "totalReviews" => $totalReviews,
                "average" => $averageRating
            ]
        ]);

    }

    public function productRating ($id) {

        $product = Product::find($id);

        $review = $product->review()->paginate(8);

        $reviewData = $review->map(function ($review) {
            return [
                "id" => $review->id,
                "name" => $review->customer->user->name,
                "rating" => $review->rating,
                "comment" => $review->comment,
                "date" => $review->created_at,
            ];
        });

        return response()->json([
            "data" => $reviewData,
            "meta" => [
                "current_page" => $review->currentPage(),
                "last_page" => $review->lastPage(),
                "total" => $review->total(),
            ]

        ]);
    }

    public function ratingData($id){

        $product = Product::find($id);

        return response()->json([
            "title" => $product->name,
            "photo" => $product->cover_photo,
        ]);

    }

    public function availablePayments(){

        $payments = Payment::query()->where("status","available")->get()->map(function($payment){
            return [
                "name" => $payment->payment,
                "id" => $payment->id
            ];
        });

        return response()->json($payments);

    }

    public function searchInput (Request $request) {

        $searchTerm = $request->input("q");
        $gender = $request->input("gender");

        $query = Product::query();

        if($gender) {
            $query = $query->where("gender", $gender)->orWhere("gender", "All");
        }

        $products = $query->where("name","like","%$searchTerm%")->orWhereHas("brand",function ($q) use ($searchTerm) {$q->where("name","like","%$searchTerm%");})->limit(8)->get()->map(function($product){
            return [
                "id" => $product->id,
                "name" => $product->name,
                "cover_photo" => $product->cover_photo,
                "brand" => $product->brand->name,
                "amount" => $product->price
            ];
        });

        return response()->json([
            "data" => $products
        ]);

    }

    public function search (Request $request) {

        $searchTerm = $request->input("q");
        $gender = $request->input("gender");

        $query = Product::query();

        if($gender) {
            $query = $query->where("gender", $gender)->orWhere("gender", "All");
        }

        $query = $query->where("name","like","%$searchTerm%")->orWhereHas("brand",function ($q) use ($searchTerm) {$q->where("name","like","%$searchTerm%");})->paginate(25);

        $data = $query->map(function($product){
            return [
                "id" => $product->id,
                "name" => $product->name,
                "cover_photo" => $product->cover_photo,
                "brand" => $product->brand->name,
                "amount" => $product->price
            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
        ]);

    }

}
