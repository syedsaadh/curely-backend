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
Route::group(['prefix' => 'users'], function () {
    Route::post('login', 'Auth\JWTLoginController@authenticate');
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
    });
    Route::group(['prefix' => 'vitalsigns'], function () {
        Route::get('all', 'Admin\VitalSignsController@index');
        Route::post('create', 'Admin\VitalSignsController@store');
        Route::post('edit', 'Admin\VitalSignsController@edit');
        Route::get('delete/{id}', 'Admin\VitalSignsController@delete');
    });
    Route::group(['prefix' => 'inventory'], function () {
        Route::get('all', 'Admin\InventoryController@index');
        Route::post('create', 'Admin\InventoryController@store');
        Route::post('edit', 'Admin\InventoryController@edit');
        Route::get('delete/{id}', 'Admin\InventoryController@delete');
        Route::get('get/{id}', 'Admin\InventoryController@getById');
    });
});