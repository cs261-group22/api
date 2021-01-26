<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamUsersController extends Controller
{
    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $team = Team::findOrFail($id);

        // Only admins and team leaders can modify users in a team
        if (! $user->is_admin || ! $team->managedByUser($user)) {
            return response('You are not authorized to modify the users in this team', 403);
        }

        $this->validate($request, [
            'users' => 'present|array',
            'users.*.id' => 'required|integer|exists:users,id',
            'users.*.is_leader' => 'nullable|boolean',
        ]);

        $updatedUsers = [];
        foreach ($request->input('users') as $requestUser) {
            $updatedUsers[$requestUser['id']] = ['is_leader' => $requestUser['is_leader'] ?? false];
        }

        // Overwrite the users in the team with the provided array
        $team->users()->sync($updatedUsers);

        return response()->noContent();
    }
}
