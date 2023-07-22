<?php
namespace AbnDevs\Installer\Http\Controllers;

use AbnDevs\Installer\Facades\License;
use AbnDevs\Installer\Http\Requests\StoreSMTPRequest;
use App\Http\Controllers\Controller;
use Brotzka\DotenvEditor\DotenvEditor;
use Illuminate\Support\Facades\Cache;

class SMTPController extends Controller
{
    public function __construct(readonly DotenvEditor $dotenvEditor)
    {

    }

    public function index()
    {
        $verifyLicense = License::verify();

        if (! $verifyLicense['status']) {
            flash($verifyLicense['message'], 'error');

            return redirect()->route('installer.license.index');
        }

        return view('installer::smtp');
    }

    public function store(StoreSMTPRequest $request)
    {
        $driver = $request->validated('driver');

        $settings = config("mail.mailers.{$driver}");

        $connectionArray = array_merge($settings, [
            'transport' => $driver,
            'host' => $request->validated('host'),
            'port' => $request->validated('port'),
            'username' => $request->validated('username'),
            'password' => $request->validated('password'),
            'encryption' => $request->validated('encryption'),
        ]);

        config([
            'mail.default' => $driver,
            'mail.mailers' => [
                $driver => $connectionArray,
            ],
            'mail.from' => [
                'name' => $request->validated('name'),
                'address' => $request->validated('email'),
            ],
        ]);

        try {

            $this->dotenvEditor->addData([
                'MAIL_MAILER' => $driver,
                'MAIL_HOST' => $request->validated('host'),
                'MAIL_PORT' => $request->validated('port'),
                'MAIL_USERNAME' => $request->validated('username'),
                'MAIL_PASSWORD' => $request->validated('password'),
                'MAIL_ENCRYPTION' => $request->validated('encryption'),
                'MAIL_FROM_ADDRESS' => $request->validated('email'),
                'MAIL_FROM_NAME' => $request->validated('name'),
            ]);

            Cache::put('installer.smtp', true);

            // Check if external routes are available
            if (config('installer.admin.show_form')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'SMTP Configured Successfully',
                    'redirect' => route('installer.admin.index'),
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'SMTP Configured Successfully',
                    'redirect' => route('installer.finish.index'),
                ]);
            }

        } catch (\Exception $e) {
            Cache::forget('installer.smtp');
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
