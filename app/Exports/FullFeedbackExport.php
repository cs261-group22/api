<?php

namespace App\Exports;

use App\Models\Event;
use App\Traits\FeedbackExportTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class FullFeedbackExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    use FeedbackExportTrait;

    public Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Map a session to a list of rows to output to the spreadsheet.
     *
     * @param mixed $session
     * @return array
     */
    public function map($session): array
    {
        // If the question has no responses, don't include it in the output
        if ($session->responses->count() === 0) {
            return [];
        }

        // The first row should contain all information
        $rows = [
            $this->buildResponseRow($session, $session->responses->first()),
        ];

        // Subsequent rows should not contain session metadata
        foreach ($session->responses->skip(1) as $response) {
            array_push(
                $rows, $this->buildResponseRow($session, $response, true)
            );
        }

        // Filter duplicate multiple choice prompts from the results and add a separator row
        $rows = $this->filterRepeatedQuestionData($rows);
        array_push($rows, []);

        return $rows;
    }

    /**
     * Builds the query used to fetch session data.
     *
     * @return HasMany|Builder
     */
    public function query()
    {
        return $this->event
            ->sessions()
            ->with(['user', 'responses', 'responses.question', 'responses.answer']);
    }

    /**
     * Returns the headings that should be shown in the spreadsheet.
     *
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'User ID',
            'Submitted at',
            'Name',
            'Email',
            'Explicit Mood',
            'Question ID',
            'Question Type',
            'Question Prompt',
            'Response(s)',
            'Implicit Mood',
            'Urgency',
            'Subjects',
            'Entities',
            'Word Pairs',
            'Important Words',
        ];
    }
}
