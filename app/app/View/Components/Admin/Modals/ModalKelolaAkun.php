<?php

namespace App\View\Components\Admin\Modals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalKelolaAkun extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $type = 'default',
        public $judul = null,
        public $d = null,
        public $id = null
    )
    {
        $this->type = $type;
        $this->judul = $judul;
        $this->d = $d;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.modals.modal-kelola-akun');
    }
}
