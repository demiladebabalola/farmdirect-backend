<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'category',
        'price',
        'unit',
        'farmer_id',
        'location',
        'image',
        'gallery',
        'rating',
        'reviews_count',
        'stock',
        'description',
        'badge',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'price' => 'integer',
            'rating' => 'float',
        ];
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function negotiations()
    {
        return $this->hasMany(Negotiation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Shapes this product exactly like the frontend's `Product` type in mock-data.ts
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'unit' => $this->unit,
            'farmer' => $this->farmer->farm_name ?? $this->farmer->name,
            'farmerAvatar' => $this->farmer->avatar,
            'location' => $this->location,
            'image' => $this->image,
            'gallery' => $this->gallery ?? [],
            'rating' => $this->rating,
            'reviews' => $this->reviews_count,
            'stock' => $this->stock,
            'description' => $this->description,
            'badge' => $this->badge,
        ];
    }
}
