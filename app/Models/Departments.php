<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $fillable = [
        'name', 'desc', 'bed_count',
    ];

    public function users() {
        return $this->belongsToMany('App\User', 'department_user', 'department_id', 'user_id');
    }
    protected $table = 'departments';
}
