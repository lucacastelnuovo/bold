<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;

class Lock extends Model
{
    protected static function booted(): void
    {
        // TODO: test the belongs to user global scope
        static::addGlobalScope('user', function (Builder $builder) {
            $builder->when(auth()->check(), function (Builder $builder) {
                $builder->where('user_id', auth()->id());
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function trigger(User $owner, string $causer): string
    {
        if (blank($owner->bold_key_id) || blank($owner->bold_key_secret)) {
            return 'error: api key missing';
        }

        $response = Http::acceptJson()
            ->withHeader('Bold-Client-Token', config('services.bold.firewall_key'))
            ->withBasicAuth($owner->bold_key_id, $owner->bold_key_secret)
            ->post("https://api.boldsmartlock.com/v1/devices/{$this->bold_id}/remote-activation");

        if ($response->failed()) {
            if ($response->json()) {
                return "error: {$response->json()}";
            }

            return "error: {$response->status()}";
        }

        activity()
            ->causedBy($owner)
            ->performedOn($this)
            ->event('lockActivated')
            ->log("{$causer} heeft de {$this->value} geopend");

        return 'ok';
    }
}
