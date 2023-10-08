<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bold_key_id',
        'bold_key_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'bold_key_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'bold_key_secret'   => 'encrypted',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function locks(): HasMany
    {
        return $this->hasMany(Lock::class);
    }

    public function createBoldApiKey(string $boldToken): bool
    {
        $accountResponse = Http::acceptJson()
            ->withHeader('Bold-Client-Token', config('services.bold.firewall_key'))
            ->withToken($boldToken)
            ->get('https://api.boldsmartlock.com/v1/account');

        if ($accountResponse->failed()) {
            return false;
        }

        $accountData = $accountResponse->json();

        $keyResponse = Http::acceptJson()
            ->withHeader('Bold-Client-Token', config('services.bold.firewall_key'))
            ->withToken($boldToken)
            ->post(
                "https://api.boldsmartlock.com/v1/accounts/{$accountData['id']}/api-keys",
                [
                    'organizationId' => $accountData['personalOrganization']['id'],
                    'description'    => config('app.name') . ' (' . now() . ')',
                ]
            );

        if ($keyResponse->failed()) {
            return false;
        }

        $keyData = $keyResponse->json();

        return $this->update([
            'bold_key_id'     => $keyData['apiKeyId'],
            'bold_key_secret' => $keyData['apiKeySecret'],
        ]);
    }
}
