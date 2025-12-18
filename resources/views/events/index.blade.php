@extends('layouts.app')

@section('title', 'Liste des événements')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Événements Secret Santa</h1>
        <a href="{{ route('events.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            + Nouvel événement
        </a>
    </div>

    @if($events->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg mb-4">Aucun événement pour le moment.</p>
            <a href="{{ route('events.create') }}" class="text-red-600 hover:text-red-700 font-medium">
                Créer votre premier événement →
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $event)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $event->name }}</h3>
                        @if($event->description)
                            <p class="text-gray-600 text-sm mb-4">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                {{ $event->participants_count }} participant{{ $event->participants_count > 1 ? 's' : '' }}
                            </span>
                            <div class="flex gap-2">
                                <a href="{{ route('events.show', $event) }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    Voir →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

