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
                Log::info('attempting');
                $response = Http::post(
                    config('cs261.analytics.endpoint').'/analytics', $payload
                );

                Log::info('got response');

                return $response->json();
            } catch (\Exception $e) {
                $failedRequestCount++;
                Log::warn('Detected failed request to analytics...');

                if ($failedRequestCount > 5) {
                    return [];
                }
            }
        }
    }
}
