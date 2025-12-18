<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_access_with_token(): void
    {
        $event = Event::factory()->create();
        $participant = Participant::factory()->create([
            'event_id' => $event->id,
            'token' => 'test-token-123',
        ]);

        $response = $this->get(route('participant.show', ['token' => 'test-token-123']));

        $response->assertStatus(200);
        $response->assertViewIs('participant.show');
        $response->assertViewHas('participant');
    }

    public function test_participant_cannot_access_with_invalid_token(): void
    {
        $response = $this->get(route('participant.show', ['token' => 'invalid-token']));

        $response->assertStatus(404);
    }

    public function test_participant_sees_assignment_when_generated(): void
    {
        $event = Event::factory()->create(['assignments_generated' => true]);
        $participant1 = Participant::factory()->create(['event_id' => $event->id]);
        $participant2 = Participant::factory()->create(['event_id' => $event->id]);

        $assignment = new Assignment();
        $assignment->event_id = $event->id;
        $assignment->participant_id = $participant1->id;
        $assignment->setAssignedToId($participant2->id);
        $assignment->save();

        $response = $this->get(route('participant.show', ['token' => $participant1->token]));

        $response->assertStatus(200);
        $response->assertViewHas('assignedParticipant', function ($assigned) use ($participant2) {
            return $assigned && $assigned->id === $participant2->id;
        });
    }

    public function test_participant_viewed_at_is_updated(): void
    {
        $event = Event::factory()->create(['assignments_generated' => true]);
        $participant = Participant::factory()->create([
            'event_id' => $event->id,
            'has_viewed_assignment' => false,
        ]);

        $this->assertNull($participant->viewed_at);

        $this->get(route('participant.show', ['token' => $participant->token]));

        $participant->refresh();
        $this->assertTrue($participant->has_viewed_assignment);
        $this->assertNotNull($participant->viewed_at);
    }
}
