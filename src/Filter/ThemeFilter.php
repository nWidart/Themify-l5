<?php namespace Nwidart\Themify\Filter;

use Illuminate\Events\Dispatcher as EventDispatcher;
use Nwidart\Themify\Themify;

class ThemeFilter
{

    /**
     * @var \Nwidart\Themify\Themify
     */
    protected $themify;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    public function __construct(Themify $themify, EventDispatcher $events)
    {
        $this->themify = $themify;
        $this->events = $events;
    }

    public function filter()
    {
        if (($theme = $this->themify->get()) !== null) {
            $this->events->fire('theme.set', [$theme, 5]);
        }
    }
}
