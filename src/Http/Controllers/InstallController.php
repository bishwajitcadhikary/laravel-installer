<?php
namespace KinDigi\Installer\Http\Controllers;

use KinDigi\Installer\Facades\License;
use KinDigi\Installer\Http\Requests\StoreAgreementRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InstallController extends Controller
{
    public function index()
    {
        Cache::clear();

        $path = base_path(config('installer.user_agreement_file_path'));
        if (File::isFile($path)) {
            $agreement = file_get_contents($path);
        }else{
            $agreement = file_get_contents(__DIR__.'/../../../AGREEMENT.md');
        }

        return view('installer::index', [
            'agreement' => $agreement,
            'showAgreement' => config('installer.show_user_agreement'),
        ]);
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
        if (! $verifyLicense['status']) {
            flash($verifyLicense['message'], 'error');

            return redirect()->route('installer.license.index');
        }

        // Check if SMTP is configured
        if (! Cache::get('installer.smtp')) {
            flash('Please configure SMTP first.', 'error');

            return redirect()->route('installer.smtp.index');
        }

        Storage::disk('local')->put('installed', now());

        Cache::clear();

        if (config('installer.extra.command')){
            Artisan::call(config('installer.extra.command'));
        }

        return view('installer::finish');
    }
}
