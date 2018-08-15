<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdLabOrders extends Model
{
    protected $fillable = ['ipd_admission_visit_id', 'lab_test_id', 'lab_test_name', 'instruction', 'updated_by_user'];
    protected $hidden = ['created_at', 'updated_at'];
    public function test() {
        return $this->hasOne('App\Model\LabTests');
    }
}
