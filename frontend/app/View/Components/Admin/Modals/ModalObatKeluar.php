<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalObatKeluar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $type = 'default',
        public $judul = null,
        public $obatList = [],
        public $satuan = [],
        public $kategori = [],
        public $d = null,
        public $obat = [],
        public $kodeBatch = [],
        public $supplier = [],
        public $cabang = [],
    ) {
        $this->type = $type;
        $this->judul = $judul;
        $this->obatList = $obatList;
        $this->satuan = $satuan;
        $this->kategori = $kategori;
        $this->d = $d;
        $this->supplier = $supplier;
        $this->obat = $obat;
        $this->kodeBatch = $kodeBatch;
        $this->cabang = $cabang;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('apoteker.modals.modal-obat-keluar');
    }
}
