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
        $producto = Producto_Carrito::find($request->input('id'));
        
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


    public function getCarritosPendientes(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|integer',
            'search' => 'nullable|string'
        ]);
    
        $query = Carrito::with('usuario')->whereIn('status', ['pago confirmado', 'Listo para enviar','Listo para recolectar']);
    
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->whereHas('usuario', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhere('total', 'like', '%' . $search . '%');

            });
        }
    
        $carritos = $query->paginate($request->get('itemsPerPage', 15));
    
        return response()->json($carritos);
    }

    public function ventaConfirmada(Request $request)
    {

        $carrito = Carrito::where('usuario_id', $request->user()->id)
            ->where('status', 'pago por confirmar')
            ->orderBy('created_at', 'desc')
            ->first();

        if($carrito){
            return response()->json(['data' => $carrito, 'prueba' => 'aun no se confirmo']);
        }else{
            $carrito = Carrito::where('usuario_id', $request->user()->id)
                ->where('status', 'pago confirmado')
                ->orderBy('created_at', 'desc')
                ->first();
            
            return response()->json(['data' => $carrito, 'prueba' => 'ya se confirmo']);
        }
    }

    public function show($id)
    {
        $user = auth()->user();
    
        $carrito = Carrito::with('usuario')->find($id);
    
        if (!$carrito) {
            return response()->json(['error' => 'Carrito no encontrado'], 404);
        }
    
        if ($carrito->usuario_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
    
        $ordenes = Orden::with('files')->where('carrito_id', $id)->get();
    
        $productos = Producto_Carrito::with('producto.files', 'productoCarritoArchivos')->where('carrito_id', $id)->get();
    
        $files = [];
        foreach ($ordenes as $orden) {
            foreach ($orden->files as $file) {
                $files[] = $file;
            }
        }
    
        $direccion = Direccion::find($carrito->direccion_id);
    
        $response = [
            'carrito' => $carrito,
            'files' => $files,
            'productos' => $productos,
        ];
    
        if ($direccion) {
            $response['direccion'] = $direccion;
        }
    
        return response()->json($response);
    }
    

    public function ventaExitosa(Request $request, Carrito $carrito)
    {
        if($carrito->usuario_id = Auth::user()->id){
            if ($carrito->status == 'Finalizado') {
                return response()->json(['data' => $carrito, 'is_old' => true]);
                
            } else {
                $carrito->status = 'pago por confirmar';
                $this->enviarCorreoConfirmacion($request->user(),$carrito);

                return response()->json(['data' => $carrito]);
            }
        } else {

            return response()->json(['data' => '#404']);

        }

    }

    public function listoParaEnvio($id, Request $request) {
        $carrito = Carrito::find($id);
    
        if ($request->input('status')) {
            $carrito->status = 'Finalizado';
            $user = User::find($carrito->usuario_id); 
            if ($carrito->recoleccion == true) {
                Mail::to($user->email)->send(new \App\Mail\PedidoRecoleccion($user, $carrito));
            } else {
                Mail::to($user->email)->send(new \App\Mail\PedidoEnviado($user, $carrito));
            }
    
        } else {
            if ($carrito->direccion_id === null) {
                $carrito->status = 'Listo para recolectar';
            } else {
                $carrito->status = 'Listo Para Enviar';
            }
        }
    
        $carrito->save();
    
        return response()->json(['data' => 'exito']);
    }
    
    

    public function ConfirmarVenta(Request $request){
        $carrito = Carrito::where('status', 'pago confirmado')->latest();
        if($carrito->total === 0){
            return response()->json(['data'=>'la venta ya ha sido cerrada', 'code'=>404]);
        } else{
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


    public function traerPedidosViejos(Request $request)
    {
        $request->validate([
            'itemsPerPage' => 'nullable|integer',
            'search' => 'nullable|string',
            'sortBy' => 'nullable|string'
        ]);
    
        $query = Carrito::with('orden.files', 'productos.producto.files', 'usuario')
                        ->where('status', 'Finalizado');
    
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->whereHas('usuario', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('telefono', 'like', '%' . $search . '%');
            })
            ->orWhere('total', 'like', '%' . $search . '%')
            ->orWhere('id', 'like', '%' . $search . '%')
            ->orWhere('created_at', 'like', '%' . $search . '%');
        }
    
        if ($request->has('sortBy') && !empty($request->sortBy)) {
            switch ($request->input('sortBy')) {
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'total_desc':
                    $query->orderBy('total', 'desc');
                    break;
                case 'total_asc':
                    $query->orderBy('total', 'asc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
    
        $pedidosViejos = $query->paginate($request->input('itemsPerPage', 10));
    
        return response()->json($pedidosViejos);
    }
    

    

    public function totalPedidosPendientes(Request $request)
    {
        $pedidosViejos = Carrito::whereIn('status', ['pago confirmado', 'Listo para enviar', 'Listo para recolectar'])->get();
        $total = $pedidosViejos->sum('total');

        return response()->json(['data' => $total]);
    }

    public function userHistorial(Request $request) {
        $perPage = $request->input('perPage', 10); 
        $pedidos = Carrito::with('orden.files', 'productos.producto.files')
            ->whereIn('status', ['Listo Para Enviar', 'pago confirmado', 'Finalizado', 'pago por confirmar'])
            ->where('usuario_id', $request->user()->id)
            ->paginate($perPage);
        
        return response()->json($pedidos); 
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
