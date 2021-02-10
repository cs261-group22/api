<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\HigherOrderTapProxy;
use App\Models\Question;
use App\Models\Event;
use App\Http\Resources\QuestionResource;
use Illuminate\Support\Facades\Auth;

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
    public function store(Request $request)
    {
        // $user = Auth::user();
        $this->validateQuestion($request);
        $question = $this->populateQuestion(new Question(), $request);

        $event = Event::findOrFail($question->event_id);

        // The user can only update events that they manage
        if (! $event->hostedByUser(Auth::user())) {
            return response('You must host this event to add questions to it', 403);
        }

        $question->save();

        //Given information about the question and the event to associate it with, creates a new question.
        // Accepts requests from the event hosts, or administrators.
        return new QuestionResource($question);
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


    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateQuestion(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'event_id' => 'required|exists:events,id',
            'type' => 'required|in:free_text,multiple_choice',
            'prompt' => 'required|string',
            'min_responses' => 'nullable|integer',
            'max_responses' => 'nullable|integer|gte:min_responses',
            'order' => 'required|integer',
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
            $question->order = $request->input('order');
            $question->prompt = $request->input('prompt');
            $question->event_id = $request->input('event_id');
            $question->min_responses = $request->input('min_responses');
            $question->max_responses = $request->input('max_responses');
        });
    }
}
