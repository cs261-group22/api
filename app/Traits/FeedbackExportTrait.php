<?php

namespace App\Traits;

use App\Models\Question;
use App\Models\Response;
use App\Models\Session;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait FeedbackExportTrait
{
    /**
     * Get styles for the worksheet.
     *
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Make the heading row bold
        $sheet->getStyle('1')->getFont()->setBold(true);

        // Add a border below the top row
        $sheet->getStyle('1')->getBorders()->getBottom()->setBorderStyle(true);
    }

    /**
     * Builds a serialized row for the provided session/response.
     *
     * @param Session $session
     * @param Response $response
     * @param bool $omitSessionData
     * @param bool $omitAnalyticsData
     * @return array
     */
    public function buildResponseRow(Session $session, Response $response, bool $omitSessionData = false, bool $omitAnalyticsData = false): array
    {
        $responseData = $this->serializeResponseData($response);

        $sessionData = $omitSessionData
            ? array_fill(0, 5, '')
            : $this->serializeSessionData($session);

        $analyticsData = $omitAnalyticsData
            ? array_fill(0, 6, '')
            : $this->serializeAnalyticsData($response);

        return array_merge($sessionData, $responseData, $analyticsData);
    }

    /**
     * Serializes metadata from the provided session.
     *
     * @param Session $session
     * @return array
     */
    public function serializeSessionData(Session $session): array
    {
        return [
            $session->user->id,
            $session->started_at,
            $session->user->name ?? 'Guest',
            $session->user->email ?? 'N/A',
            $session->mood.'%',
        ];
    }

    /**
     * Serializes question/answer data from the provided response.
     *
     * @param Response $response
     * @return array
     */
    public function serializeResponseData(Response $response)
    {
        return [
            $response->question->order,
            $response->question->type,
            $response->question->prompt,
            $response->value ?: $response->answer->value,
        ];
    }

    /**
     * Serializes analytics data from the provided response.
     *
     * @param Response $response
     * @return array
     */
    public function serializeAnalyticsData(Response $response): array
    {
        // Format mood...
        $score = $response->sentiment['sentiment']['score'] ?? 0;
        $mood = round(
            ($score > 0 ? 0.5 + $score / 2 : (1 - abs($score)) / 2) * 100
        );

        // Format urgency...
        $urgency = round(
            ($response->sentiment['urgency'] ?? 0) * 100
        );

        // Format subjects...
        $subjects = implode(
            ', ', array_keys($response->sentiment['subjects'] ?? [])
        );

        // Format entities...
        $entities = implode(
            ', ', $response->sentiment['entities'] ?? []
        );

        // Format word pairs...
        $wordPairs = implode(
            ', ', $response->sentiment['word_pairs'] ?? []
        );

        // Format frequent words...
        $frequentWords = implode(
            ', ', array_keys($response->sentiment['frequent_words'] ?? [])
        );

        return [
            $response->value ? ($mood ? $mood.'%' : '0%') : '',
            $response->value ? ($urgency ? $urgency.'%' : '0%') : '',
            $response->value ? ($subjects ?: 'None identified') : '',
            $response->value ? ($entities ?: 'None identified') : '',
            $response->value ? ($wordPairs ?: 'None identified') : '',
            $response->value ? ($frequentWords ?: 'None identified') : '',
        ];
    }

    /**
     * Removes repeated question data from the provided rows.
     *
     * @param array $rows
     * @return array
     */
    public function filterRepeatedQuestionData(array $rows): array
    {
        foreach ($rows as $index => $response) {
            // Ignore if this is the first response
            if ($index === 0) {
                continue;
            }

            // Ignore if the current or previous response is free text
            if (in_array(Question::TYPE_FREE_TEXT, [$response[6], $rows[$index - 1][6]])) {
                continue;
            }

            // Ignore if this is a different question to the previous
            if ($rows[$index - 1][5] !== '' && $response[5] !== $rows[$index - 1][5]) {
                continue;
            }

            // Otherwise, remove the duplicate question id, type and prompt for this response
            $rows[$index][5] = '';
            $rows[$index][6] = '';
            $rows[$index][7] = '';
        }

        return $rows;
    }
}
