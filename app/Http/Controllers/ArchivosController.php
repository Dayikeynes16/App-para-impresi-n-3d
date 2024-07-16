<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;
use App\Models\Orden;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Http;

class ArchivosController extends Controller
{
    public function calculate(Request $request)
    {
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
    
            $filePath = $request->file('file')->store('files');
            $file = new Files([
                'path' => $filePath,
                'nombre' => $request->file('file')->getClientOriginalName(),
                'minutos' => ($response['estimated_printing_time_seconds'] / 60) / 3.5,
                'precio' => (($response['estimated_printing_time_seconds'] / 60) / 3.5) * 1.5,
            ]);
            $orden->files()->save($file);
    
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


        if (!$file || !Storage::exists($file->path)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

 
        return Storage::download($file->path, $file->nombre);
    }

    public function guardarSTLproducto(Request $request){
        $request->validate([
            'file' => 'file|required',
            'producto_id' => 'numeric|required'
        ]);
        $producto = Product::find($request->input('producto_id'));
        $filePath = $request->file('file')->store('files');
        $file = new Files([
            'path' => $filePath,
            'nombre' => $request->file('file')->getClientOriginalName()
        ]);
        $producto->files()->save($file);
        return response()->json(['data'=>200]);
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
