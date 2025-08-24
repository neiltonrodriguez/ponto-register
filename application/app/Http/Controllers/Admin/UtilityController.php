<?php

namespace App\Http\Controllers\Admin;


use App\Services\ViaCepService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UtilityController extends Controller
{
    public function searchZipCode(Request $request, ViaCepService $viaCepService)
    {
        $result = $viaCepService->search($request->zip_code);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        $data = $result['data'];
        return response()->json([
            'address' => $data['logradouro'] ?? '',
            'neighborhood' => $data['bairro'] ?? '',
            'city' => $data['localidade'] ?? '',
            'state' => $data['uf'] ?? ''
        ]);
    }
}
