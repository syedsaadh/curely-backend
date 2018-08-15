<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdAdmission extends Model
{
    protected $fillable = [
        'patient_id', 'admitted_on', 'discharged_on', 'in_department', 'notes', 'bed_no', 'soft_delete', 'updated_by_user'
    ];
    protected $table = 'ipd_admission';

    public function patient()
    {
        return $this->hasOne('App\Models\Patients', 'id', 'patient_id');
    }
    public function department()
    {
        return $this->hasOne('App\Models\Departments', 'id', 'in_department');
    }
    public function visits()
    {
        return $this->hasMany('App\Models\IpdAdmissionVisit', 'ipd_admission_id');
    }
}
