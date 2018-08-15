<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpdAdmissionVisit extends Model
{
    use SoftDeletes;
    protected $fillable = ['ipd_admission_id', 'visit_type', 'visited_by', 'created_by_user_id', 'updated_by_user'];
    protected $table = 'ipd_admission_visit';
    protected $dates = ['deleted_at'];

    public function vitalSigns()
    {
        return $this->hasOne('App\Models\IpdVitalSigns', 'ipd_admission_visit_id');
    }

    public function clinicalNotes()
    {
        return $this->hasOne('App\Models\IpdClinicalNotes', 'ipd_admission_visit_id');
    }

    public function prescriptions()
    {
        return $this->hasMany('App\Models\IpdPrescriptions', 'ipd_admission_visit_id');
    }

    public function labOrders()
    {
        return $this->hasMany('App\Models\IpdLabOrders', 'ipd_admission_visit_id');
    }

    public function treatmentPlans()
    {
        return $this->hasMany('App\Models\IpdTreatmentPlans', 'ipd_admission_visit_id');
    }

    public function completedProcedures()
    {
        return $this->hasMany('App\Models\IpdCompletedProcedures', 'ipd_admission_visit_id');
    }

    public function scopeWithRecords()
    {
        return $this->with(['vitalSigns', 'clinicalNotes', 'prescriptions', 'labOrders', 'treatmentPlans', 'completedProcedures']);
    }
}
