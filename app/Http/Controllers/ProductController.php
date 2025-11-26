<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return[
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
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

        $product = $request->user()->products()->create($fields);

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
        Gate::authorize('modify', $product);

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
        Gate::authorize('modify', $product);
        
        $product->delete();

        return ['message' => "The product was deleted"];
    }
}
