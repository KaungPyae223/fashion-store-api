<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Type;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $productRepository;

    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        $searchBrand = $request->input('brand');
        $searchStatus = $request->input('status');
        $searchType = $request->input('type');
        $searchCategory = $request->input('category');


        $query = Product::query();

        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        if ($searchBrand && $searchBrand != "all") {
            $query->whereHas('brand', function ($q) use ($searchBrand) {
                $q->where('name', $searchBrand);
            });
        }

        if ($searchStatus && $searchStatus != "all") {
            $query->where('status', $searchStatus);
        }

        if ($searchType && $searchType != "all") {
            $query->whereHas('type', function ($q) use ($searchType) {
                $q->where('type', $searchType);
            });
        }

        if ($searchCategory && $searchCategory != "all") {
            $query->whereHas('category', function ($q) use ($searchCategory) {
                $q->where('category', $searchCategory);
            });
        }

        $query->where("is_delete",false);

        // Paginate the results
        $products = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = ProductResource::collection($products);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function promotion(Request $request)
    {
        $searchTerm = $request->input('q');
        $searchBrand = $request->input('brand');
         $searchType = $request->input('type');
        $searchCategory = $request->input('category');



        $query = Product::query();

        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        if ($searchBrand && $searchBrand != "all") {
            $query->whereHas('brand', function ($q) use ($searchBrand) {
                $q->where('name', $searchBrand);
            });
        }


        $query->where('status', "public");


        if ($searchType && $searchType != "all") {
            $query->whereHas('type', function ($q) use ($searchType) {
                $q->where('type', $searchType);
            });
        }

        if ($searchCategory && $searchCategory != "all") {
            $query->whereHas('category', function ($q) use ($searchCategory) {
                $q->where('category', $searchCategory);
            });
        }

        $query->where("is_delete",false);

        // Paginate the results
        $products = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = $products->map(function($product){


            $discount_percent = 0;

            $start_date = $product->discount_start;

            if($start_date && $start_date < now()){
                $discount_percent = $product->discount;
            }

            $profit = $product->price * ($product->profit_percent/100) ;


            $sellPrice = $product->price + $profit;

            $discountAmount = $sellPrice * ($product->discount/100);
            $currentDiscountAmount = $sellPrice * ($discount_percent/100);


            $actual_profit = $profit-$discountAmount;
            $promotion_sell_price = $product->price + $actual_profit;
            $currentSellPrice = $product->price + $profit - $currentDiscountAmount;

            return [
                "id" => $product->id,
                "original_price" => $product->price,
                "cover_photo" => $product->cover_photo,
                "name" => $product->name,
                "profit_percent" => $product->profit_percent,
                "discount" => $product->discount,
                "discount_start_date" => $product->discount_start,
                "discount_end_date" => $product->discount_end,
                "qty" => $product->product_size->sum("qty"),
                "discount_amount" => $discountAmount,
                "profit" => $actual_profit,
                "sell_price" => $currentSellPrice,
                "promotion_sell_price" => $promotion_sell_price
            ];
        });

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function createPromotion(Request $request,$id){

        $product = $this->productRepository->find($id);

        $product = $product->update([
            "discount_start" => $request->start,
            "discount_end" => $request->end,
            "discount" => $request->discount
        ]);

    }

    public function productTrash(Request $request)
    {
        $searchTerm = $request->input('q');
        $searchBrand = $request->input('brand');
        $searchStatus = $request->input('status');
        $searchType = $request->input('type');
        $searchCategory = $request->input('category');


        $query = Product::query();

        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        if ($searchBrand && $searchBrand != "all") {
            $query->whereHas('brand', function ($q) use ($searchBrand) {
                $q->where('name', $searchBrand);
            });
        }

        if ($searchStatus && $searchStatus != "all") {
            $query->where('status', $searchStatus);
        }

        if ($searchType && $searchType != "all") {
            $query->whereHas('type', function ($q) use ($searchType) {
                $q->where('type', $searchType);
            });
        }

        if ($searchCategory && $searchCategory != "all") {
            $query->whereHas('category', function ($q) use ($searchCategory) {
                $q->where('category', $searchCategory);
            });
        }

        $query->where("is_delete",true);

        // Paginate the results
        $products = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = ProductResource::collection($products);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    public function getAllFilterData(){

        $brands = Brand::orderBy('name')->get()->map(function($brand){
            return [
                "id" => $brand->id,
                "name" => $brand->name
            ];
        });

        $types = Type::orderBy('type')->get()->map(function($type){
            return [
                "id" => $type->id,
                "name" => $type->type,
                "category" => $type->category->category
            ];
        });

        return response()->json(
            [
                "brands" => $brands,
                "types" => $types,
            ]
        );

    }

    public function getProductProperties(Request $request)
    {
        $category_id = $request->input("category");
        $gender = $request->input("gender");

        // Fetch category with related types and sizes
        $category = Category::with(['type', 'size'])->find($category_id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Get brands
        $brands = Brand::orderBy('name')->get()->map(function ($brand) {
            return [
                "id" => $brand->id,
                "name" => $brand->name,
            ];
        });

        // Get types and filter by gender
        $types = $category->type;

        if ($gender && $gender !== "All") {
            $types = $types->filter(function ($type) use ($gender) {
                return $type->gender === $gender || $type->gender === "All";
            });
        }

        $types = $types->map(function ($type) {
            return [
                "id" => $type->id,
                "name" => $type->type,
            ];
        })->sortBy("name")->values();

        // Get colors
        $colors = Color::orderBy('color')->get()->map(function ($color) {
            return [
                "id" => $color->id,
                "name" => $color->color,
            ];
        });

        // Get sizes
        $sizes = $category->size->map(function ($size) {
            return [
                'id' => $size->id,
                'name' => $size->size,
            ];
        })->sortBy("name")->values();

        // Return JSON response
        return response()->json([
            "brands" => $brands,
            "types" => $types,
            "colors" => $colors,
            "sizes" => $sizes,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {


        $product = $this->productRepository->create([
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "color_id" => $request->color_id,
            "name" => $request->name,
            "cover_photo" => $request->file("cover_photo"),
            "details_photos" => $request->file("details_photos"),
            "price" => $request->price,
            "profit_percent" => $request->profit_percent,
            "description" => $request->description,
            "status" => $request->status,
            "gender" => $request->gender,
            "size_id" => $request->size_id,
        ]);


        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ], 201);

    }

    public function getProductQuantity($id){

        $product = $this->productRepository->find($id);

        return response()->json([
            'product_data' => [
                "name" => $product->name,
                "cover_image" => $product->cover_photo,
            ],
            'quantity' => $product->product_size->map(function($el){
                return [
                    'size_id' => $el->id,
                    'size' => $el->size->size,
                    'quantity' => $el->qty
                ];
            }),
            'total_quantity' => $product->product_size->sum('qty')
        ], 200);

    }

    public function updateProductQuantity(Request $request, $id){


        $product_size = ProductSize::find($id);

        $product_size->update([
            "qty" => $request->qty
        ]);

        return response()->json([
            'message' => 'Product quantity updated successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Display the specified resource.
     */

     public function adminProductDetails($id){

        $baseProduct = $this->productRepository->find($id);

        $discount_price = $baseProduct->discount ?  $baseProduct->price - ($baseProduct->price * ($baseProduct->discount / 100)) : 0;


        $product = [
            "id" => $baseProduct->id,
            "type" => $baseProduct->type->type,
            "brand" =>  $baseProduct->brand->name,
            "category" => $baseProduct->category->category,
            "color" => $baseProduct->color->color,
            "sizes" => $baseProduct->size->map(function($el){
                return [
                    "id" => $el->id,
                    "name" => $el->size
                ];
            }),
            "name" => $baseProduct->name,
            "cover_photo" => $baseProduct->cover_photo,
            "original_price" => $baseProduct->price,
            "profit_percent" => $baseProduct->profit_percent,
            "discount_price" => $discount_price,
            "price" => $baseProduct->price + ($baseProduct->price * ($baseProduct->profit_percent / 100)) - $discount_price,
            "description" => $baseProduct->description,
            "status" => $baseProduct->status,
            "gender" => $baseProduct->gender,
            "created_at" => $baseProduct->created_at,
            "updated_at" => $baseProduct->updated_at,
            "product_images" => $baseProduct->productPhoto->map(function ($image) {
                return [
                    "url" => $image->Photo,
                ];
            }),
        ];

        return response()->json([
            "data" => $product,
        ], 200);

     }

     public function productUpdateData($id){

        $baseProduct = $this->productRepository->find($id);

        $sellPrice = $baseProduct->price + ($baseProduct->price * ($baseProduct->profit_percent / 100));

        $product = [
            "id" => $baseProduct->id,
            "type" => [
                "id" => $baseProduct->type_id,
                "name" => $baseProduct->type->type
            ],
            "brand" => [
                "id" => $baseProduct->brand_id,
                "name" => $baseProduct->brand->name
            ],
            "category_id" => $baseProduct->category_id,
            "color" =>[
                "id" => $baseProduct->color_id,
                "name" => $baseProduct->color->color
            ],
            "sizes" => $baseProduct->size->map(function($el){
                return [
                    "id" => $el->id,
                    "name" => $el->size
                ];
            }),
            "profit_percent" => $baseProduct->profit_percent,
            "name" => $baseProduct->name,
            "cover_photo" => $baseProduct->cover_photo,
            "price" => $baseProduct->price,
            "description" => $baseProduct->description,
            "status" => $baseProduct->status,
            "gender" => $baseProduct->gender,
            "is_delete" => $baseProduct->is_delete,
            "created_at" => $baseProduct->created_at,
            "updated_at" => $baseProduct->updated_at,
            "sell_price" => $sellPrice,
            "product_images" => $baseProduct->productPhoto->map(function ($image) {
                return [
                    "url" => $image->Photo,
                ];
            }),
        ];

        return response()->json([
            "data" => $product,
        ], 200);

     }


    public function show($id)
    {


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    public function updateCoverPhoto(Request $request,$id)
    {

        $request->validate([
            "cover_photo" => "required",
        ]);

        $product = $this->productRepository->updateCoverPhoto([
            "id" => $id,
            "cover_photo" => $request->file("cover_photo"),
        ]);

        return new ProductResource($product);

    }

    public function updateDetailsPhoto(Request $request,$id)
    {


        $request->validate([
            "photos.*" => "required",
        ]);

        $product = $this->productRepository->updateDetailsPhoto([
            "id" => $id,
            "details_photos" => $request->file("photos"),
        ]);

        return new ProductResource($product);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {

        $updatedProduct = $this->productRepository->update([
            "id" => $product->id,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "color_id" => $request->color_id,
            "name" => $request->name,
            "price" => $request->price,
            "profit_percent" => $request->profit_percent,
            "description" => $request->description,
            "status" => $request->status,
            "gender" => $request->gender,
            "size_id" => $request->size_id,

        ]);


        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

    }

    public function deleteProduct($id)
    {

        $product = $this->productRepository->delete($id);

        return response()->json(["message" => "Product deleted successfully"]);

    }

    public function restoreProduct($id)
    {

        $product = $this->productRepository->restoreProduct($id);

        return response()->json(["message" => "Product restore successfully"]);

    }

}
