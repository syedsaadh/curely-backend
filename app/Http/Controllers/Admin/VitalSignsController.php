<?php

namespace App\Http\Controllers\Admin;

use App\Models\Response;
use App\Models\VitalSigns;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VitalSignsController extends Controller
{
    public function index() {
        $response = new Response();
        $data = VitalSigns::all();
        return $response->getSuccessResponse("Success!", $data);
    }
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'desc' => 'present',
            'unit' => 'required',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (VitalSigns::where('name', $request->input('name'))->exists()) {
            return $response->getAlreadyPresent();
        }

        $vitalSign = new VitalSigns();
        $vitalSign->name = $request->input('name');
        $vitalSign->desc = $request->input('desc');
        $vitalSign->unit = $request->input('unit');

        try {
            $vitalSign->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Vital Sign!');
        }
        return $response->getSuccessResponse('Created Vital Sign Successfully!', ['id' => $vitalSign->id]);
    }

    public function delete($id)
    {
        $response = new Response();
        $vitalSign = VitalSigns::find($id);
        if (!$vitalSign) {
            return $response->getNotFound('Vital Sign Not Found');
        }
        try {
            $vitalSign->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Vital Sign!');
        }
        return $response->getSuccessResponse();
    }

    public function edit(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'desc' => 'present',
            'unit' => 'required',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $vitalSign = VitalSigns::find( $request->input('id'));
        if (!$vitalSign) {
            return $response->getNotFound();
        }

        $vitalSign->name = $request->input('name');
        $vitalSign->desc = $request->input('desc');
        $vitalSign->unit = $request->input('unit');

        try {
            $vitalSign->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Vital Sign!');
        }
        return $response->getSuccessResponse('Edited Vital Sign Successfully!', $vitalSign);
    }
}
