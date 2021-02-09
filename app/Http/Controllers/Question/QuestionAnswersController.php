<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionAnswersController extends Controller
{
    /**
     * Retrieves the answers for the question with the specified ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function index(Request $request, int $id): Response
    {
        // Retrieves a list of answers for the question with the provided ID.
        return response()->noContent();
    }
}
