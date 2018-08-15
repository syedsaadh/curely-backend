<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory;
use App\Models\InventoryStockAdd;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class InventoryStockAddController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'inventoryId' => 'required | numeric',
            'quantity' => 'required | numeric | min:1',
            'batchNumber' => 'string | nullable',
            'expiry' => 'date | nullable',
            'unitCost' => 'required | numeric'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $inventoryId = $request->input('inventoryId');
        $inventory = Inventory::find($inventoryId);
        if(!$inventory) {
            return $response->getNotFound('Inventory Not Found');
        }
        $item = new InventoryStockAdd();
        $item->inventory_id = $inventoryId;
        $item->quantity = $request->input('quantity');
        $item->batch_number = $request->input('batchNumber');
        $item->expiry = $request->input('expiry');
        $item->unit_cost = $request->input('unitCost');

        try {
            $item->save();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse('Added Stock Successfully!', ['id' => $item->id]);
    }
}
