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

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class InstallationMiddleware
{
    /**
     * Check if the installation is complete
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Storage::disk('local')->exists('installed')) {
            return redirect()->route('installer.agreement.index');
        }

        return $next($request);
    }
}
