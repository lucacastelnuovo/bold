<?php

namespace App\Enums;

use App\Enums\Support\Values;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

/**
 * @deprecated Use the Lock model instead
 */
enum Lock: string
{
    use Values;

    case VOORDEUR = 'voordeur';
    case BOVENDEUR = 'bovendeur';

    public static function createLink(User $owner, string $causer, string $expiration): string
    {
        return URL::signedRoute(
            name: 'locks.index',
            parameters: [
                'owner'  => $owner->id,
                'causer' => $causer,
            ],
            expiration: now()->addHours($expiration) // TODO: accept 1hr, 2days, etc
        );
    }

    public function getId(): string
    {
        return match ($this) {
            self::VOORDEUR  => '102059',
            self::BOVENDEUR => '88350',
        };
    }

    public function trigger(User $owner, string $causer): string
    {
        if (blank($owner->bold_key_id) || blank($owner->bold_key_secret)) {
            return 'error: api key missing';
        }

        $response = Http::acceptJson()
            ->withHeader('Bold-Client-Token', config('services.bold.firewall_key'))
            ->withBasicAuth($owner->bold_key_id, $owner->bold_key_secret)
            ->post("https://api.boldsmartlock.com/v1/devices/{$this->getId()}/remote-activation");

        if ($response->failed()) {
            if ($response->json()) {
                return "error: {$response->json()}";
            }

            return "error: {$response->status()}";
        }

        activity()
            ->causedBy($owner)
            // TODO: ->performedOn($lock)
            ->event('lockActivated')
            ->log("{$causer} heeft de {$this->value} geopend");

        return 'ok';
    }
}
