<?php

namespace MrConnect;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as SP;

class MrConnectServiceProvider extends SP
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadConfig();
        $this->loadRoutes();
        $this->loadMigrations();
    }

    private function loadConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/mrconnect.php' => config_path('mrconnect.php'),
            ],
            'whatsapp-config'
        );
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/whatsapp.php');

        $this->publishes(
            [
                __DIR__ . '/../routes' => base_path('routes'),
            ],
            'whatsapp-routes'
        );
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes(
            [
                __DIR__ . '/../database/migrations' => base_path('database/migrations'),
            ],
            'whatsapp-migrations'
        );
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'package-name-view');

        $this->publishes(
            [
                __DIR__ . '/../resources/views' => resource_path('views/vendor/package-name'),
            ],
            'whatsapp-views'
        );
    }

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'package-name-trans');

        $this->publishes(
            [
                __DIR__ . '/../resources/lang' => resource_path('lang'),
            ],
            'whatsapp-lang'
        );
    }

    private function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');

        $this->publishes(
            [
                __DIR__ . '/../database/factories' => base_path('database/factories'),
            ],
            'whatsapp-factories'
        );
    }

    private function loadComponents(): void
    {
        Blade::component('package-name-view::components.my-component', 'my_component');
    }

    private function loadClassComponents(): void
    {
        $this->loadViewComponentsAs('package-name-comp', [
        ]);
    }

    private function loadCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
            ]);
        }
    }
}
