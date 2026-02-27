<?php

namespace App\View\Components\Admin\Modals;

use App\Models\Admin\DataCabang;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class ModalDataObat extends Component
{
    /**
     * Create a new component instance.
     */

    public function __construct(
        public $type = 'default',
        public $judul = [],
        public $d = [],
        public $satuan = [],
        public $supplier = [],
        public $kategori = []
    ) {
        $this->type = $type;
        $this->judul = $judul;
        $this->d = $d;
        $this->satuan = $satuan;
        $this->supplier = $supplier;
        $this->kategori = $kategori;
    }
    private function generateKodeCabang(): string
    {
        $lastCabang = DataCabang::orderBy('id', 'desc')->first();

        if ($lastCabang) {
            $lastNumber = (int) Str::after($lastCabang->kode_cabang, 'SF-');
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return 'SF' . $nextNumber;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modals.modal-data-obat');
    }
}
