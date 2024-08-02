<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Images;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductosController extends Controller
{
    function StoreProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric'
        ]);

        $product = Product::create(
            array_merge($request->all())
        );
        return response()->json(['data' => $product, 'code'=>200]);
    }

    function traerProductos(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|integer'
        ]);

        return Product::with('Imagenes')
            ->where('is_custom',false)
            ->paginate($request->get('perPage',6));
    }
    

    

    function eliminarProducto(Request $request){
        $id = $request->input('id');
        $imagenes = Images::where('producto_id',$id)->delete();
        $producto = Product::find($id);
        $producto->delete();
        return response(['data'=> 200, 'message'=>'producto eliminado con exito']);

    }


        function show(Product $producto)
    {
        return response()->json(['data' => $producto]);
    }

    function update(Product $producto, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric'
        ]);
        $producto->update($request->all());
        return response()->json(['data' => $producto]);
    }
}
