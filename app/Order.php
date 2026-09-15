<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'customer_id',
        'farmer_id',
        'product_id',
        'negotiation_id',
        'quantity',
        'total',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function negotiation()
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Shapes this like the frontend's dashboard "recentOrders" entries
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->reference,
            'name' => $this->product->name,
            'image' => $this->product->image,
            'total' => $this->total,
            'status' => $this->status,
        ];
    }
}
