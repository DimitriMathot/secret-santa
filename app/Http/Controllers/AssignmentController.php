<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Notifications\SecretSantaAssignmentNotification;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct(
        private AssignmentService $assignmentService
    ) {}

    public function generate(Event $event)
    {
        if ($event->assignments_generated) {
            return back()->withErrors(['error' => 'Les assignations ont déjà été générées pour cet événement.']);
        }

        $success = $this->assignmentService->generateAssignments($event);

        if (!$success) {
            return back()->withErrors([
                'error' => 'Impossible de générer les assignations. Vérifiez qu\'il y a au moins 3 participants et que les exclusions permettent un matching valide.'
            ]);
        }

        // Send email notifications to all participants
        $event->load('participants.assignment');
        foreach ($event->participants as $participant) {
            if ($participant->assignment) {
                $participant->notify(new SecretSantaAssignmentNotification($event, $participant));
            }
        }

        return back()->with('success', 'Assignations générées et emails envoyés avec succès');
    }
}
