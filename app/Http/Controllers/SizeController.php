<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use App\Repositories\SizeRepository;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $sizeRepository;

    function __construct(SizeRepository $sizeRepository)
    {
        $this->sizeRepository = $sizeRepository;
    }

    public function index()
    {

        return response()->json([
            "data" => SizeResource::collection(Size::all())
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
    public function store(StoreSizeRequest $request)
    {

        $size = $this->sizeRepository->create([
            "category_id" => $request->category_id,
            "size" => $request->size,
        ]);

        return response()->json($size);

        // return new SizeResource($size);

    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Size $size)
    {
        return new SizeResource($size);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSizeRequest $request, Size $size)
    {
        $size = $this->sizeRepository->update([
            "id" => $size->id,
            "category_id" => $request->category_id,
            "size" => $request->size,
        ]);

        return new SizeResource($size);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $this->sizeRepository->delete($size->id);
    }
}
