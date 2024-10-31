<?php

namespace Towoju5\LaravelWallet\Services;

use Illuminate\Support\Facades\Http;

class CurrencyExchangeService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('wallet.cryptocompare.api_key');
        $this->baseUrl = 'https://min-api.cryptocompare.com/data/';
    }

    /**
     * Fetch the exchange rate between two currencies.
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float|null
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): ?float
    {
        $response = Http::get($this->baseUrl . 'price', [
            'fsym' => strtoupper($fromCurrency),
            'tsyms' => strtoupper($toCurrency),
            'api_key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data[strtoupper($toCurrency)] ?? null;
        }

        return null;
    }

    /**
     * Convert an amount from one currency to another.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float|null
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): ?float
    {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        
        return $rate ? $amount * $rate : null;
    }
}
