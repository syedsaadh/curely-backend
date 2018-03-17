<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugCatalog extends Model
{
    protected $fillable = [
        'name', 'drug_type', 'default_dosage', 'default_dosage_unit', 'instruction'
    ];
    protected $table = 'drug_catalog';
}
