<?php

namespace App\Traits;

use App\Enums\Ability;
use Laravel\Sanctum\HasApiTokens;

trait HasAbilities
{
    use HasApiTokens;

    public function hasAbilityTo(Ability $ability): bool
    {
        return $this->tokenCan($ability->value);
    }
}
