<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_events_index(): void
    {
        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertViewIs('events.index');
    }

    public function test_can_create_event(): void
    {
        $response = $this->post(route('events.store'), [
            'name' => 'Test Secret Santa',
            'description' => 'Test description',
            'event_date' => '2024-12-25',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'name' => 'Test Secret Santa',
            'description' => 'Test description',
        ]);
    }

    public function test_can_add_participant_to_event(): void
    {
        $event = Event::factory()->create();

        $response = $this->post(route('events.participants.store', $event), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'event_id' => $event->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $this->assertNotNull(Participant::where('email', 'john@example.com')->first()->token);
    }

    public function test_can_add_exclusion(): void
    {
        $event = Event::factory()->create();
        $participant1 = Participant::factory()->create(['event_id' => $event->id]);
        $participant2 = Participant::factory()->create(['event_id' => $event->id]);

        $response = $this->post(route('events.participants.exclusions.store', [$event, $participant1]), [
            'excluded_participant_id' => $participant2->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exclusions', [
            'participant_id' => $participant1->id,
            'excluded_participant_id' => $participant2->id,
        ]);
    }

    public function test_cannot_add_same_exclusion_twice(): void
    {
        $event = Event::factory()->create();
        $participant1 = Participant::factory()->create(['event_id' => $event->id]);
        $participant2 = Participant::factory()->create(['event_id' => $event->id]);

        $this->post(route('events.participants.exclusions.store', [$event, $participant1]), [
            'excluded_participant_id' => $participant2->id,
        ]);

        $response = $this->post(route('events.participants.exclusions.store', [$event, $participant1]), [
            'excluded_participant_id' => $participant2->id,
        ]);

        $response->assertSessionHasErrors();
    }
}
