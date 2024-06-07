<?php
namespace KinDigi\Installer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KinDigi\Installer\Installer
 */
class Installer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \KinDigi\Installer\Installer::class;
    }
}
