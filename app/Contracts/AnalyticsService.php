<?php

namespace App\Contracts;

interface AnalyticsService
{
    /**
     * Requests an analysis for an individual user response.
     *
     * @param array $payload The response data.
     * @return array The JSON data returned by analytics.
     */
    public function RequestIndividualAnalysis(array $payload): array;
}
