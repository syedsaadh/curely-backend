<?php

namespace App\Http\Controllers\Admin;

use App\Models\Response;
use App\Procedures;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProceduresController extends Controller
{
    public function index() {
        $response = new Response();
        $data = Procedures::all();
        return $response->getSuccessResponse('Success!', $data);
    }
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'cost' => 'required',
            'instruction' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (Procedures::where('name', $request->input('name'))->exists()) {
            return $response->getAlreadyPresent();
        }

        $procedure = new Procedures();
        $procedure->name = $request->input('name');
        $procedure->cost = $request->input('cost');
        $procedure->instruction = $request->input('instruction');

        try {
            $procedure->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Procedure!');
        }
        return $response->getSuccessResponse('Created Procedure Successfully!', ['id' => $procedure->id]);
    }

    public function delete($id)
    {
        $response = new Response();
        $procedure = Procedures::find($id);
        if (!$procedure) {
            return $response->getNotFound('Procedure Not Found');
        }
        try {
            $procedure->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Procedure!');
        }
        return $response->getSuccessResponse();
    }

    public function edit(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'cost' => 'required',
            'instruction' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $procedure = Procedures::find( $request->input('id'));
        if (!$procedure) {
            return $response->getNotFound();
        }

        $procedure->name = $request->input('name');
        $procedure->cost = $request->input('cost');
        $procedure->instruction = $request->input('instruction');

        try {
            $procedure->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Procedure!');
        }
        return $response->getSuccessResponse('Edited Department Successfully!', $procedure);
    }
}
