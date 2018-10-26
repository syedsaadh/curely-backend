<?php

namespace App\Http\Controllers\Auth;

use App\Models\Response;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
    public function changePassword(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'newPassword' => 'required'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $credentials = $request->only('password');
        $credentials['email'] = $request->user()->email;
        $newPassword = Hash::make($request->get('newPassword'));

        try {
            $user = User::where('email', '=',$request->user()->email)->first();
            if (!$user) {
                return $response->getNotFound('Email Not Found');
            }
            if (!$token = JWTAuth::attempt($credentials)) {
                return $response->getBadRequestError('Invalid Old Password!');
            }
            $user->password = $newPassword;

            $user->save();

        } catch (JWTException $e) {
            // something went wrong
            return $response->getUnknownError('Something Went Wrong!');
        }

        return $response->getSuccessResponse('Success!', compact('token'));
    }
}
