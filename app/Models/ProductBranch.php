<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'branch_id',
        'stock',
        'selling_price',
        'margin_percent',
        'is_active',
    ];

    protected $casts = [
        'stock' => 'decimal:3',
        'selling_price' => 'decimal:2',
        'margin_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
