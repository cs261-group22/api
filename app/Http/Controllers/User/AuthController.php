<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Logs the user in with the provided email and password.
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     * @throws ValidationException
     */
    public function loginEmployee(Request $request)
    {
        $this->validateLogin($request);

        // Retrieve the user associated with the provided email from the database
        $user = User::where('email', $request->input('email'))->firstOrFail();

        // The users email address must be verified to login
        if (! $user->email_verified) {
            return response('Please check your inbox for a verification link before logging in', 401);
        }

        // Attempt to log the user in with the provided details
        if (! $this->authenticateUser($request->input('email'), $request->input('password'))) {
            return response('Invalid account credentials provided', 401);
        }

        return response()->json([
            'token' => $user->createToken($user->email)->plainTextToken,
        ]);
    }

    /**
     * Logs the user in under a new guest account.
     *
     * @return JsonResponse
     */
    public function loginGuest(): JsonResponse
    {
        // Create a guest account with no name/email/password
        $user = User::create([
            'is_guest' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'token' => $user->createToken($user->id)->plainTextToken,
        ]);
    }

    /**
     * Log the current authenticated user out of the application.
     *
     * @return Application|ResponseFactory|Response
     */
    public function logout()
    {
        $user = Auth::user();

        if (! $user) {
            return response('No user is currently authenticated', 403);
        }

        $user->tokens()->delete();

        return response()->noContent();
    }

    /**
     * Determines if the provided email and password pair are valid.
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    protected function authenticateUser(string $email, string $password): bool
    {
        return Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * Validates the incoming request.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }
}
