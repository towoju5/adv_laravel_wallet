<?php

namespace Towoju5\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $table = 'wallet_transactions';

    protected $fillable = [
        'wallet_id', 
        'type', 
        'amount', 
        'balance_before', 
        'balance_after', 
        'decimal_places', 
        'meta', 
        'description', 
        '_account_type'
    ];

    protected $casts = [
        'meta' => 'array',
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'decimal_places' => 'integer',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getBalanceBeforeAttribute($value)
    {
        return $value / 100;
    }

    public function getBalanceAfterAttribute($value)
    {
        return $value / 100;
    }
}
