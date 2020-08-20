<?php

Route::group(['prefix' => 'hr','namespace' => 'Hr'], function(){
	Route::get('/', 'DashboardController@index');


	// Adminstrator --------------------------------------------------------
	Route::group(['prefix' => 'adminstrator','namespace' => 'Adminstrator'], function(){
		Route::get('users', 'UserController@index');
		Route::post('user/list', 'UserController@getUserList');
		Route::get('user/create', 'UserController@create');
		
		Route::get('get_emp_as_pic', 'UserController@getEmpAsPic');
		Route::post('user/store', 'UserController@store');
		Route::get('user/edit/{id}', 'UserController@edit');
		Route::post('user/update/{id}', 'UserController@update'); 
		Route::get('user/delete/{id}', 'UserController@destroy');
		Route::get('user/permission-assign', 'UserController@permissionAssign');
		Route::get('user/get-permission', 'UserController@getPermission');
		Route::get('user/sync-permission', 'UserController@syncPermission');

		Route::get('employee/search', 'UserController@employeeSearch');
		Route::get('user/search', 'UserController@userSearch');

		Route::get('role/create', 'RolesController@create');
		Route::post('role/create', 'RolesController@store');
		Route::get('role/edit/{id}', 'RolesController@edit');
		Route::post('role/edit/{id}', 'RolesController@update'); 
		Route::get('role/delete/{id}', 'RolesController@destroy');
	});

	// settings ------------------------------------------------------------
	Route::group(['prefix' => 'settings','namespace' => 'Settings'], function(){
		# unit settings
		Route::get('unit','UnitController@index');
		Route::post('unit','UnitController@store');
	});





	
	@include 'xinnah.php';
});
