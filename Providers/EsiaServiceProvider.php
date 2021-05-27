<?php

namespace Modules\Esia\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Laravel\Socialite\Facades\Socialite;

class EsiaServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Esia';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'esia';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerAssets();

        Socialite::extend('esia', function (Container $container) {
            return $container->make(EsiaSocialiteProvider::class);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(EsiaSocialiteProvider::class, function (Container $container) {
            $config = $container->make('config');

            $request = $container->make('request');

            if ($request->has('scopes')) {
                Session::put('scopes', $request->scopes);
            }

            $scopes = Session::get('scopes');

            return new EsiaSocialiteProvider(
                $request,
                $config->get('esia.clientId'),
                '',
                $config->get('esia.redirectUrl'),
                [
                    'signer' => $config->get('esia.signer'),
                    'certPath' => $config->get('esia.certPath'),
                    'privateKeyPath' => $config->get('esia.privateKeyPath'),
                    'privateKeyPassword' => $config->get('esia.privateKeyPassword'),
                    'tmpPath' => $config->get('esia.tmpPath'),
                ],
                array_map('trim', explode(',', $scopes)),
                $config->get('esia.test')
            );
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/esia.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/esia.php'), $this->moduleNameLower
        );
    }

    /**
     * Register assets.
     *
     * @return void
     */
    protected function registerAssets()
    {
        $this->publishes([
            module_path($this->moduleName, 'Resources/assets') => public_path('modules/' . $this->moduleNameLower),
        ], 'public');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
