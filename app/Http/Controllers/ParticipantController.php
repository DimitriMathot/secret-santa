<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Exclusion;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Check if email already exists for this event
        $existingParticipant = $event->participants()
            ->where('email', $validated['email'])
            ->first();

        if ($existingParticipant) {
            return back()->withErrors(['email' => 'Cet email est déjà utilisé pour cet événement.']);
        }

        $participant = $event->participants()->create($validated);

        return back()->with('success', 'Participant ajouté avec succès');
    }

    public function destroy(Event $event, Participant $participant)
    {
        // Ensure participant belongs to event
        if ($participant->event_id !== $event->id) {
            abort(404);
        }

        $participant->delete();

        return back()->with('success', 'Participant supprimé avec succès');
    }

    public function storeExclusion(Request $request, Event $event, Participant $participant)
    {
        $validated = $request->validate([
            'excluded_participant_id' => 'required|exists:participants,id',
        ]);

        // Ensure excluded participant belongs to same event
        $excludedParticipant = Participant::findOrFail($validated['excluded_participant_id']);
        
        if ($excludedParticipant->event_id !== $event->id || $participant->event_id !== $event->id) {
            return back()->withErrors(['excluded_participant_id' => 'Participant invalide.']);
        }

        if ($participant->id === $excludedParticipant->id) {
            return back()->withErrors(['excluded_participant_id' => 'Un participant ne peut pas s\'exclure lui-même.']);
        }

        // Check if exclusion already exists
        $existingExclusion = Exclusion::where('participant_id', $participant->id)
            ->where('excluded_participant_id', $excludedParticipant->id)
            ->first();

        if ($existingExclusion) {
            return back()->withErrors(['excluded_participant_id' => 'Cette exclusion existe déjà.']);
        }

        Exclusion::create([
            'participant_id' => $participant->id,
            'excluded_participant_id' => $excludedParticipant->id,
        ]);

        return back()->with('success', 'Exclusion ajoutée avec succès');
    }

    public function destroyExclusion(Event $event, Participant $participant, Exclusion $exclusion)
    {
        // Ensure exclusion belongs to participant and event
        if ($exclusion->participant_id !== $participant->id || $participant->event_id !== $event->id) {
            abort(404);
        }

        $exclusion->delete();

        return back()->with('success', 'Exclusion supprimée avec succès');
    }
}
