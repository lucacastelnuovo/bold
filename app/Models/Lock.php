<?php

namespace App\Models;

use App\Services\Bold\BoldService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Activity;

class Lock extends Model
{
    /** @use HasFactory<\Database\Factories\LockFactory> */
    use HasFactory;

    use HasUlids;

    protected $fillable = [
        'bold_id',
        'bold_name',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Activity, $this>
     */
    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function activate(?string $tokenName = null, ?string $guestName = null): bool
    {
        $boldLock = app(BoldService::class);

        if (!$boldLock->activateLock($this->user, $this->bold_id)) {
            return false;
        }

        $message = sprintf(':subject.bold_name: activated by %s', $this->user->name);

        if ($tokenName) {
            $message .= " [token: {$tokenName}]";
        }

        if ($guestName) {
            $message .= " [guest: {$guestName}]";
        }

        activity()
            ->causedBy($this->user)
            ->performedOn($this)
            ->event('activated')
            ->log($message);

        return true;
    }
}
