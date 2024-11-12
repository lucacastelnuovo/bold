<?php

namespace App\Http\Controllers;

use App\Enums\Ability;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiUserController
{
    public function show(#[CurrentUser] $user): UserResource
    {
        return new UserResource($user);
    }

    public function storeTokens(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        if (!$user->hasAbilityTo(Ability::USER_TOKENS_UPDATE)) {
            return response()->json(
                status: Response::HTTP_FORBIDDEN,
                data: [
                    'error' => sprintf('Missing ability: %s!', Ability::USER_TOKENS_UPDATE->value),
                ]
            );
        }

        $request->validate([
            'access_token'  => 'required|max:255',
            'refresh_token' => 'required|max:255',
        ]);

        $user->update([
            'bold_access_token'  => $request->get('access_token'),
            'bold_refresh_token' => $request->get('refresh_token'),
        ]);

        return response()->json(
            status: Response::HTTP_OK,
            data: [
                'message' => 'ok',
            ]
        );
    }
}
