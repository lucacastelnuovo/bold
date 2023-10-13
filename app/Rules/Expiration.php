<?php

namespace App\Rules;

use Carbon\Exceptions\InvalidFormatException;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class Expiration implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            new Carbon($value);
        } catch (InvalidFormatException) {
            $fail('The :attribute field is not a valid date.');
        }
    }
}
