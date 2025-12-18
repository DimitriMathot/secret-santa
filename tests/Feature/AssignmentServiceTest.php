<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Exclusion;
use App\Models\Participant;
use App\Services\AssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_generate_assignments_with_less_than_3_participants(): void
    {
        $event = Event::factory()->create();
        Participant::factory()->count(2)->create(['event_id' => $event->id]);

        $service = new AssignmentService();
        $result = $service->generateAssignments($event);

        $this->assertFalse($result);
        $this->assertFalse($event->fresh()->assignments_generated);
    }

    public function test_can_generate_assignments_with_3_participants(): void
    {
        $event = Event::factory()->create();
        Participant::factory()->count(3)->create(['event_id' => $event->id]);

        $service = new AssignmentService();
        $result = $service->generateAssignments($event);

        $this->assertTrue($result);
        $this->assertTrue($event->fresh()->assignments_generated);
        $this->assertEquals(3, Assignment::where('event_id', $event->id)->count());
    }

    public function test_assignments_respect_exclusions(): void
    {
        $event = Event::factory()->create();
        $participant1 = Participant::factory()->create(['event_id' => $event->id]);
        $participant2 = Participant::factory()->create(['event_id' => $event->id]);
        $participant3 = Participant::factory()->create(['event_id' => $event->id]);

        Exclusion::create([
            'participant_id' => $participant1->id,
            'excluded_participant_id' => $participant2->id,
        ]);

        $service = new AssignmentService();
        $result = $service->generateAssignments($event);

        $this->assertTrue($result);
        
        $assignment1 = Assignment::where('participant_id', $participant1->id)->first();
        $assignedTo1 = $assignment1->getAssignedToId();
        
        // Participant1 should not be assigned to participant2 (excluded)
        $this->assertNotEquals($participant2->id, $assignedTo1);
        // Participant1 should not be assigned to themselves
        $this->assertNotEquals($participant1->id, $assignedTo1);
    }

    public function test_assignments_are_encrypted(): void
    {
        $event = Event::factory()->create();
        Participant::factory()->count(3)->create(['event_id' => $event->id]);

        $service = new AssignmentService();
        $service->generateAssignments($event);

        $assignment = Assignment::first();
        
        // The encrypted value should not be readable as plain ID
        $this->assertNotEquals((string)$assignment->participant_id, $assignment->assigned_to_id_encrypted);
        
        // But we should be able to decrypt it
        $decryptedId = $assignment->getAssignedToId();
        $this->assertIsInt($decryptedId);
        $this->assertGreaterThan(0, $decryptedId);
    }
}
