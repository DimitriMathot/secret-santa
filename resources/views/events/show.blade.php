@extends('layouts.app')

@section('title', $event->name)

@section('content')
<div class="px-4 py-6 sm:px-0" x-data="eventManagement()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
            @if($event->description)
                <p class="text-gray-600 mt-2">{{ $event->description }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('events.edit', $event) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                Modifier
            </a>
        </div>
    </div>

    <!-- Participants Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Participants ({{ $event->participants->count() }})</h2>
            @if(!$event->assignments_generated && $event->participants->count() >= 3)
                <form action="{{ route('events.assignments.generate', $event) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                        onclick="return confirm('Êtes-vous sûr de vouloir générer les assignations ? Cette action est irréversible.')"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        🎲 Générer les assignations
                    </button>
                </form>
            @elseif($event->assignments_generated)
                <span class="text-green-600 font-semibold">✓ Assignations générées</span>
            @else
                <span class="text-gray-500">Minimum 3 participants requis</span>
            @endif
        </div>

        <!-- Add Participant Form -->
        <form action="{{ route('events.participants.store', $event) }}" method="POST" class="mb-6">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <input type="text" name="name" placeholder="Nom" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <input type="email" name="email" placeholder="Email" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Ajouter
                    </button>
                </div>
            </div>
        </form>

        <!-- Participants List -->
        <div class="space-y-3">
            @forelse($event->participants as $participant)
                <div class="border border-gray-200 rounded-lg p-4" 
                     x-data="{ showExclusions: false }">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $participant->name }}</div>
                            <div class="text-sm text-gray-600">{{ $participant->email }}</div>
                            @if($participant->excludedParticipants->count() > 0)
                                <div class="text-xs text-gray-500 mt-1">
                                    Exclut: {{ $participant->excludedParticipants->pluck('name')->join(', ') }}
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button @click="showExclusions = !showExclusions" 
                                class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                <span x-show="!showExclusions">Gérer exclusions</span>
                                <span x-show="showExclusions">Masquer</span>
                            </button>
                            <form action="{{ route('events.participants.destroy', [$event, $participant]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    onclick="return confirm('Supprimer ce participant ?')"
                                    class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Exclusions Form -->
                    <div x-show="showExclusions" x-transition class="mt-4 pt-4 border-t border-gray-200">
                        <form action="{{ route('events.participants.exclusions.store', [$event, $participant]) }}" method="POST">
                            @csrf
                            <div class="flex gap-2">
                                <select name="excluded_participant_id" required
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    <option value="">Sélectionner un participant à exclure</option>
                                    @foreach($event->participants->where('id', '!=', $participant->id) as $otherParticipant)
                                        @if(!$participant->excludedParticipants->contains($otherParticipant->id))
                                            <option value="{{ $otherParticipant->id }}">{{ $otherParticipant->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Ajouter exclusion
                                </button>
                            </div>
                        </form>

                        <!-- Existing Exclusions -->
                        @if($participant->exclusions->count() > 0)
                            <div class="mt-3 space-y-2">
                                @foreach($participant->exclusions as $exclusion)
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                        <span class="text-sm">{{ $exclusion->excludedParticipant->name }}</span>
                                        <form action="{{ route('events.participants.exclusions.destroy', [$event, $participant, $exclusion]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                                                ✕
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Aucun participant pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
function eventManagement() {
    return {
        // Alpine.js data
    }
}
</script>
@endsection

