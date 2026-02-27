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
        public $obatMasukList = [],
        public $satuan = [],
        public $kategori = [],
        public $d = null,
        public $obat = [],
        public $kodeObat = null,
        public $kodeBatch = null
    ) {
        $this->type = $type;
        $this->judul = $judul;
        $this->obatMasukList = $obatMasukList;
        $this->satuan = $satuan;
        $this->kategori = $kategori;
        $this->d = $d;
        $this->kodeObat = $kodeObat;
        $this->kodeBatch = $kodeBatch;
        $this->obat = $obat;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modals.modal-obat-masuk');
    }
}
