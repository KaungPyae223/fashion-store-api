<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorepageRequest;
use App\Http\Requests\UpdatepageRequest;
use App\Models\page;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    public function store(StorepageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(page $page)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatepageRequest $request, page $page)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(page $page)
    {
        //
    }
}