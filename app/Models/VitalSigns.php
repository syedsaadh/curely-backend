<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VitalSigns extends Model
{
    protected $fillable = [
        'name', 'desc', 'unit', 'column_name'
    ];
    protected $table = 'vital_signs';
}
