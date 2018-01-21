<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    protected $fillable = [
        'patient_id', 'scheduled_from', 'scheduled_to', 'for_department', 'for_doctor', 'notes', 'deleted'
    ];
    protected $table = 'appointments';
}
