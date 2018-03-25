<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdCompletedProcedures extends Model
{
    protected $fillable = ['ipd_admission_id', 'procedure_id', 'procedure_name', 'procedure_units', 'procedure_cost', 'procedure_discount', 'notes', 'updated_by_user'];
    protected $hidden = ['created_at', 'updated_at'];
    public function procedure() {
        return $this->hasOne('App\Model\Procedures');
    }
}
