<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockConsume extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'inventory_stock_add_id', 'quantity'
    ];
    protected $dates = ['deleted_at'];
    protected $table = 'inventory_stock_consume';
}
