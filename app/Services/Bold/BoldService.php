<?php

namespace App\Services\Bold;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Throwable;

class BoldService
{
    public function __construct(
        private PendingRequest $client
    ) {}

    public function refreshTokens(string $refreshToken): ?BoldTokenData
    {
        // TODO: this does not work, but it seems like the access_token does not expire?

        try {
            $tokens = $this->client
                ->post('v2/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'client_id' => 'BoldPortal',
                    'client_secret' => 'Pa2yx7PAvMEJFs3SchGR575e',
                    'refresh_token' => $refreshToken,
                ])
                ->json();

            return new BoldTokenData($tokens->access_token, $tokens->refresh_token);
        } catch (Throwable) {
            return null;
        }
    }

    public function getLocks(User $user): ?Collection
    {
        try {
            $response = $this->client
                ->withToken($user->bold_access_token)
                ->get('v1/devices')
                ->json();

            return BoldLockData::collect($response, Collection::class);
        } catch (Throwable) {
            return null;
        }
    }

    public function activateLock(User $user, string $id): bool
    {
        try {
            $this->client
                ->withToken($user->bold_access_token)
                ->post(sprintf('v1/devices/%s/remote-activation', $id))
                ->json();
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
