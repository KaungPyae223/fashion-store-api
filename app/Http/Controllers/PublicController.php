<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Hero;
use App\Models\page;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Size;
use App\Models\Type;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getHeaderAds()
    {

        $ads = page::find(1)->ads;

        return response()->json([
            "ads" => $ads
        ], 200);
    }

    public function getCarousels()
    {

        $hero = Hero::all()->map(function ($carousel) {
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

    public function getHomePage(Request $request)
    {

        $gender = $request->input("gender");

        $hero = Hero::all()->map(function ($carousel) {
            return [
                "id" => $carousel->id,
                "image" => $carousel->image,
                "title" => $carousel->title,
                "sub_title" => $carousel->subtitle,
                "link" => $carousel->link,
                "link_title" => $carousel->link_title
            ];
        });

        function formatData($data)
        {

            return $data->map(function ($product) {

                $discount_price = 0;
                $discount_percent = 0;

                $profit = $product->price * ($product->profit_percent / 100);

                $originalSellPrice = $profit + $product->price;

                $start_date = $product->discount_start;



                if ($start_date && $start_date < now()) {

                    $discount_percent = $product->profit_percent;

                    $discount_price = $product->price * ($discount_percent / 100);
                }

                return [
                    "id" => $product->id,
                    "title" => $product->name,
                    "img" => $product->cover_photo,
                    "amount" => $originalSellPrice,
                    "discount_price" => $discount_price,
                    "discount_percent" => $discount_percent,
                    "color" => $product->color->color,
                ];
            });
        }

        $latest = Product::query()
            ->join('product_sizes as PS', 'products.id', '=', 'PS.product_id')  // Join product_sizes with products
            ->select('products.*')
            ->groupBy('products.id')
            ->havingRaw('SUM(PS.qty) > 0')
            ->orderBy('PS.product_id')
            ->where('products.category_id', 1)
            ->where('products.is_delete', false)
            ->where('products.status', 'public')
            ->orderBy("created_at", "desc");

        if ($gender) {
            $latest->where(function ($query) use ($gender) {
                $query->where('products.gender', $gender)
                    ->orWhere('products.gender', 'All');
            });
        }

        $latest = $latest->limit(10)->get();  // Get the first 10 products


        $brand = Brand::query()->inRandomOrder()->limit(4)->get();


        $sneakers = Product::query()
            ->join('product_sizes as PS', 'products.id', '=', 'PS.product_id')  // Join product_sizes with products
            ->select('products.*')
            ->groupBy('products.id')
            ->havingRaw('SUM(PS.qty) > 0')
            ->orderBy('PS.product_id')
            ->where('products.category_id', 2)
            ->where('products.is_delete', false)
            ->where('products.status', 'public')
            ->orderBy("created_at", "desc");

        if ($gender) {
            $sneakers->where(function ($query) use ($gender) {
                $query->where('products.gender', $gender)
                    ->orWhere('products.gender', 'All');
            });
        }

        $sneakers = $sneakers->limit(10)->get();  // Get the first 10 products


        $trending = Product::query()
            ->join('product_sizes as PS', 'products.id', '=', 'PS.product_id')  // Join product_sizes with products
            ->select('products.*')
            ->groupBy('products.id')
            ->havingRaw('SUM(PS.qty) > 0')
            ->orderBy('PS.product_id')
            ->where('products.category_id', 1)
            ->where('products.is_delete', false)
            ->where('products.status', 'public');

        if ($gender) {
            $trending->where(function ($query) use ($gender) {
                $query->where('products.gender', $gender)
                    ->orWhere('products.gender', 'All');
            });
        }

        $trending = $trending->inRandomOrder()->limit(10)->get();  // Get the first 10 products


        $accessories = Product::query()
            ->join('product_sizes as PS', 'products.id', '=', 'PS.product_id')  // Join product_sizes with products
            ->select('products.*')
            ->groupBy('products.id')
            ->havingRaw('SUM(PS.qty) > 0')
            ->orderBy('PS.product_id')
            ->where('products.category_id', 3)
            ->where('products.is_delete', false)
            ->where('products.status', 'public')
            ->orderBy("created_at", "desc");

        if ($gender) {
            $accessories->where(function ($query) use ($gender) {
                $query->where('products.gender', $gender)
                    ->orWhere('products.gender', 'All');
            });
        }

        $accessories = $accessories->limit(10)->get();  // Get the first 10 products


        $lifeStyle = Product::query()
            ->join('product_sizes as PS', 'products.id', '=', 'PS.product_id')  // Join product_sizes with products
            ->select('products.*')
            ->groupBy('products.id')
            ->havingRaw('SUM(PS.qty) > 0')
            ->orderBy('PS.product_id')
            ->where('products.category_id', 4)
            ->where('products.is_delete', false)
            ->where('products.status', 'public')
            ->orderBy("created_at", "desc");

        if ($gender) {
            $lifeStyle->where(function ($query) use ($gender) {
                $query->where('products.gender', $gender)
                    ->orWhere('products.gender', 'All');
            });
        }

        $lifeStyle = $lifeStyle->limit(10)->get();



        return response()->json([
            "hero" => $hero,
            "latest" => formatData($latest),
            "brand" => $brand,
            "sneakers" => formatData($sneakers),
            "trending" => formatData($trending),
            "accessories" => formatData($accessories),
            "lifeStyle" => formatData($lifeStyle)
        ]);
    }

    public function getFilterData(Request $request, $id)
    {

        $gender = $request->input("gender");


        $category =  Category::find($id);

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
            ->unique()
            ->values();


        return response()->json([
            "brands" => ["All", ...$brands],
            "types" => ["All", ...$types],
            "colors" => ["All", ...$colors],
            "sizes" => ["All", ...$size]
        ]);
    }

    public function getProducts(Request $request, $id)
    {

        $gender = $request->input("gender");

        $brand = $request->input("brand");
        $type = $request->input("type");
        $color = $request->input("color");
        $size = $request->input("size");
        $maxPrice = $request->input("max_price");
        $minPrice = $request->input("min_price");

        $query = Product::query();

        $query = $query->join('product_sizes as PS', 'products.id', '=', 'PS.product_id')  // Join product_sizes with products
            ->select('products.*')
            ->groupBy('products.id')
            ->havingRaw('SUM(PS.qty) > 0');

        $query = $query->where("products.is_delete", false)->where("status", "public");

        if ($gender && $gender !== "All") {
            $query->where("products.gender", $gender)->orWhere("gender", "All");
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
            $query->whereHas('size', function ($q) use ($size) {
                $q->where('size', $size)->where('qty', '>', 0);
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


        $products = $query->where("category_id", $id)->paginate(16);

        $data = $products->map(function ($product) {

            $discount_price = 0;
            $discount_percent = 0;

            $profit = $product->price * ($product->profit_percent / 100);

            $originalSellPrice = $profit + $product->price;

            $start_date = $product->discount_start;



            if ($start_date && $start_date < now()) {

                $discount_percent = $product->profit_percent;

                $discount_price = $product->price * ($discount_percent / 100);
            }

            return [
                "id" => $product->id,
                "name" => $product->name,
                "cover_photo" => $product->cover_photo,
                "price" => $originalSellPrice,
                "discount_price" => $discount_price,
                "discount_percent" => $discount_percent,
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

    public function productDetailsData($id)
    {

        $product = Product::find($id);

        $totalReviews = $product->review->count();
        $averageRating = $product->review->average("rating");

        $query = Product::query();

        $query = $query->where("category_id", $product->category_id)
            ->where("id", "!=", $id)
            ->where("is_delete", false)
            ->where("status", "public")
            ->where(function ($q) use ($product) {
                $q->where("gender", "All")
                    ->orWhere("gender", $product->gender);
            })
            ->where("type_id", $product->type_id)
            ->limit(6)
            ->get()
            ->map(function ($product) {
                return [
                    "id" => $product->id,
                    "name" => $product->name,
                    "cover_photo" => $product->cover_photo,
                    "price" => $product->price,
                    "color" => $product->color->color,
                ];
            });

        $discount_price = 0;
        $discount_percent = 0;

        $profit = $product->price * ($product->profit_percent / 100);

        $originalSellPrice = $profit + $product->price;

        $start_date = $product->discount_start;



        if ($start_date && $start_date < now()) {

            $discount_percent = $product->profit_percent;

            $discount_price = $product->price * ($discount_percent / 100);
        }


        return response()->json([
            "DetailsData" => [
                "id" => $product->id,
                "rating" => 5,
                "color" => $product->color->color,
                "price" => $originalSellPrice,
                "discount_price" => $discount_price,
                "discount_percent" => $discount_percent,
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

    public function productRating($id)
    {

        $product = Product::find($id);

        $review = $product->review()->orderBy("id", "desc")->paginate(8);

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

    public function ratingData($id)
    {

        $product = Product::find($id);

        return response()->json([
            "title" => $product->name,
            "photo" => $product->cover_photo,
        ]);
    }

    public function availablePayments()
    {

        $payments = Payment::query()->where("status", "available")->get()->map(function ($payment) {
            return [
                "name" => $payment->payment,
                "id" => $payment->id
            ];
        });

        return response()->json($payments);
    }

    public function searchInput(Request $request)
    {

        $searchTerm = $request->input("q");
        $gender = $request->input("gender");

        $query = Product::query();

        if ($gender) {
            $query = $query->where("gender", $gender)->orWhere("gender", "All");
        }

        $products = $query->where("name", "like", "%$searchTerm%")->orWhereHas("brand", function ($q) use ($searchTerm) {
            $q->where("name", "like", "%$searchTerm%");
        })->limit(8)->get()->map(function ($product) {
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

    public function search(Request $request)
    {

        $searchTerm = $request->input("q");
        $gender = $request->input("gender");

        $query = Product::query();

        if ($gender) {
            $query = $query->where("gender", $gender)->orWhere("gender", "All");
        }

        $query = $query->where("name", "like", "%$searchTerm%")->orWhereHas("brand", function ($q) use ($searchTerm) {
            $q->where("name", "like", "%$searchTerm%");
        })->paginate(25);

        $data = $query->map(function ($product) {
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

    public function allBrands()
    {

        $brands = Brand::paginate(20);

        return response()->json([
            "data" => $brands->map(function ($brand) {
                return [
                    "id" => $brand->id,
                    "img" => $brand->photo,
                    "name" => $brand->name
                ];
            }),
            'meta' => [
                'current_page' => $brands->currentPage(),
                'last_page' => $brands->lastPage(),
                'total' => $brands->total(),
            ],
        ]);
    }

    public function brandFilter($brand, Request $request)
    {

        $gender = $request->input("gender");

        $brandData = Brand::query()->where("name", $brand)->get();

        $types = Type::query();



        $types = $types->whereHas("product.brand", function ($q) use ($brand) {
            $q->where("name", $brand);
        })->get();

        if ($gender) {
            $types =   $types->filter(function ($type) use ($gender) {
                return $type->gender === "Women" || $type->gender === "All";
            });
        }

        $types = $types->map(function ($type) {
            return [
                "name" => $type->type,
                "category_id" => $type->category_id
            ];
        })->unique("name")->values();


        $colors = Color::query();

        $colors = $colors->whereHas("product.brand", function ($q) use ($brand) {
            $q->where("name", $brand);
        })->orderBy("color")->pluck("color")->unique()->values();


        $sizes = Size::query();

        $sizes = $sizes->whereHas("product.brand", function ($q) use ($brand) {
            $q->where("name", $brand);
        })
            ->orderBy("size")
            ->select("size", "category_id")
            ->get()
            ->map(function ($size) {
                return [
                    "name" => $size->size,
                    "category_id" => $size->category_id
                ];
            })->unique("name")->values();


        return response()->json(
            [
                "brand_image" => $brandData[0]->photo,
                "filerData" => [
                    "types" => $types,
                    "colors" => ["All", ...$colors],
                    "sizes" => $sizes,
                ]
            ]
        );
    }

    public function brandProducts($brand, Request $request)
    {

        $gender = $request->input("gender");
        $type = $request->input("type");
        $color = $request->input("color");
        $size = $request->input("size");
        $maxPrice = $request->input("max_price");
        $minPrice = $request->input("min_price");
        $category = $request->input("category");

        $query = Product::query();

        $query = $query->where("is_delete", false)->where("status", "public");

        if ($gender && $gender !== "All") {
            $query->where("gender", $gender)->orWhere("gender", "All");
        }

        $query->whereHas('brand', function ($q) use ($brand) {
            $q->where('name', $brand);
        });


        if ($type && $type != "All") {
            $query->whereHas('type', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($category && $category != "All") {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category', $category);
            });
        }

        if ($size && $size != "All") {
            $query->whereHas('size', function ($q) use ($size) {
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

        $products = $query->paginate(16);

        $data = $products->map(function ($product) {
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
}
