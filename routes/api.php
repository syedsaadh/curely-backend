<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('authenticate', 'Auth\JWTLoginController@authenticate');
//Route::get('profile', 'Admin\UsersController@getProfile')->middleware('auth.jwt');

Route::group(['prefix' => 'users'], function () {
    Route::post('login', 'Auth\JWTLoginController@authenticate');
    Route::group(['prefix' => 'profile', 'middleware' => 'auth.jwt'], function () {
        Route::get('/', 'Admin\UsersController@getProfile');
        Route::post('/update', 'Admin\UsersController@updateProfile');
        Route::post('/changepassword', 'Auth\JWTLoginController@changePassword');
    });
});
Route::group(['prefix' => 'admin', 'middleware' => ['ability:admin']], function () {
    Route::group(['prefix' => 'patients'], function () {
        Route::get('all', 'Admin\PatientsController@index');
        Route::get('search/{q}', 'Admin\PatientsController@search');
        Route::get('get/{id}', 'Admin\PatientsController@getPatientById');
        Route::post('create', 'Admin\PatientsController@store');
        Route::post('edit', 'Admin\PatientsController@edit');
        Route::get('delete/{id}', 'Admin\PatientsController@delete');

    });
    Route::group(['prefix' => 'users'], function () {
        Route::post('login', 'Auth\JWTLoginController@authenticate');
        Route::get('all', 'Admin\UsersController@index');
        Route::get('doctors', 'Admin\UsersController@getDoctors');
        Route::get('admins', 'Admin\UsersController@getAdmins');
        Route::get('staffs', 'Admin\UsersController@getStaffs');
        Route::post('createStaff', 'Admin\UsersController@createAndAssignRole');
        Route::post('editStaff', 'Admin\UsersController@editStaff');
        Route::post('assignRole', 'Admin\UsersController@assignRole');
    });
    Route::group(['prefix' => 'departments'], function () {
        Route::get('all', 'Admin\DepartmentsController@index');
        Route::post('create', 'Admin\DepartmentsController@store');
        Route::post('edit', 'Admin\DepartmentsController@edit');
        Route::get('delete/{id}', 'Admin\DepartmentsController@delete');
    });
    Route::group(['prefix' => 'labtests'], function () {
        Route::get('all', 'Admin\LabTestsController@index');
        Route::post('create', 'Admin\LabTestsController@store');
        Route::get('search/{q}', 'Admin\LabTestsController@search');
        Route::post('edit', 'Admin\LabTestsController@edit');
        Route::get('delete/{id}', 'Admin\LabTestsController@delete');
    });
    Route::group(['prefix' => 'procedures'], function () {
        Route::get('all', 'Admin\ProceduresController@index');
        Route::get('search/{q}', 'Admin\ProceduresController@search');
        Route::post('create', 'Admin\ProceduresController@store');
        Route::post('edit', 'Admin\ProceduresController@edit');
        Route::get('delete/{id}', 'Admin\ProceduresController@delete');
    });
    Route::group(['prefix' => 'drugs'], function () {
        Route::get('all', 'Admin\DrugCatalogController@index');
        Route::get('search/{q}', 'Admin\DrugCatalogController@search');
        Route::post('create', 'Admin\DrugCatalogController@store');
        Route::post('edit', 'Admin\DrugCatalogController@edit');
        Route::get('delete/{id}', 'Admin\DrugCatalogController@delete');
    });
    Route::group(['prefix' => 'roles'], function () {
        Route::get('all', 'Admin\RolesController@index');
        Route::post('create', 'Admin\RolesController@createRole');
    });
    Route::group(['prefix' => 'appointment'], function () {
        Route::get('all', 'Admin\AppointmentsController@index');
        Route::get('get', 'Admin\AppointmentsController@get');
        Route::get('get/{id}', 'Admin\AppointmentsController@getById');
        Route::post('create', 'Admin\AppointmentsController@store');
        Route::post('edit', 'Admin\AppointmentsController@edit');
        Route::post('cancel', 'Admin\AppointmentsController@cancelAppointment');
        Route::post('vitalsigns/update', 'Admin\AppointmentsVitalSignsController@store');
        Route::get('vitalsigns/delete/{appointmentId}', 'Admin\AppointmentsVitalSignsController@delete');
        Route::post('clinicalnotes/update', 'Admin\AppointmentsClinicalNotesController@store');
        Route::get('clinicalnotes/delete/{appointmentId}', 'Admin\AppointmentsClinicalNotesController@delete');
        Route::post('completedprocedures/update', 'Admin\AppointmentsCompletedProceduresController@store');
        Route::get('completedprocedures/delete/{appointmentId}', 'Admin\AppointmentsCompletedProceduresController@delete');
        Route::post('treatmentplans/update', 'Admin\AppointmentsTreatmentPlansController@store');
        Route::get('treatmentplans/delete/{appointmentId}', 'Admin\AppointmentsTreatmentPlansController@delete');
        Route::post('laborders/update', 'Admin\AppointmentsLabOrdersController@store');
        Route::get('laborders/delete/{appointmentId}', 'Admin\AppointmentsLabOrdersController@delete');
        Route::post('prescriptions/update', 'Admin\AppointmentsPrescriptionController@store');
        Route::get('prescriptions/delete/{appointmentId}', 'Admin\AppointmentsPrescriptionController@delete');
    });
    Route::group(['prefix' => 'ipd'], function () {
        Route::get('all', 'Ipd\IpdAdmissionController@index');
        Route::get('get', 'Ipd\IpdAdmissionController@get');
        Route::get('get/{id}', 'Ipd\IpdAdmissionController@getById');
        Route::get('getAdmissionsVisits/{id}', 'Ipd\IpdController@getAllAdmissionVisitsByPatientId');
        Route::get('getAvailableBeds/{deptId}', 'Ipd\IpdAdmissionController@getAvailableBeds');
        Route::post('create', 'Ipd\IpdAdmissionController@store');
        Route::post('edit', 'Ipd\IpdAdmissionController@edit');
        Route::post('delete', 'Ipd\IpdAdmissionController@deleteAdmission');
        Route::post('discharge', 'Ipd\IpdAdmissionController@dischargeAdmission');
        Route::post('vitalsigns/update', 'Ipd\IpdVitalSignsController@store');
        Route::get('vitalsigns/delete/{id}', 'Ipd\IpdVitalSignsController@delete');
        Route::post('clinicalnotes/update', 'Ipd\IpdClinicalNotesController@store');
        Route::get('clinicalnotes/delete/{id}', 'Ipd\IpdClinicalNotesController@delete');
        Route::post('completedprocedures/update', 'Ipd\IpdCompletedProceduresController@store');
        Route::get('completedprocedures/delete/{id}', 'Ipd\IpdCompletedProceduresController@delete');
        Route::post('treatmentplans/update', 'Ipd\IpdTreatmentPlansController@store');
        Route::get('treatmentplans/delete/{id}', 'Ipd\IpdTreatmentPlansController@delete');
        Route::post('laborders/update', 'Ipd\IpdLabOrdersController@store');
        Route::get('laborders/delete/{id}', 'Ipd\IpdLabOrdersController@delete');
        Route::post('prescriptions/update', 'Ipd\IpdPrescriptionController@store');
        Route::get('prescriptions/delete/{id}', 'Ipd\IpdPrescriptionController@delete');

        Route::get('admitted/{department}', 'Ipd\IpdAdmissionController@getAdmittedPatientsByDept');
        Route::get('visit/get/{id}', 'Ipd\IpdVisitController@get');
        Route::post('visit/add', 'Ipd\IpdVisitController@addVisit');
        Route::post('visit/edit', 'Ipd\IpdVisitController@edit');
        Route::get('visit/delete/{id}', 'Ipd\IpdVisitController@delete');

    });
    Route::group(['prefix' => 'vitalsigns'], function () {
        Route::get('all', 'Admin\VitalSignsController@index');
        Route::post('create', 'Admin\VitalSignsController@store');
        Route::post('edit', 'Admin\VitalSignsController@edit');
        Route::get('delete/{id}', 'Admin\VitalSignsController@delete');
    });
    Route::group(['prefix' => 'inventory'], function () {
        Route::get('all', 'Inventory\InventoryController@index');
        Route::post('create', 'Inventory\InventoryController@store');
        Route::post('edit', 'Inventory\InventoryController@edit');
        Route::get('delete/{id}', 'Inventory\InventoryController@delete');
        Route::get('get/{id}', 'Inventory\InventoryController@getById');
        Route::post('stock/add', 'Inventory\InventoryStockAddController@store');
        Route::post('stock/consume', 'Inventory\InventoryStockConsumeController@store');

    });
});