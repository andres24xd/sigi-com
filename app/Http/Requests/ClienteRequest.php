<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+()\-\s]{7,20}$/'],
        ];
    }
}
