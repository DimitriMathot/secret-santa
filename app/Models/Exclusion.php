<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exclusion extends Model
{
    protected $fillable = [
        'participant_id',
        'excluded_participant_id',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function excludedParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'excluded_participant_id');
    }
}
