<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Question;
use App\Models\Response;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class SessionResponsesController extends Controller
{
    /**
     * Retrieves responses for the session with the provided ID.
     *
     * @param int $id
     * @return AnonymousResourceCollection
     */
    public function index(int $id)
    {
        $responses = Response::with('answer', 'session', 'question')
            ->where('session_id', $id)
            ->get();

        return ResponseResource::collection($responses);
    }

    /**
     * Creates a new response for the specified session.
     *
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $session = Session::findOrFail($id);

        $this->validateResponses($request);

        $responses = $request['responses'];

        $questions = Question::with('answers')
            ->whereIn('id', array_map(fn ($response) => $response['question_id'], $responses))
            ->get()
            ->keyBy('id');

        foreach ($responses as $response) {
            $question = $questions->get($response['question_id']);

            if (isset($response['answer_id']) && isset($response['value'])) {
                return response()->json([
                    'error' => 'A multiple choice answer and a free text response cannot both be provided',
                ], 422);
            }

            if (isset($response['answer_id']) && $question->type === Question::TYPE_FREE_TEXT) {
                return response()->json([
                    'error' => 'A multiple choice answer cannot be provided for a free text question',
                ], 422);
            }

            if (isset($response['value']) && $question->type === Question::TYPE_MULTIPLE_CHOICE) {
                return response()->json([
                    'error' => 'A text response cannot be provided for a multiple choice question',
                ], 422);
            }

            if (isset($response['answer_id']) && ! $question->answers->firstWhere('id', $response['answer_id'])) {
                return response()->json([
                    'error' => 'The provided answer does not belong to the provided question',
                ], 422);
            }
        }

        // Remove all existing responses in the session
        $session->responses()->delete();

        $data = array_map(fn ($response) => array_merge([
            'session_id' => $session->id,
            'created_at' => now(),
            'updated_at' => now(),
            'value' => null,
            'answer_id' => null,
        ], $response), $responses);

        Response::insert($data);

        return response()->noContent();
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
            'responses.*.value' => 'nullable|string',
            'responses.*.answer_id' => 'nullable|exists:answers,id',
            'responses.*.question_id' => 'required|exists:questions,id',
        ]);
    }
}
