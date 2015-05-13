<?php namespace Philf\Setting;

use Illuminate\Support\ServiceProvider;
use Philf\Setting\interfaces\LaravelFallbackInterface;

class SettingServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/setting.php';
        $this->publishes([
            $configPath => config_path('setting.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/setting.php';
        $this->mergeConfigFrom($configPath, 'setting');

        $this->app['setting'] = $this->app->share(function($app)
        {
            $path     = $app['config']['setting.path'];
            $filename = $app['config']['setting.filename'];

            return new Setting($path, $filename, $app['config']['setting.fallback'] ? new LaravelFallbackInterface() : null);
        });

        $this->app->booting(function($app)
        {
            if ($app['config']['setting.autoAlias'])
            {
                $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                $loader->alias('Setting', 'Philf\Setting\Facades\Setting');
            }
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('setting');
    }

}
