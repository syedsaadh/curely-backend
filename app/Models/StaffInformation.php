<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StaffInformation extends Model
{
    //use Searchable; Algolia and Scout
    protected $hidden = ['created_at'];
    protected $table = 'staff_information';
}
