<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionResponsesController extends Controller
{
    /**
     * Retrieves responses for the session with the provided ID.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        // Retrieves a list of responses recorded for the session with the provided ID.
        // Accepts requests from the user that own the session, users that host the event associated with the session, or administrators.
        return response()->noContent();
    }

    /**
     * Creates a new response for the specified session.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        // Given a question and answer, creates and associates a new response with the provided session.
        // Accepts requests from the user that owns the session.
        return response()->noContent();
    }
}
