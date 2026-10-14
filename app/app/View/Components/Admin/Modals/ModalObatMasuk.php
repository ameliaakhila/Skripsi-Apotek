<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalObatMasuk extends Component
{
    /**
     * Create a new component instance.
     */

    public function __construct(
        public $type = 'default',
        public $judul = null,
        public $kodeBatch = null,

        public $d = null,
        public $data = [],
        public $obat = [],
    ) {
        $this->type = $type;
        $this->judul = $judul;
        $this->kodeBatch = $kodeBatch;
        $this->d = $d;
        $this->data = $data;
        $this->obat = $obat;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('apoteker.modals.modal-obat-masuk');
    }
}
