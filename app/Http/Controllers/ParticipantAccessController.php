<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantAccessController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $participant = Participant::where('token', $token)->firstOrFail();
        
        $participant->load(['event', 'assignment']);

        $assignedParticipant = null;
        if ($participant->assignment) {
            $assignedParticipant = $participant->assignment->getAssignedParticipant();
            
            // Mark as viewed if not already
            if (!$participant->has_viewed_assignment) {
                $participant->update([
                    'has_viewed_assignment' => true,
                    'viewed_at' => now(),
                ]);
            }
        }

        return view('participant.show', [
            'participant' => $participant,
            'assignedParticipant' => $assignedParticipant,
        ]);
    }
}
