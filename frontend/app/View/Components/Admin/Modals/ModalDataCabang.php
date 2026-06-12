<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalDataCabang extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $judul,
        public $type,
        public $d = null,
        public $kodeCabang = []
    ) {
        $this->judul = $judul;
        $this->type = $type;
        $this->d = $d;
        $this->kodeCabang = $kodeCabang;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.modals.modal-data-cabang');
    }
}
