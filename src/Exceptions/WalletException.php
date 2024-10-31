<?php

namespace Towoju5\LaravelWallet\Exceptions;

use Exception;

class WalletException extends Exception
{
    /**
     * Exception for when the wallet has insufficient funds.
     *
     * @param string $currency
     * @param float $balance
     * @param float $required
     * @return static
     */
    public static function insufficientFunds(string $currency = 'usd', float $balance = 0, float $required = 0): self
    {
        return new self(
            "Insufficient funds in wallet. Available balance: {$balance} {$currency}, required: {$required} {$currency}."
        );
    }

    /**
     * Exception for currency mismatch between wallets.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return static
     */
    public static function currencyMismatch(string $fromCurrency = '', string $toCurrency = ''): self
    {
        return new self(
            "Currency mismatch between wallets. From wallet currency: {$fromCurrency}, to wallet currency: {$toCurrency}."
        );
    }

    /**
     * Exception for invalid transaction.
     *
     * @param string $message
     * @return static
     */
    public static function invalidTransaction(string $message = "Invalid transaction operation."): self
    {
        return new self($message);
    }

    /**
     * Exception for general wallet error.
     *
     * @param string $message
     * @return static
     */
    public static function generalError(string $message = 'Error Encountered'): self
    {
        return new self($message);
    }
}
