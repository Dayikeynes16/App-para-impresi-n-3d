<?php

namespace App\Http\Requests\Carrito;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'producto_id' => 'required|integer|exists:products,id',
            'cantidad' => 'required|integer|min:1',
            'files' => 'array',
        ];
    }
}
