<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Http\Response;
use App\Http\Resources\ResponseResource;
use App\Models\Session;
use App\Models\Question;
use App\Models\Response;
use PhpParser\Node\Expr\Cast\Array_;

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
    public function update(Request $request, int $id)
    {
        $session = Session::findOrFail($id);

        // Given a question and answer, creates and associates a new response with the provided session.
        // Accepts requests from the user that owns the session.
        $this->validateResponses($request);
        $collections = collect([]);

        $responses = $request['responses'];

        $questions = Question::with('answers')
            ->whereIn('id', array_map(fn ($response) => $response['question_id'], $responses))
            ->get()
            ->keyBy('id');


        foreach ($responses as $response) {
            $question = $questions->get($response['question_id']);
            $collections = $collections->concat([$response["question_id"]]);

            if (isset($response['answer_id']) && $question->type === Question::TYPE_FREE_TEXT) {
                return response()->json([
                    'error' => 'A multiple choice answer cannot be provided for a free text question'], 422);
            }

            if (isset($response['value']) && $question->type === Question::TYPE_MULTIPLE_CHOICE) {
                return response()->json([
                    'error' => 'A text response cannot be provided for a multiple choice question'], 422);
            }

            if (isset($response['answer_id']) && !$question->answers->firstWhere('id', $response['answer_id'])) {
                return response()->json([
                    'error' => 'The provided answer does not belong to the provided question']);
            }
        }

        // $counts->each(function ($item, $key)  use ($errors) {
        //     $question = Question::findOrFail($key);
        //     if ($question->min_responses > $item)
        //         $errors = $errors->concat(['min responses for question id ' . $key . ' is ' . $question->min_responses]);
        //     if ($question->max_responses < $item)
        //         $errors = $errors->concat(['max responses for question id ' . $key . ' is ' . $question->max_responses]);
        // });

        // Ensure the number of responses for each multiple choice question is within the accepted range
        if (!collect($responses)->groupBy('question_id')->contains(function ($responses, $questionId) use ($questions) {
            $question = $questions->get($questionId);

            // Skip validation if it isn't multiple choice
            if ($question->type === Question::TYPE_FREE_TEXT) {
                return null;
            }

            $minResponses = $question->min_responses ?? 0;
            $maxResponses = $question->max_responses ?? PHP_INT_MAX;
            return (count($responses) >= $minResponses) && (count($responses) <= $maxResponses);
        }))
            return response()->json([
                'error' => 'A number of responses given is outside the bounds of the question'
            ], 400);


        // Remove all existing responses in the session
        $session->responses()->delete();

        $data = array_map(fn ($response) => array_merge($response, [
            'session_id' => $session->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]),$responses);
        // Create new responses from the data provided in the body
    //    return $data;
        Response::insert($data);

        return $this->index($id);
    }


    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateResponses(Request $request)
    {
        $this->validate($request, [
            'responses' => 'present|array',
            'responses.*.value' => 'required_without:responses.*.answer_id|string',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.answer_id' => 'required_without:responses.*.value|exists:answers,id',
        ]);
    }


    /**
     * Populates an response with data from the provided request.
     *
     * @param Response $response
     * @param Request $request
     * @return Response
     */
    protected function populateResponse(Response $response, array $request, int $id): Response
    {

        return tap($response, function (Response $response) use ($request, $id) {
            $response->session_id = $id;
            $response->question_id = $request["question_id"];
            if ($request["type"] === "free_text")
                $response->value = $request["value"];
            else
                $response->answer_id = $request["value"];
        });
    }
}
