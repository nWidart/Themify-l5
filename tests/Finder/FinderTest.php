<?php

use Mockery as M;
use Nwidart\Themify\Finder\ThemeViewFinder;

class ThemeViewFinderTest extends PHPUnit_Framework_TestCase
{

    protected $files;
    protected $viewFinder;
    protected $location = '../../app/themes';

    public function setUp()
    {
        $this->mockFilesystem();
    }

    public function tearDown()
    {
        M::close();
    }

    public function testCanAddLocation()
    {
        $this->createViewFinder();
        $this->viewFinder->addThemeLocation($this->location, 1);

        $this->assertContains($this->location, $this->viewFinder->getPaths());
    }

    public function testLocationIsAddedAtTheBeginningOfArray()
    {
        $initialPaths = array(
            '../../app/views',
            '../../app/Acme/views',
        );

        $this->createViewFinder($initialPaths);
        $this->viewFinder->addThemeLocation($this->location, 1);
        $paths = $this->viewFinder->getPaths();

        $this->assertEquals($paths[0], $this->location);
    }

    public function testPreviousLocationIsReplacedWhenAnotherIsAddedWithHigherPriority()
    {
        $this->createViewFinder();
        $anotherLocation = '../../app/Acme/views';

        // Add first location and check it's there
        $this->viewFinder->addThemeLocation($this->location, 5);
        $this->assertContains($this->location, $this->viewFinder->getPaths());

        // Add another location with higher priority
        // Remember priority uses reverse order, so 1 is higher than 5
        $this->viewFinder->addThemeLocation($anotherLocation, 1);

        // Check it only contains the newer location
        $this->assertNotContains($this->location, $this->viewFinder->getPaths());
        $this->assertContains($anotherLocation, $this->viewFinder->getPaths());
    }

    public function testPreviousLocationIsNotReplacedWhenAnotherIsAddedWithLowerPriority()
    {
        $this->createViewFinder();
        $anotherLocation = '../../app/Acme/views';

        // Add first location and check it's there
        $this->viewFinder->addThemeLocation($this->location, 3);
        $this->assertContains($this->location, $this->viewFinder->getPaths());

        // Add another location with lower priority
        // Remember priority uses reverse order, so 1 is higher than 5
        $this->viewFinder->addThemeLocation($anotherLocation, 5);

        // Check it only contains the first location
        $this->assertContains($this->location, $this->viewFinder->getPaths());
        $this->assertNotContains($anotherLocation, $this->viewFinder->getPaths());
    }

    /**
     *
     */
    protected function mockFileSystem()
    {
        $this->files = M::mock('Illuminate\Filesystem\Filesystem');
    }

    /**
     *
     */
    protected function createViewFinder(array $paths = array())
    {
        $this->viewFinder = new ThemeViewFinder($this->files, $paths);
    }
}
