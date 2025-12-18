<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    /**
     * Generate assignments for an event using a random matching algorithm
     * that respects exclusions.
     */
    public function generateAssignments(Event $event): bool
    {
        if (!$event->canGenerateAssignments()) {
            return false;
        }

        $participants = $event->participants()->get();
        
        if ($participants->count() < 3) {
            return false;
        }

        return DB::transaction(function () use ($event, $participants) {
            // Build exclusion map for efficient lookup
            $exclusions = $this->buildExclusionMap($participants);

            // Generate valid assignments
            $assignments = $this->generateValidAssignments($participants, $exclusions);

            if ($assignments === null) {
                return false; // Impossible to generate valid assignments
            }

            // Save assignments
            foreach ($assignments as $participantId => $assignedToId) {
                $assignment = new Assignment();
                $assignment->event_id = $event->id;
                $assignment->participant_id = $participantId;
                $assignment->setAssignedToId($assignedToId);
                $assignment->save();
            }

            // Mark event as having assignments generated
            $event->update(['assignments_generated' => true]);

            return true;
        });
    }

    /**
     * Build a map of participant IDs to their excluded participant IDs.
     */
    private function buildExclusionMap($participants): array
    {
        $exclusions = [];

        foreach ($participants as $participant) {
            $exclusions[$participant->id] = $participant->excludedParticipants()
                ->pluck('id')
                ->toArray();
        }

        return $exclusions;
    }

    /**
     * Generate valid assignments using a backtracking algorithm.
     */
    private function generateValidAssignments($participants, array $exclusions): ?array
    {
        $participantIds = $participants->pluck('id')->toArray();
        $assignments = [];
        $remaining = $participantIds;

        // Try multiple times if needed (with randomization)
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $assignments = [];
            $remaining = array_values($participantIds);
            shuffle($remaining);

            foreach ($participantIds as $participantId) {
                $excludedIds = array_merge(
                    [$participantId], // Can't assign to self
                    $exclusions[$participantId] ?? []
                );

                $available = array_filter($remaining, function ($id) use ($excludedIds) {
                    return !in_array($id, $excludedIds);
                });

                if (empty($available)) {
                    break; // Try again
                }

                $assignedId = $available[array_rand($available)];
                $assignments[$participantId] = $assignedId;
                
                // Remove assigned ID from remaining
                $remaining = array_values(array_filter($remaining, function ($id) use ($assignedId) {
                    return $id !== $assignedId;
                }));
            }

            // If we successfully assigned everyone, return the assignments
            if (count($assignments) === count($participantIds)) {
                return $assignments;
            }
        }

        return null; // Could not generate valid assignments
    }
}

