<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patients;
use App\Models\Response;
use App\Models\PatientsMedicalHistory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientsController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = Patients::with('medicalHistory')->get();
        return $response->getSuccessResponse('Success!', $data);
    }
    public function search($q)
    {
        $response = new Response();
        $data = Patients::search($q)->get();
        return $response->getSuccessResponse('Success!', $data);
    }
    public function getPatientById($id) {
        $response = new Response();
        $data = Patients::with('medicalHistory')->find($id);
        if(!$data) {
            return $response->getNotFound();
        }
        return $response->getSuccessResponse('Success!', $data);
    }
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile'=> 'present',
            'email'=> 'present',
            'gender'=> 'present',
            'dob'=> 'present',
            'age'=> 'present',
            'bloodGroup'=> 'present',
            'occupation'=> 'present',
            'streetAddress'=> 'present',
            'pincode'=> 'present',
            'city'=> 'present',
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
        $patient->occupation = $request->input('occupation');
        $patient->street_address = $request->input('streetAddress');
        $patient->pincode = $request->input('pincode');
        $patient->city = $request->input('city');

        try {
            $patient->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Patient!');
        }
        $patientHistory = $request->json('medicalHistory');
        if ($patientHistory && is_array($patientHistory)) {
            $insertData = array();
            foreach ($patientHistory as $history) {
                array_push($insertData, array('patient_id' => $patient->id, 'description'=> $history,
                    'created_at'=> now(), 'updated_at' => now()));
            }
            PatientsMedicalHistory::insert($insertData);
        }
        return $response->getSuccessResponse('Created Patient Successfully!', ['id' => $patient->id]);
    }

    public function delete($id)
    {
        $response = new Response();
        $patient = Patients::find($id);
        if (!$patient) {
            return $response->getNotFound('Patient Not Found');
        }
        try {
            $patient->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Patient!');
        }
        return $response->getSuccessResponse();
    }

    public function edit(Request $request) {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'mobile'=> 'present',
            'email'=> 'present',
            'gender'=> 'present',
            'dob'=> 'present',
            'age'=> 'present',
            'bloodGroup'=> 'present',
            'occupation'=> 'present',
            'streetAddress'=> 'present',
            'pincode'=> 'present',
            'city'=> 'present'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $patient = Patients::find( $request->input('id'));
        if (!$patient) {
            return $response->getNotFound();
        }

        $patient->name = $request->input('name');
        $patient->email = $request->input('email');
        $patient->mobile = $request->input('mobile');
        $patient->gender = $request->input('gender');
        $patient->dob = $request->input('dob');
        $patient->age = $request->input('age');
        $patient->blood_group = $request->input('bloodGroup');
        $patient->occupation = $request->input('occupation');
        $patient->street_address = $request->input('streetAddress');
        $patient->pincode = $request->input('pincode');
        $patient->city = $request->input('city');

        try {
            $patient->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Editing Patient!');
        }

        $storedHistory = array_pluck($patient->medicalHistory->toArray(), 'description');
        $patientHistory = $request->json('medicalHistory');
        if($patientHistory) {
            foreach ($storedHistory as $history) {
                if (!in_array($history, $patientHistory)) {
                    $i = PatientsMedicalHistory::where(['patient_id' => $patient->id, 'description' => $history]);
                    $i->delete();
                }
            }
            foreach ($patientHistory as $history) {
                PatientsMedicalHistory::updateOrCreate(
                    ['patient_id' => $patient->id, 'description' => $history],
                    ['description' => $history]
                );
            }
        }
        $patient = Patients::with('medicalHistory')->find( $request->input('id'));
        return $response->getSuccessResponse('Edited Patient Successfully!', $patient);
    }
}
