<?php

use Mockery as M;
use Nwidart\Themify\Filter\ThemeFilter;

class FilterTest extends PHPUnit_Framework_TestCase
{

    protected $filter;
    protected $themify;
    protected $events;

    public function setUp()
    {
        $this->mockThemify();
        $this->mockDispatcher();
        $this->createFilter();
    }

    public function tearDown()
    {
        M::close();
    }

    public function testFiresEventIfThemeIsFound()
    {
        // Mock themify so it founds a theme
        $this->themify->shouldReceive('get')
            ->once()
            ->andReturn('footheme');

        // Expect the event dispatcher to run fire()
        // with these params.
        // If not, Mockery will throw an exception
        $this->events->shouldReceive('fire')
            ->once()
            ->with('theme.set', array('footheme', 5));

        $this->filter->filter();
    }

    /**
     *
     */
    protected function mockThemify()
    {
        $this->themify = M::mock('Nwidart\Themify\Themify');
    }

    /**
     *
     */
    protected function mockDispatcher()
    {
        $this->events = M::mock('Illuminate\Events\Dispatcher');
    }

    /**
     *
     */
    protected function createFilter()
    {
        $this->filter = new ThemeFilter($this->themify, $this->events);
    }
}
