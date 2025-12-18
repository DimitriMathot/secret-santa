<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'event_date',
        'assignments_generated',
    ];

    protected $casts = [
        'event_date' => 'date',
        'assignments_generated' => 'boolean',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function canGenerateAssignments(): bool
    {
        return $this->participants()->count() >= 3 && !$this->assignments_generated;
    }
}
