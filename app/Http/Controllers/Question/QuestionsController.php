<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class QuestionsController extends Controller
{
    /**
     * Retrieves the question with the specified ID.
     *
     * @param int $id
     * @return QuestionResource|Application|ResponseFactory|Response
     */
    public function show(int $id)
    {
        $question = Question::findOrFail($id);

        // The user can only update events that they manage
        if (!$question->event->hostedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        return new QuestionResource($question);
    }

    /**
     * Creates a new question.
     *
     * @param Request $request
     * @return QuestionResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validateQuestion($request);
        $question = $this->populateQuestion(new Question(), $request);

        // The user can only update events that they manage
        if (!$question->event->hostedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $question->save();

        return new QuestionResource($question);
    }

    /**
     * Updates the details of an question.
     *
     * @param Request $request
     * @param int $id
     * @return QuestionResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $question = Question::findOrFail($id);

        // The user can only update events that they manage
        if (!$question->event->hostedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $this->validateQuestion($request);
        $question = $this->populateQuestion($question, $request);
        $question->save();

        return new QuestionResource($question);
    }

    /**
     * Move the specified question.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function move(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        // The user can only update events that they manage
        if (!$question->event->hostedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }
        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $request->input('direction') === 'down'
            ? $question->moveOrderDown()
            : $question->moveOrderUp();

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
        $question = Question::findOrFail($id);

        // The user can only update events that they manage
        if (!$question->event->hostedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $question->delete();

        return response()->noContent();
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateQuestion(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required|exists:events,id',
            'type' => 'nullable|in:free_text,multiple_choice',
            'prompt' => 'nullable|string',
            'min_responses' => 'nullable|integer',
            'max_responses' => 'nullable|integer|gte:min_responses',
        ]);
    }

    /**
     * Populates a question with data from the provided request.
     *
     * @param Question $question
     * @param Request $request
     * @return Question|mixed
     */
    protected function populateQuestion(Question $question, Request $request): Question
    {
        return tap($question, function (Question $question) use ($request) {
            $question->type = $request->input('type');
            $question->prompt = $request->input('prompt');
            $question->event_id = $request->input('event_id');
            $question->min_responses = $request->input('min_responses');
            $question->max_responses = $request->input('max_responses');
        });
    }
}
