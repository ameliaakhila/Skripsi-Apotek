<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class App extends Component
{
    /**
     * The title of the page.
     */
    public string $title;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title = 'Sistem Apotek Sena Farma')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.app', ['title' => $this->title]);
    }
}
