<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;

class Lock extends Model
{
    public function home(): BelongsTo
    {
        return $this->belongsTo(Home::class);
    }

    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function trigger(string $causer): string
    {
        if (blank($this->home->bold_key_id) || blank($this->home->bold_key_secret)) {
            return 'error: api key missing';
        }

        $response = Http::acceptJson()
            ->withHeader('Bold-Client-Token', config('services.bold.firewall_key'))
            ->withBasicAuth($this->home->bold_key_id, $this->home->bold_key_secret)
            ->post("https://api.boldsmartlock.com/v1/devices/{$this->bold_id}/remote-activation");

        if ($response->failed()) {
            if ($response->json()) {
                return "error: {$response->json()}";
            }

            return "error: {$response->status()}";
        }

        activity()
            ->causedBy($this->home)
            ->performedOn($this)
            ->event('lockActivated')
            ->log(__(':causer heeft de :subject.name geopend', [
                'causer' => $causer,
            ]));

        return 'ok';
    }
}
