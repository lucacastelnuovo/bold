<?php

namespace App\Models;

use App\Services\Bold\BoldService;
use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use CausesActivity;
    use HasAbilities;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use LogsActivity;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bold_access_token',
        'bold_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'bold_access_token',
        'bold_refresh_token',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty()
            ->logOnly([
                'name', 'email', 'email_verified_at',
            ])
            ->setDescriptionForEvent(
                fn (string $eventName): string => ":subject.name: {$eventName}"
            );
    }

    /**
     * @return HasMany<Lock>
     */
    public function locks(): HasMany
    {
        return $this->hasMany(Lock::class);
    }

    public function refreshTokens(): bool
    {
        $bold = app(BoldService::class);

        if (!$tokens = $bold->refreshTokens($this->bold_refresh_token)) {
            return false;
        }

        activity()
            ->causedBy($this)
            ->performedOn($this)
            ->event('refreshed')
            ->log(':subject.name: tokens refreshed');

        $this->update([
            'bold_access_token'  => $tokens->accessToken,
            'bold_refresh_token' => $tokens->refreshToken,
        ]);

        return true;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'bold_access_token'  => 'encrypted',
            'bold_refresh_token' => 'encrypted',
        ];
    }
}
