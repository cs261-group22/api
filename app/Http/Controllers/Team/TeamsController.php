<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Validation\ValidationException;

class TeamsController extends Controller
{
    /**
     * Retrieves the teams managed by the requesting user.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::user();

        if ($user->is_guest) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }
        // Retrieve all teams that the user manages
        $teams = Team::whereManagedByUser($user)->get();

        return TeamResource::collection($teams);
    }

    /**
     * Retrieves the team with the specified ID.
     *
     * @param int $id
     * @return TeamResource|Application|ResponseFactory|Response
     */
    public function show(int $id)
    {
        $team = Team::with('users')->findOrFail($id);

        if (!$team->managedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        return new TeamResource($team);
    }

    /**
     * Creates a new team.
     *
     * @param Request $request
     * @return TeamResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // Only admin users can create new teams
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $this->validateTeam($request);

        $team = $this->populateTeam(new Team(), $request);
        $team->save();

        return new TeamResource($team);
    }

    /**
     * Updates the details of a team managed by the requesting user.
     *
     * @param int $id
     * @param Request $request
     * @return TeamResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function update(int $id, Request $request)
    {
        $team = Team::findOrFail($id);

        // The user can only update teams that they manage
        if (!$team->managedByUser(Auth::user())) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $this->validateTeam($request);

        $team = $this->populateTeam($team, $request);
        $team->save();

        return new TeamResource($team);
    }

    /**
     * Deletes a team managed by the requesting user.
     *
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function destroy(int $id)
    {
        $team = Team::findOrFail($id);

        // Only admin users can deletea  team
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }

        $team->delete();

        return response()->noContent();
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateTeam(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'department' => 'nullable|string',
        ]);
    }

    /**
     * Populates a team with data from the provided request.
     *
     * @param Team $team
     * @param Request $request
     * @return Team|HigherOrderTapProxy|mixed
     */
    protected function populateTeam(Team $team, Request $request)
    {
        return tap($team, function (Team $team) use ($request) {
            $team->name = $request->input('name');
            $team->department = $request->input('department');
        });
    }
}
