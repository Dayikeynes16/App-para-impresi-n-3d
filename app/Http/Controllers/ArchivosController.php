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
                'minutos' => ($response['print_time'] / 60) / 3.5,
                'precio' => (($response['print_time'] / 60) / 3.5) * 1.5,
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
        $url = "https://stl-info-insight.p.rapidapi.com/3dslicer-02/calculate_volume";

        $headers = [
            "X-RapidAPI-Key" => "97016016d1msh22a2de77d63209bp14b5e9jsn6a63ea5210dc",
            "X-RapidAPI-Host" => "stl-info-insight.p.rapidapi.com",
        ];

        $response = Http::withHeaders($headers)
            ->timeout(60)
            ->attach(
                'stl_file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName(),
                ['Content-Type' => $file->getMimeType()]
            )
            ->post($url, [
                'material_type' => 'ABS'
            ]);

        if (!$response->json('http_code')) throw new Exception('Ocurrió un problema al procesar el archivo');
        return $response->json('result_data');
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
    }
    public function traerArchivos(Request $request)
    {
        $producto = Product::with('files')->find($request->input('id'));

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        $files =$producto->files;

        return response()->json(['data'=> $files]);
    }

}
