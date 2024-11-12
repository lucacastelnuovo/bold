<?php

namespace App\Http\Controllers;

use App\Enums\Ability;
use App\Http\Resources\LockResource;
use App\Models\Lock;
use App\Models\User;
use App\Services\Bold\BoldService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ApiLockController
{
    public function __construct(protected BoldService $bold) {}

    public function index(#[CurrentUser] User $user): AnonymousResourceCollection
    {
        return LockResource::collection($user->locks);
    }

    public function sync(#[CurrentUser] User $user): JsonResponse
    {
        if (!$user->hasAbilityTo(Ability::LOCK_SYNC)) {
            return response()->json(
                status: Response::HTTP_FORBIDDEN,
                data: [
                    'error' => sprintf('Missing ability: %s!', Ability::LOCK_SYNC->value),
                ]
            );
        }

        if (!$locks = $this->bold->getLocks($user)) {
            return response()->json(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: [
                    'error' => 'BoldService: locks could not be retrieved!',
                ]
            );
        }

        DB::transaction(function () use ($user, $locks) {
            foreach ($locks as $lock) {
                /* Update existing locks */
                if ($existingLock = $user->locks->firstWhere('bold_id', $lock->id)) {
                    $existingLock->update([
                        'bold_name' => $lock->name,
                    ]);

                    continue;
                }

                /* Create new locks */
                $user->locks()->create([
                    'bold_id'   => $lock->id,
                    'bold_name' => $lock->name,
                ]);
            }

            /* Delete old locks */
            if ($locksToDelete = array_diff($user->locks->pluck('bold_id')->toArray(), $locks->pluck('id')->toArray())) {
                $user->locks()->whereIn('bold_id', $locksToDelete)->delete();
            }
        });

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->event('synced')
            ->log(':causer.name: synced locks');

        return response()->json(
            status: Response::HTTP_OK,
            data: [
                'message' => 'ok',
            ]
        );
    }

    public function show(#[CurrentUser] $user, Lock $lock): LockResource
    {
        return new LockResource($lock);
    }

    public function activate(#[CurrentUser] User $user, Lock $lock): JsonResponse
    {
        if (!$user->hasAbilityTo(Ability::LOCK_ACTIVATE)) {
            return response()->json(
                status: Response::HTTP_FORBIDDEN,
                data: [
                    'error' => sprintf('Missing ability: %s!', Ability::LOCK_ACTIVATE->value),
                ]
            );
        }

        if (!$lock->activate(tokenName: $user->currentAccessToken()->name)) {
            return response()->json(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: [
                    'error' => 'BoldService: lock could not be activated!',
                ]
            );
        }

        return response()->json(
            status: Response::HTTP_OK,
            data: [
                'message' => 'ok',
            ]
        );
    }
}
