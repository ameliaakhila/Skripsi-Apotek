<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelolaAkunController extends Controller
{
    public function index()
    {
        $data = [
            [
                'id_user' => 1,
                'username' => 'Admin',
                'status' => 'Aktif'
            ],
            [
                'id_user' => 2,
                'username' => 'Apotek',
                'status' => 'Aktif'
            ],
            [
                'id_user' => 3,
                'username' => 'Manajer',
                'status' => 'Nonaktif'
            ]
        ];
        return view('components.kelola_akun', compact('data'));
    }
}
