<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Negotiation;
use App\Models\NegotiationMessage;
use App\Models\Product;
use Illuminate\Http\Request;

class NegotiationController extends Controller
{
    // GET /api/products/{slug}/negotiation
    // Powers negotiate.$productId.tsx on load — returns the buyer's existing
    // negotiation for this product, or creates a fresh one (mirrors the
    // frontend's `defaultNegotiation()` fallback behaviour).
    public function show(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $buyer = $request->user();

        $negotiation = Negotiation::with('messages')
            ->where('product_id', $product->id)
            ->where('buyer_id', $buyer->id)
            ->first();

        if (! $negotiation) {
            $negotiation = $this->startNegotiation($product, $buyer);
        }

        return response()->json($negotiation->toFrontendArray());
    }

    // POST /api/negotiations/{id}/offer
    // Buyer sends a new offer. Mirrors the frontend's sendOffer() logic:
    // if the offer meets or beats the farmer's ask, auto-accept; otherwise
    // record a counter-offer.
    public function sendOffer(Request $request, Negotiation $negotiation)
    {
        $this->authorizeBuyer($request, $negotiation);

        $validated = $request->validate([
            'offer' => 'required|integer|min:1',
        ]);
        $offer = $validated['offer'];

        NegotiationMessage::create([
            'negotiation_id' => $negotiation->id,
            'side' => 'buyer',
            'text' => "New offer: ₦{$offer} per {$negotiation->product->unit}.",
        ]);

        $negotiation->buyer_offer = $offer;

        if ($offer >= $negotiation->farmer_ask) {
            $negotiation->status = 'Accepted';
            $negotiation->settled_price = $offer;
            NegotiationMessage::create([
                'negotiation_id' => $negotiation->id,
                'side' => 'farmer',
                'text' => "Deal! ₦{$offer} per {$negotiation->product->unit} works. I'll pack it today.",
            ]);
        } else {
            $counter = max($offer, (int) round(($offer + $negotiation->farmer_ask) / 2));
            $negotiation->farmer_ask = $counter;
            $negotiation->status = 'Counter-Offer Received';
            NegotiationMessage::create([
                'negotiation_id' => $negotiation->id,
                'side' => 'farmer',
                'text' => "I can meet you at ₦{$counter} per {$negotiation->product->unit}. Final from my side.",
            ]);
        }

        $negotiation->save();

        return response()->json($negotiation->fresh('messages')->toFrontendArray());
    }

    // POST /api/negotiations/{id}/accept
    // Buyer accepts the farmer's current ask.
    public function accept(Request $request, Negotiation $negotiation)
    {
        $this->authorizeBuyer($request, $negotiation);

        NegotiationMessage::create([
            'negotiation_id' => $negotiation->id,
            'side' => 'buyer',
            'text' => "Accepted at ₦{$negotiation->farmer_ask} per {$negotiation->product->unit}. Please pack {$negotiation->product->name}.",
        ]);

        $negotiation->update([
            'status' => 'Accepted',
            'settled_price' => $negotiation->farmer_ask,
            'buyer_offer' => $negotiation->farmer_ask,
        ]);

        return response()->json($negotiation->fresh('messages')->toFrontendArray());
    }

    // POST /api/negotiations/{id}/reject
    // Buyer declines the farmer's current ask.
    public function reject(Request $request, Negotiation $negotiation)
    {
        $this->authorizeBuyer($request, $negotiation);

        NegotiationMessage::create([
            'negotiation_id' => $negotiation->id,
            'side' => 'buyer',
            'text' => "That's still above my budget — I'll pass for now.",
        ]);

        $negotiation->update(['status' => 'Rejected']);

        return response()->json($negotiation->fresh('messages')->toFrontendArray());
    }

    // POST /api/negotiations/{id}/message
    // Free-text chat message (not a price offer), sent by either side.
    public function sendMessage(Request $request, Negotiation $negotiation)
    {
        $user = $request->user();
        if ($negotiation->buyer_id !== $user->id && $negotiation->farmer_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate(['text' => 'required|string|max:1000']);
        $side = $negotiation->buyer_id === $user->id ? 'buyer' : 'farmer';

        NegotiationMessage::create([
            'negotiation_id' => $negotiation->id,
            'side' => $side,
            'text' => $validated['text'],
        ]);

        return response()->json($negotiation->fresh('messages')->toFrontendArray());
    }

    private function startNegotiation(Product $product, $buyer): Negotiation
    {
        $ask = $product->price;
        $openingOffer = (int) round($ask * 0.85);

        $negotiation = Negotiation::create([
            'product_id' => $product->id,
            'buyer_id' => $buyer->id,
            'farmer_id' => $product->farmer_id,
            'buyer_offer' => $openingOffer,
            'farmer_ask' => $ask,
            'status' => 'Counter-Offer Received',
        ]);

        NegotiationMessage::create([
            'negotiation_id' => $negotiation->id,
            'side' => 'buyer',
            'text' => "Hi, I'd like to offer ₦{$openingOffer} per {$product->unit} for a bulk order.",
        ]);
        NegotiationMessage::create([
            'negotiation_id' => $negotiation->id,
            'side' => 'farmer',
            'text' => "I can't go that low on a bulk order, but I can do ₦{$ask}.",
        ]);

        return $negotiation->load('messages');
    }

    private function authorizeBuyer(Request $request, Negotiation $negotiation): void
    {
        if ($negotiation->buyer_id !== $request->user()->id) {
            abort(403, 'Only the buyer on this negotiation can perform this action.');
        }
        if ($negotiation->status === 'Accepted' || $negotiation->status === 'Rejected') {
            abort(422, 'This negotiation is already settled.');
        }
    }
}
