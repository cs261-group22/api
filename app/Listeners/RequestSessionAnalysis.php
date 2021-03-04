<?php

namespace App\Listeners;

use App\Contracts\AnalyticsService;

class RequestSessionAnalysis
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $session = $event->session;

        // Request an analysis for each free text response in the session
        foreach ($session->responses()->toFreeTextQuestions()->get() as $response) {
            $analysis = $this->analyticsService->RequestIndividualAnalysis([
                'response' => $response->value
            ]);

            $response->update([
                'sentiment' => $analysis
            ]);
        }
    }
}
