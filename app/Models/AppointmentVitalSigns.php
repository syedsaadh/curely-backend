<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentVitalSigns extends Model
{
    protected $table = 'appointment_vital_signs';
    public function appointment()
    {
        return $this->belongsTo('Appointment');
    }
    public function fields() {
        return $this->hasMany('\App\Models\AppointmentVitalSignsValue', 'appointment_vital_signs_id', 'id');
    }
}
