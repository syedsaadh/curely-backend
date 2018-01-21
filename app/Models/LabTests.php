<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTests extends Model
{
    protected $fillable = [
        'name', 'description',
    ];
    protected $table = 'lab_tests';
}
