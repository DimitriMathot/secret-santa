<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Assignment extends Model
{
    protected $fillable = [
        'event_id',
        'participant_id',
        'assigned_to_id_encrypted',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function setAssignedToId(int $participantId): void
    {
        $this->assigned_to_id_encrypted = Crypt::encryptString((string) $participantId);
    }

    public function getAssignedToId(): ?int
    {
        if (empty($this->assigned_to_id_encrypted)) {
            return null;
        }

        try {
            return (int) Crypt::decryptString($this->assigned_to_id_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAssignedParticipant(): ?Participant
    {
        $assignedToId = $this->getAssignedToId();
        
        if ($assignedToId === null) {
            return null;
        }

        return Participant::find($assignedToId);
    }
}
