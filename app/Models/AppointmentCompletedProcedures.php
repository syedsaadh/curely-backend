<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentCompletedProcedures extends Model
{
    protected $fillable = ['appointment_id', 'procedure_id', 'procedure_name', 'procedure_units', 'procedure_cost', 'procedure_discount', 'notes'];
    protected $hidden = ['created_at', 'updated_at'];
    public function procedure() {
        return $this->hasOne('App\Model\Procedures');
    }
}
