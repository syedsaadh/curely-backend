<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdVitalSigns extends Model
{
    protected $table = 'ipd_vital_signs';
    public function appointment()
    {
        return $this->belongsTo('Appointment');
    }
    public function fields() {
        return $this->hasMany('\App\Models\IpdVitalSignsValue', 'ipd_vital_signs_id', 'id');
    }
}
