<?php
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\ImagenesController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\CarritoController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\CostoEnvioController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrecioImpresionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioCotizacionController;
use App\Http\Controllers\UsuariosRolesController;
use App\Http\Controllers\WebhookController;



//Autenticación
Route::get('/formulario-recuperar-contrasenia', [AuthController::class, 'formularioRecuperarContrasenia'])->name('formulario-recuperar-contrasenia');
Route::post('/enviar-recuperar-contrasenia', [AuthController::class, 'enviarRecuperarContraseña'])->name('enviar-recuperacion');
Route::get('/reiniciar-contrasenia/{token}/{email}', [AuthController::class, 'formularioActualizacion'])->name('formulario-actualizar-contrasenia');
Route::post('/actualizar-contrasenia', [AuthController::class, 'actualizarContrasenia']);
Route::get('/auth', [AuthController::class, 'auth']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/cerrarSesion', [AuthController::class, 'cerrarSesion']);
Route::post('quick-quote', [UsuarioCotizacionController::class, 'quickQuote']);


//stripe
Route::post('/paymentSuccess', [WebhookController::class, 'paymentSuccess']);
Route::post('/checkout', [CheckoutController::class, 'createSession']);

//AuthController
Route::post('/update-user/{user}', [AuthController::class, 'updateUser']);
Route::get('/get-user/{user}', [AuthController::class, 'getUser']);
Route::get('/getUsersRoles',[AuthController::class, 'getUsersRoles']); ///
Route::get('/get_user', [AuthController::class, 'get_user']);
Route::post('/crearUsuariosRoles',[AuthController::class, 'crearUsuariosRoles']);
Route::post('/usuarios', [AuthController::class, 'crear']);

Route::group(['middleware' => ['auth']], function(){

    //notificaciones
    Route::get('/notificaciones', [NotificacionesController::class, 'index']);

    //costos de envio, impresión y conversion
    Route::apiResource('/precio-impresion', PrecioImpresionController::class);
    Route::apiResource('/precio-envio', CostoEnvioController::class);
    Route::apiResource('/factor-conversion', ConversionController::class);
    
    //cotizacion
    Route::group(['prefix' => 'cotizacion'], static function (){
    Route::post('/cotizar', [UsuarioCotizacionController::class, 'cotizar']);
    Route::post('/update', [UsuarioCotizacionController::class, 'update']);
    Route::get('/archivo-cotizados', [UsuarioCotizacionController::class, 'index']);
    Route::post('/delete/{id}', [UsuarioCotizacionController::class, 'delete']);
    });
        
    //roles y permisos
    Route::apiResource('/permissions',PermissionController::class);
    Route::post('/deleteUser/{user}',[UsuariosRolesController::class, 'delete']);
    Route::apiResource('/roles', RoleController::class);
    Route::post('/addRole/{user}', [AuthController::class,'addRole']);
    Route::put('/actualizarRolesUsuario/{user}',[UsuariosRolesController::class, 'update']);
    Route::post('/addPermissionsToRole', [AuthController::class,'addPermissionsToRole']);

    //DireccionesController
    Route::post('/guardarDireccion', [DireccionesController::class, 'guardarDireccion']);
    Route::post('/eliminarDireccion', [DireccionesController::class, 'eliminarDireccion']);
    Route::get('/getDirecciones', [DireccionesController::class, 'getDirecciones']);
    Route::post('/actualizarDireccion',[DireccionesController::class, 'actualizarDireccion']);
    Route::get('/direccion/{direccion}',[DireccionesController::class, 'show']);
    Route::post('/updateDireccion/{direccion}',[DireccionesController::class, 'update']);
    Route::post('/direccionEntrega',[DireccionesController::class, 'direccionEntrega']);
    Route::post('/direccionEntregaSucursal',[DireccionesController::class, 'direccionEntregaSucursal']);

    //ProductosController
    Route::post('/eliminarProducto', [ProductosController::class, 'eliminarProducto']);
    Route::get('/modelos', [ProductosController::class, 'traerProductos']);
    Route::post('/savemodel', [ProductosController::class, 'StoreProduct']);
    Route::get('/productos/{producto}', [ProductosController::class, 'show']);
    Route::post('/productos/{producto}', [ProductosController::class, 'update']);

    //ArchivosController
    Route::post('/deletefile', [ArchivosController::class, 'deletefile']);
    Route::post('/calculate', [ArchivosController::class, 'calculate']);
    Route::get('/DownloadFile/{id}', [ArchivosController::class, 'downloadFile']);
    Route::get('/DownloadArchivo/{productoCarritoArchivo}', [ArchivosController::class, 'downloadArchivo']);
    Route::get('/stlViewer/{usuarioCotizacion}', [ArchivosController::class, 'stlViewer']);

    // Route::get('/downloadArchivo/{productoCarritoArchivo}', [ArchivosController::class, 'downloadSArchivo']);
    Route::post('/guardarSTLproducto',[ArchivosController::class, 'guardarSTLproducto']);  
    Route::get('/traerArchivos',[ArchivosController::class, 'traerArchivos']) ;
    Route::post('/borrarArchivo',[ArchivosController::class, 'eliminarArchivo']);
    Route::get('/download-stl/{id}', [UsuarioCotizacionController::class, 'download'])->name('download.stl');

    //ModelsController
    Route::get('/traerarchivos', [ModelsController::class, 'traerarchivos']);

    //CarritoController
    Route::group(['prefix' => '/carrito'], static function () {
        Route::get('/', [CarritoController::class, 'index']);
        Route::post('/borrar', [CarritoController::class, 'borrar']);
        Route::post('/agregar', [CarritoController::class, 'agregar']);
        Route::post('/update-file/{productoCarritoArchivo}', [CarritoController::class, 'actualizarCarritoArchivo']);
        Route::get('/userHistorial', [CarritoController::class, 'userHistorial']);
        Route::get('/ventaConfirmada', [CarritoController::class, 'ventaConfirmada']);
    });
    Route::post('/borrarProducto', [CarritoController::class, 'borrarProducto']);
    Route::post('/actualizarProductoCarrito', [CarritoController::class, 'actualizar']);
    Route::post('/añadirStlCarrito', [CarritoController::class, 'añadirStlCarrito']);
    Route::post('/actualizarFileCarrito', [CarritoController::class, 'actualizarFileCarrito']);
    Route::post('/finalizarCarrito',[CarritoController::class, 'finalizarCarrito']);
    Route::get('/getCarritosPendientes', [CarritoController::class, 'getCarritosPendientes']);
    Route::get('/totalCarritosPendientes', [CarritoController::class, 'totalPedidosPendientes']);
    Route::get('/carrito/{carrito}',[CarritoController::class, 'show']);
    Route::get('/ventaExitosa/{carrito}',[CarritoController::class, 'ventaExitosa']);
    Route::post('/listoParaEnvio/{id}',[CarritoController::class, 'listoParaEnvio']);
    Route::post('ConfirmarVenta',[CarritoController::class, 'ConfirmarVenta']);
    Route::get('/traerPedidosViejos', [CarritoController::class, 'traerPedidosViejos']);
    Route::get('/traerCarrito', [CarritoController::class, 'traerCarrito']);

    //ImagenesController
    Route::post('/guardarImagen', [ImagenesController::class, 'guardarImagen']);
    Route::get('/getImagenes', [ImagenesController::class, 'getImagenes']);
    Route::post('/eliminarImagen', [ImagenesController::class, 'eliminarImagen']);
    Route::get('/images/{image}/image', [ImagenesController::class, 'showImage'])->name('images.image');
    
});

    Route::get('/{any}', function (): View {
        return view('welcome');
    })->where('any', '.*');
