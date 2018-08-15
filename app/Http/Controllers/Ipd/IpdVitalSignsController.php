<?php

namespace App\Http\Controllers\Ipd;


use App\Models\Departments;
use App\Models\IpdAdmission;
use App\Models\IpdAdmissionVisit;
use App\Models\IpdVitalSigns;
use App\Models\IpdVitalSignsValue;
use App\Models\Patients;
use App\Models\Response;
use App\Models\VitalSigns;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class IpdVitalSignsController extends Controller
{

    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'visitId' => 'required',
            'vitalSigns' => 'present|array',
            'vitalSigns.*.name' => 'required',
            'vitalSigns.*.value' => 'required',
            'vitalSignsId' => 'present'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $data = array();
        $vitalSigns = $request->json('vitalSigns');
        $vitalSignsId = $request->input('vitalSignsId');
        $visitId = $request->input('visitId');
        $visit = IpdAdmissionVisit::find($visitId);
        if (!is_array($vitalSigns)) {
            return $response->getValidationError('Vital Signs Required');
        }
        if (!$visit) {
            return $response->getNotFound('Visit Id Not Found');
        }
        foreach ($vitalSigns as $vitalSign) {
            if (!array_key_exists('name', $vitalSign) || !array_key_exists('value', $vitalSign)) {
                return $response->getValidationError('Vital Signs Format Incorrect');
            }
            if (!$vitalSign['name'] || !$vitalSign['value']) {
                return $response->getValidationError('Vital Signs Format Incorrect');
            }
            $temp = array('name' => $vitalSign['name'], 'value' => $vitalSign['value']);
            $vitalSignAttr = VitalSigns::where('name', $vitalSign['name'])->first();
            if ($vitalSignAttr) {
                $temp = array_add($temp, 'unit', $vitalSignAttr->unit);
            } else {
                return $response->getUnknownError('Some VitalSigns are not allowed');
            }
            array_push($data, $temp);
        }
        $visitVitalSign = IpdVitalSigns::find($vitalSignsId);
        if ($visitVitalSign) {
            $dbValues = IpdVitalSignsValue::where('ipd_vital_signs_id', $vitalSignsId);
            try {
                $dbValues->delete();
            } catch (QueryException $e) {
                return $response->getUnknownError('Error Creating VitalSign!');
            }
        } else {
            $visitVitalSign = new IpdVitalSigns();
        }
        try {
            $visitVitalSign->ipd_admission_visit_id = $visitId;
            $visitVitalSign->updated_at = Carbon::now();
            $visitVitalSign->updated_by_user = $request->user()->id;
            $visitVitalSign->save();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }

        foreach ($data as &$vitalSign) {
            $vitalSign['ipd_vital_signs_id'] = $visitVitalSign->id;
            $vitalSign['created_at'] = Carbon::now();
            $vitalSign['updated_at'] = Carbon::now();
        }
        try {
            IpdVitalSignsValue::insert($data);
        } catch (QueryException $e) {
            $visitVitalSign->delete();
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($visitId) {
        $response = new Response();
        $vitalSign = IpdVitalSigns::where('ipd_admission_visit_id', '=', $visitId);
        if (!$vitalSign) {
            return $response->getNotFound('Not Found');
        }
        try {
            $vitalSign->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse();
    }
}
