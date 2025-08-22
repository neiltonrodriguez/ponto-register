<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', $value);

        if (strlen($cpf) != 11) {
            $fail('The CPF must have 11 digits.');
            return;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('The CPF provided is invalid.');
            return;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $fail('The CPF provided is invalid.');
                return;
            }
        }
    }
}