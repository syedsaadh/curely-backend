<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryDrugCatalog extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'drug_type', 'default_dosage', 'default_dosage_unit', 'instruction'
    ];
    protected $table = 'inventory_drug_catalog';
}
