<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Role;
use App\Models\StaffInformation;
use App\User;
use Carbon\Carbon;
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

    public function getDoctors()
    {
        $response = new Response();
        $data = User::whereHas('roles', function ($q) {
            $q->where('name', 'doctor');
        })->get();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function getStaffs()
    {
        $response = new Response();
        $otherRole = User::whereHas('roles', function ($q) {
            $q->whereNotIn('name', ['doctor', 'admin']);
        })->with('roles:name')->get()->toArray();
        foreach ($otherRole as $user) {
            $user->role_names = array_pluck($user->roles, 'name');
            unset($user['roles']);
        }
        return $response->getSuccessResponse('Success!', $otherRole);
    }

    public function getAdmins()
    {
        $response = new Response();
        $data = User::whereHas('roles', function ($q) {
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
        $user = User::find($request->input('id'));
        if (!$user) {
            return $response->getNotFound('User Not Found');
        }
        if (!$role) {
            return $response->getNotFound('Role Not Found');
        }
        if ($request->input('email') !== $user->email) {
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

    public function getProfile(Request $request)
    {
        $response = new Response();
        $info = StaffInformation::where('user_id', '=', $request->user()->id)->first();
        $user = array();
        $user['name'] = $request->user()->name;
        $user['email'] = $request->user()->email;
        $user['mobile'] = $request->user()->mobile;

        if ($info) {
            $user = array_merge($user, $info->toArray());
        }
        return $response->getSuccessResponse('Success!', $user);
    }

    public function updateProfile(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'present',
            'gender' => 'present',
            'dob' => 'present',
            'bloodGroup' => 'present',
            'streetAddress' => 'present',
            'pincode' => 'present',
            'city' => 'present',
            'registrationNumber' => 'present',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $info = StaffInformation::where('user_id', '=', $request->user()->id)->first();

        if (!$info) {
            $info = new StaffInformation();
            $info->user_id = $request->user()->id;
        }

        $info->gender = $request->get('gender');
        $info->dob = $request->get('dob');
        $info->blood_group = $request->get('bloodGroup');
        $info->street_address = $request->get('streetAddress');
        $info->pincode = $request->get('pincode');
        $info->city = $request->get('city');
        $info->registration_number = $request->get('registrationNumber');
        $info->updated_at = Carbon::now();

        $user = User::find($request->user()->id);

        $user->name = $request->get('name');
        $user->mobile = $request->get('mobile');

        try {

            $user->save();
            $info->save();

        } catch (QueryException $e) {

            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse('Updated User Information Successfully!');
    }
}
