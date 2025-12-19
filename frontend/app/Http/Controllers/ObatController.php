<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index()
    {
        $data = Obat::with('KategoriObat')->get();
        return view('components.main.data_obat', compact('data'));
    }
}
