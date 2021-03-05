<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EventsController extends Controller
{
    /**
     * Retrieves the events managed by the requesting user.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::user();

        // Retrieve events that are managed by the requesting user
        $events = Event::whereHostedByUser($user)->get();

        return EventResource::collection($events);
    }

    /**
     * Retrieves the event with the provided ID.
     *
     * @param int $id
     * @return EventResource
     */
    public function show(int $id): EventResource
    {
        $event = Event::with('hosts', 'questions', 'questions.answers')
            ->where('id', $id)
            ->firstOrFail();

        return new EventResource($event);
    }

    /**
     * Retrieves the event with the provided code.
     *
     * @param string $code
     * @return EventResource
     */
    public function code(string $code): EventResource
    {
        $event = Event::with('questions', 'questions.answers')
            ->where('code', $code)
            ->firstOrFail();

        return new EventResource($event);
    }

    /**
     * Creates a new event.
     *
     * @param Request $request
     * @return EventResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $this->validateEvent($request);
        $event = $this->populateEvent(new Event(), $request);

        // Generate a unique code for the event and associate the host
        $event->code = Event::generateUniqueEventCode();
        $event->save();

        $event->hosts()->attach($user->id);

        return new EventResource($event);
    }

    /**
     * Updates the details of an event managed by the requesting user.
     *
     * @param int $id
     * @param Request $request
     * @return EventResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function update(int $id, Request $request)
    {
        $event = Event::findOrFail($id);

        // The user can only update events that they manage
        if (!$event->hostedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $this->validateEvent($request);

        $event = $this->populateEvent($event, $request);
        $event->save();

        return new EventResource($event);
    }

    /**
     * Publishes the specified draft event.
     *
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function publish(int $id)
    {
        $event = Event::findOrFail($id);

        // The user can only publish events that they manage
        if (!$event->hostedByUser(Auth::user())) {
            return response($status = 403)->json(['message' => 'Unauthenticated']);
        }

        // The event must be publishable
        if (!$event->is_publishable) {
            return response('This event cannot be published in it\'s current state', 422);
        }

        $event->is_draft = false;
        $event->save();

        return response()->noContent();
    }

    /**
     * Deletes an event managed by the requesting user.
     *
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function destroy(int $id)
    {
        $event = Event::findOrFail($id);

        // The user can only update events that they manage
        if (!$event->hostedByUser(Auth::user())) {
            return response($status = 403)->json(['message' => 'Unauthenticated']);
        }

        $event->delete();

        return response()->noContent();
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateEvent(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'ends_at' => 'nullable|date',
            'starts_at' => 'nullable|date',
            'is_draft' => 'present|boolean',
            'description' => 'nullable|string',
            'allow_guests' => 'present|boolean',
            'max_sessions' => 'nullable|integer',
        ]);
    }

    /**
     * Populates an event with data from the provided request.
     *
     * @param Event $event
     * @param Request $request
     * @return Event
     */
    protected function populateEvent(Event $event, Request $request): Event
    {
        return tap($event, function (Event $event) use ($request) {
            $event->name = $request->input('name');
            $event->ends_at = $request->input('ends_at');
            $event->is_draft = $request->input('is_draft');
            $event->starts_at = $request->input('starts_at');
            $event->description = $request->input('description');
            $event->max_sessions = $request->input('max_sessions');
            $event->allow_guests = $request->input('allow_guests');
        });
    }
}
