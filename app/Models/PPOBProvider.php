<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPOBProvider extends Model
{
    use HasFactory;

    protected $table = 'ppob_providers';

    protected $fillable = [
        'name',
        'code',
        'api_endpoint',
        'credentials',
        'is_enabled',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(PPOBProduct::class, 'provider_id');
    }

    public function transactions()
    {
        return $this->hasMany(PPOBTransaction::class, 'provider_id');
    }
}
