<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // GET /api/products?category=Vegetables&search=tomato
    // Powers the Browse page (browse.tsx)
    public function index(Request $request)
    {
        $query = Product::with('farmer');

        if ($request->filled('category') && $request->category !== 'All Produce') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->get()->map(fn ($p) => $p->toFrontendArray());

        return response()->json($products);
    }

    // GET /api/products/{slug}
    // Powers the Product Details page (product.$productId.tsx)
    public function show(string $slug)
    {
        $product = Product::with('farmer')->where('slug', $slug)->firstOrFail();

        return response()->json($product->toFrontendArray());
    }

    // POST /api/products (farmer only)
    public function store(Request $request)
    {
        $this->authorizeFarmer($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|integer|min:1',
            'unit' => 'required|string',
            'location' => 'required|string',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'stock' => 'required|string',
            'description' => 'nullable|string',
            'badge' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);
        $validated['farmer_id'] = $request->user()->id;

        $product = Product::create($validated);

        return response()->json($product->toFrontendArray(), 201);
    }

    // PUT /api/products/{slug} (farmer only, own products)
    public function update(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        if ($product->farmer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->update($request->only([
            'name', 'category', 'price', 'unit', 'location',
            'image', 'gallery', 'stock', 'description', 'badge',
        ]));

        return response()->json($product->toFrontendArray());
    }

    // DELETE /api/products/{slug} (farmer only, own products)
    public function destroy(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        if ($product->farmer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function authorizeFarmer(Request $request): void
{
    if (! $request->user() || ! $request->user()->isFarmer()) {
        abort(403, 'Only farmers can perform this action.');
    }
    if (! $request->user()->isApproved()) {
        abort(403, 'Your farmer account is still pending admin approval. You will be able to list products once approved.');
    }
}
}