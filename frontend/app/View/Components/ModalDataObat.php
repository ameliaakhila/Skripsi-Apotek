<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalDataObat extends Component
{
    /**
     * Create a new component instance.
     */

    public $type, $d, $kategori;
    public function __construct($type = 'default', $d = [], $kategori = [])
    {
        $this->type = $type;
        $this->d = $d;
        $this->kategori = $kategori;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modals.modal_data_obat');
    }
}
