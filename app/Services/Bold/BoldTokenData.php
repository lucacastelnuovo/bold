<?php

namespace App\Services\Bold;

use Spatie\LaravelData\Data;

class BoldTokenData extends Data
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken
    ) {}
}
