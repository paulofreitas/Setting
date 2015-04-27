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
        $config = realpath(__DIR__.'/../../config/setting.php');
        $this->publishes([
            $config => config_path('setting.php'),
        ]);
        $this->mergeConfigFrom($config, 'philf/setting');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['setting'] = $this->app->share(function($app)
        {
            $path     = $app['config']['philf/setting.path'];
            $filename = $app['config']['philf/setting.filename'];

            return new Setting($path, $filename, $app['config']['philf/setting.fallback'] ? new LaravelFallbackInterface() : null);
        });

        $this->app->booting(function($app)
        {
            if ($app['config']['philf/setting.autoAlias'])
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
