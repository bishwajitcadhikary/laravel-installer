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

namespace AbnDevs\Installer\Http\Controllers;

use AbnDevs\Installer\Facades\License;
use AbnDevs\Installer\Http\Requests\StoreDatabaseRequest;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jackiedo\DotenvEditor\DotenvEditor;

class DatabaseController extends Controller
{
    public function __construct(readonly DotenvEditor $dotenvEditor)
    {
        $this->dotenvEditor->load(base_path('.env'));
        $this->dotenvEditor->autoBackup(false);
    }

    public function index()
    {
        $license = License::verify();

        if (! $license['status']) {
            flash($license['message'], 'error');

            return redirect()->route('installer.license.index');
        }

        return view('installer::database');
    }

    public function store(StoreDatabaseRequest $request)
    {
        $license = License::verify();

        if (! $license['status']) {
            return response()->json([
                'status' => 'error',
                'message' => $license['message'],
                'redirect' => route('installer.license.index'),
            ]);
        }

        try {
            $this->checkDatabaseConnection($request);

            // Check if database is empty
            $tables = DB::select('SHOW TABLES');

            if ($tables) {
                return error('Database is not empty. Please select another database');
            }
        } catch (Exception $e) {
            return error('Please check your database credentials');
        }

        // Save database credentials
        try {
            $this->dotenvEditor->setKeys([
                'DB_CONNECTION' => $request->validated('driver'),
                'DB_HOST' => $request->validated('db_host'),
                'DB_PORT' => $request->validated('db_port'),
                'DB_DATABASE' => $request->validated('db_name'),
                'DB_USERNAME' => $request->validated('db_username'),
                'DB_PASSWORD' => $request->validated('db_password'),
            ]);

            $this->dotenvEditor->save();

            // Migrate database
            Artisan::call('migrate:fresh --seed --force');
            Artisan::call('key:generate --force');
            Artisan::call('storage:link');

            Storage::disk('local')->put('database_created', now()->toDateTimeString());
            Cache::put('installer.database', true);

            return response()->json([
                'status' => 'success',
                'message' => 'Database credentials saved successfully',
                'redirect' => route('installer.smtp.index'),
            ]);
        } catch (Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(StoreDatabaseRequest $request): void
    {
        $driver = $request->validated('driver');

        $settings = config("database.connections.$driver");

        $connectionArray = array_merge($settings, [
            'driver' => $driver,
            'database' => $request->validated('database'),
            'username' => $request->validated('username'),
            'password' => $request->validated('password'),
            'host' => $request->validated('host'),
            'port' => $request->validated('port'),
        ]);

        config([
            'database' => [
                'migrations' => 'migrations',
                'default' => $driver,
                'connections' => [$driver => $connectionArray],
            ],
        ]);

        DB::purge($driver);

        DB::connection()->getPdo();
    }
}
