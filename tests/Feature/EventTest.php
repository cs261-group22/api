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
        $response = $this->get('/api/v1/events/code/' . $code);
        // $response->dump();
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
        $response = $this->runShowEvent(0);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminShowEvent()
    {
        $response = $this->runShowEvent(3);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminShowEventNotOwned()
    {
        $response = $this->runShowEvent(2);
        $this->unauthenticated($response);
    }

    public function testGuestShowEvent()
    {
        $response = $this->runShowEvent(4);
        $this->unauthenticated($response);
    }

    private function runShowEvent($actingUser)
    {
        $event = $this->insertEvents($actingUser)['events'][4];

        return $this->get('/api/v1/events/' . $event->id);
    }

    public function testAdminUpdateEvent()
    {
        $response = $this->runUpdateEvent(0);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testNonAdminUpdateEvent()
    {
        $response = $this->runUpdateEvent(3);
        $response->assertStatus(200)->assertJsonStructure(['data' => $this->getEventJsonStructure()]);
    }

    public function testGuestUpdateEvent()
    {
        $response = $this->runUpdateEvent(4);
        $this->unauthenticated($response);
    }

    public function testNonAdminUpdateEventNoAccess()
    {
        $response = $this->runUpdateEvent(2);
        $this->unauthenticated($response);
    }

    private function runUpdateEvent($actingUser)
    {
        $event = $this->insertEvents($actingUser)['events'][4];

        return $this->putJson('/api/v1/events/' . $event->id, [
            'name' => 'name',
            'ends_at' => now(),
            'starts_at' => now(),
            'is_draft' => true,
            'description' => 'description',
            'allow_guests' => true,
            'max_sessions' => 4,
        ]);
    }

    public function testAdminDeleteEvent()
    {
        $response = $this->runDeleteEvent(0);
        $response->assertStatus(204);
    }

    public function testNonAdminDeleteEvent()
    {
        $response = $this->runDeleteEvent(3);
        $response->assertStatus(204);
    }

    public function testGuestDeleteEvent()
    {
        $response = $this->runDeleteEvent(4);
        $this->unauthenticated($response);
    }

    public function testNonAdminDeleteEventNoAccess()
    {
        $response = $this->runDeleteEvent(2);
        $this->unauthenticated($response);
    }

    private function runDeleteEvent($actingUser)
    {
        $event = $this->insertEvents($actingUser)['events'][4];

        return $this->delete('/api/v1/events/' . $event->id);
    }

    public function testAdminPublishEvent()
    {
        $response = $this->runPublishEvent(0);
        $response->assertStatus(204);
    }

    public function testNonAdminPublishEvent()
    {
        $response = $this->runPublishEvent(3);
        $response->assertStatus(204);
    }

    public function testGuestPublishEvent()
    {
        $response = $this->runPublishEvent(4);
        $this->unauthenticated($response);
    }

    public function testNonAdminPublishEventNoAccess()
    {
        $response = $this->runPublishEvent(2);
        $this->unauthenticated($response);
    }

    private function runPublishEvent($actingUser)
    {
        $users = $this->insertUsers($actingUser);
        $event = Event::factory()->draft()->create();
        Question::factory()->create([
            'event_id' => $event->id,
        ]);
        $users[3]->eventsHosted()->sync([$event->id]);

        return $this->post('/api/v1/events/' . $event->id . '/publish');
    }

    public function testAdminEventHosts()
    {
        $response = $this->runGetEventHosts(0);
        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->getUserJsonStructure()]]);
    }

    public function testNonAdminEventHosts()
    {
        $response = $this->runGetEventHosts(3);
        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->getUserJsonStructure()]]);
    }

    public function testGuestEventHosts()
    {
        $response = $this->runGetEventHosts(4);
        $this->unauthenticated($response);
    }

    public function testNonAdminEventHostsNoAccess()
    {
        $response = $this->runGetEventHosts(2);
        $this->unauthenticated($response);
    }

    private function runGetEventHosts($actingUser)
    {
        $event = $this->insertEvents($actingUser)['events'][4];

        return $this->get('/api/v1/events/' . $event->id . '/hosts');
    }

    public function testAdminEventHostsUpdate()
    {
        $this->runEventHostsUpdate(0, true);
    }

    public function testNonAdminEventHostsUpdate()
    {
        $this->runEventHostsUpdate(3, true);
    }

    public function testGuestEventHostsUpdate()
    {
        $this->runEventHostsUpdate(4, false);
    }

    public function testNonAdminEventHostsUpdateNoAccess()
    {
        $this->runEventHostsUpdate(2, false);
    }

    public function runEventHostsUpdate($actingUser, $hasAccess)
    {
        $data = $this->insertEvents($actingUser);
        $user = $data['users'][2];
        $event = $data['events'][4];
        $response = $this->patchJson('/api/v1/events/' . $event->id . '/hosts', ['hosts' => [$user->email]]);
        if ($hasAccess)
            $this->validHosts($response, collect([$user]));
        else
            $this->unauthenticated($response);
    }

    private function insertUsers($actingUser)
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();
        $userC = User::factory()->non_admin()->create();
        $userD = User::factory()->non_admin()->create();
        $userE = User::factory()->guest()->create();
        $users = [$userA, $userB, $userC, $userD, $userE];
        $this->actingAs($users[$actingUser], 'sanctum');

        return $users;
    }

    private function insertEvents($actingUser)
    {
        $users = $this->insertUsers($actingUser);
        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();
        $eventC = Event::factory()->create();
        $eventD = Event::factory()->create();
        $eventE = Event::factory()->create();
        $events = [$eventA, $eventB, $eventC, $eventD, $eventE];

        $users[1]->eventsHosted()->sync([$eventA->id, $eventB->id]);
        $users[2]->eventsHosted()->sync([$eventC->id, $eventD->id]);
        $users[3]->eventsHosted()->sync([$eventE->id]);

        return ['users' => $users, 'events' => $events];
    }

    private function validHosts($response, $users)
    {
        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->getUserJsonStructure()]]);

        $ids = collect($response['data'])->pluck('id');
        $users->each(function (User $user) use ($ids) {
            $this->assertContains($user->id, $ids);
        });
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
