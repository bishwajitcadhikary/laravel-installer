<?php
namespace WovoSoft\Installer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WovoSoft\Installer\Installer
 */
class Installer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \WovoSoft\Installer\Installer::class;
    }
}
