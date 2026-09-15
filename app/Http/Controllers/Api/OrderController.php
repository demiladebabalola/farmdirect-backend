<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Negotiation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // GET /api/orders — returns orders for the logged-in user, scoped by their role
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = $user->isFarmer()
            ? $user->ordersAsFarmer()->with('product')->latest()->get()
            : $user->ordersAsCustomer()->with('product')->latest()->get();

        return response()->json($orders->map(fn ($o) => $o->toFrontendArray()));
    }

    // POST /api/orders — create an order from an accepted negotiation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'negotiation_id' => 'required|exists:negotiations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $negotiation = Negotiation::with('product')->findOrFail($validated['negotiation_id']);

        if ($negotiation->buyer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($negotiation->status !== 'Accepted' || ! $negotiation->settled_price) {
            return response()->json(['message' => 'Negotiation must be accepted before ordering.'], 422);
        }

        $order = Order::create([
            'reference' => 'FD-'.strtoupper(Str::random(6)),
            'customer_id' => $negotiation->buyer_id,
            'farmer_id' => $negotiation->farmer_id,
            'product_id' => $negotiation->product_id,
            'negotiation_id' => $negotiation->id,
            'quantity' => $validated['quantity'],
            'total' => $negotiation->settled_price * $validated['quantity'],
            'status' => 'Processing',
        ]);

        return response()->json($order->load('product')->toFrontendArray(), 201);
    }

    // PATCH /api/orders/{id}/status — farmer updates order status
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->farmer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Processing,Ready for Pickup,In Transit,Delivered,Cancelled',
        ]);

        $order->update($validated);

        return response()->json($order->toFrontendArray());
    }
}
