<?php namespace Nwidart\Themify\Finder;

use Illuminate\View\FileViewFinder;

/**
 * Add functionality to the original FileViewFinder from
 * Laravel, to add view paths at the beginning of the paths
 * array, so they get preference against the rest of view paths.
 *
 * Since the main package class will replace FileViewFinder
 * in IoC container with this class, it will be used transparently
 * by the application.
 */
class ThemeViewFinder extends FileViewFinder
{

    /**
     * Location of the last theme added to paths.
     *
     * @var string $prevLocation
     */
    protected $prevLocation;

    /**
     * Priority that the last theme had when set.
     *
     * @var int prevPriority
     */
    protected $prevPriority;

    /**
     * Prepend a location to the finder paths, instead of
     * appending. This way, theme views have priority.
     *
     * @param string $location
     * @param int $priority Priority of the setter method in reverse order
     * @return void
     */
    public function addThemeLocation($location, $priority)
    {
        // Make changes only if the priority number is lower
        if ($this->prevPriority === null || $priority < $this->prevPriority) {
            // If present, remove old location from $paths
            // If not, prepend it to $paths
            if (($pos = $this->hasPreviousThemeLocation()) !== false) {
                array_splice($this->paths, $pos, 1, $location);
            } else {
                array_unshift($this->paths, $location);
            }

            $this->prevLocation = $location;
            $this->prevPriority = $priority;
        }
    }

     /**
     * Check if a previous theme location has been added to $paths.
     *
     * @return mixed Index of the $location if found, false if not
     */
    protected function hasPreviousThemeLocation()
    {
        return $this->prevLocation ? array_search($this->prevLocation, $this->paths) : false;
    }
}
