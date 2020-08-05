<?php

Route::group(['prefix' => 'hr','namespace' => 'Hr'], function(){
	Route::get('/', 'DashboardController@index');






	// settings ------------------------------------------------------------
	Route::group(['prefix' => 'settings','namespace' => 'Settings'], function(){
		# unit settings
		Route::get('unit','UnitController@index');
		Route::post('unit','UnitController@store');
	});


	
	@include 'xinnah.php';
});
