<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'farmer_id',
        'buyer_offer',
        'farmer_ask',
        'status',
        'settled_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function messages()
    {
        return $this->hasMany(NegotiationMessage::class)->orderBy('created_at');
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // Shapes this exactly like the frontend's `Negotiation` type in mock-data.ts
    public function toFrontendArray(): array
{
    return [
        'id' => $this->id,
        'productId' => $this->product->slug,
            'status' => $this->status,
            'buyerOffer' => $this->buyer_offer,
            'farmerAsk' => $this->farmer_ask,
            'messages' => $this->messages->map(fn ($m) => $m->toFrontendArray())->values(),
        ];
    }
}
