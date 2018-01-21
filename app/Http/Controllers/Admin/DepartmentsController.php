<?php

namespace App\Http\Controllers\Admin;

use App\Models\Departments;
use App\Http\Controllers\Controller;
use App\Models\Response;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{
    public function index(Request $request)
    {
        $response = new Response();
        $with = $request->query('with');
        if(!in_array($with, ['doctor', 'admin', 'nurse'])) {
            $data = Departments::all();
            return $response->getSuccessResponse('Success!', $data);
        }
        $data = Departments::with(['users'])->get();
        foreach ($data as $department) {
            foreach ($department->users as $key => $user) {
                if(!$user->hasRole($with)) {
                    unset($department->users[$key]);
                }
            }
        }
        return $response->getSuccessResponse('Success!', $data);
    }

    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'desc' => 'required',
            'bedCount' => 'required',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (Departments::where('name', $request->input('name'))->exists()) {
            return $response->getAlreadyPresent();
        }

        $department = new Departments();
        $department->name = $request->input('name');
        $department->desc = $request->input('desc');
        $department->bed_count = $request->input('bedCount');

        try {
            $department->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Department!');
        }
        return $response->getSuccessResponse('Created Department Successfully!', ['id' => $department->id]);
    }

    public function delete($id)
    {
        $response = new Response();
        $department = Departments::find($id);
        if (!$department) {
            return $response->getNotFound('Department Not Found');
        }
        try {
            $department->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Department!');
        }
        return $response->getSuccessResponse();
    }

    public function edit(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'desc' => 'required',
            'bedCount' => 'required',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $department = Departments::find( $request->input('id'));
        if (!$department) {
            return $response->getNotFound();
        }

        $department->name = $request->input('name');
        $department->desc = $request->input('desc');
        $department->bed_count = $request->input('bedCount');

        try {
            $department->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Department!');
        }
        return $response->getSuccessResponse('Edited Department Successfully!', $department);
    }
}
