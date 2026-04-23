<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPOBProduct extends Model
{
    use HasFactory;

    protected $table = 'ppob_products';

    protected $fillable = [
        'provider_id',
        'code',
        'name',
        'category',
        'cost',
        'price',
        'margin_percent',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
        'margin_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function provider()
    {
        return $this->belongsTo(PPOBProvider::class, 'provider_id');
    }

    public function transactions()
    {
        return $this->hasMany(PPOBTransaction::class, 'product_id');
    }
}
