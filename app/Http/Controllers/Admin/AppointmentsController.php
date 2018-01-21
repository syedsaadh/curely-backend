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

class AppointmentsController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = Appointments::all();
        return $response->getSuccessResponse('Success!', $data);
    }

    public function store(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'patientId' => 'present',
            'name' => 'required',
            'mobile'=> 'present',
            'email'=> 'present',
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
        $patient = new Patients();
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
        $doctor = User::find($request->input('doctor'));
        $department = Departments::find($request->input('department'));

        if($doctorId && !$doctor) {
            return $response->getNotFound('Doctor Not Found');
        }
        if($departmentId && !$department) {
            return $response->getNotFound('Department Not Found');
        }
        $appointment = new Appointments();
        $appointment->patient_id = $patient->id;
        $appointment->scheduled_from = $scheduledFrom;
        $appointment->scheduled_to = $scheduledTo;
        $appointment->for_department = $department->id;
        $appointment->for_doctor = $doctor->id;
        $appointment->notes = $notes;
        $appointment->cancelled = false;
        try {
            $appointment->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Appointment!');
        }
        return $response->getSuccessResponse('Created Appointment Successfully!', ['id' => $appointment->id]);
    }
    public function edit(Request $request) {
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
        $appointment = Appointments::find( $request->input('id'));

        if(!$appointment) {
            return $response->getNotFound('Appointment Not Found');
        }
        $scheduledFrom = $request->input('scheduledFrom');
        $scheduledTo = $request->input('scheduledTo');
        $notes = $request->input('notes');
        $doctorId = $request->input('doctor');
        $departmentId = $request->input('department');

        $doctor = User::find($request->input('doctor'));
        $department = Departments::find($request->input('department'));

        if($doctorId && !$doctor) {
            return $response->getNotFound('Doctor Not Found');
        }
        if($departmentId && !$department) {
            return $response->getNotFound('Department Not Found');
        }

        $appointment->scheduled_from = $scheduledFrom;
        $appointment->scheduled_to = $scheduledTo;
        $appointment->for_department = $department->id;
        $appointment->for_doctor = $doctor->id;
        $appointment->notes = $notes;

        try {
            $appointment->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Appointment!');
        }
        return $response->getSuccessResponse('Edited Appointment Successfully!');
    }
    public function cancelAppointment(Request $request) {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'deletePermanent' => 'present|boolean',
            'reason' => 'present'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $appointment = Appointments::find( $request->input('id'));
        $deletePermanent = $request->input('deletePermanent');
        $reason = $request->input('reason');

        if(!$appointment) {
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
        }
        catch (QueryException $e) {
            return $response->getUnknownError('Error Cancelling Appointment!');
        }
        return $response->getSuccessResponse();
    }
}
