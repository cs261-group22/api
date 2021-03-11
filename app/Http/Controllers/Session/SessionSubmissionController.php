<?php

namespace App\Http\Controllers\Session;

use App\Events\SessionSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SessionSubmissionController extends Controller
{
    /**
     * Marks the specified session as submitted.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    public function store(Request $request, int $id)
    {
        $session = Session::findOrFail($id);

        $this->validate($request, [
            'mood' => 'required|integer|min:1|max:100',
        ]);

        $groupedResponses = $session->responses()
            ->with('question', 'answer')
            ->get()
            ->groupBy('question_id');

        // Ensure the number of responses for each multiple choice question is within the accepted range
        if (! $groupedResponses->contains(function ($responses) {
            $question = $responses->first()->question;

            // Skip validation if it isn't multiple choice
            if ($question->type === Question::TYPE_FREE_TEXT) {
                return true;
            }

            $minResponses = $question->min_responses ?? 0;
            $maxResponses = $question->max_responses ?? PHP_INT_MAX;

            return (count($responses) >= $minResponses) && (count($responses) <= $maxResponses);
        })) {
            return response()->json([
                'error' => 'A number of responses given is outside the bounds of the question',
            ], 422);
        }

        $session->is_submitted = true;
        $session->mood = (int) $request->input('mood');

        $session->save();

        // Trigger analysis of responses in the session
        event(new SessionSubmitted($session));

        return response()->noContent();
    }
}
