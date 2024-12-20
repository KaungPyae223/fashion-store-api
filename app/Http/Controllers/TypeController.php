<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeRequest;
use App\Http\Requests\UpdateTypeRequest;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use App\Repositories\TypeRepository;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $typeRepository;

    function __construct(TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    public function index()
    {
        return response()->json([
            "data" => TypeResource::collection(Type::all())
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
    public function store(StoreTypeRequest $request)
    {

        $type = $this->typeRepository->create([
            "category_id" => $request->category_id,
            "type" => $request->type,
            "admin_id" => $request->admin_id,
        ]);

        return new TypeResource($type);
    }

    /**
     * Display the specified resource.
     */
    public function show(Type $type)
    {
        return new TypeResource($type);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Type $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeRequest $request, Type $type)
    {
        $type = $this->typeRepository->update([
            "id" => $type->id,
            "category_id" => $request->category_id,
            "type" => $request->type,
            "admin_id" => $request->admin_id,
        ]);

        return new TypeResource($type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        $type = $this->typeRepository->delete($type->id);
    }
}
