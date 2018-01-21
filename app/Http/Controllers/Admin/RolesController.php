<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Response;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = Role::all(['name', 'id', 'description']);
        return $response->getSuccessResponse('Success!', $data);
    }

    public function createRole(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles',
            'displayName' => 'required',
            'description' => 'nullable',
        ]);

        if ($validator->fails())
            return $response->getValidationError($validator->messages());

        $role = new Role();
        $role->name = $request->input('name');
        $role->display_name = $request->input('displayName');
        $role->description = $request->input('description');
        $role->save();

        $response->setCode(0);
        $response->setMessage('Created Role Successfully!');
        $response->setPayload(['id' => $role->id]);
        return $response->getJsonResponse();
    }
    public function attachPermission(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $role = Role::where('name', '=', $request->input('role'))->first();
        $permission = Permission::where('name', '=', $request->input('permissionName'))->first();

        if (!$role) {
            return $response->getNotFound('Role Not Found');
        }
        if (!$permission) {
            return $response->getNotFound('Permission Not Found');
        }
        if($role->hasPermission([$permission->name])) {
            return $response->getAlreadyPresent('Permission is Already assigned to this Role');
        }
        $role->attachPermission($permission);

        return $response->getSuccessResponse('Assigned Permission Successfully!');
    }
}
