<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalDataSupplier extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $type = 'default',
        public $judul = null,
        public $data = []
    ) {
        $this->type = $type;
        $this->judul = $judul;
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modals.modal-data-supplier');
    }
}
