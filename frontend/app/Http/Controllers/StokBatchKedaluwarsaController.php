<?php

namespace App\Http\Controllers;

use App\Models\Admin\StokBatch;
use App\Models\Admin\StokBatchKedaluwarsa;
use Illuminate\Http\Request;

class StokBatchKedaluwarsaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stok = StokBatch::all();
        // dd($stok->count());
        $title = "  Data Batch & Kedaluwarsa";
        return view('components.admin.stok-batch-kedaluwarsa', compact('stok', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StokBatchKedaluwarsa $stokBatchKedaluwarsa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokBatchKedaluwarsa $stokBatchKedaluwarsa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StokBatchKedaluwarsa $stokBatchKedaluwarsa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokBatchKedaluwarsa $stokBatchKedaluwarsa)
    {
        //
    }
}
