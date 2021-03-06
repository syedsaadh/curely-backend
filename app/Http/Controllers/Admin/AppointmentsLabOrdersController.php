<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppointmentLabOrders;
use App\Models\Appointments;
use App\Models\LabTests;
use App\Models\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AppointmentsLabOrdersController extends Controller
{
    public function store(Request $request)
    {
        $response = new Response();
        $validator = Validator::make($request->all(), [
            'appointmentId' => 'required',
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
        $appointmentId = $request->input('appointmentId');
        $appointment = Appointments::find($appointmentId);
        if (!$appointment) {
            return $response->getNotFound('Appointment Id Not Found');
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
                AppointmentLabOrders::find($item['id'])->delete();
            } catch (QueryException $e) {
            }
        }
        foreach ($toUpdate as $item) {
            try {
                $it = AppointmentLabOrders::find($item['id']);
                if ($it) {
                    $it->lab_test_name = $item['name'];
                    $it->instruction = $item['instruction'];
                    $it->save();
                }
            } catch (QueryException $e) {
            }
        }
        foreach ($toInsert as $item) {
            try {
                $it = new AppointmentLabOrders();
                $test = LabTests::find($item['lab_test_id']);
                if ($test) {
                    $it->appointment_id = $appointmentId;
                    $it->lab_test_id = $test->id;
                    $it->lab_test_name = $test->name;
                    $it->instruction = $item['instruction'];
                    $it->save();
                }
            } catch (QueryException $e) {
            }
        }
        return $response->getSuccessResponse('Success!');
    }
    public function delete($appointmentId) {
        $response = new Response();
        $tp = AppointmentLabOrders::where('appointment_id', '=', $appointmentId);
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
