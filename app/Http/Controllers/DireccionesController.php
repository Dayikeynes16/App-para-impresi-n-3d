<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
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
    public function direccionEntrega(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        $validatedData = $request->validate([
            'direccion_id' => 'required',
        ]);
        $carrito = $this->obtenerCarrito($user_id);
    

        $direccion = Direccion::where('id', $request->input('direccion_id'))->where('usuario_id', $user_id)->first();
    
        if (!$direccion) {
            return response()->json(['error' => 'Dirección no válida o no pertenece al usuario.'], 400);
        }
    

        $carrito->direccion_id = $direccion->id;
        $carrito->recoleccion = false;
        // $carrito->status = 'pagada';
        $carrito->save();
    
        return response()->json(['message' => 'Dirección asignada correctamente.', 'direccion' => $direccion, 'carrito' => $carrito], 200);
    }
    


    public function obtenerCarrito($user_id)
    {
        $carrito = Carrito::with('productos.producto','orden.files')->firstorCreate([
            'status' => 'activo',
            'usuario_id' => $user_id
        ], [
            'total' => 0,
            'status' => 'activo',
            'direccion_id' => null
        ]);
        return $carrito;
       
    }

    public function direccionEntregaSucursal(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        $carrito = $this->obtenerCarrito($user_id);
        $carrito->recoleccion = true;
        $carrito->direccion_id = null;
        $carrito->save();

    
        return response()->json(['message' => 'se ha programado para recoger en sucursal',  'carrito' => $carrito], 200);
    }
}
