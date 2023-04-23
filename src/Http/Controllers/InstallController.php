<?php
/**
 * @version 1.0.0
 * @link https://codecanyon.net/user/abndevs/portfolio
 * @author Bishwajit Adhikary
 * @copyright (c) 2023 abnDevs
 * @license https://codecanyon.net/licenses/terms/regular
 **/

namespace AbnDevs\Installer\Http\Controllers;

use AbnDevs\Installer\Facades\License;
use AbnDevs\Installer\Http\Requests\StoreAgreementRequest;
use AbnDevs\Installer\Facades\Installer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class InstallController extends Controller
{
    public function index()
    {
        Cache::clear();

        return view('installer::index');
    }

    public function store(StoreAgreementRequest $request)
    {
        if ($request->validated('agree')) {
            Cache::put('installer.agreement', true);

            return redirect()->route('installer.requirements.index');
        }
    }

    public function finish()
    {
        // Check if License is verified
        $verifyLicense = License::verify();
        if (!$verifyLicense['status']) {
            flash($verifyLicense['message'], 'error');
            return redirect()->route('installer.license.index');
        }

        // Check if Database is installed
        $databaseInstalled = Installer::isDatabaseInstalled();

        if (!$databaseInstalled) {
            flash('Please install database first.', 'error');
            return redirect()->route('installer.database.index');
        }

        // Check if SMTP is configured
        if (!Cache::get('installer.smtp')) {
            flash('Please configure SMTP first.', 'error');
            return redirect()->route('installer.smtp.index');
        }

        // Check if external routes are configured
        if(config('installer.external')){
            // Get last route
            $lastRoute = collect(config('installer.external'))->last();

            // Check if last route is configured
            if(!Cache::get("installer.{$lastRoute['cache']}")){
                flash("Please configure {$lastRoute['title']} first", 'error');
                return redirect()->route($lastRoute['index']);
            }
        }

        Cache::clear();

        return view('installer::finish');
    }
}
