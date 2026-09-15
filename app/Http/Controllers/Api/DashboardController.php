<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET /api/dashboard/customer
    // Shapes response like the frontend's `customerDashboard` mock object
    public function customer(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isCustomer(), 403);

        $orders = $user->ordersAsCustomer()->with('product')->latest()->take(5)->get();

        return response()->json([
            'name' => $user->name,
            'avatar' => $user->avatar,
            'recentOrders' => $orders->map(fn ($o) => $o->toFrontendArray()),
        ]);
    }

    // GET /api/dashboard/farmer
    // Shapes response like the frontend's `farmerDashboard` mock object
    public function farmer(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isFarmer(), 403);

        $orders = $user->ordersAsFarmer()->with('product')->latest()->take(5)->get();
        $pendingNegotiations = $user->farmerNegotiations()
            ->whereIn('status', ['Offer Sent', 'Counter-Offer Received'])
            ->with(['product', 'buyer'])
            ->get();

        return response()->json([
            'farm' => $user->farm_name,
            'avatar' => $user->avatar,
            'stats' => [
                'products' => $user->products()->count(),
                'pendingOrders' => $user->ordersAsFarmer()->where('status', 'Processing')->count(),
                'negotiations' => $pendingNegotiations->count(),
                'sales' => (int) $user->ordersAsFarmer()->where('status', 'Delivered')->sum('total'),
            ],
            'recentOrders' => $orders->map(fn ($o) => [
                'name' => $o->product->name,
                'ref' => '#'.$o->reference,
                'image' => $o->product->image,
                'total' => $o->total,
                'status' => $o->status,
            ]),
            'bids' => $pendingNegotiations->map(fn ($n) => [
                'productId' => $n->product->slug,
                'buyer' => $n->buyer->name,
                'offer' => $n->buyer_offer,
            ]),
        ]);
    }
}
