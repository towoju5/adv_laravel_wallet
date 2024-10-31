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
    ];

    protected $casts = [
        'balance' => 'string',
        'meta' => 'array',
    ];

    // Example constructor
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->meta = [
            'meta' => [
                'code' => strtoupper($this->currency),
                'symbol' => $this->getCurrencySymbol($this->currency),
            ],
            'name' => "{$this->currency} Wallet",
            'slug' => $this->currency,
        ];
    }

    // Fetching currency symbol based on currency code
    private function getCurrencySymbol($currency)
    {
        $symbols = [
            'ngn' => '₦',
            // Add other currency symbols as needed
        ];

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
        if ($amount <= 0)
            throw new WalletException("Deposit amount must be positive.");

        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $balanceAfter = $this->balance;

        $this->save();

        $this->transactions()->create([
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

    public function withdrawal($amount, array $meta = [])
    {
        if ($amount > $this->balance)
            throw new WalletException("Insufficient balance.");

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $balanceAfter = $this->balance;

        $this->save();

        $this->transactions()->create([
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
