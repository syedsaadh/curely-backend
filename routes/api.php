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
        Route::post('create', 'Admin\ProceduresController@store');
        Route::post('edit', 'Admin\ProceduresController@edit');
        Route::get('delete/{id}', 'Admin\ProceduresController@delete');
    });
    Route::group(['prefix' => 'roles'], function () {
        Route::get('all', 'Admin\RolesController@index');
        Route::post('create', 'Admin\RolesController@createRole');
    });
    Route::group(['prefix' => 'appointments'], function () {
        Route::get('all', 'Admin\AppointmentsController@index');
        Route::post('create', 'Admin\AppointmentsController@store');
        Route::post('edit', 'Admin\AppointmentsController@edit');
        Route::post('cancel', 'Admin\AppointmentsController@cancel');
    });
});

Route::get('labs', 'Admin\LabTestsController@index');