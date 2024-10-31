<?php

namespace Towoju5\Wallet\Facades;

use Illuminate\Support\Facades\Facade;

class WalletFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wallet';
    }
}
