<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Event;
use App\Models\User;
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
        if (! ($user->is_admin || $event->hostedByUser($user))) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        return UserResource::collection($event->hosts);
    }

    /**
     * Updates the hosts for the provided event.
     *
     * @param Request $request
     * @param int $id
     * @return Application|ResponseFactory|AnonymousResourceCollection|Response
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        // Only admins and event hosts can modify hosts for an event
        if (! ($user->is_admin || $event->hostedByUser($user))) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $this->validate($request, [
            'hosts' => 'present|array',
            'hosts.*' => 'required|email',
        ]);

        $users = User::whereIn('email', $request->input('hosts'))->get();

        if ($users->count() !== count($request->input('hosts'))) {
            return response()->json(['message' => 'One or more of the provided users do not exist in the system'], 403);
        }

        // Overwrite the hosts for the event from the provided array
        $event->hosts()->sync(
            $users->pluck('id')
        );

        return UserResource::collection($users);
    }
}
