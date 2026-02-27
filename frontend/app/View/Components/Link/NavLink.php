<?php

namespace App\View\Components\Link;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavLink extends Component
{
    // public $route, $icon, $label;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $route,
        public $icon = null,
        public $label = null
    ) {
        $this->route = $route;
        $this->icon = $icon;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.link.nav-link');
    }
}
