<?php

namespace App\View\Components\Link;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavGroup extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $label,
        public $icon = null,
        public $route = null
    ) {
        $this->label = $label;
        $this->icon = $icon;
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.link.nav-group');
    }
}
