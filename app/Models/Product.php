<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function getImageUrlAttribute(): string
    {
        $meta = is_array($this->meta) ? $this->meta : [];
        $imageUrl = $meta['image_url'] ?? null;

        if (is_string($imageUrl) && trim($imageUrl) !== '') {
            if (Str::startsWith($imageUrl, ['http://', 'https://', 'data:image'])) {
                return $imageUrl;
            }

            return asset(ltrim($imageUrl, '/'));
        }

        return $this->placeholderImage();
    }

    private function placeholderImage(): string
    {
        $name = trim((string) $this->name);
        $initials = collect(explode(' ', $name))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        $initials = $initials !== '' ? $initials : 'PR';
        $label = $this->category?->name ?? 'Produk';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 420">
    <defs>
        <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#2563eb"/>
            <stop offset="55%" stop-color="#06b6d4"/>
            <stop offset="100%" stop-color="#0f172a"/>
        </linearGradient>
    </defs>
    <rect width="600" height="420" rx="36" fill="url(#g)"/>
    <circle cx="472" cy="96" r="72" fill="rgba(255,255,255,0.14)"/>
    <circle cx="124" cy="328" r="96" fill="rgba(255,255,255,0.08)"/>
    <text x="300" y="220" text-anchor="middle" font-size="108" font-family="Arial, sans-serif" font-weight="700" fill="#ffffff">{$initials}</text>
    <text x="300" y="286" text-anchor="middle" font-size="28" font-family="Arial, sans-serif" fill="rgba(255,255,255,0.92)">{$label}</text>
</svg>
SVG;

        return 'data:image/svg+xml;charset=UTF-8,'.rawurlencode($svg);
    }
}
