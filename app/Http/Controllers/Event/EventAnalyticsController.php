<?php

namespace App\Http\Controllers\Event;

use App\Contracts\AnalyticsService;
use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EventAnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Retrieves the sessions for the specified event.
     *
     * @param Request $request
     * @param int $id
     * @return array|Application|ResponseFactory|Response
     */
    public function index(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        // Only event hosts can view the analytics for the event
        if (!$event->hostedByUser($user)) {
            return response('You are not authorized to view the analytics for the specified event', 403);
        }

        $corpus = $event->responses()
            ->select('value')
            ->whereNotNull('value')
            ->get()
            ->reduce(fn ($carry, $item) => $carry.('. ').$item->value, '');

        return $this->analyticsService->RequestIndividualAnalysis([
            'corpus' => substr($corpus, 2),
        ]);
    }
}
