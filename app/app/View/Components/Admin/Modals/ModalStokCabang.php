<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalStokCabang extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $type = 'default',
        public $stok = []
    ) {
        $this->type = $type;
        $this->stok = $stok;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view(view: 'admin.modals.modal-stok-cabang');
    }
}
