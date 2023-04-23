<?php
/**
 * @version 1.0.0
 * @link https://codecanyon.net/user/abndevs/portfolio
 * @author Bishwajit Adhikary
 * @copyright (c) 2023 abnDevs
 * @license https://codecanyon.net/licenses/terms/regular
 **/

namespace AbnDevs\Installer;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InstallerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('installer')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasRoute('web')
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->startWith(function(InstallCommand $command) {
                        $command->info('Hello, and welcome to my great new package!');
                    })
                    ->publishConfigFile()
                    ->publishAssets()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('bishwajitcadhikary/laravel-installer');
            });
    }
}
