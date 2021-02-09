<?php

namespace App\Http\Controllers\User;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Retrieve all users.
     *
     * @param Request $request
     * @return Application|ResponseFactory|AnonymousResourceCollection|Response
     */
    public function index(Request $request)
    {
        // Only admins can view all users in the system
        if (! Auth::user()->is_admin) {
            return response('Only admin users can retrieve all users in the system', 403);
        }

        // Todo: paginate results to prevent loading all users at once
        $users = User::applyFilters($request->all())->get();

        return UserResource::collection($users);
    }

    /**
     * Retrieves the profile of the currently authenticated user.
     *
     * @return UserResource|Response
     */
    public function me()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->noContent();
        }

        return new UserResource($user->load('teams'));
    }

    /**
     * Creates a new user in the system.
     *
     * @param Request $request
     * @return UserResource|Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'name' => 'nullable|string',
            'is_admin' => 'present|boolean',
            'email' => 'required|string',
        ]);

        // The user must lead at least one team to be able to create new users
        if (! ($user->is_team_leader || $user->is_admin)) {
            return response('Only team leaders and admins can create new accounts', 403);
        }

        // Only admin users can add new admins
        if ($request->input('is_admin') && ! $user->is_admin) {
            return response('Only admin users can create new admin accounts', 403);
        }

        // Create the new user
        $newUser = User::updateOrCreate(
            ['email' => $request->input('email')],
            [
                'name' => $request->input('name'),
                'is_admin' => $request->input('is_admin'),
            ]
        );

        // Ensure verification emails are sent
        if (! $newUser->email_verified) {
            event(new UserRegistered($newUser->refresh()));
        }

        return new UserResource($newUser);
    }

    /**
     * Updates the details of the provided user.
     *
     * @param Request $request
     * @param int $id
     * @return UserResource
     */
    public function update(Request $request, int $id): UserResource
    {
        // Only admins can update the details of other users
        if (! Auth::user()->is_admin) {
            return response('Only admin update the details of other users', 403);
        }

        $user = User::findOrFail($id);

        $user->update($request->validate([
            'name' => 'nullable|string',
            'is_admin' => 'present|boolean',
        ]));

        return new UserResource($user);
    }
}
