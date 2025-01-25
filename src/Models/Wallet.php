<?php

namespace Towoju5\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Towoju5\Wallet\Exceptions\WalletException;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'balance',
        'meta',
        'title',
        'is_department',
        'department_id',
        'role'
    ];

    protected $casts = [
        // 'balance' => 'string',
        'meta' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wallet) {
            // var_dump($wallet['currency']); exit;
            $wallet->meta = [
                'meta' => [
                    'code' => $wallet->currency,
                    'symbol' => $wallet->getCurrencySymbol($wallet->currency),
                ],
                'name' => "{$wallet->currency} Wallet",
                'slug' => $wallet->currency,
            ];
        });
    }

    // Fetching currency symbol based on currency code
    private function getCurrencySymbol($currency)
    {
        if(is_string($currency)) {
            $symbols = [
                'ngn' => '₦',
                'usd' => '$'
            ];
        } else {
            return null;
        }

        return $symbols[$currency] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = (int) ($value * 100);
    }

    public function getBalanceAttribute($value)
    {
        return $value / 100;
    }

    public function deposit($amount, array $meta = [])
    {
        if ($amount <= 0) {
            throw new \Exception("Deposit amount must be positive.");
        }
        // var_dump($this->balance); exit;
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $balanceAfter = $this->balance;

        $this->save();

        return $this->transactions()->create([
            'type' => 'deposit',
            'amount' => (int) ($amount * 100),
            'balance_before' => (int) ($balanceBefore * 100),
            'balance_after' => (int) ($balanceAfter * 100),
            'decimal_places' => 2,
            'meta' => $meta,
            'description' => $meta['description'] ?? 'Deposit',
            '_account_type' => $this->user->current_role ?? 'general',
        ]);
    }

    public function withdraw($amount, array $meta = [])
    {
        return $this->withdrawal($amount, $meta);
    }
    
    public function withdrawal($amount, array $meta = [])
    {
        if ($amount > $this->balance) {
            throw new \Exception("Insufficient balance.");
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $balanceAfter = $this->balance;

        $this->save();

        return $this->transactions()->create([
            'type' => 'withdrawal',
            'amount' => (int) ($amount * 100),
            'balance_before' => (int) ($balanceBefore * 100),
            'balance_after' => (int) ($balanceAfter * 100),
            'decimal_places' => 2,
            'meta' => $meta,
            'description' => $meta['description'] ?? 'Withdrawal',
            '_account_type' => $this->user->current_role ?? 'general',
        ]);
    }
}
