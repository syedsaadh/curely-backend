<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $fillable = [
        'name', 'mobile', 'email', 'gender', 'dob', 'age', 'blood_group', 'occupation', 'street_address', 'pincode',
        'city'
    ];
    protected $table = 'patients';
    public function medicalHistory() {
        return $this->hasMany('App\Models\PatientsMedicalHistory', 'patient_id');
    }
    protected $hidden = ['updated_at'];
}
