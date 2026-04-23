<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category_id',
        'cost_price',
        'base_price',
        'is_active',
        'description',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function branches()
    {
        return $this->hasMany(ProductBranch::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(PurchaseBatch::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
