<?php

namespace Towoju5\Wallet\Traits;

use Towoju5\Wallet\Models\Wallet;
use Towoju5\Wallet\Services\WalletService;

trait HasWallets
{
    public function getWallet($currency = 'usd')
    {
        return WalletService::getWallet($this, $currency);
    }

    public function wallets($currency = 'usd')
    {
        $user = $this;
        $role = $user->current_role ?? 'general';
        $where = [
            'role' => $role,
            'user_id' => $user->id
        ];
        $wallets = Wallet::where($where);
        return $wallets;
    }

    public function createWallet($currency = 'usd')
    {
        $wallet = Wallet::updateOrCreate([
            'user_id' => $this->id,
            'currency' => $currency,
        ], [
            'user_id' => $this->id,
            'currency' => $currency,
        ]);
        return $wallet;
    }
}
