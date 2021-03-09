<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class PasswordRestorationController extends Controller
{
    /**
     * Attempts to update the password of the requesting user.
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function reset(Request $request)
    {
        $this->validatePasswordRestoration($request);

        $email = Crypt::decrypt($request->input('email'));
        $timestamp = Crypt::decrypt($request->input('timestamp'));

        // Retrieve the user associated with the provided email from the database
        $user = User::where('email', $email)->firstOrFail();

        if (! $user->passwordResetTokenValid($timestamp)) {
            return response()->json(['message' => 'The provided password reset token has expired'], 401);
        }

        $user->update(['password' => $request->input('password')]);

        return response()->noContent();
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validatePasswordRestoration(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'timestamp' => 'required|string',
            'password' => 'required|string|same:password_confirmation',
            'password_confirmation' => 'required|string|same:password',
        ]);
    }
}
