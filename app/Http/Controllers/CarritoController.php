<?php

namespace App\Http\Controllers;

use App\Http\Requests\Carrito\StoreRequest;
use App\Models\Carrito;
use App\Models\Files;
use App\Models\Orden;
use App\Models\Product;
use App\Models\Producto_Carrito;
use App\Models\Direccion;
use App\Models\ProductoCarritoArchivo;
use App\Models\User;
use App\Models\UsuarioCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Input\Input;

class CarritoController extends Controller
{
    // listar los items del modelo
    public function index()
    {
        return response()->json(['data' => $this->getUserItems()]);
    }


    public function getUserItems()
    {
        $userId = Auth::user()->id;
        $carrito = Carrito::with(['productos.producto', 'productos.productoCarritoArchivos'])
            ->where('usuario_id', $userId)
            ->where('status', 'activo')
            ->first();

        $orden = [];
        $orden['id'] = $carrito->id;
        $orden['total'] = $carrito->total;
        if ($carrito->productos) {
            $orden['articulos'] = $carrito->productos->map(static function($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->producto->name,
                    'precio' => $item->producto->price,
                    'cantidad' => $item->cantidad,
                    'is_file' => $item->producto->files()->exists(),
                    'files' => $item->productoCarritoArchivos ?? [],
                    'total' => $item->total,
                ];
            });
        }

        return $orden;
    }

    public function borrar(Request $request)
    {
        $producto = Producto_Carrito::find($request->input('id'))->first();
        if ($producto->has('productoCarritoArchivos')) {
            $producto->productoCarritoArchivos()->delete();
        }
        $producto->delete();

        $this->calcularCarrito();

        return response()->json('exito');
    }

    public function calcularCarrito()
    {
        $total = 0;
        $carrito = $this->getUserItems();

        foreach ($carrito['articulos'] as $item) {
            $totalArticulo = $item['total'];

            if(isset($carrito['articulos']['files']) && count($carrito['articulos']['files']) > 0) {
                $totalArticulo = $this->calcularTotalArchivos($carrito['articulos']['files']);
            }

            $total += $totalArticulo;
        }

        $carritoM = Carrito::find($carrito['id']);
        $carritoM->update(['total' => $total]);

        return response()->json(['data' => $carritoM]);
    }

    public function agregar(StoreRequest $request)
    {
        $producto = Product::find($request->input('producto_id'));
        $cantidad = $request->input('cantidad');

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
            'is_file' =>  true, //$producto->files()->exists(),
            'carrito_id' => $carrito->id,
            'total' => $producto->price * $cantidad,
            'cantidad' => $cantidad
        ]);
        
        if ($request->has('files')) {
            ProductoCarritoArchivo::insert(
               collect($request->input('files'))->map(static function($item) use ($productoCarrito) {
                    return [

                        'nombre' => $item['nombre'],
                        'path' => $item['path'],
                        'minutos' => $item['minutos'],
                        'precio' => $item['precio'],
                        'cantidad' => $item['cantidad'],
                        'producto_carrito_id' => $productoCarrito->id,
                    ];

               })->toArray()

            );
            $productoCarrito->total = $this->calcularTotalArchivos($request->input('files'));
            $productoCarrito->save();
        }

        // $this->calcularCarrito($carrito->id);

        return response()->json($carrito);
    }

    public function calcularTotalArchivos($archivos) {
        $total = 0;

        foreach ($archivos as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return $total;
    }



    // ---------------------------------------------------


    public function obtenerCarrito($user_id)
    {
        $carrito = Carrito::with('productos.producto.files','orden.files')->firstorCreate([
            'status' => 'activo',
            'usuario_id' => $user_id
        ], [
            'total' => 0,
            'status' => 'activo',
        ]);
        return $carrito;
    }

    public function traerCarrito(Request $request)
    {
        $user = $request->user()->id;
        $carrito = $this->obtenerCarrito($user);
        $direccion = Direccion::find($carrito->direccion_id);
        return response()->json(['data'=>$carrito, 'direccion'=>$direccion]);
    }


    public function actualizar(Request $request)
    {
        $productoCarrito = Producto_Carrito::with('producto')->find($request->input('id'));

        $producto = Product::find($productoCarrito->producto_id);
        $productoCarrito->cantidad = $request->input('cantidad');
        $productoCarrito->total = $productoCarrito->cantidad * $producto->price;
        $productoCarrito->save();
        $this->calcularCarrito();

        return response('exito');
    }

    public function actualizarCarritoArchivo(ProductoCarritoArchivo $productoCarritoArchivo, Request $request) {
        Log::info($request->input('cantidad'));
        $productoCarritoArchivo->cantidad = $request->input('cantidad');
        $productoCarritoArchivo->total = $productoCarritoArchivo->precio * $request->input('cantidad');
        $productoCarritoArchivo->save();

        $productoCarrito = Producto_Carrito::with('productoCarritoArchivos')
                ->find($productoCarritoArchivo->producto_carrito_id);

        $productoCarrito->update([
            'total' => $this->calcularTotalArchivos($productoCarrito->productoCarritoArchivos),
        ]);


        $this->calcularCarrito();

        return response()->json([
            'data' => $productoCarritoArchivo
        ]);
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


    public function getCarritosPendientes(){
        $carritos = Carrito::with('usuario','orden.files','productos')->where('status', 'pago confirmado')->get();
        return response($carritos);
    }

    public function ventaConfirmada(Request $request)
    {
        $carrito = Carrito::where('usuario_id', $request->user()->id)
            ->where('status', 'pago confirmado')
            ->latest()
            ->first();
    
        return response()->json(['data' => $carrito]);
    }

    public function show($id)
    {

        $carritos = Carrito::find($id);
        $ordenes = Orden::with('files')->where('carrito_id', $id)->get();
        $productos = Producto_Carrito::with('producto.files', 'productoCarritoArchivos')->where('carrito_id', $id)->get();
        $files = [];
        foreach ($ordenes as $orden) {
            foreach ($orden->files as $file) {
                $files[] = $file;
            }
        }
        $direccion = Direccion::find($carritos->direccion_id);
        if ($direccion){
            return response()->json(['carrito' => $carritos, 'files' => $files, 'productos'=>$productos, 'direccion'=>$direccion]);

        }else{
            return response()->json(['carrito' => $carritos, 'files' => $files, 'productos'=>$productos]);

        }
    }

    public function listoParaEnvio($id){
        $carrito = Carrito::find($id);
        $carrito->status ='Listo Para Enviar';
        $carrito->save();

        return response()->json(['data'=>'exito']);
    }

    public function ConfirmarVenta(Request $request){
        $carrito = Carrito::where('status', 'pago confirmado')->latest();
        if($carrito->total === 0){
            return response()->json(['data'=>'la venta ya ha sido cerrada', 'code'=>404]);
        } else{
            // $carrito->status = 'pagada';
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


    public function traerPedidosViejos(Request $request){
        $pedidosViejos = Carrito::with('orden.files', 'productos.producto.files')->where('status', 'pago confirmado')->get();
        return response()->json(['data'=>$pedidosViejos]);

    }

    public function userHistorial(Request $request){
        $pedidos = Carrito::with('orden.files', 'productos.producto.files')->where('status', 'pago confirmado')->
            where('usuario_id', $request->user()->id)->get();
        return response()->json(['data' => $pedidos]);
    }



    /**
     *  Modelo autos
     */

    /**
     * get      -> /autos         -> listar
     * post     -> /autos         -> guardar 1 auto / request (Campos auto)
     * get      -> /autos/{auto} -> buscar un auto especifico
     * post     -> /autos/{auto} -> Modificar un auto especifico
     * put      -> /autos/{auto} -> Modificar un auto especifico
     * patch    -> /autos/{auto} -> Modificar un auto especifico
     * delete   -> /autos/{auto} -> eliminar un auto
     */

    //  Route::resourceAPI()


    // post     -> /autos/{auto} -> Modificar un auto especifico

    /**
     * public function update(Request $request)
     * {
     *    $auto = Auto::find($request->id);
     *    $auto->modelo = 2024;
     *    $auto->save();
     * 
     *    return [...];
     * }
     */

    /**
     * public function update(Auto $auto, Request $request)
     * {
     *    return ['data' => $auto->update($request->all())];
     * }
     */

    


    // get      -> /autos/{auto} -> buscar un auto especifico

    /**
     * public function show(Request $request)
     * {
     *      $auto = Auto:find($request->id);
     * 
     *      return [$atuo];
     * 
     * }
     */

    /**
     * public function show(Auto $auto)
     * {
     *      return [$auto];
     * }
     */
}
