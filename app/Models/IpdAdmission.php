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

    public function vitalSigns()
    {
        return $this->hasOne('App\Models\IpdVitalSigns', 'appointment_id');
    }

    public function clinicalNotes()
    {
        return $this->hasOne('App\Models\IpdClinicalNotes', 'appointment_id');
    }

    public function prescriptions()
    {
        return $this->hasMany('App\Models\IpdPrescriptions', 'appointment_id');
    }

    public function labOrders()
    {
        return $this->hasMany('App\Models\IpdLabOrders', 'appointment_id');
    }

    public function treatmentPlans()
    {
        return $this->hasMany('App\Models\IpdTreatmentPlans', 'appointment_id');
    }

    public function completedProcedures()
    {
        return $this->hasMany('App\Models\IpdCompletedProcedures', 'appointment_id');
    }

    public function scopeWithRecords()
    {
        return $this->with(['vitalSigns', 'clinicalNotes', 'prescriptions', 'labOrders', 'treatmentPlans', 'completedProcedures']);
    }
}
