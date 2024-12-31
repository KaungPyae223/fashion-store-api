<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
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

    public function getProductProperties($id){
        $category = Category::find($id);

        $brands = Brand::orderBy('name')->get()->map(function($brand){
            return [
                "id" => $brand->id,
                "name" => $brand->name
            ];
        });

        $types = $category->type->map(function($type){
            return [
                "id" => $type->id,
                "name" => $type->type
            ];
        })->sortBy("name")->values();

        $colors = Color::orderBy('color')->get()->map(function($color){
            return [
                "id" => $color->id,
                "name" => $color->color
            ];
        });

        $sizes = $category->size->map(function ($size) {
            return [
                'id' => $size->id,
                'name' => $size->size,
            ];
        })->sortBy("name")->values();

        return response()->json(
            [
                "brands" => $brands,
                "types" => $types,
                "colors" => $colors,
                "sizes" => $sizes,
            ]
        );

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

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    public function updatePhoto(Request $request)
    {

        $request->validate([
            "id" => "required|exists:products,id",
            "cover_photo" => "required|image|mimes:jpeg,png,jpg,gif",
            "admin_id" => "required|exists:admins,id"
        ]);

        $product = $this->productRepository->updatePhoto([
            "id" => $request->id,
            "cover_photo" => $request->file("cover_photo"),
            "admin_id" => $request->admin_id
        ]);

        return new ProductResource($product);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {

        $product = $this->productRepository->update([
            "id" => $product->id,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "color_id" => $request->color_id,
            "name" => $request->name,
            "price" => $request->price,
            "description" => $request->description,
            "status" => $request->status,
            "gender" => $request->gender,
            "admin_id" => $request->admin_id,
            // "product_size" => $request->size_id,

        ]);


        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

    }

    public function deleteProduct(Request $request)
    {

        $product = $this->productRepository->find($request->id);

        $product->update([
            "is_delete" => true
        ]);

        $this->productRepository->addAdminActivity([
            "admin_id" => $request->admin_id,
            "method" => "Delete",
            "type" => "Product",
            "action" => "Delete a product ".$product->name
        ]);

        return response()->json(["message" => "Product deleted successfully"]);
    }

    public function restoreProduct(Request $request)
    {

        $product = $this->productRepository->find($request->id);

        $product->update([
            "is_delete" => false
        ]);

        $this->productRepository->addAdminActivity([
            "admin_id" => $request->admin_id,
            "method" => "Delete",
            "type" => "Product",
            "action" => "Restore a product ".$product->name
        ]);

        return response()->json(["message" => "Product restore successfully"]);
    }

}
