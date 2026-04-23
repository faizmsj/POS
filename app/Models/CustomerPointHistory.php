<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'related_id',
        'related_type',
        'points',
        'balance',
        'type',
        'description',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function related()
    {
        return $this->morphTo();
    }
}
