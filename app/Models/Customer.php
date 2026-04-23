<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'code',
        'loyalty_tier',
        'points_balance',
        'meta',
    ];

    protected $casts = [
        'points_balance' => 'decimal:2',
        'meta' => 'array',
    ];

    public function pointHistories()
    {
        return $this->hasMany(CustomerPointHistory::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
