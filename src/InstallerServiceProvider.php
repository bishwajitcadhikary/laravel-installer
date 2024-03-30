<?php
namespace WovoSoft\Installer;

use WovoSoft\Installer\Http\Middleware\InstallationMiddleware;
use WovoSoft\Installer\Http\Middleware\LicensedMiddleware;
use WovoSoft\Installer\Http\Middleware\RedirectIfInstalledMiddleware;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
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
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to my great new package!');
                    })
                    ->publishConfigFile()
                    ->publishAssets()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('bishwajitcadhikary/laravel-installer');
            });
    }

    /**
     * @throws BindingResolutionException
     */
    public function bootingPackage()
    {
        parent::bootingPackage();

        $this->app['router']->aliasMiddleware('install', InstallationMiddleware::class);
        $this->app['router']->aliasMiddleware('installed', RedirectIfInstalledMiddleware::class);
        $this->app['router']->aliasMiddleware('licensed', LicensedMiddleware::class);

        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddlewareToGroup('web', InstallationMiddleware::class);
        $kernel->prependMiddlewareToGroup('web', LicensedMiddleware::class);
    }
}
