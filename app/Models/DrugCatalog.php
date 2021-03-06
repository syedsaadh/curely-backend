<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrugCatalog extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'drug_type', 'default_dosage', 'default_dosage_unit', 'instruction'
    ];
    protected $dates = ['deleted_at'];

    protected $table = 'drug_catalog';
}
