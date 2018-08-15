<?php

namespace App\Http\Controllers\Inventory;

use App\Models\DrugCatalog;
use App\Models\Inventory;
use App\Models\InventoryDrugCatalog;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function index()
    {
        $response = new Response();
        $data = Inventory::all();
        foreach ($data as $inventory) {
            $drug = null;
            if ($inventory->item_type === 'drug') {
                $drug = InventoryDrugCatalog::find($inventory->item_details_id);
                $inventory->name = $drug->name;
            }
            $inventory->drug = $drug;
        }
        return $response->getSuccessResponse('Success!', $data);
    }
    public function getById($id)
    {
        $response = new Response();
        $data = Inventory::find($id);
        if (!$data) {
            return $response->getNotFound();
        }
        $drug = InventoryDrugCatalog::find($data->item_details_id);
        $data->name = $drug->name;
        $data->drug = $drug;
        return $response->getSuccessResponse('Success!', $data);
    }
    public function store(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'name' => 'required | string',
            'code' => 'string | nullable',
            'manufacturer' => 'string | nullable',
            'stockingUnit' => 'string | required',
            'reorderLevel' => 'integer | nullable',
            'price' => 'numeric | nullable',
            'itemType' => 'string | required',
            'itemTypePrefix' => 'string | nullable',
            'strength' => 'string | nullable',
            'strengthUnit' => 'string | nullable',
            'instruction' => 'string | nullable'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }

        $itemType = $request->input('itemType');
        $drugId = null;
        if ($itemType === 'drug') {
            $drug = new InventoryDrugCatalog();
            $drug->name = $request->input('name');
            $drug->drug_type = $request->input('itemTypePrefix');
            $drug->default_dosage = $request->input('strength');
            $drug->default_dosage_unit = $request->input('strengthUnit');
            $drug->instruction = $request->input('instruction');
            $drug->created_by_user = $request->user()->id;
            $drug->updated_by_user = $request->user()->id;
            try {
                $drug->save();
                $drugId = $drug->id;
            } catch (QueryException $e) {
                return $response->getUnknownError('Error Creating Inventory!');
            }
        }
        $item = new Inventory();
        $item->item_details_id = $drugId;
        $item->item_code = $request->input('code');
        $item->item_manufacturer = $request->input('manufacturer');
        $item->item_stocking_unit = $request->input('stockingUnit');
        $item->item_reorder_level = $request->input('reorderLevel');
        $item->item_retail_price = $request->input('price');
        $item->item_type = $request->input('itemType');
        $item->created_by_user = $request->user()->id;
        $item->updated_by_user = $request->user()->id;
        try {
            $item->save();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse('Created Inventory Successfully!', ['id' => $item->id]);
    }

    public function edit(Request $request)
    {
        $response = new Response();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'itemDetailsId' => 'required',
            'prevItemType' => 'string | required',
            'name' => 'required | string',
            'code' => 'string | nullable',
            'manufacturer' => 'string | nullable',
            'stockingUnit' => 'string | nullable',
            'reorderLevel' => 'integer | nullable',
            'price' => 'numeric | nullable',
            'itemType' => 'string | required',
            'itemTypePrefix' => 'string | nullable',
            'strength' => 'string | nullable',
            'strengthUnit' => 'string | nullable',
            'instruction' => 'string | nullable'
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $inventoryId = $request->input('id');
        $itemDetailsId = $request->input('itemDetailsId');
        $inventory = Inventory::find($inventoryId);
        if (!$inventory) {
            return $response->getNotFound();
        }
        $itemType = $request->input('itemType');
        $prevItemType = $request->input('prevItemType');
        $drugId = null;
        if ($prevItemType != $itemType) {
            if ($prevItemType === 'drug') {
                $drug = InventoryDrugCatalog::find($itemDetailsId);
                try {
                    $drug->delete();
                } catch (QueryException $e) {
                    return $response->getUnknownError('Error Editing!');
                }
            }
        }
        if ($itemType === 'drug') {
            $drug = InventoryDrugCatalog::find($itemDetailsId);
            if (!$drug) $drug = new InventoryDrugCatalog();
            $drug->name = $request->input('name');
            $drug->drug_type = $request->input('itemTypePrefix');
            $drug->default_dosage = $request->input('strength');
            $drug->default_dosage_unit = $request->input('strengthUnit');
            $drug->instruction = $request->input('instruction');
            try {
                $drug->save();
                $drugId = $drug->id;
            } catch (QueryException $e) {
                return $response->getUnknownError('Error Creating Inventory!');
            }
        }
        $inventory->item_details_id = $drugId;
        $inventory->item_code = $request->input('code');
        $inventory->item_manufacturer = $request->input('manufacturer');
        $inventory->item_stocking_unit = $request->input('stockingUnit');
        $inventory->item_reorder_level = $request->input('reorderLevel');
        $inventory->item_retail_price = $request->input('price');
        $inventory->item_type = $request->input('itemType');
        $inventory->updated_by_user = $request->user()->id;
        try {
            $inventory->save();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Creating Inventory!');
        }
        return $response->getSuccessResponse('Edited Inventory Successfully!', ['id' => $inventory->id]);
    }

    public function delete($id)
    {
        $response = new Response();
        $inventory = Inventory::find($id);
        $drug = null;
        if (!$inventory) {
            return $response->getNotFound(  'Inventory Not Found');
        }
        if ($inventory->item_type === 'drug') {
            $drug = InventoryDrugCatalog::find($inventory->item_details_id);
        }
        try {
            if ($drug) $drug->delete();
            $inventory->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError('Error Deleting Inventory!');
        }
        return $response->getSuccessResponse();
    }
}
