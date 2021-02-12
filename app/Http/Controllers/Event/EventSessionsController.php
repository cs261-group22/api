<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Models\Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;

class EventSessionsController extends Controller
{
    /**
     * Retrieves the sessions for the specified event.
     *
     * @param Request $request
     * @param int $id
     * @return Application|ResponseFactory|AnonymousResourceCollection|Response
     */
    public function index(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        // Only admins and event hosts can view the sessions for the event
        if (! $user->is_admin || ! $event->hostedByUser($user)) {
            return response('You are not authorized to view the sessions for the specified event', 403);
        }

        return SessionResource::collection($event->sessions);
    }

    /**
     * Creates a new session for the requesting user.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function store(Request $request, int $id)
    {
        $this->validateSession($request);

        $event = Event::findOrFail($id);
        $session = $this->populateSession(new Session(), $request);


        $session->event_id = $id;

        $session->save();
        // Given a question and answer, creates and associates a new response with the provided session.
        // Accepts requests from the user that owns the session.
        return new SessionResource($session);
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateSession(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'started_at' => 'required|date',
        ]);
    }

    /**
     * Populates an session with data from the provided request.
     *
     * @param Session $session
     * @param Request $request
     * @return Session|mixed
     */
    protected function populateSession(Session $session, Request $request): Session
    {
        return tap($session, function (Session $session) use ($request) {
            $session->user_id = $request->input('user_id');
            $session->started_at = $request->input('started_at');
        });
    }
}
