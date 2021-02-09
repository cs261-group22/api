<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionsController extends Controller
{
    /**
     * Retrieves the question with the specified ID.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        // Retrieves information about the question with the provided ID.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Creates a new question.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        // Given information about the question and the event to associate it with, creates a new question.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Updates the details of an question.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        // Updates the content of the question with the provided ID.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Deletes the specified question.
     *
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function destroy(int $id)
    {
        // Deletes the question with the provided ID.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }
}
