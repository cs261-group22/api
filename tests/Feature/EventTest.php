<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EventTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private function unauthenticated(TestResponse $response)
    {
        $response->assertStatus(403)->assertExactJson([
            'message' => 'Unauthenticated',
        ]);
    }

    /**
     * A test for admins to access a list of all events.
     *
     * @return void
     */
    public function testAdminGetEvents()
    {
        $userA = User::factory()->admin()->create();
        $this->actingAs($userA, 'sanctum');
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);

        $expectedEvents = collect([$eventA, $eventB, $eventC, $eventD, $eventC]);
        $response = $this->get('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => $this->getEventJsonStructure(),
            ],
        ]);

        $ids = collect($response['data'])->pluck('id');
        $expectedEvents->each(function (Event $event) use ($ids) {
            $this->assertContains($event->id, $ids);
        });
    }

    /**
     * A test for non admins to access a list of all events.
     *
     * @return void
     */
    public function testNonAdminGetEvents()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $this->actingAs($userC, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);

        $expectedEvents = collect([$eventC, $eventD]);
        $response = $this->get('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => $this->getEventJsonStructure(),
            ],
        ]);

        $ids = collect($response['data'])->pluck('id');
        $expectedEvents->each(function (Event $event) use ($ids) {
            $this->assertContains($event->id, $ids);
        });
    }

    /**
     * A test for guest to access a list of all events.
     *
     * @return void
     */
    public function testGuestGetEvents()
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user, 'sanctum');
        Event::factory()->create();
        $response = $this->get('/api/v1/events');
        $this->unauthenticated($response);
    }

    /**
     * A test for admins to access a list of all events.
     *
     * @return void
     */
    public function testAdminJoinEvent()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user, 'sanctum');
        $this->JoinEventTest();
    }

    /**
     * A test for non admins to access a list of all events.
     *
     * @return void
     */
    public function testNonAdminJoinEvent()
    {
        $user = User::factory()->non_admin()->create();
        $this->actingAs($user, 'sanctum');
        $this->JoinEventTest();
    }

    /**
     * A test for guest to access a list of all events.
     *
     * @return void
     */
    public function testGuestJoinEvent()
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user, 'sanctum');
        $this->JoinEventTest();
    }

    private function JoinEventTest()
    {
        $code = Event::generateUniqueEventCode();
        Event::factory()->create(['code' => $code]);

        $response = $this->get('/api/v1/events/code/'.$code);
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'code',
                'ends_at',
                'is_draft',
                'starts_at',
                'description',
                'allow_guests',
                'max_sessions',

            ],
        ])->assertJsonPath('data.code', $code);
    }

    /**
     * A test for admins to access a list of all events.
     *
     * @return void
     */
    public function testAdminAddEvent()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->AddEventTest();
        $response->assertStatus(201)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    /**
     * A test for non admins to access a list of all events.
     *
     * @return void
     */
    public function testNonAdminAddEvent()
    {
        $user = User::factory()->non_admin()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->AddEventTest();
        $response->assertStatus(201)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    /**
     * A test for guest to access a list of all events.
     *
     * @return void
     */
    public function testGuestAddEvent()
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->AddEventTest();
        $this->unauthenticated($response);
    }

    private function AddEventTest(): TestResponse
    {
        $name = $this->faker->catchPhrase();
        $guests = $this->faker->boolean();
        $draft = $this->faker->boolean();
        $response = $this->postJson('/api/v1/events', ['name' => $name, 'allow_guests' => $guests, 'is_draft' => $draft]);

        return $response;
    }

    public function testAdminShowEvent()
    {
        $userA = User::factory()->admin()->create();
        $this->actingAs($userA, 'sanctum');
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);

        $response = $this->get('/api/v1/events/'.$eventA->id);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminShowEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $this->actingAs($userC, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);

        $response = $this->get('/api/v1/events/'.$eventC->id);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminShowEventNotOwned()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $this->actingAs($userC, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);

        $response = $this->get('/api/v1/events/'.$eventE->id);
        $this->unauthenticated($response);
    }

    public function testGuestShowEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userE, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);

        $response = $this->get('/api/v1/events/'.$eventE->id);
        $this->unauthenticated($response);
    }

    public function testGuestUpdateEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userE, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->putJson('/api/v1/events/'.$eventE->id, [
            'name' => 'name',
            'ends_at' => now(),
            'starts_at' => now(),
            'is_draft' => true,
            'description' => 'description',
            'allow_guests' => true,
            'max_sessions' => 4,
        ]);
        $this->unauthenticated($response);
    }

    public function testAdminUpdateEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userA, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->putJson('/api/v1/events/'.$eventE->id, [
            'name' => 'name',
            'ends_at' => now(),
            'starts_at' => now(),
            'is_draft' => true,
            'description' => 'description',
            'allow_guests' => true,
            'max_sessions' => 4,
        ]);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminUpdateEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userD, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->putJson('/api/v1/events/'.$eventE->id, [
            'name' => 'name',
            'ends_at' => now(),
            'starts_at' => now(),
            'is_draft' => true,
            'description' => 'description',
            'allow_guests' => true,
            'max_sessions' => 4,
        ]);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminUpdateEventNoAccess()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userC, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->putJson('/api/v1/events/'.$eventE->id, [
            'name' => 'name',
            'ends_at' => now(),
            'starts_at' => now(),
            'is_draft' => true,
            'description' => 'description',
            'allow_guests' => true,
            'max_sessions' => 4,
        ]);
        $this->unauthenticated($response);
    }

    public function testAdminDeleteEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userA, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->delete('/api/v1/events/'.$eventE->id);
        $response->assertStatus(204);
    }

    public function testNonAdminDeleteEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userD, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->delete('/api/v1/events/'.$eventE->id);
        $response->assertStatus(204);
    }

    public function testGuestDeleteEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userE, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->delete('/api/v1/events/'.$eventE->id);
        $this->unauthenticated($response);
    }

    public function testNonAdminDeleteEventNoAccess()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userC, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->delete('/api/v1/events/'.$eventE->id);
        $this->unauthenticated($response);
    }

    public function testAdminPublishEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userA, 'sanctum');

        $eventE = Event::factory()->draft()->create();
        $question = Question::factory()->create([
            'event_id' => $eventE->id,
        ]);

        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->post('/api/v1/events/'.$eventE->id.'/publish');
        $response->assertStatus(204);
    }

    public function testNonAdminPublishEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userD, 'sanctum');

        $eventE = Event::factory()->draft()->create();
        $question = Question::factory()->create([
            'event_id' => $eventE->id,
        ]);

        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->post('/api/v1/events/'.$eventE->id.'/publish');
        $response->assertStatus(204);
    }

    public function testGuestPublishEvent()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userE, 'sanctum');

        $eventE = Event::factory()->draft()->create();
        $question = Question::factory()->create([
            'event_id' => $eventE->id,
        ]);

        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->post('/api/v1/events/'.$eventE->id.'/publish');
        $this->unauthenticated($response);
    }

    public function testNonAdminPublishEventNoAccess()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userC, 'sanctum');

        $eventE = Event::factory()->draft()->create();
        $question = Question::factory()->create([
            'event_id' => $eventE->id,
        ]);

        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->post('/api/v1/events/'.$eventE->id.'/publish');
        $this->unauthenticated($response);
    }

    public function testAdminEventHosts()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userA, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->get('/api/v1/events/'.$eventE->id.'/hosts');
        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->getUserJsonStructure()]]);
    }

    public function testNonAdminEventHosts()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userD, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->get('/api/v1/events/'.$eventE->id.'/hosts');
        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->getUserJsonStructure()]]);
    }

    public function testGuestEventHosts()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userE, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->get('/api/v1/events/'.$eventE->id.'/hosts');
        $this->unauthenticated($response);
    }

    public function testNonAdminEventHostsNoAccess()
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $this->actingAs($userC, 'sanctum');

        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();

        $userB->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $userC->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $userD->eventsHosted()->sync([$eventE->id]);
        $response = $this->get('/api/v1/events/'.$eventE->id.'/hosts');
        $this->unauthenticated($response);
    }

    private function getEventJsonStructure()
    {
        return [
            'id',
            'name',
            'code',
            'ends_at',
            'is_draft',
            'starts_at',
            'description',
            'allow_guests',
            'max_sessions',
        ];
    }

    private function getUserJsonStructure()
    {
        return [
            'id',
            'name',
            'name',
            'is_admin',
            'is_guest',
            'email_verified_at',
        ];
    }
}
