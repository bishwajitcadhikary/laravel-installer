<?php
namespace KinDigi\Installer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KinDigi\Installer\License
 */
class License extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \KinDigi\Installer\License::class;
    }
}
