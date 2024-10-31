<?php

namespace Towoju5\Wallet\Services;

use Towoju5\Wallet\Models\Wallet;

class WalletService
{
    protected $currencyExchangeService;
    
    public static function getWallet($user, $currency = 'usd')
    {
        $role = $user->current_role ?? 'general';
        return Wallet::firstOrCreate(['user_id' => $user->id, 'role' => $role, 'currency' => $currency]);
    }

    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    /**
     * Convert and transfer funds from one wallet to another with different currencies.
     *
     * @param Wallet $fromWallet
     * @param Wallet $toWallet
     * @param float $amount
     * @return bool
     */
    public function transferBetweenCurrencies(Wallet $fromWallet, Wallet $toWallet, float $amount): bool
    {
        $rate = $this->currencyExchangeService->getExchangeRate($fromWallet->currency, $toWallet->currency);
        if (!$rate) {
            throw new \Exception("Unable to fetch exchange rate between {$fromWallet->currency} and {$toWallet->currency}");
        }

        // Deduct from sender wallet
        $fromWallet->withdrawal($amount, [
            'description' => "Transfer to {$toWallet->title} at rate {$rate}"
        ]);

        // Convert and deposit to recipient wallet
        $convertedAmount = $amount * $rate;
        $toWallet->deposit($convertedAmount, [
            'description' => "Received from {$fromWallet->title} with rate {$rate}"
        ]);

        return true;
    }
}
