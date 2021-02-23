<?php

namespace App\Services;

use App\Contracts\AnalyticsService;
use Illuminate\Support\Facades\Http;

class ProductionAnalyticsService implements AnalyticsService
{
    public function RequestIndividualAnalysis(array $payload): array
    {
        return Http::post(
            config('analytics.endpoint').'/response', $payload
        )->json();
    }

    public function RequestAggregateAnalysis(array $payload): array
    {
        return Http::post(
            config('analytics.endpoint').'/aggregate', $payload
        )->json();
    }
}
