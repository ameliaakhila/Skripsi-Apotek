<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalObatMasuk extends Component
{
    /**
     * Create a new component instance.
     */
    public $type, $d, $obatMasukList, $obat, $satuanList, $kategori, $kodeObat;

    public function __construct(
        $type = 'default',
        $obatMasukList = [],
        $satuanList = [],
        $kategori = [],
        $d = null,
        $obat = [],
        $kodeObat = null
    ) {
        $this->type = $type;
        $this->obatMasukList = $obatMasukList;
        $this->satuanList = $satuanList;
        $this->kategori = $kategori;
        $this->d = $d;
        $this->kodeObat = $kodeObat;
        $this->obat = $obat;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modals.modal_obat_masuk');
    }
}
