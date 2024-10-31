# Laravel Multi-Wallet Package

**Package Name**: `towoju5/laravel-wallet`  
**Description**: A multi-wallet package for Laravel applications, with support for user roles, currency-based wallets, and transaction logging.

## Table of Contents
- [Features](#features)
- [Installation](#installation)
  - [Step 1: Install the Package via Composer](#step-1-install-the-package-via-composer)
  - [Step 2: Publish Configuration and Migrations](#step-2-publish-configuration-and-migrations)
  - [Step 3: Run Migrations](#step-3-run-migrations)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Creating and Accessing Wallets](#creating-and-accessing-wallets)
  - [Depositing Funds](#depositing-funds)
  - [Withdrawing Funds](#withdrawing-funds)
  - [Viewing Balance](#viewing-balance)
  - [Transaction Logs](#transaction-logs)
  - [Example Usage in Code](#example-usage-in-code)
- [Database Structure](#database-structure)

## Features

- **Multiple Wallets per User**: Each user can have multiple wallets, such as for different currencies or purposes.
- **Role-Based Wallets**: Wallets are tied to user roles, allowing users to manage funds based on their active role.
- **Transaction Tracking**: Full tracking of deposits, withdrawals, and balances, with detailed transaction logs.
- **Flexible Balance Storage**: Balances are stored as integers in the database (in "cents" format) to avoid floating-point inaccuracies.

## Features

- **Multiple Wallets per User**: Each user can have multiple wallets, such as for different currencies or purposes.
- **Role-Based Wallets**: Wallets are tied to user roles, allowing users to manage funds based on their active role.
- **Transaction Tracking**: Full tracking of deposits, withdrawals, and balances, with detailed transaction logs.
- **Flexible Balance Storage**: Balances are stored as integers in the database (in "cents" format) to avoid floating-point inaccuracies.

## Installation

### Step 1: Install the Package via Composer

```bash
composer require towoju5/laravel-wallet
```

### Step 2: Publish Configuration and Migrations

After installation, publish the configuration file and migrations:

```bash
php artisan vendor:publish --provider="Towoju5\\Wallet\\Providers\\WalletServiceProvider"
```

### Step 3: Run Migrations

Run the migrations to create the `wallets` and `_transaction` tables.

```bash
php artisan migrate
```

## Configuration

The default currency for wallets can be set in the configuration file:

```php
// config/wallet.php
return [
    'default_currency' => 'usd',
];
```

## Usage

### Creating and Accessing Wallets

Each user can have multiple wallets based on currency or other criteria. Use the `getWallet()` method to create or retrieve a wallet for the user:

```php
$user = User::find(1); // Retrieve a user instance
$wallet = $user->getWallet('usd'); // Retrieve or create a wallet for USD currency
```

### Depositing Funds

Add funds to the wallet with the `deposit()` method. Optionally, you can include metadata or descriptions for the transaction:

```php
$wallet->deposit(100, ['description' => 'Task completed', 'meta' => ['task_id' => 123]]);
```

### Withdrawing Funds

Withdraw funds from the wallet with the `withdraw()` method, similarly passing metadata as needed:

```php
$wallet->withdraw(50, ['description' => 'Purchase of domain', 'meta' => ['order_id' => 456]]);
```

### Viewing Balance

Retrieve the wallet’s balance, automatically converted from stored format:

```php
echo $wallet->balance; // E.g., "10.50" for 1050 stored in cents
```

### Transaction Logs

Each transaction is logged in the `_transaction` table with the following attributes:

- `type`: Transaction type (`deposit` or `withdrawal`).
- `amount`: The amount involved in the transaction.
- `balance_before` and `balance_after`: Track balance changes.
- `description`: Additional context or notes on the transaction.
- `_account_type`: Indicates the role-based context for the transaction.

### Example Usage in Code

```php
$user = User::find(1);
$wallet = $user->getWallet('usd');

$wallet->deposit(1000, ['description' => 'Initial deposit']); // Adds $10.00 in USD
$wallet->withdraw(250, ['description' => 'Payment for service']); // Deducts $2.50

echo "Current Balance: " . $wallet->balance; // Output balance as a decimal value

// For currency swap or conversion

use Towoju5\LaravelWallet\Models\Wallet;
use Towoju5\LaravelWallet\Services\WalletService;
use Towoju5\LaravelWallet\Services\CurrencyExchangeService;

// Assuming dependency injection or manual instantiation
$walletService = new WalletService(new CurrencyExchangeService());

// Create wallets
$usdWallet = Wallet::create(['user_id' => $user->id, 'currency' => 'usd']);
$eurWallet = Wallet::create(['user_id' => $user->id, 'currency' => 'eur']);

// Deposit in USD wallet
$usdWallet->deposit(1000, ['description' => 'Initial deposit in USD']);

// Transfer funds from USD wallet to EUR wallet
$walletService->transferBetweenCurrencies($usdWallet, $eurWallet, 500);

```

## Database Structure

1. **`wallets`**: Stores wallet information for each user and role combination.
   - `user_id`: User who owns the wallet.
   - `role`: User role associated with the wallet (e.g., 'admin', 'general').
   - `currency`: Wallet currency (e.g., 'usd', 'eur').
   - `balance`: Stored in integer format (cents) for precision.

2. **`_transaction`**: Logs all wallet transactions with relevant details.
   - `type`: Transaction type (`deposit` or `withdrawal`).
   - `balance_before`: Balance before transaction.
   - `balance_after`: Balance after transaction.
   - `meta`: Additional data (JSON format).
   - `_account_type`: Active user role when the transaction occurred.

---

Add screenshots of the following to demonstrate:
1. Database tables (`wallets` and `_transaction`) with example data.
2. Example code execution and output showing wallet creation, deposits, and withdrawals.

---

This setup should help you create a seamless, multi-currency, role-based wallet system in your Laravel application. Let me know if you'd like more specific guidance on adding the screenshots or other package improvements!
