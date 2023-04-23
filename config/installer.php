<?php
/**
 * @version 1.0.0
 * @link https://codecanyon.net/user/abndevs/portfolio
 * @author Bishwajit Adhikary
 * @copyright (c) 2023 abnDevs
 * @license https://codecanyon.net/licenses/terms/regular
 **/

// config for AbnDevs/Installer
return [
    'prefix' => env('INSTALLER_PREFIX', 'installer'),

    'license' => [
        'product_id' => '',
        'api_url' => 'https://license.abndevs.net',
        'api_key' => '',
        'api_language' => 'english',
        'current_version' => env('APP_VERSION', 'v1.0.0'),
        'verify_type' => 'Regular License', // Regular License, Extended License
        'verification_period' => 3,
    ],

    /*
   |--------------------------------------------------------------------------
   | Server Requirements
   |--------------------------------------------------------------------------
   |
   | This is the default Laravel server requirements, you can add as many
   | as your application require, we check if the extension is enabled
   | by looping through the array and run "extension_loaded" on it.
   |
   */
    'core' => [
        'minPhpVersion' => '8.1.0',
    ],
    'final' => [
        'key' => true,
        'publish' => false,
    ],
    'requirements' => [
        'php' => [
            'cType',
            'cURL',
            'DOM',
            'FileInfo',
            'Filter',
            'Hash',
            'Mbstring',
            'OpenSSL',
            'PCRE',
            'PDO',
            'PDO_MySQL',
            'Session',
            'Tokenizer',
            'XML',
            'XMLWriter',
            'JSON',
            'BCMath',
            'GD',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list below.
    |
    */
    'permissions' => [
        base_path('storage/framework/') => '775',
        base_path('storage/logs/') => '775',
        base_path('bootstrap/cache/') => '775',
    ],
];
