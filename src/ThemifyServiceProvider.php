<?php namespace Nwidart\Themify;

use Illuminate\Support\ServiceProvider;
use Nwidart\Themify\Filter\ThemeFilter;
use Nwidart\Themify\Finder\ThemeViewFinder;
use Nwidart\Themify\Resolver\Resolver;

class ThemifyServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->registerConfiguration();

        $this->registerResolver();
        $this->registerViewFinder();
        $this->registerMainClass();
        $this->registerThemeFilter();
    }

    /**
     * Add a package before filter that gets executed for every request.
     * @return void
     */
    public function boot()
    {
        // $this->app['router']->filter('themify.resolve', 'ThemeFilter');
        // $this->app['router']->when('*', 'themify.resolve');
    }

    /**
     * Register the configuration file so Laravel can publish them
     * Also merges the published config file with original
     */
    private function registerConfiguration()
    {
        $configPath = __DIR__ . '/../config/themify.php';
        $this->mergeConfigFrom($configPath, 'themify');
        $this->publishes([$configPath => config_path('themify.php')]);
    }

    /**
     * Register Themify class in IoC container.
     * @return void
     */
    protected function registerMainClass()
    {
        $this->app['themify'] = $this->app->share(function ($app) {
            return new Themify(
                $app['themify.resolver'],
                $app['view.finder'],
                $app['events'],
                $app['config']
            );
        });
    }

    /**
     * Register Nwidart\Themify\Resolver\Resolver in IoC container.
     * @return void
     */
    protected function registerResolver()
    {
        $this->app->bindShared('themify.resolver', function ($app) {
            return new Resolver($app);
        });
    }

    /**
     * Register ThemeViewFinder class.
     * It will override Laravel default's ViewFinder
     * to provide functionality for searching theme views.
     * @return void
     */
    protected function registerViewFinder()
    {
        $this->app->bindShared('view.finder', function ($app) {
            $paths = $app['config']['view.paths'];

            return new ThemeViewFinder($app['files'], $paths);
        });
    }

    /**
     * Register Nwidart\Themify\Filter\ThemeFilter.
     * @return void
     */
    protected function registerThemeFilter()
    {
        $this->app->bind('ThemeFilter', function ($app) {
            return new ThemeFilter($app['themify'], $app['events']);
        });
    }
}
