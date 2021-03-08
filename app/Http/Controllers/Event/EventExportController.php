<?php

namespace App\Http\Controllers\Event;

use App\Exports\FullFeedbackExport;
use App\Exports\LimitedFeedbackExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EventExportController extends Controller
{
    /**
     * Exports feedback for the specified event.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|BinaryFileResponse
     */
    public function export(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        // The user must host the event to export data for it...
        if (! $event->hostedByUser($user)) {
            return response()->json([
                'message' => 'You do not have permissions to export feedback for this event',
            ], 403);
        }

        // Adjust the scope of the export from the type parameter
        if ($request->input('type') === 'full') {
            $export = new FullFeedbackExport($event);
        } else {
            $export = new LimitedFeedbackExport($event);
        }

        // Adjust the download type based on the format parameter...
        if ($request->input('format') === 'xlsx') {
            return Excel::download($export, 'feedback.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        return Excel::download($export, 'feedback.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
