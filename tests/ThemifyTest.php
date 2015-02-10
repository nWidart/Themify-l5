<?php

use Mockery as M;
use Nwidart\Themify\Themify;

class ThemifyTest extends Orchestra\Testbench\TestCase
{

    /**
     * @var \Nwidart\Themify\Themify
     */
    protected $t;

    public function setUp()
    {
        parent::setUp();

        $this->t = $this->app->make('themify');

       // $this->app['router']->enableFilters();
    }

    public function tearDown()
    {
        M::close();
    }

    public function testThemeIsBeingSet()
    {
        // Explicitly set theme
        $this->t->set('footheme');

        // Check it's been set properly
        $this->assertEquals($this->t->get(), 'footheme');
    }

    public function testReturnsResolvedThemeWhenOwnThemeIsNull()
    {
        // We don't call set() explicitly,
        // so next priority would be $themify->resolve(),
        // which we are mocking here
        $resolver = $this->mockResolver();
        $resolver->shouldReceive('resolve')
            ->once()
            ->andReturn('bartheme');

        // Use a Themify instance with a mocked resolver
        $t = new Themify(
            $resolver,
            $this->mockViewFinder(),
            $this->mockDispatcher(),
            $this->mockConfig()
        );

        // Check that the theme being returned is the
        // one that the resolver found
        $this->assertEquals($t->get(), 'bartheme');
    }

    public function testFilterDetectsControllerTheme()
    {
        // Create mock controller with all needed methods
        // and $theme property
        $c = $this->mockController('FooController', 'footheme');

        // Bind route to mocked controller with existing $theme property
        // and call the route bound to it
        $this->app['router']->get('foo', 'FooController@foo');
        $this->call('GET', 'foo');

        $this->assertEquals($this->t->get(), 'footheme');
    }

    public function testSetThemeOverridesControllerTheme()
    {
        // Create mock controller with all needed methods
        // and $theme property
        $c = $this->mockController('FooController', 'footheme');

        // Add a after filter to app, to set a different theme
        $this->app->after(function ($request) {
            $this->t->set('bartheme');
        });

        // Bind route to mocked controller with existing $theme property
        // and call the route bound to it
        $this->app['router']->get('foo', 'FooController@foo');
        $this->call('GET', 'foo');

        // Check that the final theme is the one set in the filter
        // As set() should have priority over the controller property
        $this->assertEquals($this->t->get(), 'bartheme');
    }

    public function testControllerThemeDoesNotOverridePreviousSet()
    {
        // Create mock controller with all needed methods
        // and $theme property
        $c = $this->mockController('FooController', 'footheme');

        // Add a before filter to app, to set a different theme
        $this->app->before(function ($request) {
            $this->t->set('bartheme');
        });

        // Bind route to mocked controller with existing $theme property
        // and call the route bound to it
        $this->app['router']->get('foo', 'FooController@foo');
        $this->call('GET', 'foo');

        // Check that the final theme is the one set in the filter
        // As set() should have priority over the controller property
        $this->assertEquals($this->t->get(), 'bartheme');
    }

    /**
     *
     */
    protected function mockResolver()
    {
        return M::mock('Nwidart\Themify\Resolver\Resolver');
    }

    /**
     *
     */
    protected function mockViewFinder()
    {
        return M::mock('Nwidart\Themify\Finder\ThemeViewFinder');
    }

    /**
     *
     */
    protected function mockDispatcher()
    {
        $events = M::mock('Illuminate\Events\Dispatcher');
        $events->shouldReceive('listen')->once();

        return $events;
    }

    /**
     *
     */
    protected function mockConfig()
    {
        return M::mock('Illuminate\Config\Repository');
    }

    /**
     *
     */
    protected function mockController($name, $theme)
    {
        $controller = M::mock($name);

        $controller->shouldReceive('getAfterFilters')
            ->once()
            ->andReturn(array());

        $controller->shouldReceive('getBeforeFilters')
            ->once()
            ->andReturn(array());

        $controller->shouldReceive('callAction')
            ->once()
            ->withAnyArgs();

        $controller->theme = $theme;

        // Bind controller class to the mocked controller
        $this->app->bind($name, function ($app) use ($controller) {
            return $controller;
        });

        return $controller;
    }

    /**
     * Override Orchestra\Testbench methods.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Nwidart\Themify\ThemifyServiceProvider'];
    }

    /**
     * Override Orchestra\Testbench methods.
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return ['Nwidart\Themify\Facades\Themify'];
    }
}
