<?php

namespace App\Http\Controllers\Answer;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AnswersController extends Controller
{
    /**
     * Retrieves the answer with the specified ID.
     *
     * @param int $id
     * @return AnswerResource|Response
     */
    public function show(int $id)
    {
        $answer = Answer::findOrFail($id);
        $event = $answer->question->event;

        // The user can only show answers that they manage
        if (! $event->hostedByUser(Auth::user())) {
            return response('You must host this event to get the answers of questions from it', 403);
        }

        return new AnswerResource($answer);
    }

    /**
     * Creates a new answer.
     *
     * @param Request $request
     * @return AnswerResource|Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validateAnswer($request);

        $answer = $this->populateAnswer(new Answer(), $request);
        $event = $answer->question->event;

        // The user can only update events that they manage
        if (! $event->hostedByUser(Auth::user())) {
            return response('You must host this event to add answers a question from it', 403);
        }

        $answer->save();

        return new AnswerResource($answer);
    }

    /**
     * Updates the details of an answer.
     *
     * @param Request $request
     * @param int $id
     * @return AnswerResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $answer = Answer::findOrFail($id);
        $event = $answer->question->event;

        // The user can only update answers that they manage
        if (! $event->hostedByUser(Auth::user())) {
            return response('You must host this event to update the answers of questions from it', 403);
        }

        $this->validateAnswer($request);
        $answer = $this->populateAnswer($answer, $request);
        $answer->save();

        return new AnswerResource($answer);
    }

    /**
     * Deletes the specified answer.
     *
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function destroy(int $id)
    {
        $answer = Answer::findOrFail($id);
        $event = $answer->question->event;

        // The user can only show answers that they manage
        if (! $event->hostedByUser(Auth::user())) {
            return response('You must host this event to update the answers of questions from it', 403);
        }

        $answer->delete();

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

    /**
     * Populates an answer with data from the provided request.
     *
     * @param Answer $answer
     * @param Request $request
     * @return Answer|mixed
     */
    protected function populateAnswer(Answer $answer, Request $request): Answer
    {
        return tap($answer, function (Answer $answer) use ($request) {
            $answer->value = $request->input('value');
            $answer->order = $request->input('order');
            $answer->question_id = $request->input('question_id');
        });
    }
}
