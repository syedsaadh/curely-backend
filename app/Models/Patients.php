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
        return $this->hasMany('App\Models\Appointments', 'patient_id', 'id')->with(['vitalSigns.fields','clinicalNotes',
            'labOrders','prescriptions', 'completedProcedures', 'treatmentPlans']);
    }
    public function medicalHistory() {
        return $this->hasMany('App\Models\PatientsMedicalHistory', 'patient_id');
    }
}
