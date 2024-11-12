<?php

namespace App\Http\Controllers;

use App\Enums\Ability;
use App\Http\Resources\ActivityResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\Activitylog\Models\Activity;

class ApiActivityController
{
    public function index(#[CurrentUser] User $user): AnonymousResourceCollection|JsonResponse
    {
        if (!$user->hasAbilityTo(Ability::ACTIVITY_VIEW)) {
            return response()->json(
                status: Response::HTTP_FORBIDDEN,
                data: [
                    'error' => sprintf('Missing ability: %s!', Ability::ACTIVITY_VIEW->value),
                ]
            );
        }

        return ActivityResource::collection(Activity::orderByDesc('id')->get());
    }
}
