<?php

namespace App\View\Components\Admin\Modals;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalDataSatuan extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $judul = []
    ) {
        $this->judul = $judul;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modals.modal-data-satuan');
    }
}
