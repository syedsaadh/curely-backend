<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppointmentPrescriptions;
use App\Models\Appointments;
use App\Models\DrugCatalog;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AppointmentsPrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'appointmentId' => 'required',
            'prescriptions' => 'present|array',
            'prescriptions.*.id' => 'numeric|nullable',
            'prescriptions.*.drug_id' => 'required|numeric',
            'prescriptions.*.intake' => 'required|numeric',
            'prescriptions.*.frequency' => 'required|string',
            'prescriptions.*.display_frequency' => 'required|string',
            'prescriptions.*.food_precedence' => 'required|string',
            'prescriptions.*.duration' => 'required|numeric',
            'prescriptions.*.duration_unit' => 'required|string',
            'prescriptions.*.instruction' => 'present|string|nullable',
            'prescriptions.*.delete' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $prescriptions = $request->json('prescriptions');
        $appointmentId = $request->input('appointmentId');
        $appointment = Appointments::find($appointmentId);
        if (!$appointment) {
            return $response->getNotFound('Appointment Id Not Found');
        }
        $toDelete = array_filter($prescriptions, function ($el) {
            return $el['delete'] == true && array_key_exists('id', $el);
        });
        $toInsert = array_filter($prescriptions, function ($el) {
            return $el['delete'] == false && $el['id'] === null;
        });
        $toUpdate = array_filter($prescriptions, function ($el) {
            return $el['delete'] == false && array_key_exists('id', $el);
        });
        foreach ($toDelete as $item) {
            try {
                $it = AppointmentPrescriptions::find($item['id']);
                if($it) {
                    $it->delete();
                }
            } catch (QueryException $e) {
            }
        }
        foreach ($toUpdate as $item) {
            try {
                $it = AppointmentPrescriptions::find($item['id']);
                if ($it) {
                    $it->intake = $item['intake'];
                    $it->frequency = $item['frequency'];
                    $it->display_frequency = $item['display_frequency'];
                    $it->food_precedence = $item['food_precedence'];
                    $it->duration = $item['duration'];
                    $it->duration_unit = $item['duration_unit'];
                    $it->instruction = $item['instruction'];
                    $it->updated_by_user_id = $request->user()->id;
                    $it->save();
                }
            } catch (QueryException $e) {
            }
        }
        foreach ($toInsert as $item) {
            try {
                $it = new AppointmentPrescriptions();
                $drug = DrugCatalog::find($item['drug_id']);
                if ($drug) {
                    $it->appointment_id = $appointmentId;
                    $it->drug_id = $drug->id;
                    $it->intake = $item['intake'];
                    $it->frequency = $item['frequency'];
                    $it->display_frequency = $item['display_frequency'];
                    $it->food_precedence = $item['food_precedence'];
                    $it->duration = $item['duration'];
                    $it->duration_unit = $item['duration_unit'];
                    $it->instruction = $item['instruction'];
                    $it->updated_by_user_id = $request->user()->id;
                    $it->created_by_user_id = $request->user()->id;
                    $it->save();
                }
            } catch (QueryException $e) {
                return $response->getSuccessResponse('Success!', $e->getMessage());
            }
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($appointmentId) {
        $response = new Response();
        $tp = AppointmentPrescriptions::where('appointment_id', '=', $appointmentId);
        if (!$tp) {
            return $response->getNotFound('Not Found');
        }
        try {
            $tp->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse();
    }
}
