<?php
/**
 * @version 1.0.0
 * @link https://codecanyon.net/user/abndevs/portfolio
 * @author Bishwajit Adhikary
 * @copyright (c) 2023 abnDevs
 * @license https://codecanyon.net/licenses/terms/regular
 **/

namespace AbnDevs\Installer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AbnDevs\Installer\License
 */
class License extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \AbnDevs\Installer\License::class;
    }
}
