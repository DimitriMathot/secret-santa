<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantAccessController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

// Public participant access (token-based, no authentication)
Route::get('/participant/{token}', [ParticipantAccessController::class, 'show'])
    ->name('participant.show');

// Admin routes (events management)
Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::resource('events', EventController::class);

// Participant management (within events)
Route::post('events/{event}/participants', [ParticipantController::class, 'store'])
    ->name('events.participants.store');
Route::delete('events/{event}/participants/{participant}', [ParticipantController::class, 'destroy'])
    ->name('events.participants.destroy');

// Exclusions management
Route::post('events/{event}/participants/{participant}/exclusions', [ParticipantController::class, 'storeExclusion'])
    ->name('events.participants.exclusions.store');
Route::delete('events/{event}/participants/{participant}/exclusions/{exclusion}', [ParticipantController::class, 'destroyExclusion'])
    ->name('events.participants.exclusions.destroy');

// Assignment generation
Route::post('events/{event}/assignments/generate', [AssignmentController::class, 'generate'])
    ->name('events.assignments.generate');
