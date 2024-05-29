<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuoteController extends Controller
{
    public function testing()
    {
        return view('example');
    }

    public function calculate(request $request)
{   $request->validate([
    'file'=>'file|required',
    
]);
   
        try {
            $response = $this->apiRequest($request->file('file'));
            return $response;
        } catch (\Exception $e) {
            return response()->json(['message'=>  $e->getMessage() ],409);
        }
    
    return back()->with('error', 'Error al subir el archivo.');
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
                            'stl_file', file_get_contents($file->path()), $file->getClientOriginalName(),
                            ['Content-Type' => $file->getMimeType()]
                        )
                        ->post($url, [
                            'material_type' => 'ABS'
                        ]);
                        if (!$response->json('http_code')) throw new Exception('Ocurrió un problema al procesar el archivo');

        return $response->json();

    }
}
