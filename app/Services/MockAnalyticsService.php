<?php

namespace App\Services;

use App\Contracts\AnalyticsService;

class MockAnalyticsService implements AnalyticsService
{
    public function RequestIndividualAnalysis(array $payload): array
    {
        return [
            'keywords' => ['a', 'b'],
            'urgency' => mt_rand() / mt_getrandmax(),
            'sentiment' => mt_rand() / mt_getrandmax(),
        ];
    }

    public function RequestAggregateAnalysis(array $payload): array
    {
        return [];
    }
}
