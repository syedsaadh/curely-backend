<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Procedures extends Model
{
    protected $fillable = [
        'name', 'cost', 'instruction',
    ];
    protected $table = 'procedures';
}
