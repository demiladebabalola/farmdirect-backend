<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'avatar',
        'farm_name',
        'location',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
	public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isApproved(): bool
{
    return $this->status === 'approved';
}

    // Products this user lists (only relevant if role = farmer)
    public function products()
    {
        return $this->hasMany(Product::class, 'farmer_id');
    }

    // Negotiations where this user is the buyer
    public function buyerNegotiations()
    {
        return $this->hasMany(Negotiation::class, 'buyer_id');
    }

    // Negotiations where this user is the farmer
    public function farmerNegotiations()
    {
        return $this->hasMany(Negotiation::class, 'farmer_id');
    }

    // Orders placed by this user (as customer)
    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    // Orders received by this user (as farmer)
    public function ordersAsFarmer()
    {
        return $this->hasMany(Order::class, 'farmer_id');
    }

    // Reviews this farmer has received
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'farmer_id');
    }
}
