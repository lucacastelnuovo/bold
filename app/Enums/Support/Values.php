<?php

namespace App\Enums\Support;

trait Values
{
    public static function getValues(): array
    {
        return array_column(static::cases(), 'value');
    }
}
