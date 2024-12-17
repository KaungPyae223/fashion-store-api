<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerQuestionRequest;
use App\Http\Requests\UpdateCustomerQuestionRequest;
use App\Models\CustomerQuestion;

class CustomerQuestionController extends Controller
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
    public function store(StoreCustomerQuestionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerQuestion $customerQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerQuestion $customerQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerQuestionRequest $request, CustomerQuestion $customerQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerQuestion $customerQuestion)
    {
        //
    }
}
