<?php

namespace App\Http\Requests;

use App\Rules\ValidateCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', new ValidateCpf, Rule::unique('users')],
            'email' => 'required|string|email|max:255|unique:users',
            'position' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'zip_code' => 'required|string|size:9',
            'address' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
        ];
    }
}
