<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPOBTransaction extends Model
{
    use HasFactory;

    protected $table = 'ppob_transactions';

    protected $fillable = [
        'provider_id',
        'product_id',
        'branch_id',
        'created_by',
        'external_reference',
        'amount',
        'fee',
        'status',
        'response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'response' => 'array',
    ];

    public function provider()
    {
        return $this->belongsTo(PPOBProvider::class, 'provider_id');
    }

    public function product()
    {
        return $this->belongsTo(PPOBProduct::class, 'product_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
