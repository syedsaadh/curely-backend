<?php

namespace App\Http\Controllers\Ipd;

use App\Models\IpdAdmissionVisit;
use App\Models\IpdClinicalNotes;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class IpdClinicalNotesController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'visitId' => 'required',
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
        $visitId = $request->input('visitId');
        $visit = IpdAdmissionVisit::find($visitId);
        $clinicalNotesId = $request->input('id');
        if (!$visit) {
            return $response->getNotFound('Visit Id Not Found');
        }
        $complaintsStr = implode(";", $complaints);
        $observationsStr = implode(";", $observations);
        $diagnosisStr = implode(";", $diagnosis);
        $notesStr = implode(";", $notes);
        $clinicalNotes = IpdClinicalNotes::find($clinicalNotesId);
        if(!$clinicalNotes) {
            $clinicalNotes = new IpdClinicalNotes();
        }
        $clinicalNotes->ipd_admission_visit_id = $visitId;
        $clinicalNotes->complaints = $complaintsStr;
        $clinicalNotes->observations = $observationsStr;
        $clinicalNotes->diagnosis = $diagnosisStr;
        $clinicalNotes->notes = $notesStr;
        $clinicalNotes->updated_by_user = $request->user()->id;
        try {
            $clinicalNotes->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Updating Clinical Notes!'.$e);
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($visitId) {
        $response = new Response();
        $clinicalNotes = IpdClinicalNotes::where('ipd_admission_visit_id', '=', $visitId);
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
