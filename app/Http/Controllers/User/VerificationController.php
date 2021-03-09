<?php

namespace App\Http\Controllers\User;

use App\Events\UserReferred;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    /**
     * Verifies and sets the password for a new user.
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function verify(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string',
            'timestamp' => 'required|string',
            'password' => 'required|string|same:password_confirmation',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $userId = Crypt::decryptString($request->input('id'));
        $userEmail = Crypt::decryptString($request->input('email'));
        $timestamp = Crypt::decryptString($request->input('timestamp'));

        // Find the user associated with the verification token
        $user = User::where(['id' => $userId, 'email' => $userEmail])->firstOrFail();

        // The activation token must not have expired
        if (! $user->activationTokenValid($timestamp)) {
            return response()->json(['message' => 'The activation link has expired'], 403);
        }

        // Only allow verification if the user is not verified already
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'That account has already been validated'], 403);
        }

        $user->markEmailAsVerified();

        // Hash and set the users password and name
        $user->update([
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->noContent();
    }

    /**
     * Resends the verification email for the requesting user.
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     * @throws ValidationException
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email_token' => 'required_without:email_address|string',
            'email_address' => 'required_without:email_token|string',
        ]);

        // The user may provide either an email token or an address
        $email = $request->input('email_token')
            ? Crypt::decryptString($request->input('email_token'))
            : $request->input('email_address');

        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'That account has already been validated'], 403);
        }

        event(new UserReferred($user));

        return response()->noContent();
    }
}
