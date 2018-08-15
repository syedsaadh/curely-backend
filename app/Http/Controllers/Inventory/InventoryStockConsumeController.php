<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory;
use App\Models\InventoryStockAdd;
use App\Models\InventoryStockConsume;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryStockConsumeController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'stockAddId' => 'required | numeric',
            'quantity' => 'required | numeric | min:1'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $inventoryId = $request->input('stockAddId');
        $inventory = InventoryStockAdd::find($inventoryId);

        if(!$inventory) {
            return $response->getNotFound('Inventory Not Found');
        }
        $totalQuantity = DB::table('inventory_stock_consume')
            ->where('inventory_stock_add_id', $inventoryId)
        ->sum('quantity');
        $totalQuantity += $request->input('quantity');
        if($inventory->quantity < $totalQuantity) {
            return $response->getBadRequestError('Not Enough Quantity!');
        }
        $item = new InventoryStockConsume();
        $item->inventory_stock_add_id = $inventoryId;
        $item->quantity = $request->input('quantity');

        try {
            $item->save();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse('Added Stock Successfully!', ['id' => $item->id]);
    }
}
