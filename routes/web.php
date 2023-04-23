<?php
/**
 * @version 1.0.0
 * @link https://codecanyon.net/user/abndevs/portfolio
 * @author Bishwajit Adhikary
 * @copyright (c) 2023 abnDevs
 * @license https://codecanyon.net/licenses/terms/regular
 **/


use AbnDevs\Installer\Http\Controllers\DatabaseController;
use AbnDevs\Installer\Http\Controllers\InstallController;
use AbnDevs\Installer\Http\Controllers\LicenseController;
use AbnDevs\Installer\Http\Controllers\PermissionController;
use AbnDevs\Installer\Http\Controllers\RequirementController;
use AbnDevs\Installer\Http\Controllers\SMTPController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('installer.prefix', 'installer'), 'as' => 'installer.'], function (){
    Route::get('/', [InstallController::class, 'index'])->name('agreement.index');
    Route::post('/', [InstallController::class, 'store'])->name('agreement.store');

    Route::get('requirements', [RequirementController::class, 'index'])->name('requirements.index');
    Route::post('requirements', [RequirementController::class, 'store'])->name('requirements.store');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');

    Route::get('license', [LicenseController::class, 'index'])->name('license.index');
    Route::post('license', [LicenseController::class, 'store'])->name('license.store');

    Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
    Route::post('database', [DatabaseController::class, 'store'])->name('database.store');

    Route::get('smtp', [SMTPController::class, 'index'])->name('smtp.index');
    Route::post('smtp', [SMTPController::class, 'store'])->name('smtp.store');

    Route::get('finish', [InstallController::class, 'finish'])->name('finish.index');
});
