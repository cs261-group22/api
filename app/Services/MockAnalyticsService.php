<?php

namespace App\Services;

use App\Contracts\AnalyticsService;

class MockAnalyticsService implements AnalyticsService
{
    public function RequestIndividualAnalysis(array $payload): array
    {
        $words = ['great', 'fantastic', 'helpful', 'terrible', 'bad', 'complaint'];
        shuffle($words);

        return [
            'subjects' => ['Ryan'],
            'entities' => [['Ryan', 'PERSON']],
            'important_words' => array_slice($words, 0, 2),
            'urgency' => round(mt_rand() / mt_getrandmax(), 2),
            'sentiment' => [
                'score' => [1, -1][array_rand([1, -1], 1)] * round(mt_rand() / mt_getrandmax(), 2),
                'magnitude' => round(mt_rand() / mt_getrandmax(), 2)
            ],
            'word_pairs' => [
                ['Bad', 'experience'],
                ['terrible', 'speaker']
            ]
        ];
    }

    public function RequestAggregateAnalysis(array $payload): array
    {
        return [];
    }
}
