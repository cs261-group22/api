<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use App\Http\Resources\ResponseResource;
use App\Models\Session;
use App\Models\Response;

class SessionResponsesController extends Controller
{
    /**
     * Retrieves responses for the session with the provided ID.
     *
     * @param int $id
     * @return Response
     */
    public function index(int $id)
    {
        $responses = Response::get()->where('session_id', '==', $id);
        // Retrieves a list of responses recorded for the session with the provided ID.
        // Accepts requests from the user that own the session, users that host the event associated with the session, or administrators.
        return ResponseResource::collection($responses);
    }

    /**
     * Creates a new response for the specified session.
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Given a question and answer, creates and associates a new response with the provided session.
        // Accepts requests from the user that owns the session.
        return response()->noContent();
    }
}
