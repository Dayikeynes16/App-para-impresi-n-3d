<?php

namespace App\Http\Controllers;

use App\Models\FactorConversion;
use Illuminate\Http\Request;

class ConversionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data' => FactorConversion::first()]);
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
    public function show(FactorConversion $factorConversion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FactorConversion $factorConversion)
    {
        $factorConversion = FactorConversion::find(1);
        $factorConversion->update([
            'conversion' => $request->input('conversion')
        ]);

        return response()->json(['data' => $factorConversion]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FactorConversion $factorConversion)
    {
        //
    }
}
