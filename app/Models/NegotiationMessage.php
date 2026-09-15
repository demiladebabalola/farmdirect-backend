<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegotiationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'negotiation_id',
        'side',
        'text',
    ];

    public function negotiation()
    {
        return $this->belongsTo(Negotiation::class);
    }

    // Shapes this exactly like the frontend's `NegotiationMessage` type
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'side' => $this->side,
            'text' => $this->text,
            'time' => $this->created_at->timezone('Africa/Lagos')->format('h:i A'),
        ];
    }
}
