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

namespace AbnDevs\Installer;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

if ((@ini_get('max_execution_time') !== '0') && (@ini_get('max_execution_time')) < 600) {
    @ini_set('max_execution_time', 600);
}
@ini_set('memory_limit', '256M');

class Installer
{
    private Client $client;

    private string $productID;

    private string $currentVersion;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->productID = config('installer.license.product_id');
        $this->currentVersion = config('installer.license.current_version');
    }

    public function isDatabaseInstalled(): bool
    {
        if (Storage::disk('local')->exists('database_created')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the license is valid
     */
    public function checkUpdate(): PromiseInterface|Response
    {
        return $this->client->post('/api/check_update', [
            'product_id' => $this->productID,
            'current_version' => $this->currentVersion,
        ]);
    }

    /**
     * Download the update file it will follow the respective order
     */
    public function downloadUpdate(): array
    {
        ini_set('max_execution_time', 300);

        $update = $this->checkUpdate();

        if ($update->json('status')) {
            $mainFilePath = $this->downloadMainFile($update);

            if ($update->json('has_sql')) {
                $sqlFilePath = $this->downloadSqlFile($update);
            }
        }

        return [
            'version' => $update->json('version'),
            'main_file' => $mainFilePath ?? null,
            'sql_file' => $sqlFilePath ?? null,
        ];
    }

    /**
     * Update the downloaded file
     */
    public function update(): void
    {
        $files = $this->downloadUpdate();

        if (isset($files['main_file'])) {
            $this->updateMainFile($files['main_file']);
        }

        if (isset($files['sql_file'])) {
            $this->updateSqlFile($files['sql_file']);
        }

        $this->updateVersion($files['version']);
    }

    /**
     * Get the license file
     */
    private function getLicenseFile(): bool|string|null
    {
        $path = storage_path('app/.license');

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return null;
    }

    /**
     * Download the main file
     */
    private function downloadMainFile(PromiseInterface|Response $update): ?string
    {
        try {
            // Check if the update is already downloaded
            if (file_exists(base_path('/update_main_'.$update->json('version').'.zip'))) {
                return base_path('/update_main_'.$update->json('version').'.zip');
            }

            // Download the update
            $response = $this->client->post('/api/download_update/main/'.$update->json('update_id'), [
                'license_file' => $this->getLicenseFile(),
            ]);

            // Check if the download is successful
            if ($response->successful()) {
                $mainFilePath = base_path('/update_main_'.$update->json('version').'.sql');

                // Save the file
                file_put_contents($mainFilePath, $response->body());

                return $mainFilePath;
            }

            return null;
        } catch (Exception $e) {
            // Delete the file if it is already downloaded
            if (isset($mainFilePath)) {
                @chmod($mainFilePath, 0777);
                if (is_writable($mainFilePath)) {
                    unlink($mainFilePath);
                }
            }

            flash($e->getMessage(), 'error');

            return null;
        }
    }

    /**
     * Download the sql file
     */
    private function downloadSqlFile(PromiseInterface|Response $update): ?string
    {
        try {
            // Check if the update is already downloaded
            if (file_exists(base_path('/update_sql_'.$update->json('version').'.sql'))) {
                return base_path('/update_sql_'.$update->json('version').'.sql');
            }

            // Download the update
            $response = $this->client->post('/api/download_update/sql/'.$update->json('update_id'), [
                'license_file' => $this->getLicenseFile(),
            ]);

            if ($response->successful()) {
                $sqlFilePath = base_path('/update_sql_'.$update->json('version').'.sql');

                // Save the file
                file_put_contents($sqlFilePath, $response->body());

                return $sqlFilePath;
            }

            return null;
        } catch (Exception $e) {
            // Delete the file if it is already downloaded
            if (isset($sqlFilePath)) {
                @chmod($sqlFilePath, 0777);
                if (is_writable($sqlFilePath)) {
                    unlink($sqlFilePath);
                }
            }

            flash($e->getMessage(), 'error');

            return null;
        }
    }

    /**
     * Extract the main file
     */
    private function updateMainFile(mixed $main_file): void
    {
        try {
            $zip = new ZipArchive;
            $res = $zip->open($main_file);

            if ($res === true) {
                $zip->extractTo(base_path());
                $zip->close();

                @chmod($main_file, 0777);
                if (is_writable($main_file)) {
                    unlink($main_file);
                }

            }
        } catch (Exception $e) {
            flash($e->getMessage(), 'error');
        }
    }

    /**
     * Update the sql file
     */
    private function updateSqlFile(mixed $sql_file): void
    {
        try {
            DB::unprepared(file_get_contents($sql_file));

            @chmod($sql_file, 0777);
            if (is_writable($sql_file)) {
                unlink($sql_file);
            }
        } catch (Exception $e) {
            flash($e->getMessage(), 'error');
        }
    }

    /**
     * Update the version
     */
    private function updateVersion(mixed $version): void
    {
        @chmod(base_path('.env'), 0777);

        if (is_writable(base_path('.env'))) {
            // Get the content of the .env file
            $env = file_get_contents(base_path('.env'));

            // Check if the APP_VERSION key is present
            if (! str_contains($env, 'APP_VERSION=')) {
                // Add the APP_VERSION key if it doesn't exist
                $env .= "\nAPP_VERSION=".$version;
            } else {
                // Replace the version
                $env = preg_replace('/^APP_VERSION=(.*)$/m', 'APP_VERSION='.$version, $env);
            }

            // Save the .env file
            file_put_contents(base_path('.env'), $env);
        }
        @chmod(base_path('.env'), 0644);
    }
}
