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

class IpdController extends Controller
{

    public function getAllAdmissionVisitsByPatientId($id)
    {
        $response = new Response();
        $data = IpdAdmission::with(['patient', 'visits.vitalSigns.fields', 'visits.clinicalNotes',
            'visits.labOrders', 'visits.prescriptions', 'visits.completedProcedures', 'visits.treatmentPlans'])->where([[
                "patient_id", '=', $id
        ]])->get();
        if (!$data) return $response->getNotFound();
        return $response->getSuccessResponse('Success!', $data);
    }
}
