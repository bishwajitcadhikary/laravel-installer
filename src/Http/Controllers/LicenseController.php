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
use AbnDevs\Installer\Http\Requests\StoreLicenseRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class LicenseController extends Controller
{
    public function index()
    {
        if (!Cache::get('installer.agreement')) {
            flash('Please agree to the terms and conditions.', 'error');
            return redirect()->route('installer.agreement.index');
        }

        if (!Cache::get('installer.requirements')) {
            flash('Please check the requirements.', 'error');
            return redirect()->route('installer.requirements.index');
        }

        if (!Cache::get('installer.permissions')) {
            flash('Please check the permissions.', 'error');
            return redirect()->route('installer.permissions.index');
        }

        return view('installer::license');
    }

    public function store(StoreLicenseRequest $request)
    {
        $response = License::activate($request->validated('purchase_code'), $request->validated('envato_username'));

        if ($response['status']) {
            Cache::put('installer.license', true);

            return response()->json([
                'status' => 'success',
                'message' => $response['message'],
                'redirect' => route('installer.database.index'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response['message'],
            ], 422);
        }
    }
}
