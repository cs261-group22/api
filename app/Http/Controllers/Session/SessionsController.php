<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SessionsController extends Controller
{
    /**
     * Retrieves the session with the specified ID.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        // Retrieves information about the specified session.
        // Accepts requests from the user that owns the session.
        return response()->noContent();
    }
}
