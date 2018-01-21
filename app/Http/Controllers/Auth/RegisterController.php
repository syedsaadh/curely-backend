<?php

namespace App\Http\Controllers\Auth;

use App\Models\Response;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{

    public function index(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (User::where('email', $request->input('email'))->exists()) {
            return $response->getAlreadyPresent();
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        try {
            $user->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Registering User!');
        }
        return $response->getSuccessResponse('Registered User Successfully!');
    }
}
