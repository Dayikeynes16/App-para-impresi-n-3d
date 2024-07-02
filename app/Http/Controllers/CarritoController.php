<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Files;
use App\Models\Orden;
use App\Models\Product;
use App\Models\Producto_Carrito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CarritoController extends Controller
{
    
    public function añadirCarrito(Request $request)
    {
        $producto = Product::find($request->input('id'));
        $cantidad = $request->input('cantidad');



        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $carrito = Carrito::firstOrCreate(
            [
                'status' => 'activo',
                'usuario_id' => $request->user()->id
            ],
            [
                'total' => 0,
                'status' => 'activo',
                'direccion_id' => null
            ]
        );

        $productoCarrito = Producto_Carrito::create([
            'producto_id' => $producto->id,
            'carrito_id' => $carrito->id,
            'total' => $producto->price * $cantidad,
            'cantidad' => $cantidad
        ]);

        $this->calcularCarrito($carrito->id);

        return response()->json($carrito);
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

    public function traerCarrito(Request $request)
    {
        $user = $request->user()->id;
        $carrito = $this->obtenerCarrito($user);
        return response()->json($carrito);
    }

    public function borrarProducto(Request $request)
    {
        $producto = Producto_Carrito::find($request->input('id'))->first();
        $carritoId = $producto->carrito_id;
        $producto->delete();
        $this->calcularCarrito($carritoId);
        return response()->json('exito');
    }
    public function actualizar(Request $request)
    {
        $productoCarrito = Producto_Carrito::with('producto')->find($request->input('id'));
        $producto = Product::find($productoCarrito->producto_id);
        $productoCarrito->cantidad = $request->input('cantidad');
        $productoCarrito->total = $productoCarrito->cantidad * $producto->price;
        $productoCarrito->save();
        $this->calcularCarrito($productoCarrito->carrito_id);

        return response('exito');
    }

    public function calcularCarrito($id)
    {
        $carrito = Carrito::with('productos.producto', 'orden.files')->find($id);

        if ($carrito) {
            $total = 0;

         
            foreach ($carrito->productos as $item) {
                $total += $item->producto->price * $item->cantidad;
            }



          
            foreach ($carrito->orden as $orden) {
                foreach ($orden->files as $file) {
                    $total += $file->precio;
                }
            }

            $carrito->total = $total;
            $carrito->save();
        }
    }
    

    public function añadirStlCarrito( Request $request){
        $orden = Orden::with('files')->find($request->input('id'));
        $carrito = Carrito::where('usuario_id', $request->user()->id)->where('status', 'activo')->first();  
        $orden->status = 'inactivo';
        $orden->carrito_id = $carrito->id;
        $orden->save();
        $this->calcularCarrito($carrito->id);
        return response()->json(['orden'=>$orden, '$carrito'=>$carrito]);

    }


    public function actualizarFileCarrito(Request $request)
    {
        $file = Files::find($request->input('id'));

        if ($file) {
            $precioUnitario = $file->precio / $file->cantidad;

            $file->cantidad = $request->input('cantidad');
            $file->precio = $file->cantidad * $precioUnitario;

            $file->save();

            $carrito = Carrito::whereHas('orden', function($query) use ($file) {
                $query->where('id', $file->orden_id);
            })->first();

            

            if ($carrito) {
                
                $this->calcularCarrito($carrito->id);
              
            }

            return response('exito');
        } else {
            return response('Error: No se encontró el archivo', 404);
        }

       
    }

    // public function finalizarCarrito(Request $request){
    //     $carrito = Carrito::find($request->input('id'));
    //     $carrito->status = 'pagada';
    //     $carrito->save();
    //     return response('todo bien');

    // }

    public function getCarritosPendientes(){
        $carritos = Carrito::with('usuario','orden.files','productos')->where('status', 'pagada')->get();
        return response($carritos);
    }

    public function show($id)
    {

        $carritos = Carrito::find($id);
        $ordenes = Orden::with('files')->where('carrito_id', $id)->get();
        $productos = Producto_Carrito::with('producto.files')->where('carrito_id', $id)->get();
        $files = [];
        foreach ($ordenes as $orden) {
            foreach ($orden->files as $file) {
                $files[] = $file;
            }
        }
        return response()->json(['carrito' => $carritos, 'files' => $files, 'productos'=>$productos]);
    }

    public function listoParaEnvio($id){
        $carrito = Carrito::find($id);
        $carrito->status ='Listo Para Enviar';
        $carrito->save();

        return response()->json(['data'=>'exito']);
    }

    public function ConfirmarVenta(Request $request){
        $carrito = $this->obtenerCarrito($request->user()->id);
        if($carrito->total == 0){
            return response()->json(['data'=>'la venta ya ha sido cerrada', 'code'=>404]);
        } else{
            $carrito->status = 'pagada';
            $carrito->save();
            $this->enviarCorreoConfirmacion($request->user(), $carrito);
            return response('la venta fue exitosa');
        }

    }

    private function enviarCorreoConfirmacion(User $user, Carrito $carrito)
    {
        $data = [
            'user' => $user,
            'carrito' => $carrito,
        ];

        Mail::send('email.ventaConfirmada', $data, function($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Confirmación de Compra');
        });
    }

    
}
