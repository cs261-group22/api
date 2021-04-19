<?php

namespace App\Services;

use App\Contracts\AnalyticsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductionAnalyticsService implements AnalyticsService
{
    public function RequestIndividualAnalysis(array $payload): array
    {
        $failedRequestCount = 0;

        while (true) {
            try {
                $response = Http::post(
                    config('cs261.analytics.endpoint').'/analytics', $payload
                );

                return $response->json();
            } catch (\Exception $e) {
                $failedRequestCount++;
                Log::error('Detected failed request to analytics:');
                Log::error($e->getMessage());

                if ($failedRequestCount > 5) {
                    return [];
                }
            }
        }
    }
}
