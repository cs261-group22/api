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
        if (!$user->is_admin || !$event->hostedByUser($user)) {
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
        Event::findOrFail($id);

        $session = new Session();

        $session->started_at = now();
        $session->event_id = $id;
        $session->user_id = Auth::user()->id;
        $session->save();

        // Given a question and answer, creates and associates a new response with the provided session.
        // Accepts requests from the user that owns the session.
        return new SessionResource($session);
    }
}
