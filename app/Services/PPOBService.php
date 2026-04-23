<?php

namespace App\Services;

use App\Models\PPOBProduct;
use App\Models\PPOBTransaction;
use Illuminate\Support\Str;

class PPOBService
{
    public function createTransaction(PPOBProduct $product, int $branchId, int $userId, array $payload = []): PPOBTransaction
    {
        return PPOBTransaction::create([
            'provider_id' => $product->provider_id,
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'created_by' => $userId,
            'external_reference' => Str::uuid()->toString(),
            'amount' => $product->price,
            'fee' => $product->price - $product->cost,
            'status' => 'pending',
            'response' => $payload,
        ]);
    }

    public function completeTransaction(PPOBTransaction $transaction, array $responseData): PPOBTransaction
    {
        $transaction->update([
            'status' => $responseData['status'] ?? 'completed',
            'response' => $responseData,
        ]);

        return $transaction;
    }
}
