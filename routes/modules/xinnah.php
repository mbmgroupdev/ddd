<?php


Route::group(['prefix' => 'recruitment','namespace' => 'Recruitment'], function(){
	Route::resource('recruit', 'RecruitController');
});