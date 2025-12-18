<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Exclusion;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class SecretSantaSeeder extends Seeder
{
    /**
     * Seed the application's database with Secret Santa test data.
     */
    public function run(): void
    {
        // Create an event
        $event = Event::create([
            'name' => 'Secret Santa 2024',
            'description' => 'Échange de cadeaux de Noël pour la famille et les amis',
            'event_date' => '2024-12-25',
            'assignments_generated' => false,
        ]);

        // Create participants
        $alice = Participant::create([
            'event_id' => $event->id,
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
        ]);

        $bob = Participant::create([
            'event_id' => $event->id,
            'name' => 'Bob Dupont',
            'email' => 'bob@example.com',
        ]);

        $charlie = Participant::create([
            'event_id' => $event->id,
            'name' => 'Charlie Bernard',
            'email' => 'charlie@example.com',
        ]);

        $diana = Participant::create([
            'event_id' => $event->id,
            'name' => 'Diana Rousseau',
            'email' => 'diana@example.com',
        ]);

        $eve = Participant::create([
            'event_id' => $event->id,
            'name' => 'Eve Leroy',
            'email' => 'eve@example.com',
        ]);

        // Create some exclusions (Alice doesn't want to be assigned to Bob)
        Exclusion::create([
            'participant_id' => $alice->id,
            'excluded_participant_id' => $bob->id,
        ]);

        $this->command->info('✅ Secret Santa test data created!');
        $this->command->info('📧 Participant tokens:');
        $this->command->info('   Alice: ' . route('participant.show', ['token' => $alice->token]));
        $this->command->info('   Bob: ' . route('participant.show', ['token' => $bob->token]));
        $this->command->info('   Charlie: ' . route('participant.show', ['token' => $charlie->token]));
        $this->command->info('   Diana: ' . route('participant.show', ['token' => $diana->token]));
        $this->command->info('   Eve: ' . route('participant.show', ['token' => $eve->token]));
    }
}
