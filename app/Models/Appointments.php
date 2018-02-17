<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    protected $fillable = [
        'patient_id', 'scheduled_from', 'scheduled_to', 'for_department', 'for_doctor', 'notes', 'deleted'
    ];
    protected $table = 'appointments';
    public function patient() {
        return $this->hasOne('App\Models\Patients', 'id', 'patient_id');
    }
    public function vitalSigns() {
        return $this->hasOne('App\Models\AppointmentVitalSigns', 'appointment_id');
    }
    public function clinicalNotes() {
        return $this->hasOne('App\Models\AppointmentClinicalNotes', 'appointment_id');
    }
    public function prescriptions() {
        return $this->hasMany('App\Models\AppointmentPrescriptions', 'appointment_id');
    }
    public function labOrders() {
        return $this->hasMany('App\Models\AppointmentLabOrders', 'appointment_id');
    }
    public function treatmentPlans() {
        return $this->hasMany('App\Models\AppointmentTreatmentPlans', 'appointment_id');
    }
    public function completedProcedures() {
        return $this->hasMany('App\Models\AppointmentCompletedProcedures', 'appointment_id');
    }
    public function scopeWithRecords() {
        return $this->with(['vitalSigns', 'clinicalNotes', 'prescriptions', 'labOrders', 'treatmentPlans', 'completedProcedures']);
    }
}
