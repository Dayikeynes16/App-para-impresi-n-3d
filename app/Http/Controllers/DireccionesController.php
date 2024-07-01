<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Http\Request;

class DireccionesController extends Controller
{
    //
    public function guardarDireccion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'destinatario' => 'required|string',
            'telefono' => 'required|numeric|digits:10',
            'direccion' => 'required|string',
            'referencias' => 'required|string',
            'estado' => 'required|string',
            'codigo_postal' => 'required|numeric|digits:5'
        ]);

        $direccion = Direccion::create([
            'usuario_id' => $request->user()->id,
            'direccion' => $request->input('direccion'),
            'telefono' => $request->input('telefono'),
            'referencias' => $request->input('referencias'),
            'destinatario' => $request->input('destinatario'),
            'nombre' => $request->input('nombre'),
            'estado' => $request->input('estado'),
            'codigo_postal'=> $request->input('codigo_postal')
        ]);

        return response()->json(['data' => $direccion], 201);
    }

    function getDirecciones(Request $request)
    {
        $direcciones = Direccion::where('usuario_id', $request->user()->id)->get();
        return response()->json($direcciones);
    }

    function eliminarDireccion(Request $request)
    {
        $direccion = Direccion::find($request->input('id'));
        $direccion->delete();
        return response()->json(['data' => 200]);
    }
    public function actualizarDireccion(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:direcciones,id',
            'nombre' => 'required|string|max:255',
            'destinatario' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:15',
            'estado' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:6',
            'referencias' => 'nullable|string|max:255',
        ]);

        $direccion = Direccion::find($request->id);
        $direccion->nombre = $request->nombre;
        $direccion->destinatario = $request->destinatario;
        $direccion->direccion = $request->direccion;
        $direccion->telefono = $request->telefono;
        $direccion->estado = $request->estado;
        $direccion->codigo_postal = $request->codigo_postal;
        $direccion->referencias = $request->referencias;
        $direccion->save();

        return response()->json($direccion);
    }

    public function show(Direccion $direccion){
        return response()->json(['data'=>$direccion]);

    }
    public function update(Direccion $direccion, Request $request){
        $request->validate([
            'nombre'  => 'required|string',
            'destinatario' => 'required|string',
            'direccion'=> 'required|string',
            'telefono' => 'required|numeric|digits:10',
            'estado' => 'required|string',
            'codigo_postal' => 'required|numeric|digits:5',
            'referencias'  => 'required|string'
        ]);
        $direccion->update($request->all());
        return response()->json(['data'=>$direccion]);

    }


    // function update(Product $producto, Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'description' => 'required|string',
    //         'price' => 'required|numeric'
    //     ]);
    //     $producto->update($request->all());
    //     return response()->json(['data' => $producto]);
    // }
}
