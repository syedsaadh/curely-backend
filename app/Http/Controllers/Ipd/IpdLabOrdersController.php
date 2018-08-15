<?php

namespace App\Http\Controllers\Ipd;

use App\Models\AppointmentLabOrders;
use App\Models\Appointments;
use App\Models\IpdAdmissionVisit;
use App\Models\IpdLabOrders;
use App\Models\LabTests;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class IpdLabOrdersController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'visitId' => 'required',
            'lab_tests' => 'present|array',
            'lab_tests.*.lab_test_id' => 'required|numeric',
            'lab_tests.*.name' => 'required|string',
            'lab_tests.*.instruction' => 'present|string|nullable',
            'lab_tests.*.id' => 'numeric|nullable',
            'lab_tests.*.delete' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return $response->getValidationError($validator->messages());
        }
        $labtests = $request->json('lab_tests');
        $visitId = $request->input('visitId');
        $visit = IpdAdmissionVisit::find($visitId);
        if (!$visit) {
            return $response->getNotFound('Visit Id Not Found');
        }
        $toDelete = array_filter($labtests, function ($el) {
            return $el['delete'] == true && array_key_exists('id', $el);
        });
        $toInsert = array_filter($labtests, function ($el) {
            return $el['delete'] == false && $el['id'] === null;
        });
        $toUpdate = array_filter($labtests, function ($el) {
            return $el['delete'] == false && array_key_exists('id', $el);
        });
        foreach ($toDelete as $item) {
            try {
                IpdLabOrders::find($item['id'])->delete();
            } catch (QueryException $e) {
            }
        }
        foreach ($toUpdate as $item) {
            try {
                $it = IpdLabOrders::find($item['id']);
                if ($it) {
                    $it->lab_test_name = $item['name'];
                    $it->instruction = $item['instruction'];
                    $it->updated_by_user = $request->user()->id;

                    $it->save();
                }
            } catch (QueryException $e) {
            }
        }
        foreach ($toInsert as $item) {
            try {
                $it = new IpdLabOrders();

                $test = LabTests::find($item['lab_test_id']);
                if ($test) {
                    $it->ipd_admission_visit_id = $visitId;
                    $it->lab_test_id = $test->id;
                    $it->lab_test_name = $test->name;
                    $it->instruction = $item['instruction'];
                    $it->updated_by_user = $request->user()->id;

                    $it->save();
                }
            } catch (QueryException $e) {
                dd($e);
            }
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($visitId) {
        $response = new Response();
        $tp = IpdLabOrders::where('ipd_admission_visit_id', '=', $visitId);
        if (!$tp) {
            return $response->getNotFound('Not Found');
        }
        try {
            $tp->delete();
        } catch (QueryException $e) {
            return $response->getUnknownError($e);
        }
        return $response->getSuccessResponse();
    }
}
