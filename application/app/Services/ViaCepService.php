<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ViaCepService
{
    public function search(string $zipCode)
    {
        $cleanZipCode = preg_replace('/\D/', '', $zipCode);

        if (strlen($cleanZipCode) !== 8) {
            return ['error' => 'Invalid ZIP code', 'status' => 400];
        }

        try {
            $response = Http::get("https://viacep.com.br/ws/{$cleanZipCode}/json/");

            if ($response->successful() && !isset($response->json()['erro'])) {
                $data = $response->json();
                return [
                    'data' => $data,
                    'status' => 200
                ];
            }
            return ['error' => 'ZIP code not found', 'status' => 404];
        } catch (Exception $e) {
            return ['error' => 'Error searching ZIP code', 'status' => 500];
        }
    }
}
