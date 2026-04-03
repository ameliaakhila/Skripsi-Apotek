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
        public $dataStokBatch = null,
        public $dataCabangTambah = null,
        public $dataCabangEdit = null
    ) {
        // Fallback ke shared data dari controller jika atribut tidak dikirim dari view.
        $this->dataStokBatch = $dataStokBatch ?? view()->shared('dataStokBatch', []);
        $this->dataCabangTambah = $dataCabangTambah ?? view()->shared('dataCabangTambah', []);
        $this->dataCabangEdit = $dataCabangEdit ?? view()->shared('dataCabangEdit', []);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modals.modal-mutasi-stok');
    }
}
