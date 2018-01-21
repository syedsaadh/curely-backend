<?php

namespace App\Http\Controllers\Auth;

use App\Models\Response;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
class JWTLoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $response = new Response();
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            $user = User::where('email', '=', $request->input('email'))->first();
            if (!$user) {
                return $response->getNotFound('Email Not Found');
            }
            if (! $token = JWTAuth::attempt($credentials)) {
                return $response->getInvalidCredentials();
            }
        } catch (JWTException $e) {
            // something went wrong
            return $response->getUnknownError('Something Went Wrong!');
        }

        // if no errors are encountered we can return a JWT
        return $response->getSuccessResponse('Success!', compact('token'));
    }
}
