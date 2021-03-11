<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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

        // Retrieve all submitted sessions for the event
        $sessions = $event->sessions()
            ->with('user', 'responses', 'responses.answer')
            ->where('is_submitted', true)
            ->get();

        return SessionResource::collection($sessions);
    }

    /**
     * Creates a new session for the requesting user.
     *
     * @param Request $request
     * @param int $id
     * @return SessionResource|JsonResponse
     */
    public function store(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        $existingSession = $user->sessions()
            ->where(['event_id' => $id, 'is_submitted' => false])
            ->first();

        // If the user has an unsubmitted session for the event, do not create a new one
        if ($existingSession) {
            return new SessionResource($existingSession);
        }

        $existingSessionCount = $user->sessions()
            ->where(['event_id' => $id])
            ->count();

        // The number of sessions must not exceed the limit for the event
        if ($existingSessionCount >= ($event->max_sessions ?: PHP_INT_MAX)) {
            return response()->json(['message' => 'You have reached the session limit for that event'], 422);
        }

        $session = new Session();

        $session->started_at = now();
        $session->event_id = $id;
        $session->user_id = $user->id;
        $session->save();

        return new SessionResource($session);
    }
}
