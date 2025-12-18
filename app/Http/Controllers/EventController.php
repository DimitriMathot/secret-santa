<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::withCount('participants')->latest()->get();
        
        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
        ]);

        $event = Event::create($validated);

        return redirect()->route('events.show', $event)
            ->with('success', 'Événement créé avec succès');
    }

    public function show(Event $event): View
    {
        $event->load('participants.excludedParticipants');
        
        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
        ]);

        $event->update($validated);

        return redirect()->route('events.show', $event)
            ->with('success', 'Événement mis à jour avec succès');
    }
}
