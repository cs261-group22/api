<?php

namespace App\Http\Controllers\User;

use App\Events\UserRegisteredVerificationRequested;
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
            'id' => 'required|integer',
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
            return response('The activation link has expired', 401);
        }

        // Only mark the email as verified if it is not already
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Hash and set the users password
        $user->update([
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
            return response('Your account has already been verified');
        }

        event(new UserRegisteredVerificationRequested($user));

        return response()->noContent();
    }
}
