<?php
namespace WovoSoft\Installer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WovoSoft\Installer\License
 */
class License extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \WovoSoft\Installer\License::class;
    }
}
