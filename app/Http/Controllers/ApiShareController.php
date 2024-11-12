<?php

namespace App\Http\Controllers;

use App\Enums\Ability;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class ApiShareController
{
    public function store(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        if (!$user->hasAbilityTo(Ability::SHARE_CREATE)) {
            return response()->json(
                status: Response::HTTP_FORBIDDEN,
                data: [
                    'error' => sprintf('Missing ability: %s!', Ability::SHARE_CREATE->value),
                ]
            );
        }

        $request->validate([
            'name'       => 'required',
            'expiration' => 'required',
        ]);

        $url = URL::temporarySignedRoute(
            name: 'share',
            parameters: [
                'user'  => $user,
                'guest' => base64_encode($request->get('name')),
            ],
            expiration: now()->add($request->get('expiration')),
        );

        $message = sprintf('%s (%s): invited by %s', $request->get('name'), $request->get('expiration'), $user->name);

        if ($tokenName = $user->currentAccessToken()->name) {
            $message .= " [token: {$tokenName}]";
        }

        activity()
            ->causedBy($user)
            ->event('shared')
            ->log($message);

        return response()->json(
            status: Response::HTTP_OK,
            data: [
                'url' => $url,
            ]
        );
    }
}
