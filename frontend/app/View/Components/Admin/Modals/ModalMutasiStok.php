<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalMutasiStok extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $type = 'default',
        public $judul = null,
        public $dataStokBatch = [],
        public $dataCabangTujuan = [],
        public $dataCabangAsal = []
    ) {
        // Fallback ke shared data dari controller jika atribut tidak dikirim dari view.
        $this->dataStokBatch = $dataStokBatch;
        $this->dataCabangTujuan = $dataCabangTujuan;
        $this->dataCabangAsal = $dataCabangAsal;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('shared.modals.modal-mutasi-stok');
    }
}
