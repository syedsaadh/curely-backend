<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientsMedicalHistory extends Model
{
    protected $fillable = ['patient_id', 'description'];
    protected $table = 'patients_medical_history';
    protected $hidden = ['id', 'created_at', 'updated_at'];
}
