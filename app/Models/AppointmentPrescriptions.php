<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentPrescriptions extends Model
{
    use SoftDeletes;
    protected $fillable = ['appointment_id', 'drug_id', 'intake', 'frequency', 'display_frequency',
        'food_precedence', 'duration', 'duration_unit', 'morning_units', 'afternoon_units', 'night_units',
        'instruction', 'created_by_user_id', 'updated_by_user_id', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function drug()
    {
        return $this->hasOne('App\Models\DrugCatalog', 'id', 'drug_id');
    }
}
