<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2017  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace veteranmina\Seat\ContractStock;

use Illuminate\Routing\Router;
use Seat\Services\AbstractSeatPlugin;
use Seat\Web\Http\Middleware\Locale;

/**
 * Class ContractStockServiceProvider
 * @package veteranmina\Seat\ContractStock
 */
class ContractStockServiceProvider extends AbstractSeatPlugin
{

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {

        // Include the Routes
        $this->add_routes();

        // Add the views for ContractStock
        $this->add_views();

        //add commands for ContractStock
        $this->addCommands();

        // Add the migrations for ContractStock
        $this->add_migrations();

        // Include our translations
        $this->add_translations();

        // Add middleware
        $this->add_middleware($router);
    }

    public function register()
    {

        // Merge the config with anything in the main app
        // Web package configurations
        $this->mergeConfigFrom(
            __DIR__ . '/Config/contractstock.config.php', 'contractstock.config');
        $this->mergeConfigFrom(
            __DIR__ . '/Config/contractstock.permissions.php', 'web.permissions');
        $this->mergeConfigFrom(
            __DIR__ . '/Config/contractstock.locale.php', 'web.locale');

        // Menu Configurations
        $this->mergeConfigFrom(
            __DIR__ . '/Config/contractstock.sidebar.php', 'package.sidebar');

    }

    private function addCommands()
    {
        $this->commands([
            Commands\CorporationCertificateSync::class,
        ]);
    }

    private function add_migrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
    }

    /**
     * Include the routes
     */
    public function add_routes()
    {

        if (!$this->app->routesAreCached())
            include __DIR__ . '/Http/routes.php';
    }

    /**
     * Set the path and namespace for the vies
     */
    public function add_views()
    {

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'contractstock');
    }

    /**
     * Include the translations and set the namespace
     */
    public function add_translations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'contractstock');
    }

    /**
     * Include the middleware needed.
     *
     * @param $router
     */
    public function add_middleware($router)
    {
        // Localization support
        $router->aliasMiddleware('locale', Locale::class);
    }

    public function getName(): string
    {
        return 'Seat-ContractStock';
    }

    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/veteranmina/contractstock';
    }

    public function getPackagistPackageName(): string
    {
        return 'Seat-ContractStock';
    }

    public function getPackagistVendorName(): string
    {
        return 'veteranmina';
    }

    public function getVersion(): string
    {
        return config('contractstock.config.version');
    }

    public function getChangelogUri(): ?string
    {
        return 'https://raw.githubusercontent.com/veteranmina/contractstock/master/CHANGELOG.md';
    }
}
