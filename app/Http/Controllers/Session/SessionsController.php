<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Event;
use App\Models\Session;
use App\Http\Resources\SessionResource;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    /**
     * Retrieves the session with the specified ID.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        $user = Auth::user();
        $session = Session::findOrFail($id);
        $event = $session->event;

        // Only admins and event hosts can view the sessions for the event
        if (! $user->is_admin || ! $event->hostedByUser($user)) {
            return response('You are not authorized to view this session', 403);
        }
        // Retrieves information about the specified session.
        // Accepts requests from the user that owns the session.
        return new SessionResource($session);
    }
}
