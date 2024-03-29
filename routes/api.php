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

/* User */
Route::get('users/leave', 'User\UserController@leave_count')->middleware('auth:api');
Route::get('users/absent', 'User\UserController@absent_count')->middleware('auth:api');
Route::get('users/active', 'User\UserController@active_count')->middleware('auth:api');
Route::get('users/list/all', 'User\UserController@list')->middleware('auth:api');
Route::get('users', 'User\UserController@index')->middleware('auth:api');
Route::get('users/recently', 'User\UserController@recently')->middleware('auth:api');
Route::get('users/{id}','User\UserController@show')->middleware('auth:api');
Route::post('users', 'User\UserController@store')->middleware('auth:api');
Route::put('users/{id}', 'User\UserController@update')->middleware('auth:api');
Route::delete('users/{id}', 'User\UserController@destroy')->middleware('auth:api');
Route::get('users/verify/{token}', 'User\UserController@verify');
Route::get('users/token/{token}','User\UserController@show_from_token');
Route::post('users/forgot_password', 'User\UserController@forgotPassword');
Route::post('users/reset_password', 'User\UserController@resetPassword');
Route::post('users/login', 'User\UserController@login');
Route::post('users/mobile/login', 'User\UserController@mobile_login');
Route::post('users/update_password/{id}', 'User\UserController@update_password')->middleware('auth:api');

//Devices
Route::get('devices/', 'Device\DeviceController@index')->middleware('auth:api');
Route::get('devices/{id}','Device\DeviceController@show')->middleware('auth:api');
Route::post('devices/', 'Device\DeviceController@store')->middleware('auth:api');
Route::put('devices/{id}', 'Device\DeviceController@update')->middleware('auth:api');
Route::delete('devices/{id}', 'Device\DeviceController@destroy')->middleware('auth:api');

//Departments
Route::get('departments/', 'Department\DepartmentController@index')->middleware('auth:api');
Route::get('departments/{id}','Department\DepartmentController@show')->middleware('auth:api');
Route::post('departments/', 'Department\DepartmentController@store')->middleware('auth:api');
Route::put('departments/{id}', 'Department\DepartmentController@update')->middleware('auth:api');
Route::delete('departments/{id}', 'Department\DepartmentController@destroy')->middleware('auth:api');

//Holiday
Route::get('holidays/', 'Holiday\HolidayController@index')->middleware('auth:api');
Route::get('holidays/{id}','Holiday\HolidayController@show')->middleware('auth:api');
Route::post('holidays/', 'Holiday\HolidayController@store')->middleware('auth:api');
Route::put('holidays/{id}', 'Holiday\HolidayController@update')->middleware('auth:api');
Route::delete('holidays/{id}', 'Holiday\HolidayController@destroy')->middleware('auth:api');

//Leaves
Route::get('leaves/', 'Leave\LeaveController@index')->middleware('auth:api');
Route::get('leaves/{id}','Leave\LeaveController@show')->middleware('auth:api');
Route::post('leaves/', 'Leave\LeaveController@store')->middleware('auth:api');
Route::put('leaves/{id}', 'Leave\LeaveController@update')->middleware('auth:api');
Route::delete('leaves/{id}', 'Leave\LeaveController@destroy')->middleware('auth:api');

//Leave Types
Route::get('leave_types/', 'Leave\LeaveTypeController@index')->middleware('auth:api');
Route::get('leave_types/{id}','Leave\LeaveTypeController@show')->middleware('auth:api');
Route::post('leave_types/', 'Leave\LeaveTypeController@store')->middleware('auth:api');
Route::put('leave_types/{id}', 'Leave\LeaveTypeController@update')->middleware('auth:api');
Route::delete('leave_types/{id}', 'Leave\LeaveTypeController@destroy')->middleware('auth:api');

//Records
Route::get('records/recently', 'Record\RecordController@recently')->middleware('auth:api');
Route::get('records/', 'Record\RecordController@index')->middleware('auth:api');
Route::get('records/{id}','Record\RecordController@show')->middleware('auth:api');
Route::post('records/', 'Record\RecordController@store')->middleware('auth:api');
Route::post('records/clock', 'Record\RecordController@manual_clock');
Route::post('records/mobile', 'Record\RecordController@mobile_clock');

//Reports
Route::post('reports/absentee', 'Report\ReportController@absentee')->middleware('auth:api');
Route::post('reports/attendance','Report\ReportController@attendance')->middleware('auth:api');
Route::post('reports/base', 'Report\ReportController@base')->middleware('auth:api');
Route::post('reports/leave', 'Report\ReportController@leave')->middleware('auth:api');
Route::post('reports/map', 'Report\ReportController@map')->middleware('auth:api');

//Access Control AND User Group
Route::get('user_groups', 'User\UserGroupController@index')->middleware('auth:api');
Route::get('user_groups/{id}','User\UserGroupController@show')->middleware('auth:api');
Route::post('user_groups', 'User\UserGroupController@store')->middleware('auth:api');
Route::put('user_groups/{id}', 'User\UserGroupController@update')->middleware('auth:api');
Route::delete('user_groups/{id}', 'User\UserGroupController@destroy')->middleware('auth:api');