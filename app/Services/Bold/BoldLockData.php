<?php

namespace App\Services\Bold;

use Spatie\LaravelData\Data;

class BoldLockData extends Data
{
    public function __construct(
        public string $id,
        public string $name
    ) {}
}
