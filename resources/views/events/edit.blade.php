@extends('layouts.app')

@section('title', 'Modifier l\'événement')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Modifier l'événement</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('events.update', $event) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom de l'événement <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $event->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Date de l'événement
                </label>
                <input type="date" name="event_date" id="event_date" value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('events.show', $event) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Annuler
                </a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

