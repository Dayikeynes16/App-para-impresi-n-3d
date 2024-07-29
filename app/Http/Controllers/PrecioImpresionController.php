<?php

namespace App\Http\Controllers;

use App\Models\PrecioMinuto;
use Illuminate\Http\Request;

class PrecioImpresionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(["data" => PrecioMinuto::first()]);
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
    public function show(PrecioMinuto $precioMinuto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrecioMinuto $precioMinuto)
    {   
        $request->validate([
            'precio' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],
        ]);
        $precioMinuto = PrecioMinuto::find(1);
        $precioMinuto->update([
            'precio' => $request->input('precio')
        ]);
        
        return response()->json(['data' => $precioMinuto]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrecioMinuto $precioMinuto)
    {
        //
    }
}
