<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHeroRequest;
use App\Http\Requests\UpdateHeroRequest;
use App\Models\Hero;
use App\Repositories\HeroRepository;

class HeroController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $heroRepository;

     function __construct(HeroRepository $heroRepository)
     {
         $this->heroRepository = $heroRepository;
     }

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
    public function store(StoreHeroRequest $request)
    {
        $hero = $this->heroRepository->createHero($request->validated());

        return response()->json([
            'message' => 'Hero created successfully',
            'data' => $hero
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hero $hero)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hero $hero)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHeroRequest $request, Hero $hero)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hero $hero)
    {
        //
    }
}
