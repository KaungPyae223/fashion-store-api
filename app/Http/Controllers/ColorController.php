<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Repositories\ColorRepository;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $colorRepository;

    function __construct(ColorRepository $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function index()
    {
        return response()->json([
            "data" => ColorResource::collection(Color::all())
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
    public function store(StoreColorRequest $request)
    {
        $color = $this->colorRepository->create(
            [
                "color" => $request->color,
                "admin_id" => $request->admin_id,
            ]
        );

        return new ColorResource($color);

    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        return new ColorResource($color);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Color $color)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateColorRequest $request, Color $color)
    {
        $color = $this->colorRepository->update(
            [
                "id" => $color->id,
                "color" => $request->color,
                "admin_id" => $request->admin_id,
            ]
        );

        return new ColorResource($color);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color = $this->colorRepository->delete(
            [
                "id" => $color->id,
            ]
        );
    }
}
