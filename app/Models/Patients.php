<?php

namespace App\Models;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    //use Searchable; Algolia and Scout
    protected $hidden = ['updated_at'];
    protected $table = 'patients';
    protected $fillable = [
        'name', 'mobile', 'email', 'gender', 'dob', 'age', 'blood_group', 'occupation', 'street_address', 'pincode',
        'city'
    ];
    public function appointments() {
        return $this->hasMany('App\Models\Appointments', 'patient_id', 'id')->with(['patient', 'vitalSigns.fields','clinicalNotes',
            'labOrders','prescriptions.drug', 'completedProcedures', 'treatmentPlans']);
    }
    public function admissions() {
        return $this->hasMany('App\Models\IPDAdmission', 'patient_id', 'id')->with(['department', 'visits.vitalSigns.fields', 'visits.clinicalNotes',
            'visits.labOrders', 'visits.prescriptions.drug', 'visits.completedProcedures', 'visits.treatmentPlans']);
    }
    public function medicalHistory() {
        return $this->hasMany('App\Models\PatientsMedicalHistory', 'patient_id');
    }
}
