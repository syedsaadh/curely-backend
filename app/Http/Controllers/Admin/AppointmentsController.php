<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointments;
use App\Models\Departments;
use App\Models\Patients;
use App\Models\Response;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentsController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = Appointments::with('patient')->get();
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
        $data = Appointments::with('patient')->where([
            ['scheduled_from', '>=', Carbon::parse($from)->addDay(-1)],
            ['scheduled_to', '<=', $to],
        ])->get();
        return $response->getSuccessResponse("Success!", $data);

    }

    public function getById($id)
    {
        $response = new Response();
//        Nested Relationship
//        $data = Appointments::with(['vitalSigns.fields', 'clinicalNotes',
//            'labOrders', 'prescriptions.drugs.nested', 'prescriptions.drug', 'completedProcedures', 'treatmentPlans'])->find($id);
        $data = Appointments::with(['vitalSigns.fields', 'clinicalNotes',
            'labOrders', 'prescriptions.drug', 'completedProcedures', 'treatmentPlans'])->find($id);
        if (!$data) return $response->getNotFound();
        return $response->getSuccessResponse('Success!', $data);
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
            'department' => 'present',
            'scheduledFrom' => 'required',
            'scheduledTo' => 'required',
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

        $scheduledFrom = $request->input('scheduledFrom');
        $scheduledTo = $request->input('scheduledTo');
        $notes = $request->input('notes');
        $doctorId = $request->input('doctor');
        $departmentId = $request->input('department');
        $doctor = User::find($doctorId);
        $department = Departments::find($departmentId)->first();

        if ($doctorId && !$doctor) {
            return $response->getNotFound('Doctor Not Found');
        }
        if ($departmentId && !$department) {
            return $response->getNotFound('Department Not Found');
        }
        $appointment = new Appointments();
        $appointment->patient_id = $patient->id;
        $appointment->scheduled_from = $scheduledFrom;
        $appointment->scheduled_to = $scheduledTo;
        $appointment->for_department = $department->id;
        $appointment->for_doctor = $doctor ? $doctor->id : null;
        $appointment->notes = $notes;
        $appointment->cancelled = false;
        if (Appointments::where([
            ['scheduled_from', '<', $scheduledTo],
            ['scheduled_from', '>', $scheduledFrom],
        ])
            ->orWhere([
                ['scheduled_from', '=', $scheduledFrom],
                ['scheduled_to', '=', $scheduledTo]
            ])
            ->orWhere([
                ['scheduled_to', '<', $scheduledTo],
                ['scheduled_to', '>', $scheduledFrom],
            ])
            ->orWhere([
                ['scheduled_from', '<', $scheduledFrom],
                ['scheduled_to', '>', $scheduledFrom],
            ])
            ->first()
        ) {
            return $response->getAlreadyPresent('Schedule is Already Taken!');
        }
        try {
            $appointment->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Appointment!');
        }
        return $response->getSuccessResponse('Created Appointment Successfully!', ['id' => $appointment->id]);
    }

    public function edit(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'doctor' => 'present',
            'department' => 'present',
            'scheduledFrom' => 'required',
            'scheduledTo' => 'required',
            'notes' => 'present',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $appointment = Appointments::find($request->input('id'));

        if (!$appointment) {
            return $response->getNotFound('Appointment Not Found');
        }
        $scheduledFrom = $request->input('scheduledFrom');
        $scheduledTo = $request->input('scheduledTo');
        $notes = $request->input('notes');
        $doctorId = $request->input('doctor');
        $departmentId = $request->input('department');

        $doctor = User::find($doctorId);
        $department = Departments::find($departmentId);

        if ($doctorId && !$doctor) {
            return $response->getNotFound('Doctor Not Found');
        }
        if ($departmentId && !$department) {
            return $response->getNotFound('Department Not Found');
        }
        $searchAppointment = Appointments::where([
            ['scheduled_from', '<', $scheduledTo],
            ['scheduled_from', '>', $scheduledFrom],

        ])
            ->orWhere([
                ['scheduled_from', '<=', $scheduledFrom],
                ['scheduled_to', '>=', $scheduledTo]
            ])
            ->orWhere([
                ['scheduled_to', '<', $scheduledTo],
                ['scheduled_to', '>', $scheduledFrom],
            ])
            ->orWhere([
                ['scheduled_from', '<', $scheduledFrom],
                ['scheduled_to', '>', $scheduledFrom],
            ])
            ->first();
        if ($searchAppointment && $searchAppointment->id != $appointment->id && $searchAppointment->cancelled != 1) {
            return $response->getAlreadyPresent('Schedule is Already Taken!');
        }

        $appointment->scheduled_from = $scheduledFrom;
        $appointment->scheduled_to = $scheduledTo;
        $appointment->for_department = $department->id;
        $appointment->for_doctor = $doctor ? $doctor->id : null;
        $appointment->notes = $notes;
        $appointment->cancelled = 0;
        $appointment->cancel_reason = null;
        try {
            $appointment->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Appointment!');
        }
        return $response->getSuccessResponse('Edited Appointment Successfully!');
    }

    public function cancelAppointment(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'delete' => 'present|boolean',
            'reason' => 'present'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $appointment = Appointments::find($request->input('id'));
        $deletePermanent = $request->input('delete');
        $reason = $request->input('reason');

        if (!$appointment) {
            return $response->getNotFound('Appointment Not Found');
        }
        try {
            if (!$deletePermanent) {
                $appointment->cancel_reason = $reason;
                $appointment->cancelled = true;
                $appointment->save();
            } else {
                $appointment->delete();
            }
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Cancelling Appointment!');
        }
        return $response->getSuccessResponse();
    }
}
