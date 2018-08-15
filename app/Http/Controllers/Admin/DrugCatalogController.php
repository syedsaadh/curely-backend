<?php

namespace App\Http\Controllers\Admin;

use App\Models\DrugCatalog;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DrugCatalogController extends Controller
{
    public function index() {
        $response = new Response();
        $data = DrugCatalog::all();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function search($q)
    {
        $response = new Response();
        $data = DrugCatalog::where('name', 'like', '%'.$q.'%')->get();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'strength' => 'string|nullable',
            'unit' => 'string|nullable',
            'instruction' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        if (DrugCatalog::where('name', $request->input('name'))->exists()) {
            return $response->getAlreadyPresent();
        }

        $drug = new DrugCatalog();
        $drug->name = $request->input('name');
        $drug->drug_type = $request->input('type');
        $drug->default_dosage = $request->input('strength');
        $drug->default_dosage_unit = $request->input('unit');
        $drug->instruction = $request->input('instruction');
        try {
            $drug->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Adding Drug!');
        }
        return $response->getSuccessResponse('Added Drug Successfully!', ['id' => $drug->id]);
    }
    public function delete($id)
    {
        $response = new Response();
        $drug = DrugCatalog::find($id);
        if (!$drug) {
            return $response->getNotFound('Drug Not Found');
        }
        try {
            $drug->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Drug!');
        }
        return $response->getSuccessResponse();
    }
}
