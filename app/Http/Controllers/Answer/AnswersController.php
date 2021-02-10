<?php

namespace App\Http\Controllers\Answer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnswersController extends Controller
{
    /**
     * Retrieves the answer with the specified ID.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        // Retrieves information about the answer with the provided ID.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Creates a new answer.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        // Given information about the answer and the question to associate it with, creates a new answer.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Updates the details of an answer.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        // Updates the content of the answer with the provided ID.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Deletes the specified answer.
     *
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function destroy(int $id)
    {
        // Deletes the answer with the provided ID.
        // Accepts requests from the event hosts, or administrators.
        return response()->noContent();
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateAnswer(Request $request)
    {
        $this->validate($request, [
            'question_id' => 'required|exists:questions,id',
            'value' => 'required|string',
            'order' => 'required|integer',
        ]);
    }
}
