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

class StoreAgreementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'agree' => ['required', 'accepted'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
