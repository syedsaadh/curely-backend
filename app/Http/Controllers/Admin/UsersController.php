<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Role;
use App\User;
use function foo\func;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = User::with('roles')->get();
        foreach ($data as $user) {
            $temp = array_pluck($user->roles, 'name');
            unset($user['roles']);
            $user->role = array_first($temp);
        }
        return $response->getSuccessResponse('Success!', $data);
    }
    public function getDoctors() {
        $response = new Response();
        $data = User::whereHas('roles', function($q){
            $q->where('name', 'doctor');
        })->get();
        return $response->getSuccessResponse('Success!', $data);
    }
    public function getStaffs() {
        $response = new Response();
        $otherRole = User::whereHas('roles', function ($q){
            $q->whereNotIn('name', ['doctor', 'admin']);
        })->with('roles:name')->get()->toArray();
        foreach ($otherRole as $user) {
            $user->role_names = array_pluck($user->roles, 'name');
            unset($user['roles']);
        }
        return $response->getSuccessResponse('Success!', $otherRole);
    }
    public function getAdmins() {
        $response = new Response();
        $data = User::whereHas('roles', function($q){
            $q->where('name', 'admin');
        })->get();
        return $response->getSuccessResponse('Success!', array_dot($data));
    }
    public function createAndAssignRole(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'mobile' => 'required|string|max:10|min:10',
            'password' => 'required|string|min:6',
            'role' => 'required',
        ]);
        $role = Role::where('name', '=', $request->input('role'))->first();
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (User::where('email', $request->input('email'))->exists()) {
            return $response->getAlreadyPresent('Email Already Present');
        }
        if (!$role) {
            return $response->getNotFound('Role Not Found');
        }
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->password = Hash::make($request->input('password'));
        try {
            $user->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Registering User!');
        }
        $user->roles()->attach($role->id);
        return $response->getSuccessResponse('Registered User Successfully!');
    }
    public function editStaff(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'mobile' => 'required|string|max:10|min:10',
            'role' => 'required',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $role = Role::where('name', '=', $request->input('role'))->first();
        $user = User::find( $request->input('id'));
        if (!$user) {
            return $response->getNotFound('User Not Found');
        }
        if (!$role) {
            return $response->getNotFound('Role Not Found');
        }
        if($request->input('email') !== $user->email) {
            if (User::where('email', $request->input('email'))->exists()) {
                return $response->getAlreadyPresent('Email Already Present');
            }
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        try {
            $user->detachAllRoles();
            $user->roles()->attach($role->id);
            $user->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Registering User!');
        }
        return $response->getSuccessResponse('Edited User Successfully!');
    }
    public function assignRole(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $user = User::where('email', '=', $request->input('email'))->first();
        $role = Role::where('name', '=', $request->input('role'))->first();

        if (!$role) {
            return $response->getNotFound('Role Not Found');
        }
        if (!$user) {
            return $response->getNotFound('User Not Found');
        }
        if ($user->hasRole([$role->name])) {
            return $response->getAlreadyPresent('User is Already assigned to this Role');
        }
        $user->roles()->attach($role->id);
        return $response->getSuccessResponse('Assigned Role Successfully!');
    }
}
