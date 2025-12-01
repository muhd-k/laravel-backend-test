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
        return [
            new Middleware('auth:api', except: ['index', 'show'])
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     @OA\Response(response=200, description="List of products", @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Sample Product"),
     *             @OA\Property(property="description", type="string", example="Product description"),
     *             @OA\Property(property="quantity", type="integer", example=10),
     *             @OA\Property(property="unit_price_cents", type="integer", example=1000),
     *             @OA\Property(property="amount_sold", type="integer", example=5),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ))
     * )
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","quantity","unit_price_cents","amount_sold"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="unit_price_cents", type="integer"),
     *             @OA\Property(property="amount_sold", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Sample Product"),
     *         @OA\Property(property="description", type="string", example="Product description"),
     *         @OA\Property(property="quantity", type="integer", example=10),
     *         @OA\Property(property="unit_price_cents", type="integer", example=1000),
     *         @OA\Property(property="amount_sold", type="integer", example=5),
     *         @OA\Property(property="created_at", type="string", format="date-time"),
     *         @OA\Property(property="updated_at", type="string", format="date-time")
     *     )),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product details", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Sample Product"),
     *         @OA\Property(property="description", type="string", example="Product description"),
     *         @OA\Property(property="quantity", type="integer", example=10),
     *         @OA\Property(property="unit_price_cents", type="integer", example=1000),
     *         @OA\Property(property="amount_sold", type="integer", example=5),
     *         @OA\Property(property="created_at", type="string", format="date-time"),
     *         @OA\Property(property="updated_at", type="string", format="date-time")
     *     )),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","quantity","unit_price_cents","amount_sold"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="unit_price_cents", type="integer"),
     *             @OA\Property(property="amount_sold", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated", @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Sample Product"),
     *         @OA\Property(property="description", type="string", example="Product description"),
     *         @OA\Property(property="quantity", type="integer", example=10),
     *         @OA\Property(property="unit_price_cents", type="integer", example=1000),
     *         @OA\Property(property="amount_sold", type="integer", example=5),
     *         @OA\Property(property="created_at", type="string", format="date-time"),
     *         @OA\Property(property="updated_at", type="string", format="date-time")
     *     )),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found")
     * )
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
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted", @OA\JsonContent(@OA\Property(property="message", type="string"))),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy(Product $product)
    {
        Gate::authorize('modify', $product);

        $product->delete();

        return ['message' => "The product was deleted"];
    }
}
