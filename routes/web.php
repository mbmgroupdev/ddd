<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::group(['middleware' => 'guest'], function(){
	Route::get('/', function () {
	    return view('login');
	});
});
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    //$exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('config:cache');
    //$exitCode = Artisan::call('route:cache');
    return 'DONE'; //Return anything
});

Auth::routes();



// need to modify this routes
Route::get('hr/payroll/promotion-jobs', 'Hr\Recruitment\BenefitController@promotionJobs');
Route::get('hr/payroll/increment-jobs', 'Hr\Recruitment\BenefitController@incrementJobs');
Route::get('hr/timeattendance/shift-jobs', 'Hr\TimeAttendance\ShiftRoasterController@shiftJobs');
Route::get('hr/leave/leave_status_jobs', 'Hr\TimeAttendance\LeaveWorkerController@maternityLeaveCheck');
Route::get('hr/leave/leave_status_update_jobs', 'Hr\TimeAttendance\LeaveWorkerController@LeaveStatusCheckAndUpdate');

Route::group(['middleware' => 'auth'], function(){

	Route::get('/search-employee-result', 'SearchController@searchEmp');
	Route::get('dashboard', 'DashboardController@index');
	
	//---------USER MANAGEMENT-----------//

	Route::get('users_management/users', 'Users_Management\UsersController@index')->middleware(['permission:View User']);
	Route::get('users_management/get_emp_as_pic', 'Users_Management\UsersController@getEmpAsPic');
	Route::post('users_management/user/data', 'Users_Management\UsersController@getUserData');
	Route::get('users_management/user/create', 'Users_Management\UsersController@create')->middleware(['permission:Add User']);
	Route::post('users_management/user/create', 'Users_Management\UsersController@store')->middleware(['permission:Add User']);
	Route::get('users_management/user/edit/{id}', 'Users_Management\UsersController@edit')->middleware(['permission:Manage User']);
	Route::post('users_management/user/edit/{id}', 'Users_Management\UsersController@update')->middleware(['permission:Manage User']); 
	Route::get('users_management/user/delete/{id}', 'Users_Management\UsersController@destroy')->middleware(['permission:Manage User']);
	Route::get('users_management/user/permission-assign', 'Users_Management\UsersController@permissionAssign')->middleware(['permission:Assign Permission']);
	Route::post('users_management/user/password/{id}', 'Users_Management\UsersController@userPassword')->middleware(['permission:Manage User']);
	Route::get('users_management/user/get-permission', 'Users_Management\UsersController@getPermission');
	Route::get('users_management/user/sync-permission', 'Users_Management\UsersController@syncPermission')->middleware(['permission:Assign Permission']);

	Route::get('system-all-user-dropdown-list', 'Users_Management\UsersController@userDropdown');
    Route::get('management_dropdown-list', 'Users_Management\UsersController@managementDropdown');
    Route::get('management_dropdown_list_for_edit', 'Users_Management\UsersController@managementDropdownEditPage');

	Route::get('users_management/permissions', 'Users_Management\PermissionsController@index');
	Route::post('users_management/permissions/store', 'Users_Management\PermissionsController@store');
	Route::get('users_management/permissions/edit/{id}', 'Users_Management\PermissionsController@edit');
	Route::post('users_management/permissions/edit/{id}', 'Users_Management\PermissionsController@update');
	Route::get('users_management/permissions/delete/{id}', 'Users_Management\PermissionsController@destroy');


	//Top Management list
    Route::get('users_management/top_management','Users_Management\TopManagementController@topManagement');
    Route::get('users_management/top_management_list','Users_Management\TopManagementController@topManagementList');
    Route::post('users_management/top_management','Users_Management\TopManagementController@StoreTopManagement');
    Route::get('users_management/top_management_edit/{id}','Users_Management\TopManagementController@topManagementEdit');
    Route::post('users_management/top_management_update','Users_Management\TopManagementController@updateTopManagement');
    Route::get('users_management/top_management_delete/{id}','Users_Management\TopManagementController@deleteTopManagement');
	
	
    //user dashboard
	Route::get('/', 'UserDashboardController@index');
	Route::get('user-dashboard/conversations', 'UserDashboardController@conversations');
	Route::get('user-dashboard/send-message', 'UserDashboardController@sendMessage');
	Route::get('user-dashboard/delete-message', 'UserDashboardController@deleteMessage');


	

  //user dashboard
	Route::get('dashboard', 'UserDashboardController@index')->name('user-dashboard');
	Route::post('user-dashboard/events', 'UserDashboardController@eventSettings');
	Route::get('user-dashboard/conversations', 'UserDashboardController@conversations');
	Route::get('user-dashboard/send-message', 'UserDashboardController@sendMessage');
	Route::get('user-dashboard/delete-message', 'UserDashboardController@deleteMessage');


	Route::get('/user-search', 'UserDashboardController@userSearch');
	// employee search
	Route::get('/search', 'SearchController@search');

});









Route::group(['middleware' => 'auth'], function(){
	Route::get('/', 'HomeController@index');

	Route::get('user/change-password', 'Hr\Adminstrator\UserController@password');
	Route::post('user/change-password', 'Hr\Adminstrator\UserController@changePassword');

	@include 'modules/hr.php';
});




