<?php

namespace App\Models;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    use Searchable;
    protected $hidden = ['updated_at'];
    protected $table = 'patients';
    protected $fillable = [
        'name', 'mobile', 'email', 'gender', 'dob', 'age', 'blood_group', 'occupation', 'street_address', 'pincode',
        'city'
    ];
    public function medicalHistory() {
        return $this->hasMany('App\Models\PatientsMedicalHistory', 'patient_id');
    }
}
