<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLabOrders extends Model
{
    protected $fillable = ['appointment_id', 'lab_test_id', 'lab_test_name', 'instruction'];
    protected $hidden = ['created_at', 'updated_at'];
    public function test() {
        return $this->hasOne('App\Model\LabTests');
    }
}
