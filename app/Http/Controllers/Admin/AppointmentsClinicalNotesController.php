<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppointmentClinicalNotes;
use App\Models\Appointments;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AppointmentsClinicalNotesController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'appointmentId' => 'required',
            'id' => 'present',
            'complaints' => 'present|array',
            'complaints.*' => 'string ',
            'observations' => 'present|array',
            'observations.*' => 'string ',
            'diagnoses' => 'present|array',
            'diagnoses.*' => 'string ',
            'notes' => 'present|array',
            'notes.*' => 'string ',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $complaints = $request->json('complaints');
        $observations = $request->json('observations');
        $diagnosis = $request->json('diagnoses');
        $notes = $request->json('notes');
        $appointmentId = $request->input('appointmentId');
        $appointment = Appointments::find($appointmentId);
        $clinicalNotesId = $request->input('id');
        if (!$appointment) {
            return $response->getNotFound('Appointment Id Not Found');
        }
        $complaintsStr = implode(";", $complaints);
        $observationsStr = implode(";", $observations);
        $diagnosisStr = implode(";", $diagnosis);
        $notesStr = implode(";", $notes);
        $clinicalNotes = AppointmentClinicalNotes::find($clinicalNotesId);
        if(!$clinicalNotes) {
            $clinicalNotes = new AppointmentClinicalNotes();
        }
        $clinicalNotes->appointment_id = $appointmentId;
        $clinicalNotes->complaints = $complaintsStr;
        $clinicalNotes->observations = $observationsStr;
        $clinicalNotes->diagnosis = $diagnosisStr;
        $clinicalNotes->notes = $notesStr;
        try {
            $clinicalNotes->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Updating Clinical Notes!');
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($appointmentId) {
        $response = new Response();
        $clinicalNotes = AppointmentClinicalNotes::where('appointment_id', '=', $appointmentId);
        if (!$clinicalNotes) {
            return $response->getNotFound('Not Found');
        }
        try {
            $clinicalNotes->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse();
    }
}
