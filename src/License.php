<?php

namespace AbnDevs\Installer;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;

if ((@ini_get('max_execution_time') !== '0') && (@ini_get('max_execution_time')) < 600) {
    @ini_set('max_execution_time', 600);
}
@ini_set('memory_limit', '256M');

class License
{
    private Client $client;

    private string $productID;

    private string $verificationType;

    private int $verificationPeriod;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->productID = config('installer.license.product_id');
        $this->verificationType = config('installer.license.verify_type');
        $this->verificationPeriod = 30;
    }

    public function checkConnection(): PromiseInterface|Response
    {
        return $this->client->post('/api/check_connection_ext');
    }

    public function getLatestVersion(): PromiseInterface|Response
    {
        return $this->client->post('/api/latest_version', [
            'product_id' => $this->productID,
        ]);
    }

    public function activate($purchaseCode, $clientName): PromiseInterface|Response
    {
        $response = $this->client->post('/api/activate_license', [
            'product_id' => $this->productID,
            'license_code' => $purchaseCode,
            'client_name' => $clientName,
            'verify_type' => $this->verificationType,
        ]);

        if ($response->successful() && $response->json('status')) {
            $this->saveLicense($response->json('lic_response'));
        } else {
            $this->removeLicense();
        }

        return $response;
    }

    public function verify($purchaseCode = null, $clientName = null, bool $timeBased = false): array
    {
        $localLicenseFile = $this->getLicenseFile();

        if ($timeBased && $this->verificationPeriod > 0) {
            match ($this->verificationPeriod) {
                1 => $this->verificationPeriod = '1 Day',
                3 => $this->verificationPeriod = '3 Days',
                7 => $this->verificationPeriod = '1 Week',
                30 => $this->verificationPeriod = '1 Month',
                90 => $this->verificationPeriod = '3 Months',
                365 => $this->verificationPeriod = '1 Year',
                default => $this->verificationPeriod = $this->verificationPeriod . ' Days',
            };
        }

        $lastCheckedAt = Cache::get('installer.last_checked_at') ?? now()->subDays($this->verificationPeriod + 1);

        if ($lastCheckedAt < now()->subDays($this->verificationPeriod) || $localLicenseFile === null) {
            $response = $this->client->post('/api/verify_license', [
                'product_id' => $this->productID,
                'license_code' => $purchaseCode,
                'client_name' => $clientName,
                'license_file' => $localLicenseFile,
            ]);

            if ($response->successful() && $response->json('status')) {
                Cache::put('installer.last_checked_at', now());
            } else {
                $this->removeLicense();
                Cache::forget('installer.last_checked_at');
            }

            return $response->json();
        }

        return [
            'status' => true,
            'message' => 'License verified successfully.',
            'data' => null,
        ];
    }

    public function deactivate($purchaseCode = null, $clientName = null): PromiseInterface|Response
    {
        $response = $this->client->post('/api/deactivate_license', [
            'product_id' => $this->productID,
            'license_code' => $purchaseCode,
            'client_name' => $clientName,
            'license_file' => $this->getLicenseFile(),
        ]);

        if ($response->successful() && $response->json('status')) {
            $this->removeLicense();
            Cache::forget('installer.last_checked_at');
        }

        return $response;
    }

    private function saveLicense(mixed $data): void
    {
        file_put_contents(storage_path('app/.license'), trim($data), LOCK_EX);
    }

    private function removeLicense(): void
    {
        if (file_exists(storage_path('app/.license'))) {
            if (!is_writable(storage_path('app/.license'))) {
                @chmod(storage_path('app/.license'), 0777);
            }

            unlink(storage_path('app/.license'));
        }
    }

    private function getLicenseFile(): bool|string|null
    {
        $path = storage_path('app/.license');

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return null;
    }
}
