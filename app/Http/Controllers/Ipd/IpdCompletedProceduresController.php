<?php

namespace App\Http\Controllers\Ipd;


use App\Models\IpdAdmissionVisit;
use App\Models\IpdCompletedProcedures;
use App\Models\Response;
use App\Procedures;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class IpdCompletedProceduresController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'visitId' => 'required',
            'procedures' => 'present|array',
            'procedures.*.procedure_id' => 'required|numeric',
            'procedures.*.name' => 'required|string',
            'procedures.*.units' => 'present|numeric|min:1',
            'procedures.*.cost' => 'present|numeric|min:0',
            'procedures.*.discount' => 'present|numeric|min:0',
            'procedures.*.notes' => 'present|string|nullable',
            'procedures.*.id' => 'numeric|nullable',
            'procedures.*.delete' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $procedures = $request->json('procedures');
        $visitId = $request->input('visitId');
        $visit = IpdAdmissionVisit::find($visitId);
        if (!$visit) {
            return $response->getNotFound('Visit Id Not Found');
        }
        $toDelete = array_filter($procedures, function ($el) {
            return $el['delete'] == true && array_key_exists('id', $el);
        });
        $toInsert = array_filter($procedures, function ($el) {
            return $el['delete'] == false && $el['id'] === null;
        });
        $toUpdate = array_filter($procedures, function ($el) {
            return $el['delete'] == false && array_key_exists('id', $el);
        });
        foreach ($toDelete as $item) {
            try {
                IpdCompletedProcedures::find($item['id'])->delete();
            } catch (QueryException $e) {
            }
        }
        foreach ($toUpdate as $item) {
            try {
                $it = IpdCompletedProcedures::find($item['id']);
                if ($it) {
                    $it->procedure_name = $item['name'];
                    $it->procedure_units = $item['units'];
                    $it->procedure_cost = $item['cost'];
                    $it->procedure_discount = $item['discount'];
                    $it->notes = $item['notes'];
                    $it->updated_by_user = $request->user()->id;
                    $it->save();
                }
            } catch (QueryException $e) {
            }
        }
        foreach ($toInsert as $item) {
            try {
                $it = new IpdCompletedProcedures();
                $it->ipd_admission_visit_id = $visitId;
                $proc = Procedures::find($item['procedure_id']);
                if ($proc) {
                    $it->procedure_id = $proc->id;
                    $it->procedure_name = $proc->name;
                    $it->procedure_units = $item['units'];
                    $it->procedure_cost = $item['cost'];
                    $it->procedure_discount = $item['discount'];
                    $it->notes = $item['notes'];
                    $it->updated_by_user = $request->user()->id;

                    $it->save();
                }
            } catch (QueryException $e) {
            }
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($visitId) {
        $response = new Response();
        $cp = IpdCompletedProcedures::where('ipd_admission_visit_id', '=', $visitId);
        if (!$cp) {
            return $response->getNotFound('Not Found');
        }
        try {
            $cp->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse();
    }
}
