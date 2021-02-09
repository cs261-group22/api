<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EventHostsController extends Controller
{
    /**
     * Retrieves the hosts for the specified event.
     *
     * @param Request $request
     * @param int $id
     * @return Application|ResponseFactory|AnonymousResourceCollection|Response
     */
    public function index(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        // Only admins and event hosts can view the joined users
        if (! $user->is_admin || ! $event->hostedByUser($user)) {
            return response('You are not authorized to view the hosts for the specified event', 403);
        }

        return UserResource::collection($event->hosts);
    }

    /**
     * Updates the hosts for the provided team.
     *
     * @param Request $request
     * @param int $id
     * @return Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        // Only admins and team leaders can modify users in a team
        if (! $user->is_admin || ! $event->hostedByUser($user)) {
            return response('You are not authorized to modify the hosts for this event', 403);
        }

        $this->validate($request, [
            'hosts' => 'present|array',
            'hosts.*' => 'required|integer|exists:users,id',
        ]);

        // Overwrite the hosts for the event from the provided array
        $event->users()->sync(
            $request->input('hosts')
        );

        return response()->noContent();
    }
}
