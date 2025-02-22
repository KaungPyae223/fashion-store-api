<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Repositories\BlogRepository;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $blogRepository;

    function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function index(Request $request)
    {

        $searchTerm = $request->input('q');


        $query = Blog::query();

        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }


        $query = $query->orderBy("id", "desc")->paginate(8);


        $blogs = BlogResource::collection($query);

        return response()->json([
            "data" => $blogs,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
            "status" => 200,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $blog = $this->blogRepository->create([
            "title" => $request->title,
            "photo" => $request->file("photo"),
            "content" => $request->content,

        ]);

        return response()->json([
            'message' => 'Blog created successfully',
            'data' => new BlogResource ($blog)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return response()->json([
            "title" => $blog->title,
            "photo" => $blog->photo,
            "content" => $blog->content,
            "time" => $blog->created_at,
            "author" => $blog->admin->user->name
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {

    }

    public function updatePhoto (Request $request){

        $request->validate([
            "admin_id" => "required|exists:admins,id",
            "id" => "required|exists:brands,id",
            "photo" => "required|image|mimes:jpeg,png,jpg,gif",
        ]);

        $admin = $this->blogRepository->updatePhoto([
            "id" => $request->id,
            "photo" => $request->file("photo"),
            "admin_id" => $request->admin_id,
        ]);

        return new BlogResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $blog = $this->blogRepository->update([
            "id" => $blog->id,
            "admin_id" => $request->admin_id,
            "title" => $request->title,
            "photo" => $request->file("photo"),
            "content" => $request->content,

        ]);
        return new BlogResource($blog);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $this->blogRepository->delete($blog->id);
    }
}
