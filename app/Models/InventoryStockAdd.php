<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockAdd extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'inventory_id', 'quantity', 'batch_number', 'expiry', 'unit_cost', 'deleted_at', 'item_type'
    ];
    protected $dates = ['deleted_at'];
    protected $table = 'inventory_stock_add';
}
