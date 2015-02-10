<?php namespace Nwidart\Themify\Resolver;

use Illuminate\Container\Container;

class Resolver
{

    /**
    * @var \Illuminate\Foundation\Application $app
    */
    protected $app;

    /**
     * Constructor.
     * @param Container|\Illuminate\Foundation\Application $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Resolve which theme has to be rendered in current request.
     * Order of preference is:
     *      - Controller theme
     *      - Default theme in package configuration
     *
     * @return mixed String with theme name or void if not found
     */
    public function resolve()
    {
        // Try to find a $theme property in current controller
        if (($theme = $this->getControllerTheme()) !== null) {
            return $theme;
        }

        // Return default theme in configuration options
        if (($theme = $this->app->make('config')->get('themify.default_theme')) !== '') {
            return $theme;
        }
    }

    /**
     * Try to get $theme property from possible current controller being
     * executed for the current route.
     *
     * @return mixed
     */
    protected function getControllerTheme()
    {
        $controller = $this->getCurrentController();

        if ($controller !== null && isset($controller->theme) && trim($controller->theme) !== '') {
            return $controller->theme;
        }
    }

    /**
     * Get an instance of the possible current controller
     * being executed for the current route.
     *
     * @return mixed
     */
    protected function getCurrentController()
    {
        $router = $this->app->make('router');
        $route = $router->currentRouteAction();

        if (($pos = strpos($route, '@')) !== false) {
            $controllerName = substr($route, 0, $pos);

            return $this->app[$controllerName];
        }
    }
}
