<?php

namespace App\Listeners;

use App\Contracts\AnalyticsService;
use App\Events\SessionAnalysisReceived;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestSessionAnalysis implements ShouldQueue
{
    protected AnalyticsService $analyticsService;

    /**
     * Create the event listener.
     *
     * @param AnalyticsService $analyticsService
     */
    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $session = $event->session;

        // Request an analysis for each free text response in the session
        foreach ($session->responses()->toFreeTextQuestions()->get() as $response) {
            $analysis = $this->analyticsService->RequestIndividualAnalysis([
                'corpus' => $response->value,
            ]);

            $response->update([
                'sentiment' => $analysis,
            ]);
        }

        // Load relationships required by the UI
        $session->load('user', 'responses');

        event(new SessionAnalysisReceived($session));
    }
}
