<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function pendingFarmers(Request $request)
    {
        $this->authorizeAdmin($request);

        $farmers = User::where('role', 'farmer')
            ->where('status', 'pending')
            ->get(['id', 'name', 'email', 'farm_name', 'location', 'created_at']);

        return response()->json($farmers);
    }

    public function allFarmers(Request $request)
    {
        $this->authorizeAdmin($request);

        $farmers = User::where('role', 'farmer')
            ->get(['id', 'name', 'email', 'farm_name', 'location', 'status', 'created_at']);

        return response()->json($farmers);
    }

    public function approve(Request $request, User $user)
    {
        $this->authorizeAdmin($request);

        if ($user->role !== 'farmer') {
            return response()->json(['message' => 'This user is not a farmer.'], 422);
        }

        $user->update(['status' => 'approved']);

        return response()->json(['message' => 'Farmer approved.', 'user' => $user]);
    }

    public function suspend(Request $request, User $user)
    {
        $this->authorizeAdmin($request);

        if ($user->role !== 'farmer') {
            return response()->json(['message' => 'This user is not a farmer.'], 422);
        }

        $user->update(['status' => 'suspended']);

        return response()->json(['message' => 'Farmer suspended.', 'user' => $user]);
    }

    private function authorizeAdmin(Request $request): void
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Only admins can perform this action.');
        }
    }
}