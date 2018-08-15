<?php

namespace App\Http\Controllers\Ipd;


use App\Models\Departments;
use App\Models\IpdAdmission;
use App\Models\Patients;
use App\Models\Response;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class IpdAdmissionController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = IpdAdmission::with('patient')->get();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function get(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:"d-m-Y"',
            'to' => 'required|date_format:"d-m-Y"|after:from'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $from = $request->input('from');
        $to = $request->input('to');
        $to = Carbon::parse($to)->addDay()->format('Y-m-d 23:59:59');
        $data = IpdAdmission::with('patient')->where([
            ['admitted_on', '>=', Carbon::parse($from)->addDay(-1)],
            ['discharged_on', '<=', $to],
            ['discharged_on', '!=', null],
        ])->get();
        return $response->getSuccessResponse("Success!", $data);

    }

    public function getAdmittedPatientsByDept($deptId)
    {
        $response = new Response();

        $data = IpdAdmission::with('patient')->where([
            ['in_department', '=', $deptId],
            ['discharged_on', '=', null],
        ])->get();
        if (!$data) return $response->getNotFound();
        return $response->getSuccessResponse("Success!", $data);

    }

    public function getById($id)
    {
        $response = new Response();
        $data = IpdAdmission::with(['vitalSigns.fields', 'clinicalNotes',
            'labOrders', 'prescriptions', 'completedProcedures', 'treatmentPlans'])->find($id);
        if (!$data) return $response->getNotFound();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function getAvailableBeds($deptId)
    {
        $response = new Response();
        $dept = Departments::find($deptId);

        $admittedPatients = IpdAdmission::where([
            ['in_department', '=', $deptId],
            ['discharged_on', '=', null]
        ])->get();
        if (!$dept) {
            return $response->getNotFound();
        }
        $bedCount = $dept->bed_count;
        $usedBeds = array();
        $totalBeds = range(1, $bedCount);
        if (!$admittedPatients) {
            return $response->getSuccessResponse('Success!', ["beds" => range(1, $bedCount)]);
        }
        foreach ($admittedPatients as $admittedPatient) {
            array_push($usedBeds, $admittedPatient->bed_no);
        }
        return $response->getSuccessResponse('Success!', ["beds" => array_values(array_diff($totalBeds, $usedBeds))]);
    }

    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'patientId' => 'present',
            'name' => 'required',
            'mobile' => 'present',
            'email' => 'present',
            'gender' => 'present',
            'dob' => 'present',
            'age' => 'present',
            'bloodGroup' => 'present',
            'doctor' => 'present',
            'department' => 'required',
            'admittedOn' => 'required',
            'bedNo' => 'required',
            'notes' => 'present',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $patientId = $request->input('patientId');
        if ($patientId) {
            $patient = Patients::find($patientId);
            if (!$patient) {
                return $response->getNotFound('Patient Not Found');
            }
        } else {
            $patient = new Patients();
        }
        $patient->name = $request->input('name');
        $patient->email = $request->input('email');
        $patient->mobile = $request->input('mobile');
        $patient->gender = $request->input('gender');
        $patient->dob = $request->input('dob');
        $patient->age = $request->input('age');
        $patient->blood_group = $request->input('bloodGroup');
        try {
            $patient->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Patient!');
        }

        $admittedOn = $request->input('admittedOn');
        $notes = $request->input('notes');
        $doctorId = $request->input('doctor');
        $departmentId = $request->input('department');
        $bedNo = $request->input('bedNo');
        $doctor = User::find($doctorId);
        $department = Departments::find($departmentId);

        if ($doctorId && !$doctor) {
            return $response->getNotFound('Doctor Not Found');
        }
        if ($departmentId && !$department) {
            return $response->getNotFound('Department Not Found');
        }
        $admission = new IpdAdmission();
        $admission->patient_id = $patient->id;
        $admission->admitted_on = $admittedOn;
        $admission->in_department = $department->id;
        $admission->referred_by_doctor = $doctor ? $doctor->id : null;
        $admission->bed_no = $bedNo;
        $admission->notes = $notes;
        $admission->updated_by_user = $request->user()->id;

        try {
            $admission->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Admitting Patient!');
        }
        return $response->getSuccessResponse('Admitted Patient Successfully!', ['id' => $admission->id]);
    }

    public function edit(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'doctor' => 'present',
            'department' => 'required',
            'admittedOn' => 'required',
            'bedNo' => 'required',
            'notes' => 'present',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $admission = IpdAdmission::find($request->input('id'));

        if (!$admission) {
            return $response->getNotFound('Admission Not Found');
        }
        $admittedOn = $request->input('admittedOn');
        $notes = $request->input('notes');
        $doctorId = $request->input('doctor');
        $departmentId = $request->input('department');
        $bedNo = $request->input('bedNo');

        $doctor = User::find($doctorId);
        $department = Departments::find($departmentId);

        if ($doctorId && !$doctor) {
            return $response->getNotFound('Doctor Not Found');
        }
        if ($departmentId && !$department) {
            return $response->getNotFound('Department Not Found');
        }


        $admission->admitted_on = $admittedOn;
        $admission->in_department = $department->id;
        $admission->referred_by_doctor = $doctor ? $doctor->id : null;
        $admission->bed_no = $bedNo;
        $admission->notes = $notes;
        $admission->updated_by_user = $request->user()->id;

        try {
            $admission->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Admission!');
        }
        return $response->getSuccessResponse('Edited Admission Successfully!');
    }

    public function deleteAdmission(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'delete' => 'present|boolean'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $admission = IpdAdmission::find($request->input('id'));
        $deletePermanent = $request->input('delete');

        if (!$admission) {
            return $response->getNotFound('Admission Not Found');
        }
        try {
            if (!$deletePermanent) {
                $admission->soft_delete = true;
                $admission->save();
            } else {
                $admission->delete();
            }
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Admission!');
        }
        return $response->getSuccessResponse();
    }

    public function dischargeAdmission(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'dischargedOn' => 'required|date'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $admission = IpdAdmission::find($request->input('id'));
        if (!$admission) {
            return $response->getNotFound('Admission Not Found');
        }
        $admittedOn = new Carbon($admission->admitted_on);
        $dischargedOn = new Carbon($request->input('dischargedOn'));
        if($admittedOn->diffInMinutes($dischargedOn) < 15)
        {
            return $response->getBadRequestError('Minimum of 15 Admit Time is Required!');
        }
        try {
            $admission->discharged_on =
            $admission->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Discharging Admission!');
        }
        return $response->getSuccessResponse();
    }
}
