<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentVitalSignsValue extends Model
{
    protected $fillable = [
        'appointment_vital_signs_id', 'name', 'unit', 'value'
    ];
    protected $table = "appointment_vital_signs_value";
    protected $hidden = ['created_at', 'updated_at'];
}
