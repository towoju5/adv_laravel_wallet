<?php

namespace Towoju5\Wallet\Traits;

use Towoju5\Wallet\Services\WalletService;

trait HasWallets
{
    public function getWallet($currency = 'usd')
    {
        return WalletService::getWallet($this, $currency);
    }
}
