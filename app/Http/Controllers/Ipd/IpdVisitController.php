<?php

namespace App\Http\Controllers\Ipd;


use App\Models\Departments;
use App\Models\IpdAdmission;
use App\Models\IpdAdmissionVisit;
use App\Models\Patients;
use App\Models\Response;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class IpdVisitController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = IpdAdmissionVisit::all();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function get($id)
    {
        $response = new Response();
        $data = IpdAdmissionVisit::with(['vitalSigns.fields', 'clinicalNotes',
            'labOrders', 'prescriptions.drug', 'completedProcedures', 'treatmentPlans'])->find($id);
        if (!$data) return $response->getNotFound();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function addVisit(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'admissionId' => 'required',
            'visitType' => 'required',
            'visitedBy' => 'present',
            'visitedOn' => 'required'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $admissionId = $request->input('admissionId');
        $admission = IpdAdmission::find($admissionId);
        if (!$admission) {
            return $response->getNotFound('Admission not Found');
        }
        $newVisit = new IpdAdmissionVisit();
        $newVisit->ipd_admission_id = $admissionId;
        $newVisit->visit_type = $request->input('visitType');
        $newVisit->visited_by = $request->input('visitedBy');
        $newVisit->visited_on = $request->input('visitedOn');
        $newVisit->created_by_user_id = $request->user()->id;
        $newVisit->updated_by_user = $request->user()->id;

        try {
            $newVisit->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Visit!');
        }
        return $response->getSuccessResponse('Added Visit Successfully!', ['id' => $newVisit->id]);
    }

    public function edit(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'visitType' => 'required',
            'visitedBy' => 'present',
            'visitedOn' => 'required'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $visit = IpdAdmissionVisit::find($request->input('id'));
        if (!$visit) {
            return $response->getNotFound();
        }

        $visit->visit_type = $request->input('visitType');
        $visit->visited_by = $request->input('visitedBy');
        $visit->visited_on = $request->input('visitedOn');

        try {
            $visit->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Visit!');
        }
        return $response->getSuccessResponse('Edited Visit Successfully!', $visit);
    }

    public function delete($id)
    {
        $response = new Response();
        $department = IpdAdmissionVisit::find($id);
        if (!$department) {
            return $response->getNotFound('Visit Not Found');
        }
        try {
            $department->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Visit!');
        }
        return $response->getSuccessResponse();
    }
}
