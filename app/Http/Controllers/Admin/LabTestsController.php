<?php

namespace App\Http\Controllers\Admin;

use App\Models\LabTests;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LabTestsController extends Controller
{
    public function index() {
        $response = new Response();
        $data = LabTests::all();
        return $response->getSuccessResponse('Success!', $data);
    }
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (LabTests::where('name', $request->input('name'))->exists()) {
            return $response->getAlreadyPresent();
        }

        $labTest = new LabTests();
        $labTest->name = $request->input('name');
        $labTest->description = $request->input('description');

        try {
            $labTest->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Labs Test!');
        }
        return $response->getSuccessResponse('Created Labs Test Successfully!', ['id' => $labTest->id]);
    }

    public function delete($id)
    {
        $response = new Response();
        $labTest = LabTests::find($id);
        if (!$labTest) {
            return $response->getNotFound('Labs Test Not Found');
        }
        try {
            $labTest->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Labs Test!');
        }
        return $response->getSuccessResponse();
    }

    public function edit(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $labTest = LabTests::find( $request->input('id'));
        if (!$labTest) {
            return $response->getNotFound();
        }

        $labTest->name = $request->input('name');
        $labTest->description = $request->input('description');

        try {
            $labTest->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Labs Test!');
        }
        return $response->getSuccessResponse('Edited Labs Test Successfully!', $labTest);
    }
}
