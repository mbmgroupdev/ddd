<?php


Route::group(['prefix' => 'recruitment','namespace' => 'Recruitment'], function(){
	Route::resource('recruit', 'RecruitController');
	Route::post('first-step-recruitment', 'RecruitController@basicRecruitStore');
	Route::post('second-step-recruitment', 'RecruitController@medicalRecruitStore');
});

Route::get('employee-type-wise-designation/{id}', 'Common\EmployeeAttributeController@getDesignation');
Route::get('area-wise-department/{id}', 'Common\EmployeeAttributeController@getDepartment');
Route::get('department-wise-section/{id}', 'Common\EmployeeAttributeController@getSection');
Route::get('section-wise-subsection/{id}', 'Common\EmployeeAttributeController@getSubSection');