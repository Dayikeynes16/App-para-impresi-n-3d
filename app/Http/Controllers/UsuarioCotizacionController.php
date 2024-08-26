<?php

namespace App\Http\Controllers;

use App\Models\FactorConversion;
use App\Models\Files;
use App\Models\PrecioMinuto;
use App\Models\ProductoCarritoArchivo;
use App\Models\UsuarioCotizacion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\New_;

class UsuarioCotizacionController extends Controller
{
    public function cotizar(Request $request)
    {
        $conversion = FactorConversion::first();
        $precioMinuto = PrecioMinuto::first();
        $request->validate([
            'file' => 'file|required',
        ]);
        try {
            $response = $this->apiRequest($request->file('file'));
            
            $originalName = $request->file('file')->getClientOriginalName();
            
            $filePath = $request->file('file')->storeAs('public/files', $originalName);
    
            $filePath = str_replace('public/', '', $filePath);
    
            $cotizacion = UsuarioCotizacion::create([
                'path' => $filePath,
                'nombre' => $originalName,
                'minutos' => ($response['estimated_printing_time_seconds'] / 60) / $conversion->conversion,
                'precio' => (($response['estimated_printing_time_seconds'] / 60) / $conversion->conversion) * $precioMinuto->precio,
                'usuario_id' => Auth::user()->id,
                'total' => (($response['estimated_printing_time_seconds'] / 60) / $conversion->conversion) * $precioMinuto->precio,
            ]);
    
            return response()->json(['data' => $cotizacion]);
    
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function quickQuote(Request $request){
        
        $conversion = FactorConversion::first();
        $precioMinuto = PrecioMinuto::first();
        $request->validate([
            'file' => 'file|required',
        ]);
        try {
            $response = $this->apiRequest($request->file('file'));
            
            $originalName = $request->file('file')->getClientOriginalName();
           
            $cotizacion = ([
                'nombre' => $originalName,
                'minutos' => ($response['estimated_printing_time_seconds'] / 60) / $conversion->conversion,
                'precio' => (($response['estimated_printing_time_seconds'] / 60) / $conversion->conversion) * $precioMinuto->precio,
                'total' => (($response['estimated_printing_time_seconds'] / 60) / $conversion->conversion) * $precioMinuto->precio,
                'file' => $request->file('file')
            ]);
    
            return response()->json(['data' => $cotizacion]);
    
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
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

    public function index()
    {
        $usuarioCotizaciones = UsuarioCotizacion::where('usuario_id', Auth::user()->id)->get();

        $usuarioCotizaciones->transform(function ($item) {
            $item->full_path = Storage::url($item->path);
            return $item;
        });

        return response()->json([
            'data' => $usuarioCotizaciones,
        ]);
    }


    public function update(Request $request) {
        $request->validate([
            'cantidad' => 'required|integer',
            'id' => 'required|integer'
        ]);

        $cotizacion = UsuarioCotizacion::find($request->input('id'));
        $cotizacion->cantidad = $request->input('cantidad');
        $cotizacion->total = $cotizacion->precio * $cotizacion->cantidad;
        $cotizacion->save();

        return response()->json(['data'=>$cotizacion]);
    }

    public function store()
    {

    }

    public function delete(UsuarioCotizacion $id) {

        $id->delete();
        return response()->json(['data' => 'ctixacion eliminada con exito']);
    }
}
