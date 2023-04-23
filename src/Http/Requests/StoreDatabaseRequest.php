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

namespace AbnDevs\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDatabaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'driver' => ['required', 'string', 'in:mysql,pgsql,sqlsrv'],
            'host' => ['required', 'string'],
            'port' => ['required', 'numeric'],
            'database' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
