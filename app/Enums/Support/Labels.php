<?php

namespace App\Enums\Support;

trait Labels
{
    public function getLabel(): ?string
    {
        return __('enum.' . self::class . '.' . $this->value);
    }
}
