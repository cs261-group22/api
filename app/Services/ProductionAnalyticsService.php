<?php

namespace App\Services;

use App\Contracts\AnalyticsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductionAnalyticsService implements AnalyticsService
{
    public function RequestIndividualAnalysis(array $payload): array
    {
        $response = Http::post(
            config('cs261.analytics.endpoint').'/analytics', $payload
        );

        return $response->json();
    }

    public function RequestAggregateAnalysis(array $payload): array
    {
        return Http::post(
            config('cs261.analytics.endpoint').'/aggregate', $payload
        )->json();
    }
}
