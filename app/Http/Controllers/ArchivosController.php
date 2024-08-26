<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;
use App\Models\Orden;
use App\Models\PrecioMinuto;
use App\Models\Product;
use App\Models\ProductoCarritoArchivo;
use App\Models\UsuarioCotizacion;
use Exception;
use Faker\Core\File;

class ArchivosController extends Controller
{
    public function calculate(Request $request)
    {

        $PrecioMinuto = PrecioMinuto::first();

        $request->validate([
            'file' => 'file|required',
        ]);
        try {
            $response = $this->apiRequest($request->file('file'));

            $orden = Orden::firstOrCreate([
                'status' => 'activo',
                'usuario_id' => $request->user()->id,
            ], [
                'total' => 0,
                'status' => 'activo',
            ]);
    
            $file = ([
                'nombre' => $request->file('file')->getClientOriginalName(),
                'piezas' => 1,
                'minutos' => ($response['estimated_printing_time_seconds'] / 60) / 3.5,
                'precio' => (($response['estimated_printing_time_seconds'] / 60) / 3.5) * $PrecioMinuto->precio,
            ]);
    
            return response()->json(['data' => $file]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    
        return response()->json(['message' => 'Error al subir el archivo.']);
    }
    

    public function apiRequest($file)
    {
        $url = "https://3d-print-stl-estimation.p.rapidapi.com/slice_and_extract?rotate_y=0&rotate_x=0&config_file=config.ini";

        $headers = [
            'x-rapidapi-host' => '3d-print-stl-estimation.p.rapidapi.com',
            'x-rapidapi-key' => '987632cf0emsh756b8484307fe63p130018jsnf063dff1b8f7',
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post($url, [
            'headers' => $headers,
            'multipart' => [
                [
                    'name'     => 'stl_file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ]
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            throw new Exception('Ocurrió un problema al procesar el archivo: ' . $response->getBody()->getContents());
        }

        return json_decode($response->getBody()->getContents(), true);
    }


    public function downloadFile($id)
    {

        $file = Files::find($id);

        return Storage::download($file->path, $file->nombre);
    }

    public function stlViewer(UsuarioCotizacion $usuarioCotizacion)
    {
        $fileUrl = asset('storage/' . $usuarioCotizacion->path);
        
        return response()->json([
            'file_url' => $fileUrl
        ]);
    }
    

    public function guardarSTLproducto(Request $request)
    {
        $request->validate([
            'file' => 'file|required',
            'producto_id' => 'numeric|required'
        ]);
    
        $producto = Product::find($request->input('producto_id'));
    
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    
        // Obtener el nombre original del archivo incluyendo la extensión
        $originalName = $request->file('file')->getClientOriginalName();
    
        // Guardar el archivo en la carpeta 'files' manteniendo el nombre original
        $filePath = $request->file('file')->storeAs('files', $originalName);
    
        // Guardar el archivo en la base de datos
        $file = new Files([
            'path' => $filePath,
            'nombre' => $originalName
        ]);
    
        // Asociar el archivo al producto
        $producto->files()->save($file);
    
        return response()->json(['data' => 200]);
    }

    public function downloadArchivo($id){

        $file = ProductoCarritoArchivo::find($id);

        return Storage::download($file->path, $file->nombre);
    }
    

    public function traerArchivos(Request $request)
    {
        $request->validate(['id' => 'required']);
        $producto = Product::with('files')->find($request->input('id'));
        return response($producto->files);
    }

    public function eliminarArchivo(Request $request){
        $request->validate(['id' => 'required']);

        $file = Files::find($request->input('id'));
        Storage::delete($file->path);
        $file->delete();

        return response()->json(['code' => 200]);

    }

}
