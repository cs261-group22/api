<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Event;
use App\Models\User;
use \Illuminate\Testing\TestResponse;

class EventTest extends TestCase
{
    use WithFaker;

    private function unauthenticated(TestResponse $response)
    {
        $response->assertStatus(401)->assertExactJson([
            'message' => 'Unauthenticated',
        ]);
    }

    /**
     * A test for admins to access a list of all events
     *
     * @return void
     */
    public function testAdminGetEvents()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user, 'sanctum');
        Event::factory()->create();
        $response = $this->get('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure([
            'data' =>
            [
                '*' =>
                [
                    'id',
                    'name',
                    'code',
                    'ends_at',
                    'is_draft',
                    'starts_at',
                    'description',
                    'allow_guests',
                    'max_sessions'
                ]
            ]
        ]);
    }

    /**
     * A test for non admins to access a list of all events
     *
     * @return void
     */
    public function testNonAdminGetEvents()
    {
        $user = User::factory()->non_admin()->create();
        $this->actingAs($user, 'sanctum');
        Event::factory()->create();
        $response = $this->get('/api/v1/events');
        $this->unauthenticated($response);
    }

    /**
     * A test for guest to access a list of all events
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
     * A test for admins to access a list of all events
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
     * A test for non admins to access a list of all events
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
     * A test for guest to access a list of all events
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
            'data' =>
            [
                'id',
                'name',
                'code',
                'ends_at',
                'is_draft',
                'starts_at',
                'description',
                'allow_guests',
                'max_sessions'

            ]
        ])->assertJsonPath('data.code', $code);
    }

    /**
     * A test for admins to access a list of all events
     *
     * @return void
     */
    public function testAdminAddEvent()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user, 'sanctum');
        $this->AddEventTest();
    }

    /**
     * A test for non admins to access a list of all events
     *
     * @return void
     */
    public function testNonAdminAddEvent()
    {
        $user = User::factory()->non_admin()->create();
        $this->actingAs($user, 'sanctum');
        $this->AddEventTest();
    }

    /**
     * A test for guest to access a list of all events
     *
     * @return void
     */
    public function testGuestAddEvent()
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user, 'sanctum');
        $this->AddEventTest();
    }

    private function AddEventTest()
    {
        $name = $this->faker->catchPhrase();
        $guests = $this->faker->boolean();
        $draft = $this->faker->boolean();
        // Event::factory()->create();
        $response = $this->postJson('/api/v1/events', ['name' => $name, 'allow_guests' => $guests, 'is_draft' => $draft]);
        // $response->assertStatus(200)->assertJsonStructure([
        //     'data' =>
        //     [
        //         '*' =>
        //         [
        //             'id',
        //             'name',
        //             'code',
        //             'ends_at',
        //             'is_draft',
        //             'starts_at',
        //             'description',
        //             'allow_guests',
        //             'max_sessions'
        //         ]
        //     ]
        // ]);
    }

    /**
     * A test for admins to access a list of all events
     *
     * @return void
     */
    public function testAdminNEWEVENTTESTTYPE()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user, 'sanctum');
        $this->NEWEVENTTESTTYPETest();
    }

    /**
     * A test for non admins to access a list of all events
     *
     * @return void
     */
    public function testNonAdminNEWEVENTTESTTYPE()
    {
        $user = User::factory()->non_admin()->create();
        $this->actingAs($user, 'sanctum');
        $this->NEWEVENTTESTTYPETest();
    }

    /**
     * A test for guest to access a list of all events
     *
     * @return void
     */
    public function testGuestNEWEVENTTESTTYPE()
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user, 'sanctum');
        $this->NEWEVENTTESTTYPETest();
    }

    private function NEWEVENTTESTTYPETest()
    {
        Event::factory()->create();
        $response = $this->get('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure([
            'data' =>
            [
                '*' =>
                [
                    'id',
                    'name',
                    'code',
                    'ends_at',
                    'is_draft',
                    'starts_at',
                    'description',
                    'allow_guests',
                    'max_sessions'
                ]
            ]
        ]);
    }
}
