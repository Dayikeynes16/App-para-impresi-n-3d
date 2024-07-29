<?php

namespace App\Http\Controllers;

use App\Models\CostoEnvio;
use Illuminate\Http\Request;

class CostoEnvioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data' => CostoEnvio::first()]);
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
    public function show(CostoEnvio $costoEnvio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CostoEnvio $costoEnvio , Request $request)
    {
        $costoEnvio = CostoEnvio::find(1);
        $costoEnvio->update([
            'precio' => $request->input('precio')
        ]);
        
        return response()->json(['data' => $costoEnvio]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostoEnvio $costoEnvio)
    {
        //
    }
}
