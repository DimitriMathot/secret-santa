<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'event_id',
        'name',
        'email',
        'token',
        'has_viewed_assignment',
        'viewed_at',
    ];

    protected $casts = [
        'has_viewed_assignment' => 'boolean',
        'viewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            if (empty($participant->token)) {
                $participant->token = Str::random(64);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(Exclusion::class);
    }

    public function excludedParticipants(): BelongsToMany
    {
        return $this->belongsToMany(
            Participant::class,
            'exclusions',
            'participant_id',
            'excluded_participant_id'
        );
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }
}
