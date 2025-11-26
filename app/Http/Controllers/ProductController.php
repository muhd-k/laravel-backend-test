<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:20',
            'description' => 'required|max:255',
            'quantity' => 'required',
            'unit_price_cents' => 'required',
            'amount_sold' => 'required'
        ]);

        $product = Product::create($fields);

        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $fields = $request->validate([
            'name' => 'required|max:20',
            'description' => 'required|max:255',
            'quantity' => 'required',
            'unit_price_cents' => 'required',
            'amount_sold' => 'required'
        ]);

        $product->update($fields);

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return ['message' => "The product was deleted"];
    }
}
