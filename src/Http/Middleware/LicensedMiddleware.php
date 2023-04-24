<?php
/**
 * @version 1.0.0
 *
 * @link https://codecanyon.net/user/abndevs/portfolio
 *
 * @author Bishwajit Adhikary
 * @copyright (c) 2023 abnDevs
 * @license https://codecanyon.net/licenses/terms/regular
 **/

namespace AbnDevs\Installer\Http\Middleware;

use AbnDevs\Installer\Facades\License;
use Closure;
use Illuminate\Http\Request;

class LicensedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $verifyLicense = License::verify();

        if (! $verifyLicense['status']) {
            flash($verifyLicense['message'], 'error');

            return redirect()->route('installer.license.activation');
        }

        return $next($request);
    }
}
