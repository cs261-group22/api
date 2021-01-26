<?php

namespace App\Http\Controllers\User;

use App\Events\PasswordRecoveryRequested;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PasswordRecoveryController extends Controller
{
    /**
     * Sends a password recover email for the requesting user.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function recover(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        // Dispatch a password recovery event, which will send a verification email
        event(
            new PasswordRecoveryRequested($request->input('email'))
        );

        return response()->noContent();
    }
}
