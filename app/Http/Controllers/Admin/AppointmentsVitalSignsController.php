<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointments;
use App\Models\AppointmentVitalSigns;
use App\Models\AppointmentVitalSignsValue;
use App\Models\Response;
use App\Models\VitalSigns;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AppointmentsVitalSignsController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'appointmentId' => 'required',
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
        $appointmentId = $request->input('appointmentId');
        $appointment = Appointments::find($appointmentId);
        if (!is_array($vitalSigns)) {
            return $response->getValidationError('Vital Signs Required');
        }
        if (!$appointment) {
            return $response->getNotFound('Appointment Id Not Found');
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
        $appointmentVitalSign = AppointmentVitalSigns::find($vitalSignsId);
        if ($appointmentVitalSign) {
            $dbValues = AppointmentVitalSignsValue::where('appointment_vital_signs_id', $vitalSignsId);
            try {
                $dbValues->delete();
            } catch (QueryException $e) {
                return $response->getUnknownError('Error Creating VitalSign!');
            }
        } else {
            $appointmentVitalSign = new AppointmentVitalSigns();
        }
        try {
            $appointmentVitalSign->appointment_id = $appointmentId;
            $appointmentVitalSign->updated_at = Carbon::now();
            $appointmentVitalSign->save();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }

        foreach ($data as &$vitalSign) {
            $vitalSign['appointment_vital_signs_id'] = $appointmentVitalSign->id;
            $vitalSign['created_at'] = Carbon::now();
            $vitalSign['updated_at'] = Carbon::now();
        }
        try {
            AppointmentVitalSignsValue::insert($data);
        } catch (QueryException $e) {
            $appointmentVitalSign->delete();
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($appointmentId) {
        $response = new Response();
        $vitalSign = AppointmentVitalSigns::where('appointment_id', '=', $appointmentId);
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
