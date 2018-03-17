<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'item_details_id', 'item_code', 'item_manufacturer', 'item_stocking_unit', 'item_reorder_level', 'item_retail_price', 'item_type'
    ];
    protected $table = 'inventory';
}
