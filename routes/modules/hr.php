<?php

Route::group(['prefix' => 'hr','namespace' => 'Hr'], function(){
	Route::get('/', 'DashboardController@index');
	
	@include 'xinnah.php';
});
