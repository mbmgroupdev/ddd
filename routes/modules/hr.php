<?php

Route::group(['prefix' => 'hr','namespace' => 'Hr'], function(){
	Route::get('/', 'DashboardController@index');


	// Adminstrator 
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
		Route::get('all-employee/search', 'UserController@allEmployeeSearch');
		Route::get('employee/female-associates', 'UserController@femaleSearch');
		Route::get('user/search', 'UserController@userSearch');

		Route::get('roles', 'RolesController@index');
		Route::get('role/create', 'RolesController@create');
		Route::post('role/store', 'RolesController@store');
		Route::get('role/edit/{id}', 'RolesController@edit');
		Route::post('role/edit/{id}', 'RolesController@update'); 
		Route::get('role/delete/{id}', 'RolesController@destroy');
		Route::get('role/sync-permission', 'RolesController@syncPermission');
	});

	// settings 
	Route::group(['prefix' => 'settings','namespace' => 'Settings'], function(){
		# unit settings
		Route::get('unit','UnitController@index');
		Route::post('unit','UnitController@store');
	});

	Route::group(['prefix' => 'recruitment','namespace' => 'Recruitment'], function(){
		Route::resource('recruit', 'RecruitController');
		Route::post('recruit/{id}/update', 'RecruitController@update');

		Route::get('recruit-bulk-upload', 'RecruitController@bulk');
		Route::get('recruit-data-list', 'RecruitController@list');
		Route::post('first-step-recruitment', 'RecruitController@basicRecruitStore');
		Route::post('second-step-recruitment', 'RecruitController@medicalRecruitStore');
		Route::post('update-step-recruitment/{type}', 'RecruitController@recruitUpdate');
	});

	// common routes
	Route::get('employee-type-wise-designation/{id}', 'Common\EmployeeAttributeController@getDesignation');
	Route::get('area-wise-department/{id}', 'Common\EmployeeAttributeController@getDepartment');
	Route::get('department-wise-section/{id}', 'Common\EmployeeAttributeController@getSection');
	Route::get('section-wise-subsection/{id}', 'Common\EmployeeAttributeController@getSubSection');


});



Route::get('menu-change', 'DashboardController@menu');

Route::get('hr/buyermode/generate_buyer_mode', 'Hr\BuyerMode\BmodeGenerateController@generateBuyerMode');
Route::post('hr/buyermode/generate_buyer_mode_data', 'Hr\BuyerMode\BmodeGenerateController@generateBuyerModeData');
Route::get('hr/buyermode/get_employees', 'Hr\BuyerMode\BmodeGenerateController@getEmployees');

	

// employee show
Route::get('hr/recruitment/employee/show/{associate_id?}', 'Hr\Recruitment\EmployeeController@show');
Route::get('hr/user-dashboard', 'Hr\DashboardController@userDashboard');
Route::post('hr/user-dashboard/events', 'Hr\DashboardController@eventSettings');
Route::get('hr/user/pdf', 'Hr\ProfileController@employeeProfile');
Route::get('hr/user/attendance_calendar/{as_id}', 'Hr\ProfileController@attendanceCalendar');
Route::post('hr/employee-status-update', 'Hr\Recruitment\EmployeeController@statusUpdate');

Route::get('hr/get-associates-by', 'Hr\Recruitment\EmployeeController@getAssociateBy');

Route::get('hr/associate-info', 'Hr\Operation\LocationChangeController@getUnit');


Route::get('hr/reports/salary-sheet-custom-individual-search', 'Hr\Reports\SalarySheetCustomController@individualSearch');
Route::get('hr/reports/salary-sheet-custom-individual-search-buyer', 'Hr\BuyerMode\BmodeSalarySheetController@individualSearch');




	Route::get('hr/over-time-report-chart', 'Hr\DashboardController@overTimeChart');
	Route::get('hr/unit-wise-employee-summary', 'Hr\DashboardController@unitWiseEmpSummary');
	Route::get('hr/attendance-chart', 'Hr\DashboardController@attendanceChart');


	//user dashboard demo

	//---------Time and Attendence-----------//

	Route::group(['prefix' => 'hr/search/','namespace' => 'Hr\Search', 'middleware' => ['permission:Query']], function(){

		Route::get('/', 'SearchController@hrSearch');

		// attendance
		Route::get('hr_att_search', 'AttendaceSearchController@hrAttSearch');
		Route::get('hr_att_search_unit', 'AttendaceSearchController@hrAttSearchUnit');
		Route::get('hr_att_search_area', 'AttendaceSearchController@hrAttSearchArea');
		Route::get('hr_att_search_department', 'AttendaceSearchController@hrAttSearchDepartment');
		Route::get('hr_att_search_floor', 'AttendaceSearchController@hrAttSearchFloor');
		Route::get('hr_att_search_section', 'AttendaceSearchController@hrAttSearchSection');
		Route::get('hr_att_search_subsection', 'AttendaceSearchController@hrAttSearchSubSection');
		Route::get('hr_att_search_employee', 'AttendaceSearchController@hrAttSearchEmployee');
		Route::get('hr_att_search_allemp', 'AttendaceSearchController@hrAttSearchAllEmployee');
		Route::get('hr_att_search_allemp_data', 'AttendaceSearchController@hrAttSearchAllEmpData');
		Route::get('hr_att_search_emp_data', 'AttendaceSearchController@hrAttSearchEmpData');
		Route::get('hr_att_search_emp_count', 'AttendaceSearchController@hrGetEmpAttCount');
		Route::get('single_emp/{associate_id}/{month}', 'AttendaceSearchController@hrAttSearchSingleEmpData');

		// all employee
		Route::get('hr_all_employee_search', 'AllEmployeeSearchController@hrAllEmpSearch');
		Route::get('hr_all_employee_search_unit', 'AllEmployeeSearchController@hrAllEmpSearchUnit');
		Route::get('hr_all_employee_search_area', 'AllEmployeeSearchController@hrAllEmpSearchArea');
		Route::get('hr_all_employee_search_department', 'AllEmployeeSearchController@hrAllEmpSearchDepartment');
		Route::get('hr_all_employee_search_floor', 'AllEmployeeSearchController@hrAllEmpSearchFloor');
		Route::get('hr_all_employee_search_section', 'AllEmployeeSearchController@hrAllEmpSearchSection');
		Route::get('hr_all_employee_search_subsection', 'AllEmployeeSearchController@hrAllEmpSearchSubSection');
		Route::get('hr_all_employee_search_employee', 'AllEmployeeSearchController@hrAllEmpSearchEmployee');
		Route::get('hr_all_employee_search_allemp', 'AllEmployeeSearchController@hrAllEmpSearchAllEmployee');
		Route::get('hr_all_employee_search_allemp_data', 'AllEmployeeSearchController@hrAllEmpSearchAllEmpData');
		Route::get('hr_all_employee_search_emp_data', 'AllEmployeeSearchController@hrAllEmpSearchEmpData');
		Route::get('hr_all_employee_search_emp_count', 'AllEmployeeSearchController@hrGetEmpAttCount');
		Route::get('single_emp_all/{associate_id}/{month}', 'AllEmployeeSearchController@hrAllEmpSearchSingleEmpData');

		Route::get('hr_all_employee_search_unit_floor', 'AllEmployeeSearchController@hrGetEmpUnitFloor');
		Route::get('hr_all_employee_search_unit_line', 'AllEmployeeSearchController@hrGetEmpUnitLine');
		Route::get('hr_all_employee_search_floor_line', 'AllEmployeeSearchController@hrGetEmpFloorLine');
		Route::get('hr_all_employee_search_all_employee', 'AllEmployeeSearchController@hrGetEmpAllEmployee');
		Route::get('hr_all_employee_show_emp_data', 'AllEmployeeSearchController@hrAllEmpShowEmpData');

		// print section
		Route::get('hr_att_searchPrint', 'AttendaceSearchController@hrAttSearchPrint');
		Route::get('hr_att_search_unitPrint', 'AttendaceSearchController@hrAttSearchUnitPrint');
		Route::get('hr_att_search_areaPrint', 'AttendaceSearchController@hrAttSearchAreaPrint');
		Route::get('hr_att_search_departmentPrint', 'AttendaceSearchController@hrAttSearchDepartmentPrint');
		Route::get('hr_att_search_floorPrint', 'AttendaceSearchController@hrAttSearchFloorPrint');
		Route::get('hr_att_search_sectionPrint', 'AttendaceSearchController@hrAttSearchSectionPrint');
		Route::get('hr_att_search_subsectionPrint', 'AttendaceSearchController@hrAttSearchSubSectionPrint');

		Route::get('hr_att_emp_count', 'AttendaceSearchController@hrAttEmpCount');
		Route::get('testf', 'AttendaceSearchController@testf');


		
		Route::get('hr_salary_search', 'SalarySearchController@hrSalarySearch');
		Route::get('hr_salary_search_unit', 'SalarySearchController@hrSalarySearchUnit');
		Route::get('hr_salary_search_area', 'SalarySearchController@hrSalarySearchArea');
		Route::get('hr_salary_search_department', 'SalarySearchController@hrSalarySearchDepartment');
		Route::get('hr_salary_search_floor', 'SalarySearchController@hrSalarySearchFloor');
		Route::get('hr_salary_search_section', 'SalarySearchController@hrSalarySearchSection');
		Route::get('hr_salary_search_subsection', 'SalarySearchController@hrSalarySearchSubscetion');
		Route::get('hr_salary_search_employee', 'SalarySearchController@hrSalarySearchEmployee');
		Route::get('hr_salary_search_employee_info', 'SalarySearchController@hrSearchEmpInfo');
		Route::get('hr_salary_search_employee_table', 'SalarySearchController@employeeTable');
		Route::get('hr_salary_search_employee_list', 'SalarySearchController@hrSalarySearchListEmployee');

		Route::get('hr_salary_search_res', 'SalarySearchController@hrSalarySearchResWise');

		//Print
		Route::post('hr_salary_search_print_page','SalarySearchController@hrSalarySearchPrintPage');
		


		Route::get('hr_ot_search', 'OTSearchController@hrOTSearch');
		Route::get('hr_ot_search_otshift', 'OTSearchController@hrOTSearchShift');
		Route::get('hr_ot_search_othour', 'OTSearchController@hrOTSearchHour');
		Route::get('hr_ot_search_unit', 'OTSearchController@hrOTSearchUnit');
		Route::get('hr_ot_search_area', 'OTSearchController@hrOTSearchArea');
		Route::get('hr_ot_search_department', 'OTSearchController@hrOTSearchDepartment');
		Route::get('hr_ot_search_floor', 'OTSearchController@hrOTSearchFloor');
		Route::get('hr_ot_search_section', 'OTSearchController@hrOTSearchSection');
		Route::get('hr_ot_search_subsection', 'OTSearchController@hrOTSearchSubscetion');
		Route::get('hr_ot_search_employee', 'OTSearchController@hrOTSearchEmployee');
		Route::get('hr_ot_search_employee_info', 'OTSearchController@hrSearchEmpInfo');

		Route::get('hr_ot_search_employee_list', 'OTSearchController@hrOtSearchListEmployee');

		//Print
		Route::post('hr_ot_search_print_page','OTSearchController@hrOtSearchPrintPage');


		// Line Change Query
		Route::get('hr_line_search', 'LineSearchController@hrLineSearch');
		Route::get('hr_line_search_unit', 'LineSearchController@hrLineSearchUnit');
		Route::get('hr_line_search_floor', 'LineSearchController@hrLineSearchFloor');
		Route::get('hr_line_search_line', 'LineSearchController@hrLineSearchLine');
		Route::get('hr_line_search_employee', 'LineSearchController@hrLineSearchEmployee');

		Route::get('hr_line_search_change', 'LineSearchController@hrLineSearchChange');
		Route::get('hr_line_search_change_list', 'LineSearchController@hrLineSearchListChange');
		Route::get('hr_line_get_emp_details', 'LineSearchController@employeeWiseChange');

		Route::get('hr_Line_search_employee_list', 'LineSearchController@hrLineSearchListEmployee');

		//Print
		Route::post('hr_line_search_print_page','LineSearchController@hrLineSearchPrintPage');


		// leave
		Route::get('hr_leave_search', 'LeaveSearchController@hrLeaveSearch');
		Route::get('hr_leave_search_unit', 'LeaveSearchController@hrLeaveSearchUnit');
		Route::get('hr_leave_search_area', 'LeaveSearchController@hrLeaveSearchArea');
		Route::get('hr_leave_search_department', 'LeaveSearchController@hrLeaveSearchDepartment');
		Route::get('hr_leave_search_floor', 'LeaveSearchController@hrLeaveSearchFloor');
		Route::get('hr_leave_search_section', 'LeaveSearchController@hrLeaveSearchSection');
		Route::get('hr_leave_search_subsection', 'LeaveSearchController@hrLeaveSearchSubSection');
		Route::get('hr_leave_search_employee', 'LeaveSearchController@hrLeaveSearchEmployee');
		Route::get('hr_leave_search_allemp', 'LeaveSearchController@hrLeaveSearchAllEmployee');
		Route::get('hr_leave_search_allemp_data', 'LeaveSearchController@hrLeaveSearchAllEmpData');
		Route::get('hr_leave_search_emp_data', 'LeaveSearchController@hrLeaveSearchEmpData');
		Route::get('hr_leave_search_emp_count', 'LeaveSearchController@hrGetEmpAttCount');
		Route::get('leave_single_emp/{associate_id}/{month}', 'LeaveSearchController@hrLeaveSearchSingleEmpData');
		// print section
		Route::get('hr_leave_searchPrint', 'LeaveSearchController@hrLeaveSearchPrint');
		Route::post('hr_leave_search_unitPrint', 'LeaveSearchController@hrLeaveSearchUnitPrint');
		Route::post('hr_leave_search_areaPrint', 'LeaveSearchController@hrLeaveSearchAreaPrint');
		Route::post('hr_leave_search_departmentPrint', 'LeaveSearchController@hrLeaveSearchDepartmentPrint');
		Route::post('hr_leave_search_floorPrint', 'LeaveSearchController@hrLeaveSearchFloorPrint');
		Route::post('hr_leave_search_sectionPrint', 'LeaveSearchController@hrLeaveSearchSectionPrint');
		Route::post('hr_leave_search_subsectionPrint', 'LeaveSearchController@hrLeaveSearchSubSectionPrint');


		// outside
		Route::get('hr_outside_search', 'OutsideSearchController@hrOutsideSearch');
		Route::get('hr_outside_search_employee', 'OutsideSearchController@hrOutsideSearchEmployee');

		Route::get('hr_outside_search_emp_data', 'OutsideSearchController@hrOutsideSearchEmpData');
		Route::get('hr_outside_search_emp_date_datalist', 'OutsideSearchController@hrOutsideSearchEmpDateDataList');
		Route::get('hr_outside_get_emp_details', 'OutsideSearchController@hrOutsideGetEmpDetails');
		// print section
		Route::get('hr_outside_searchPrint', 'OutsideSearchController@hrOutsideSearchPrint');


		//salary ratio
		Route::get('hr_salratio_search', 'SalaryRatioSearchController@hrSalRatioSearch');
		// print section
		Route::get('hr_salratio_searchPrint', 'SalaryRatioSearchController@hrSalRatioSearchPrint');

		// employee status
		Route::get('hr_empstatus_search', 'EmployeeStatusSearchController@hrEmpstatusSearch');
		Route::get('hr_empstatus_search_employee', 'EmployeeStatusSearchController@hrEmpstatusSearchEmployee');
		Route::get('hr_empstatus_search_emp_data', 'EmployeeStatusSearchController@hrEmpstatusSearchEmpData');
		Route::get('hr_empstatus_search_join_employee', 'EmployeeStatusSearchController@hrEmpstatusSearchEmpJoinData');
		Route::get('hr_empstatus_search_unit', 'EmployeeStatusSearchController@hrAllUnit');
		Route::get('hr_empstatus_search_unit_emp_list', 'EmployeeStatusSearchController@hrUnitEmployeeList');
		Route::get('hr_empstatus_search_unit_emp_data', 'EmployeeStatusSearchController@hrUnitEmpData');
		// print section
		Route::get('hr_empstatus_searchPrint', 'EmployeeStatusSearchController@hrEmpstatusSearchPrint');


		//Salary Increase Decrease..
		Route::get('hr_salincdec_search', 'SalaryIncreaseDecreaseSearchContorller@hrSalIncDecSearch');

	});


	

	// attendance
	Route::get('hr/timeattendance/daily_attendance_list', 'Hr\TimeAttendance\AttendanceController@dailyAttendance');
	Route::get('hr/timeattendance/daily_attendance_data', 'Hr\TimeAttendance\AttendanceController@getAttendanceData');

	Route::get('hr/timeattendance/attendance-upload', 'Hr\TimeAttendance\AttendaceManualController@showForm')->middleware(['permission:Attendance Upload']);
	Route::post('hr/timeattendance/attendance_manual', 'Hr\TimeAttendance\AttendaceManualController@saveData')->middleware(['permission:Attendance Upload']);

	Route::get('hr/timeattendance/default-punch', 'Hr\TimeAttendance\AttendaceManualController@defaultPunch')->middleware(['permission:Default Punch']);
	Route::post('hr/timeattendance/default-punch', 'Hr\TimeAttendance\AttendaceManualController@storeDefaultPunch')->middleware(['permission:Default Punch']);
	Route::get('hr/timeattendance/employee_by_fields', 'Hr\TimeAttendance\AttendaceManualController@employeeByField');

	Route::get('/hr/timeattendance/attendance_process_wise', 'Hr\TimeAttendance\AttendanceExcelController@importFileProcess');
	Route::get('hr/timeattendance/attendance_bulk_manual', 'Hr\TimeAttendance\AttendaceBulkManualController@bulkManual');
	Route::post('hr/timeattendance/attendance_bulk_store', 'Hr\TimeAttendance\AttendaceBulkManualController@bulkManualStore');

	//attendance new process
	Route::post('/hr/timeattendance/attendance_manual/import', 'Hr\TimeAttendance\AttendanceFileProcessController@importFile');
	Route::post('/hr/timeattendance/attendance_process_wise_data', 'Hr\TimeAttendance\AttendanceFileProcessController@attFileProcess');
	//attendance absent
	Route::get('hr/timeattendance/unit-wise-absent', 'Hr\TimeAttendance\AttendanceFileProcessController@unitAbsent');


	Route::get('hr/timeattendance/daywise_manual_attendance', 'Hr\TimeAttendance\AttendaceDaywiseManualController@dayManual');
	Route::get('hr/timeattendance/daywise_manual_attendance_process', 'Hr\TimeAttendance\AttendaceDaywiseManualController@dayManualProcess');
	Route::get('hr/attendance/floor_by_unit', 'Hr\TimeAttendance\AttendaceDaywiseManualController@getFloorByUnit');

	// test manual attendance
	Route::get('hr/timeattendance/daywise_manual_attendance_test', 'Hr\TimeAttendance\AttendaceDaywiseManualController@dayManualTest');
	Route::get('hr/timeattendance/daywise_manual_attendance_test_data', 'Hr\TimeAttendance\AttendaceDaywiseManualController@dayManualTestData');


	////
	Route::post('hr/timeattendance/attendance_daywise_store', 'Hr\TimeAttendance\AttendaceDaywiseManualController@dayManualStore');

		//new routes//

	//Process Button route for manual attendance update from temporary table(File upload)
	Route::get('hr/timeattendance/temporary_data_process/{id}', 'Hr\TimeAttendance\AttendanceExcelController@processAttendance');

	Route::get('hr/timeattendance/existing_punch', 'Hr\TimeAttendance\AttendaceManualController@getExistingPunch');

	Route::get('hr/timeattendance/manual_att_log', 'Hr\TimeAttendance\AttendaceManualController@manualAttLog');
	Route::get('hr/timeattendance/manual_att_log_data', 'Hr\TimeAttendance\AttendaceManualController@manualAttLogData');
	Route::get('hr/timeattendance/calculate_ot', 'Hr\TimeAttendance\AttendaceManualController@calculateOt');

	// activity lock/unlock check
	Route::get('hr/operation/unit-wise-activity-lock', 'Hr\Operation\AttendanceOperationController@activityLock');
	//Attendance Report
	Route::get('hr/operation/attendance-operation', 'Hr\TimeAttendance\AttendanceController@attendanceReport')->middleware(['permission:Attendance Operation']);
	// Route::get('hr/timeattendance/attendance_report_data', 'Hr\TimeAttendance\AttendanceController@attendanceReportData')->middleware(['permission:Attendance Operation']);
	Route::get('hr/timeattendance/attendance_report_data', 'Hr\Operation\AttendanceOperationController@attendanceReportData')->middleware(['permission:Attendance Operation']);
	Route::get('hr/timeattendance/attendance_summary', 'Hr\TimeAttendance\AttendanceController@attendanceSummary');
	Route::get('hr/attendance/save_from_report', 'Hr\TimeAttendance\AttendanceController@saveFromReport');
	Route::get('hr/attendance/save_from_report_absent', 'Hr\TimeAttendance\AttendanceController@saveFromReportAbsent');

	Route::get('hr/timeattendance/make_absent', 'Hr\TimeAttendance\AttendanceController@makeAbsent');
	Route::get('hr/timeattendance/make_halfday', 'Hr\TimeAttendance\AttendanceController@makeHalfday');

	//shift roaster

	Route::get('hr/reports/shift_roaster', 'Hr\TimeAttendance\ShiftRoasterController@getRoaster');
	Route::post('hr/timeattendance/shift_roaster_datatable', 'Hr\TimeAttendance\ShiftRoasterController@getRoasterDatatableData');

	Route::post('hr/timeattendance/shift_roaster_data', 'Hr\TimeAttendance\ShiftRoasterController@getRoasterData');
	Route::get('hr/timeattendance/get_floor_by_unit', 'Hr\TimeAttendance\ShiftRoasterController@getFloorByUnit');

	//added on 19-10-2019.........
	Route::get('hr/shift_roaster/ajax_get_sfhift_details', 'Hr\Setup\ShiftController@getShiftTimes');
	//----------------------------

	// Station Card

	Route::get("hr/timeattendance/station_card", "Hr\TimeAttendance\StationController@showList");
	Route::get("hr/timeattendance/station_card_data", "Hr\TimeAttendance\StationController@listData");
	Route::get("hr/operation/line-change", "Hr\TimeAttendance\StationController@showForm")->middleware(['permission:Station Card']);
	Route::post("hr/operation/line-change", "Hr\TimeAttendance\StationController@saveForm")->middleware(['permission:Station Card']);

	Route::get("hr/timeattendance/station_card/{id}/delete", "Hr\TimeAttendance\StationController@stationDelete");
	Route::get("hr/timeattendance/station_card/{id}/edit", "Hr\TimeAttendance\StationController@stationEdit");

	Route::post("hr/timeattendance/station_card_update", "Hr\TimeAttendance\StationController@stationUpdate");


	Route::get("hr/timeattendance/station_as_info", "Hr\TimeAttendance\StationController@stationAssInfo");
	Route::get("hr/timeattendance/station_line_info", "Hr\TimeAttendance\StationController@stationLineInfo");

	//station card multiple
	Route::get("hr/timeattendance/new_card/multiple_emp_for_unit", "Hr\TimeAttendance\StationController@unitEmployees");
	Route::get("hr/timeattendance/new_card/floor_for_unit", "Hr\TimeAttendance\StationController@getFloor");
	Route::get("hr/timeattendance/station_multiple_as_info", "Hr\TimeAttendance\StationController@multipleAsInfo");
	Route::post("hr/operation/line-change-multiple", "Hr\TimeAttendance\StationController@saveFormMultiple");
	Route::post("hr/operation/line-change-single", "Hr\TimeAttendance\StationController@saveFormSingle");
	// line change report
	Route::get('hr/reports/line-changes', 'Hr\TimeAttendance\StationController@listOf');
	Route::get('hr/reports/line-changes-data', 'Hr\TimeAttendance\StationController@listOfData');
	Route::post('hr/operation/line-change-close', 'Hr\TimeAttendance\StationController@updateLine');
	//shift assign
	Route::get('hr/operation/shift_assign', 'Hr\TimeAttendance\ShiftRoasterController@shiftAssign')->middleware(['permission:Shift Assign']);
	Route::post('hr/timeattendance/shift_assign', 'Hr\TimeAttendance\ShiftRoasterController@saveAssignedShift')->middleware(['permission:Shift Assign']);
	Route::post('hr/timeattendance/shift_assign_processing', 'Hr\TimeAttendance\ShiftRoasterController@assignShiftProcessing');
	Route::get('hr/timeattendance/unitshift', 'Hr\TimeAttendance\ShiftRoasterController@unitShift');
	Route::get('hr/timeattendance/sectionSubsection', 'Hr\TimeAttendance\ShiftRoasterController@sectionSubsection');
	Route::get('hr/timeattendance/areaDepartment', 'Hr\TimeAttendance\ShiftRoasterController@areaDepartment');
	Route::get('hr/timeattendance/departmentSection', 'Hr\TimeAttendance\ShiftRoasterController@departmentSection');

	Route::get('hr/timeattendance/shifttable', 'Hr\TimeAttendance\ShiftRoasterController@shiftTable');
	Route::get('hr/timeattendance/get_associate_by_type_unit_shift', 'Hr\TimeAttendance\ShiftRoasterController@getAssociateByTypeUnitShift');
	Route::get('hr/timeattendance/get_associate_by_type_unit_shift_roster_ajax', 'Hr\TimeAttendance\ShiftRoasterController@getAssociateByTypeUnitShiftRosterAjax');
	Route::get('hr/operation/shift_assign_date_wise_employee', 'Hr\OperationLoadEmployeeController@getShiftEmployee');
	Route::get('hr/operation/holiday_roster_assign_employee', 'Hr\OperationLoadEmployeeController@getHolidayRosterEmployee');
	//leave list
	Route::get('hr/timeattendance/all_leaves', 'Hr\TimeAttendance\AllLeavesController@allLeaves')->middleware(['permission:Leave List']);
	Route::post('hr/timeattendance/all_leaves_data', 'Hr\TimeAttendance\AllLeavesController@allLeavesData')->middleware(['permission:Leave List']);
	Route::get('hr/timeattendance/leave_edit/{id}', 'Hr\TimeAttendance\AllLeavesController@editLeave')->middleware(['permission:Manage Leave']);
	Route::post('hr/timeattendance/leave_update', 'Hr\TimeAttendance\AllLeavesController@updateLeave')->middleware(['permission:Manage Leave']);
	Route::get('hr/timeattendance/leave_delete/{id}', 'Hr\TimeAttendance\AllLeavesController@deleteLeave')->middleware(['permission:Manage Leave']);
	Route::get('hr/timeattendance/leave_approve/{id}', 'Hr\TimeAttendance\AllLeavesController@leaveView')->middleware(['permission:Leave Approve']);
	Route::post('hr/timeattendance/leave_approve/approve_reject', 'Hr\TimeAttendance\AllLeavesController@leaveStatus')->middleware(['permission:Leave Approve']);
	// shift roster assign section
	Route::post('/hr/operation/shift_roster_assign_action', 'Hr\Operation\ShiftRosterController@assignMulti');
	// holiday roster assign
	Route::post('hr/operation/holiday_roster_assign_action', 'Hr\Operation\HolidayRosterController@assignMulti');

	// shift Roaster
	Route::get('hr/operation/holiday-roster', 'Hr\ShiftRoaster\ShiftRoasterController@index')->middleware(['permission:Holiday Roster']);
	Route::get('hr/reports/holiday-roster', 'Hr\ShiftRoaster\ShiftRoasterController@viewRoaster')->middleware(['permission:Holiday Roster']);
	Route::post('hr/shift_roaster/save_roaster', 'Hr\ShiftRoaster\ShiftRoasterController@saveRoaster')->middleware(['permission:Holiday Roster']);
	Route::get('hr/shift_roaster/roaster_view_data', 'Hr\ShiftRoaster\ShiftRoasterController@getRoasterData');
	Route::get('hr/shift_roaster/roaster_save_changes', 'Hr\ShiftRoaster\ShiftRoasterController@roasterSaveChanges');
	Route::get('hr/shift_roaster/roaster_updated_changes', 'Hr\ShiftRoaster\ShiftRoasterController@roasterUpdatedChanges');
	//Leave approval for worker
	Route::get('hr/timeattendance/leave-entry',  'Hr\TimeAttendance\LeaveWorkerController@showForm')->middleware(['permission:Manage Leave']);
	
	Route::post('hr/timeattendance/leave_worker',  'Hr\TimeAttendance\LeaveWorkerController@saveData')->middleware(['permission:Manage Leave']);

	Route::get('hr/reports/maternity', 'Hr\Operation\MaternityPaymentController@report');

	//Operation - Maternity Leave
	Route::get('hr/operation/maternity-leave', 'Hr\Operation\MaternityPaymentController@showForm');
	Route::post('hr/operation/maternity-leave', 'Hr\Operation\MaternityPaymentController@leaveApplication');
	Route::get('hr/operation/maternity-leave/list', 'Hr\Operation\MaternityPaymentController@index');
	Route::get('hr/operation/maternity-leave/listData', 'Hr\Operation\MaternityPaymentController@listData');
	Route::get('hr/operation/maternity-leave/{id}', 'Hr\Operation\MaternityPaymentController@process');
	Route::post('hr/maternity-leave/approve', 'Hr\Operation\MaternityPaymentController@approve');

	Route::get('hr/operation/maternity-medical-process/{id}', 'Hr\Operation\MaternityPaymentController@medicalProcess');
	Route::post('hr/operation/maternity-medical-basic/', 'Hr\Operation\MaternityPaymentController@storeMedicalBasic');
	Route::post('hr/operation/maternity-medical-record/', 'Hr\Operation\MaternityPaymentController@storeMedicalRecord');

	Route::get('hr/operation/maternity-leave/doctors-clearence/{id}', 'Hr\Operation\MaternityPaymentController@doctorsClearance');
	Route::post('hr/operation/doctor-clearence-letter', 'Hr\Operation\MaternityPaymentController@clearenceLetter');

	Route::get('hr/operation/maternity-leave-payment', 'Hr\Operation\MaternityPaymentCOntroller@index')->middleware(['permission:Maternity Payment']);
	Route::get('hr/operation/get_maternity_employees', 'Hr\Operation\MaternityPaymentCOntroller@getMaternityEmployees');
	Route::get('hr/operation/get_maternity_employee_details', 'Hr\Operation\MaternityPaymentCOntroller@getMaternityEmployeeDetails');
	Route::get('hr/operation/save_maternity_salary_disburse', 'Hr\Operation\MaternityPaymentCOntroller@saveMaternityDisburse')->middleware(['permission:Maternity Payment']);


	//Operation - Leave Approval
	Route::get('hr/timeattendance/operation/leave_approval',  'Hr\TimeAttendance\LeaveApprovalController@showForm')->middleware(['permission:Leave Approve']);
	Route::post('hr/timeattendance/operation/leave_approval',  'Hr\TimeAttendance\LeaveApprovalController@saveData')->middleware(['permission:Leave Approve']);

	//Operation - Without Pay
	Route::get('hr/timeattendance/operation/without_pay_list', 'Hr\TimeAttendance\WithoutPayController@showList');
	Route::post('hr/timeattendance/operation/without_pay_list_data', 'Hr\TimeAttendance\WithoutPayController@getData');
	Route::get('hr/timeattendance/operation/without_pay', 'Hr\TimeAttendance\WithoutPayController@showForm');
	Route::post('hr/timeattendance/operation/without_pay', 'Hr\TimeAttendance\WithoutPayController@saveData');
	Route::get('hr/timeattendance/operation/without_pay_edit/{id}', 'Hr\TimeAttendance\WithoutPayController@editForm');
	Route::post('hr/timeattendance/operation/without_pay_edit', 'Hr\TimeAttendance\WithoutPayController@updateData');

	//Yearly Holiday Planner
	Route::get('hr/operation/yearly_holidays', 'Hr\TimeAttendance\YearlyHolidayController@index')->middleware(['permission:Yearly Holiday']);
	Route::post('hr/timeattendance/operation/yearly_holidays/data', 'Hr\TimeAttendance\YearlyHolidayController@getAll')->middleware(['permission:Yearly Holiday']);

	Route::get('hr/operation/yearly-holidays/create', 'Hr\TimeAttendance\YearlyHolidayController@create')->middleware(['permission:Yearly Holiday']);

	Route::post('hr/timeattendance/operation/yearly_holidays', 'Hr\TimeAttendance\YearlyHolidayController@store')->middleware(['permission:Yearly Holiday']);
	Route::get('hr/timeattendance/operation/yearly_holidays/{id}/{status}', 'Hr\TimeAttendance\YearlyHolidayController@status');
	Route::get('hr/timeattendance/operation/yearly_holidays/open_status', 'Hr\TimeAttendance\YearlyHolidayController@openStatus');
	Route::get('hr/timeattendance/get_holidays', 'Hr\TimeAttendance\YearlyHolidayController@getHolidays');
	Route::get('hr/timeattendance/operation/yearly_holidays/modal_data', 'Hr\TimeAttendance\YearlyHolidayController@modalData');
	Route::get('hr/timeattendance/operation/yearly_holidays/modal_save', 'Hr\TimeAttendance\YearlyHolidayController@modalSave');
	Route::get('hr/timeattendance/operation/yearly_holidays/modal_delete', 'Hr\TimeAttendance\YearlyHolidayController@modalDelete');
	// operation/ salary process
	Route::get('hr/operation/employee-wise-salary-sheet', 'Hr\Operation\SalaryProcessController@employeeWise');
	Route::get('hr/operation/unit-wise-salary-sheet', 'Hr\Operation\SalaryProcessController@unitWise');
	Route::get('hr/operation/salary-generate', 'Hr\Operation\SalaryProcessController@generate');

	Route::get('hr/operation/unit-wise-pay-slip', 'Hr\Reports\PayslipController@unitWise');
	//Raw Punch Data
	Route::get('hr/timeattendance/raw_punch', 'Hr\TimeAttendance\RawPunchController@rawPunch');

	//---------Hr/ Payroll-----------//
	Route::get('hr/payroll/bank-sheet', 'Hr\Payroll\BankSheetController@index');
	Route::get('hr/reports/monthly-salary-bank-report', 'Hr\Payroll\BankSheetController@report');
	Route::get('hr/payroll/ot', 'Hr\Payroll\OtController@OT');
	Route::post('hr/payroll/ot', 'Hr\Payroll\OtController@OtStore');
	Route::get('hr/payroll/ot_list', 'Hr\Payroll\OtController@otList');
	Route::get('hr/payroll/ot/{id}', 'Hr\Payroll\OtController@otEdit');
	Route::post('hr/payroll/ot_update', 'Hr\Payroll\OtController@otUpdate');
	Route::post('hr/payroll/ot_list_data', 'Hr\Payroll\OtController@otListData');
	Route::get('hr/payroll/benefit_list', 'Hr\Recruitment\BenefitController@benefitList')->middleware(['permission:Benefit List']);
	Route::post('hr/payroll/benefit_list_data', 'Hr\Recruitment\BenefitController@benefitListData')->middleware(['permission:Benefit List']);
	Route::get('hr/payroll/benefit_edit/{ben_as_id}', 'Hr\Recruitment\BenefitController@benefitEdit');
	Route::post('hr/payroll/benefit_edit', 'Hr\Recruitment\BenefitController@benefitUpdate');


	//new routes for benefits
	Route::get('hr/payroll/benefits', 'Hr\Payroll\BenefitsCalculationController@index')->middleware(['permission:End of Job Benefits']);
	Route::get('hr/payroll/benefits/get_employee_details', 'Hr\Payroll\BenefitsCalculationController@getEmployeeDetails');
	Route::get('hr/associate-search-only-active', 'Hr\Payroll\BenefitsCalculationController@associtaeSearch');
	Route::get('hr/payroll/save_benefit_data', 'Hr\Payroll\BenefitsCalculationController@saveBenefits')->middleware(['permission:End of Job Benefits']);
	Route::get('hr/payroll/given_benefits_list', 'Hr\Payroll\BenefitsCalculationController@givenBenefitList')->middleware(['permission:End of Job Benefits']);
	Route::post('hr/payroll/get_given_benefit_data_list', 'Hr\Payroll\BenefitsCalculationController@getGivenBenefitData')->middleware(['permission:End of Job Benefits']);



	//Salary add deduct bulk upload
	Route::group(['middleware' => 'permission:Salary Adjustment'], function(){
		Route::get('hr/payroll/monthly-salary-adjustment-employee', 'Hr\Payroll\SalaryAdjustmentController@adjustEmployee');
		Route::get('hr/payroll/monthly-salary-adjustment', 'Hr\Payroll\SalaryAdjustmentController@include');
		Route::post('hr/payroll/monthly-salary-adjustment-store', 'Hr\Payroll\SalaryAdjustmentController@adjustStore');
		Route::get('hr/payroll/monthly-salary-adjustment-list', 'Hr\Payroll\SalaryAdjustmentController@index');
		Route::get('hr/payroll/monthly-salary-adjustment-data', 'Hr\Payroll\SalaryAdjustmentController@data');
		// Route::get('hr/payroll/salary-adjustment', 'Hr\Payroll\SalaryController@uploadFile');
		Route::get('hr/payroll/sample_file', 'Hr\Payroll\SalaryController@getDownload');

		Route::post('hr/payroll/add_deduct', 'Hr\Payroll\SalaryController@storeFile');
	});

	//Increment
	Route::get('hr/payroll/increment', 'Hr\Payroll\IncrementController@index');
	Route::get('hr/payroll/increment-eligible', 'Hr\Payroll\IncrementController@getEligibleList');
	//Route::get('hr/payroll/increment', 'Hr\Recruitment\BenefitController@showIncrementForm');
	Route::get('hr/payroll/increment-list', 'Hr\Recruitment\BenefitController@incrementList');
	Route::get('hr/payroll/increment-list-data', 'Hr\Recruitment\BenefitController@incrementListData');
	Route::get('hr/payroll/get_associate', 'Hr\Recruitment\BenefitController@getAssociates')->middleware(['permission:Manage Increment']);
	Route::post('hr/payroll/increment', 'Hr\Recruitment\BenefitController@storeIncrement')->middleware(['permission:Manage Increment']);
	Route::get('hr/payroll/increment_edit/{id}', 'Hr\Recruitment\BenefitController@editIncrement')->middleware(['permission:Manage Increment']);
	Route::post('hr/payroll/increment_update', 'Hr\Recruitment\BenefitController@updateIncrement')->middleware(['permission:Manage Increment']);

	//arear salary
	Route::get('hr/payroll/arear_salary_disburse/{associate_id}', 'Hr\Recruitment\BenefitController@arearSalaryGive');
	Route::post('hr/payroll/arear_salary_disburse/save', 'Hr\Recruitment\BenefitController@arearSalarySave');

	//Promotion
	Route::get('hr/payroll/promotion', 'Hr\Recruitment\BenefitController@promotion');
	Route::post('hr/payroll/promotion', 'Hr\Recruitment\BenefitController@storePromotion')->middleware(['permission:Manage Promotion']);
	Route::get('hr/payroll/promotion_edit/{id}', 'Hr\Recruitment\BenefitController@promotionEdit')->middleware(['permission:Manage Promotion']);
	Route::post('hr/payroll/promotion_update', 'Hr\Recruitment\BenefitController@updatePromotion')->middleware(['permission:Manage Promotion']);
	Route::get('hr/payroll/promotion-associate-search', 'Hr\Recruitment\BenefitController@searchPromotedAssociate')->middleware(['permission:Manage Promotion']);
	Route::get('hr/payroll/promotion-associate-info', 'Hr\Recruitment\BenefitController@promotedAssociateInfo')->middleware(['permission:Manage Promotion']);
	Route::get('hr/payroll/benefit/{associate_id}', 'Hr\Recruitment\BenefitController@showAssociateBenefit');

	Route::get('hr/payroll/promotion-list', 'Hr\Recruitment\BenefitController@promotionList');
	Route::get('hr/payroll/promotion-list-data', 'Hr\Recruitment\BenefitController@promotionListData');


//Salary
Route::get('hr/payroll/salary', 'Hr\Payroll\SalaryController@view');
	//---------------------------Common Routes-------------------------//

	// Upazilla and District
	Route::get('district_wise_upazilla', 'DistrictUpazillaController@districtWiseUpazilla');
	Route::get('level_wise_degree', 'EducationController@levelWiseDegree');

	// ID Generator with department_id & joining_date
	Route::post('id/generate', 'Hr\IDGenerator@generator');


	//---------HR / Recruitment-----------//

	// Employee Search
	Route::get('hr/associate-search', 'Hr\Recruitment\EmployeeController@associtaeSearch');


	Route::get('hr/associate-tags', 'Hr\Recruitment\EmployeeController@associateTags');
	Route::get('hr/associate/{associate_id?}', 'Hr\Recruitment\EmployeeController@associtaeInfo');

	// ID CARD GENERATE
	Route::group(['middleware' => 'permission:ID Card'], function(){
		Route::get('hr/recruitment/employee/idcard/', 'Hr\Recruitment\EmployeeController@idCard');
		Route::get('hr/recruitment/employee/idcard/filter', 'Hr\Recruitment\EmployeeController@filterAssociate');
		Route::get('hr/recruitment/employee/idcard/floor_list_by_unit', 'Hr\Recruitment\EmployeeController@idCardFloorListByUnit');
		Route::get('hr/recruitment/employee/idcard/line_list_by_unit_floor', 'Hr\Recruitment\EmployeeController@idCardLineListByUnitFloor');
		Route::post('hr/recruitment/employee/idcard/search', 'Hr\Recruitment\EmployeeController@idCardSearch');
		Route::post('hr/recruitment/employee/idcard/generate', 'Hr\Recruitment\EmployeeController@idCardGenerate');
	});
	//Employee Hierarchy
	Route::get('hr/recruitment/employee/hierarchy/', 'Hr\Recruitment\EmployeeController@hierarchy');
	Route::get('hr/recruitment/employee/hierarchy_data', 'Hr\Recruitment\EmployeeController@getHierarchy');


	// Worker
	Route::get('hr/recruitment/worker/recruit_list', 'Hr\Recruitment\WorkerController@recruitList')->middleware(['permission:Recruit List']);
	Route::post('hr/recruitment/worker/recruit_data', 'Hr\Recruitment\WorkerController@recruitData')->middleware(['permission:Recruit List']);
	Route::get('hr/recruitment/worker/recruit', 'Hr\Recruitment\WorkerController@recruitForm')->middleware(['permission:New Recruit']);
	Route::get('hr/recruitment/worker/recruit_edit/{worker_id}', 'Hr\Recruitment\WorkerController@recruitEditForm')->middleware(['permission:New Recruit']);
	Route::post('hr/recruitment/worker/recruit', 'Hr\Recruitment\WorkerController@recruitStore')->middleware(['permission:New Recruit']);
	Route::post('hr/recruitment/worker/recruit-edit', 'Hr\Recruitment\WorkerController@recruitEdit')->middleware(['permission:New Recruit']);
	Route::post('hr/recruitment/worker/recruit/excel/import', 'Hr\Recruitment\WorkerExcelController@import')->middleware(['permission:Recruit List']);

	Route::group(['middleware' => 'permission:Medical Entry'], function(){
		Route::get('hr/recruitment/worker/medical_list', 'Hr\Recruitment\WorkerController@showMedicalList');
		Route::post('hr/recruitment/worker/medical_data', 'Hr\Recruitment\WorkerController@medicalData');
		Route::get('hr/recruitment/worker/medical_edit/{worker_id}', 'Hr\Recruitment\WorkerController@editMedical');
		Route::post('hr/recruitment/worker/medical', 'Hr\Recruitment\WorkerController@medicalStore');
	});

	Route::group(['middleware' => 'permission:IE Entry'], function(){
		Route::get('hr/recruitment/worker/ie_skill_list', 'Hr\Recruitment\WorkerController@showIeSkillList');
		Route::post('hr/recruitment/worker/ie_skill_data', 'Hr\Recruitment\WorkerController@getIeSkillData');
		Route::get('hr/recruitment/worker/ie_skill_edit/{worker_id}', 'Hr\Recruitment\WorkerController@editIeSkill');
		Route::post('hr/recruitment/worker/ie_skill_test', 'Hr\Recruitment\WorkerController@ieSkillStore');
	});

	Route::get('hr/recruitment/worker/migrate/{worker_id}', 'Hr\Recruitment\RecruitController@migrate');

	// Employee
	Route::group(['middleware' => 'permission:Employee List'], function(){
		Route::get('hr/employee/list', 'Hr\Recruitment\EmployeeController@showList');
		Route::get('hr/employee/new-employee', 'Hr\Recruitment\EmployeeController@today');
		Route::get('hr/recruitment/employee/today_employee_data', 'Hr\Recruitment\EmployeeController@getTodayData');

		Route::get('hr/employee/incomplete-list', 'Hr\Recruitment\EmployeeController@incompleteEmployee');
		Route::get('hr/recruitment/employee/incomplete_employee_data', 'Hr\Recruitment\EmployeeController@getIncompleteData');

		Route::get('hr/recruitment/employee/employee_list_details', 'Hr\Recruitment\EmployeeController@showListDetails');
		Route::get('hr/recruitment/employee/employee_data', 'Hr\Recruitment\EmployeeController@getData');
		Route::get('hr/recruitment/employee/dropdown_data', 'Hr\Recruitment\EmployeeController@getDropdownData');
	});
	//Route::post('hr/recruitment/employee/employee_data', 'Hr\Recruitment\EmployeeController@getData');

	Route::group(['middleware' => 'permission:Manage Employee'], function(){
		Route::get('hr/recruitment/employee/edit/{associate_id?}', 'Hr\Recruitment\EmployeeController@edit');
		Route::get('hr/recruitment/employee/delete/{associate_id?}', 'Hr\Recruitment\EmployeeController@delete');
		Route::get('hr/recruitment/employee/add_employee', 'Hr\Recruitment\EmployeeController@showEmployeeForm');
		Route::post('hr/recruitment/employee/add_employee', 'Hr\Recruitment\EmployeeController@saveEmployee');
		Route::post('hr/recruitment/employee/update_employee', 'Hr\Recruitment\EmployeeController@updateEmployee');
	});
	Route::get('hr/recruitment/employee/pdf/{associate_id}', 'Hr\Recruitment\EmployeeController@pdfEmployee');

	// Medical Info
	Route::group(['middleware' => 'permission:Manage Employee|Medical Entry'], function(){
		Route::get('hr/recruitment/operation/medical_info','Hr\Recruitment\MedicalInfoController@medicalInfo');
		Route::post('hr/recruitment/operation/medical_info','Hr\Recruitment\MedicalInfoController@medicalInfoStore');
		Route::get('hr/recruitment/operation/medical_info_list','Hr\Recruitment\MedicalInfoController@medicalInfoList');
		Route::post('hr/recruitment/operation/medical_info_list_data','Hr\Recruitment\MedicalInfoController@medicalInfoListData');
		Route::get('hr/recruitment/operation/medical_info_edit/{med_as_id}','Hr\Recruitment\MedicalInfoController@medicalInfoEdit');
		Route::post('hr/recruitment/operation/medical_info_update','Hr\Recruitment\MedicalInfoController@medicalInfoUpdate');
	});

	//Advance Information
	Route::group(['middleware' => 'permission:Manage Employee|Medical Entry|Advance Info List'], function(){
		Route::get('hr/recruitment/operation/advance_info','Hr\Recruitment\AdvanceInfoController@advanceInfo');
		Route::post('hr/recruitment/operation/advance_info','Hr\Recruitment\AdvanceInfoController@advanceInfoStore');
		Route::post('hr/recruitment/operation/education_info','Hr\Recruitment\AdvanceInfoController@educationInfoStore');
		Route::post('hr/recruitment/operation/education_info/update','Hr\Recruitment\AdvanceInfoController@educationInfoUpdate');
		Route::get('hr/recruitment/operation/education_info/delete/{id}/{associate}','Hr\Recruitment\AdvanceInfoController@educationDelete');
		
		Route::post('hr/recruitment/employee/add_employee_bn', 'Hr\Recruitment\AdvanceInfoController@saveBengali');
		Route::get('hr/recruitment/operation/advance_info_list','Hr\Recruitment\AdvanceInfoController@advanceInfoList');
		Route::post('hr/recruitment/operation/advance_info_list_data','Hr\Recruitment\AdvanceInfoController@advanceInfoListData');
		Route::get('hr/recruitment/operation/advance_info_edit/{emp_adv_info_as_id}','Hr\Recruitment\AdvanceInfoController@advanceInfoEdit');
		Route::post('hr/recruitment/operation/advance_info_update','Hr\Recruitment\AdvanceInfoController@advanceInfoUpdate');
		Route::get('hr/recruitment/education_history','Hr\Recruitment\AdvanceInfoController@educationHistory');
	});

	//Benefitfs
	Route::group(['middleware' => 'permission:Manage Employee|Assign Benifit'], function(){
		Route::get('hr/payroll/employee-benefit','Hr\Recruitment\BenefitController@benefits');
		Route::post('hr/recruitment/operation/benefits','Hr\Recruitment\BenefitController@benefitStore');
		Route::get('hr/recruitment/get_benefit_by_id','Hr\Recruitment\BenefitController@getBenefitByID');
	});
	// production bonus section
	Route::get('hr/operation/production-bonus','Hr\Operation\VoucherController@productionBonus');
	Route::post('hr/operation/production-bonus','Hr\Operation\VoucherController@storeProductionBonus');
	Route::get('hr/operation/production-bonus-list','Hr\Operation\VoucherController@productionList');
	Route::post('hr/operation/production-bonus-list-data','Hr\Operation\VoucherController@productionListData');

	//Job Portal
	Route::get('hr/recruitment/job_portal/cv', 'Hr\Recruitment\CvController@CV');
	Route::get('hr/recruitment/job_portal/job_posting', 'Hr\Recruitment\JobController@JobPosting')->middleware(['permission:Job Posting']);

	Route::get('hr/recruitment/job_portal/job_posting_list', 'Hr\Recruitment\JobController@JobPostingList')->middleware(['permission:Job Posting List']);


	Route::get('hr/recruitment/job_portal/job_posting_list/{job_po_id}/{status}', 'Hr\Recruitment\JobController@JobPostingListStatus');

	Route::post('hr/recruitment/job_portal/job_posting_data', 'Hr\Recruitment\JobController@JobPostingListData');

	Route::post('hr/recruitment/job_portal/job_posting', 'Hr\Recruitment\JobController@JobPostingStore')->middleware(['permission:Job Posting']);

	Route::get('hr/recruitment/job_portal/interview_notes', 'Hr\Recruitment\InterviewNoteController@Interview');
	Route::get('hr/recruitment/job_portal/interview_notes_list', 'Hr\Recruitment\InterviewNoteController@InterviewList')->middleware(['permission:Interview Notes List']);
	Route::post('hr/recruitment/job_portal/interview_notes_data', 'Hr\Recruitment\InterviewNoteController@InterviewListData')->middleware(['permission:Interview Notes List']);
	Route::get('hr/recruitment/job_portal/interview_notes_delete/{id}', 'Hr\Recruitment\InterviewNoteController@InterviewDelete')->middleware(['permission:Interview Notes']);
	Route::post('hr/recruitment/job_portal/interview_notes', 'Hr\Recruitment\InterviewNoteController@InterviewNoteStore')->middleware(['permission:Interview Notes']);

	// Joining Letter
	Route::get('hr/recruitment/appointment-letter','Hr\Recruitment\JoiningLetterController@Letter')->middleware(['permission:Appointment Letter']);
	Route::post('hr/recruitment/job_portal/joining_letter','Hr\Recruitment\JoiningLetterController@LetterSave')->middleware(['permission:Appointment Letter']);
	Route::get('hr/recruitment/job_portal/pdfview/{ arg}','Hr\Recruitment\JoiningLetterController@pdfDownload');

	//---------HR / Training-----------//
	//---------HR / Assets Management-----------//

	Route::get('hr/assets/assign', 'Hr\Assets\AssetController@showForm');
	Route::post('hr/assets/assign', 'Hr\Assets\AssetController@saveData');
	Route::get('hr/assets/get-product-by-category-id', 'Hr\Assets\AssetController@getProductByCategoryID');
	Route::get('hr/assets/get-product-by-product-id', 'Hr\Assets\AssetController@getAssetByProductID');
	Route::get('hr/assets/assign_list', 'Hr\Assets\AssetController@showList');
	Route::post('hr/assets/assign_data', 'Hr\Assets\AssetController@getData');
	Route::get('hr/assets/assign_edit/{id}', 'Hr\Assets\AssetController@editForm');
	Route::post('hr/assets/assign_edit', 'Hr\Assets\AssetController@updateData');


	//---------HR / Performance-----------//

	Route::get('hr/performance/operation/disciplinary_list', 'Hr\Performance\DisciplinaryRecordController@showList')->middleware(['permission:Disciplinary List']);
	Route::get('hr/performance/operation/disciplinary_data', 'Hr\Performance\DisciplinaryRecordController@getData')->middleware(['permission:Disciplinary List']);

	Route::group(['middleware' => 'permission:Disciplinary Record'], function(){
		Route::get('hr/performance/disciplinary-record', 'Hr\Performance\DisciplinaryRecordController@showForm');
		Route::post('hr/performance/operation/disciplinary_form', 'Hr\Performance\DisciplinaryRecordController@saveData');
		Route::get('hr/performance/operation/disciplinary_edit/{record_id}', 'Hr\Performance\DisciplinaryRecordController@editForm');
		Route::post('hr/performance/operation/disciplinary_edit', 'Hr\Performance\DisciplinaryRecordController@updateData');
	}); 

	Route::get('hr/performance/appraisal_list', 'Hr\Performance\AppraisalListController@appraisalList')->middleware(['permission:Performance List']);
	Route::post('hr/performance/appraisal_list_data', 'Hr\Performance\AppraisalListController@appraisalListData')->middleware(['permission:Performance List']);
	Route::group(['middleware' => 'permission:Performance Appraisal'], function(){
		Route::get('hr/performance/appraisal', 'Hr\Performance\AppraisalController@showForm');
		Route::post('hr/performance/appraisal', 'Hr\Performance\AppraisalController@saveData');
		Route::get('hr/performance/appraisal_approve/{hr_pa_as_id}', 'Hr\Performance\AppraisalListController@AppraisalView');
		Route::post('hr/performance/appraisal_approve/approve_reject', 'Hr\Performance\AppraisalListController@appraisalStatus');
	});

	//shift
	Route::get('hr/operation/shift','Hr\Setup\ShiftController@shift')->middleware(['permission:Shift Assign']);
	Route::get('hr/operation/shift_update/{hr_shift_id}','Hr\Setup\ShiftController@shiftUpdate')->middleware(['permission:Shift Assign']);
	Route::get('hr/setup/getShiftListByLineID','Hr\Setup\ShiftController@getShiftListByLineID');
	Route::post('hr/setup/shift','Hr\Setup\ShiftController@shiftStore');
	Route::get('hr/setup/shift/{hr_shift_id}','Hr\Setup\ShiftController@shiftDelete');
	Route::post('hr/setup/shift_update','Hr\Setup\ShiftController@shiftUpdateStore');


	//---------HR / Setup-----------//

	//unit
	Route::middleware(['permission:Basic Setup'])->group( function () {

		Route::get('hr/setup/unit','Hr\Setup\UnitController@unit');
		Route::post('hr/setup/unit','Hr\Setup\UnitController@unitStore');
		Route::get('hr/setup/unit/{hr_unit_id}','Hr\Setup\UnitController@unitDelete');
		Route::get('hr/setup/unit_update/{hr_unit_id}','Hr\Setup\UnitController@unitUpdate');
		Route::post('hr/setup/unit_update','Hr\Setup\UnitController@unitUpdateStore');

		//Location
		Route::get('hr/setup/location','Hr\Setup\LocationController@location');
		Route::post('hr/setup/location','Hr\Setup\LocationController@locationStore');
		Route::get('hr/setup/location/{hr_unit_id}','Hr\Setup\LocationController@locationDelete');
		Route::get('hr/setup/location_update/{hr_unit_id}','Hr\Setup\LocationController@locationUpdate');
		Route::post('hr/setup/location_update','Hr\Setup\LocationController@locationUpdateStore');

		// Unit wise location
		Route::get('hr/reports/location_by_unit', 'Hr\Setup\LocationController@locationListUnit');

		//floor
		Route::get('hr/setup/floor','Hr\Setup\FloorController@floor');
		Route::get('hr/setup/getFloorListByUnitID','Hr\Setup\FloorController@getFloorListByUnitID');
		Route::post('hr/setup/floor','Hr\Setup\FloorController@floorStore');
		Route::get('hr/setup/floor/{hr_floor_id}','Hr\Setup\FloorController@floorDelete');
		Route::get('hr/setup/floor_update/{hr_floor_id}','Hr\Setup\FloorController@floorUpdate');
		Route::post('hr/setup/floor_update','Hr\Setup\FloorController@floorUpdateStore');

		//line
		Route::get('hr/setup/line','Hr\Setup\LineController@line');
		Route::get('hr/setup/getLineListByFloorID','Hr\Setup\LineController@getLineListByFloorID');
		Route::post('hr/setup/line','Hr\Setup\LineController@lineStore');
		Route::get('hr/setup/line/{hr_line_id}','Hr\Setup\LineController@lineDelete');
		Route::get('hr/setup/line_update/{hr_line_id}','Hr\Setup\LineController@lineUpdate');
		Route::post('hr/setup/line_update','Hr\Setup\LineController@lineUpdateStore');

		

		//department
		Route::get('hr/setup/department','Hr\Setup\DepartmentController@department');
		Route::get('hr/setup/getDepartmentListByAreaID','Hr\Setup\DepartmentController@getDepartmentListByAreaID');
		Route::post('hr/setup/department','Hr\Setup\DepartmentController@departmentStore');
		Route::get('hr/setup/department/{hr_department_id}','Hr\Setup\DepartmentController@departmentDelete');
		Route::get('hr/setup/department_update/{hr_department_id}','Hr\Setup\DepartmentController@departmentUpdate');
		Route::post('hr/setup/department_update','Hr\Setup\DepartmentController@departmentUpdateStore');

		//section
		Route::get('hr/setup/section','Hr\Setup\SectionController@section');
		Route::get('hr/setup/getSectionListByDepartmentID','Hr\Setup\SectionController@getSectionListByDepartmentID');
		Route::post('hr/setup/section','Hr\Setup\SectionController@sectionStore');
		Route::get('hr/setup/section/{hr_section_id}','Hr\Setup\SectionController@sectionDelete');
		Route::get('hr/setup/section_update/{hr_section_id}','Hr\Setup\SectionController@sectionUpdate');
		Route::post('hr/setup/section_update','Hr\Setup\SectionController@sectionUpdateStore');

		//subsection
		Route::get('hr/setup/subsection','Hr\Setup\SubsectionController@subsection');
		Route::get('hr/setup/getSubSectionListBySectionID','Hr\Setup\SubsectionController@getSubSectionListBySectionID');
		Route::post('hr/setup/subsection','Hr\Setup\SubsectionController@subsectionStore');
		Route::get('hr/setup/subsection/{hr_subsec_id}','Hr\Setup\SubsectionController@subsectionDelete');
		Route::get('hr/setup/subsection_update/{hr_subsec_id}','Hr\Setup\SubsectionController@subsectionUpdate');
		Route::post('hr/setup/subsection_update','Hr\Setup\SubsectionController@subsectionUpdateStore');
	});

	//added on 19-10-2019
	Route::get('hr/setup/get_presfhit_times', 'Hr\Setup\ShiftController@getPreShiftTimes');
	Route::post('hr/setup/shift_employee_update_processing','Hr\Setup\ShiftController@shiftUpdateEmployee');
	Route::post('hr/setup/shift_roaster_employee_update_processing','Hr\Setup\ShiftController@shiftUpdateRoasterEmployee');

	//designation
	Route::group(['middleware' => 'permission:Designation Setup'], function(){
		Route::get('hr/setup/designation','Hr\Setup\DesignationController@designation');
		Route::get('hr/setup/getDesignationListByEmployeeTypeID','Hr\Setup\DesignationController@getDesignationListByEmployeeTypeID');
		Route::post('hr/setup/designation','Hr\Setup\DesignationController@designationStore');
		Route::get('hr/setup/designation/{hr_designation_id}','Hr\Setup\DesignationController@designationDelete');
		Route::get('hr/setup/designation_update/{hr_designation_id}','Hr\Setup\DesignationController@designationUpdate');
		Route::post('hr/setup/designation_update','Hr\Setup\DesignationController@designationupdateStore');
	});
	Route::post('hr/hierarchy', 'Hr\Setup\DesignationController@hierarchy');

		//Loan Type
	Route::group(['middleware' => 'permission:Basic Setup'], function(){
		Route::get('hr/setup/loan_type','Hr\Setup\LoanTypeController@addloanType');
		Route::post('hr/setup/loan_type','Hr\Setup\LoanTypeController@storeloanType');
		Route::get('hr/setup/loan_type/delete/{id}','Hr\Setup\LoanTypeController@loanTypeDelete');
		Route::get('hr/setup/loan_type/edit/{id}','Hr\Setup\LoanTypeController@loanTypeEdit');
		Route::post('hr/setup/loan_type_update','Hr\Setup\LoanTypeController@updateLoanType');
	});

	//Salary Structure
	Route::group(['middleware' => 'permission:Salary Structure Setup'], function(){
		Route::get('hr/setup/salary_structure', 'Hr\Setup\SalaryStructureController@showForm');
		Route::post('hr/setup/salary_structure', 'Hr\Setup\SalaryStructureController@save');
	});
	//Education Level
	Route::group(['middleware' => 'permission:Education Setup'], function(){
		Route::get('hr/setup/education_title', 'Hr\Setup\EducationLevelController@showForm');
		Route::post('hr/setup/education_title', 'Hr\Setup\EducationLevelController@saveData');
	});
	//Increment Type
	Route::group(['middleware' => 'permission:Library Setup'], function(){
		Route::get('hr/setup/increment_type', 'Hr\Setup\IncrementTypeController@showForm');
		Route::post('hr/setup/increment_type', 'Hr\Setup\IncrementTypeController@saveData');
		Route::get('hr/setup/increment_type_edit/{id}', 'Hr\Setup\IncrementTypeController@incrementTypeEdit');
		Route::post('hr/setup/increment_type_update', 'Hr\Setup\IncrementTypeController@incrementTypeUpdate');
		Route::get('hr/setup/increment_type_delete/{id}', 'Hr\Setup\IncrementTypeController@incrementTypeDelete');
	
		//Add Other Benifit Item
		Route::get('hr/setup/other_benefit_item', 'Hr\Setup\OtherBenefitController@otherBenefit');
		Route::post('hr/setup/other_benefit_item', 'Hr\Setup\OtherBenefitController@otherBenefitStore');
		Route::get('hr/setup/other_benefit_delete/{id}', 'Hr\Setup\OtherBenefitController@otherBenefitDelete');

		//other Benefit
		Route::post('hr/payroll/other_benefit', 'Hr\Recruitment\BenefitController@otherBenefitStore');
	});
	//Attendance bonus
	Route::get('hr/setup/attendance_bonus', 'Hr\Setup\AttendanceBonusController@showForm')->middleware(['permission:Attendance Bonus Config']);
	Route::post('hr/setup/attendance_bonus_store', 'Hr\Setup\AttendanceBonusController@attBonusStore')->middleware(['permission:Attendance Bonus Config']);


	//Attendance Bonus Config Setup
	Route::get('hr/setup/attendancebonus', 'Hr\Setup\AttendanceBonusConfigController@index')->middleware(['permission:Attendance Bonus Config']);
	Route::post('hr/setup/attendance_bonus_save', 'Hr\Setup\AttendanceBonusConfigController@saveData')->middleware(['permission:Attendance Bonus Config']);
	Route::post('hr/setup/get_values', 'Hr\Setup\AttendanceBonusConfigController@getData')->middleware(['permission:Attendance Bonus Config']);


	//Shift roaster define...
	Route::get('hr/operation/shift_roaster_define', 'Hr\Setup\ShiftRoasterDefineCOntroller@shiftRoasterDefine')->middleware(['permission:Define Shift Roster']);
	Route::get('hr/operation/get_associate_by_type_unit_shift_roaster', 'Hr\Setup\ShiftRoasterDefineCOntroller@getAssociateByTypeUnitShiftRoaster');
	Route::post('hr/operation/shift_roaster_status_save', 'Hr\Setup\ShiftRoasterDefineCOntroller@shiftRoasterStatusSave')->middleware(['permission:Define Shift Roster']);

	//Multiple Employee Shift Assign
	Route::get('hr/operation/multiple_emp_shift_assign', 'Hr\Operation\MultiEmpShiftAssignController@index')->middleware(['permission:Employee Shift Assign']);
	Route::get('hr/operation/get_shift_unit_wise', 'Hr\Operation\MultiEmpShiftAssignController@getCurrentShiftsUnitWise');
	Route::get('hr/operation/get_associate_by_type_unit_for_multi_shift_assign', 'Hr\Operation\MultiEmpShiftAssignController@getAssociateByTypeUnitShiftRoaster');
	Route::post('hr/operation/multi_emp_shift_status_save', 'Hr\Operation\MultiEmpShiftAssignController@shiftRoasterStatusSave')->middleware(['permission:Employee Shift Assign']);

	//---------HR / Profile-----------//

	//---------HR / Notification-----------//

	Route::get('hr/notification/loan/loan_app_list', 'Hr\Notification\NotifLoanController@ShowLoan');
	Route::get('hr/notification/loan/loan_data', 'Hr\Notification\NotifLoanController@LoanData');
	Route::get('hr/notification/loan/loan_approve/{hr_la_id?}', 'Hr\Notification\NotifLoanController@LoanView');
	Route::post('hr/notification/loan/loan_approve/approve_reject', 'Hr\Notification\NotifLoanController@LoanStatus');
	Route::get('hr/notification/appraisal/performance_appraisal_list', 'Hr\Notification\NotifPAController@AppraisalList');
	Route::get('hr/notification/appraisal/performance_appraisal_data', 'Hr\Notification\NotifPAController@AppraisalData');
	Route::get('hr/notification/appraisal/appraisal_approve/{id?}', 'Hr\Notification\NotifPAController@AppraisalView');
	Route::post('hr/notification/appraisal/appraisal_approve/approve_reject', 'Hr\Notification\NotifPAController@AppraisalStatus');
	Route::get('hr/notification/greivance/greivance_appeal_list', 'Hr\Notification\NotifGAController@greivanceAppealList');
	Route::get('hr/notification/greivance/greivance_appeal_data', 'Hr\Notification\NotifGAController@greivanceAppealListData');
	Route::get('hr/notification/greivance/greivance_approve', 'Hr\Notification\NotifGAController@GreivanceView');
	Route::post('hr/notification/greivance/greivance_approve', 'Hr\Notification\NotifGAController@GreivanceApprove');
	Route::post('hr/notification/greivance/greivance_approve/approve_reject', 'Hr\Notification\NotifGAController@DisciplinaryRecordStatus');

	Route::get('hr/notification/training/training_list', 'Hr\Notification\NotifTrainingController@TrainingList');
	Route::get('hr/notification/training/training_data', 'Hr\Notification\NotifTrainingController@TrainingData');
	Route::get('hr/notification/training/training_approve/{tr_as_id}', 'Hr\Notification\NotifTrainingController@TrainingView');
	Route::post('hr/notification/training/training_approve/aprrove_reject', 'Hr\Notification\NotifTrainingController@TrainingStatus');
	Route::get('hr/notification/leave/leave_app_list', 'Hr\Notification\NotifLeaveController@LeaveList');
	Route::get('hr/notification/leave/leave_app_data', 'Hr\Notification\NotifLeaveController@LeaveData');
	Route::get('hr/notification/leave/leave_approve/{hr_leave_id?}', 'Hr\Notification\NotifLeaveController@LeaveView');
	Route::post('hr/notification/leave/leave_approve/approve_reject', 'Hr\Notification\NotifLeaveController@LeaveStatus');

	//---------HR / Reports-----------//
	//application letter
	Route::get('hr/recruitment/job-application', 'Hr\Reports\JobApplicationController@applicationForm');
	Route::post('hr/recruitment/job_application', 'Hr\Reports\JobApplicationController@saveApplicationForm');


//---------HR / Training-----------//

// Add Training
Route::get('hr/training/training_list', 'Hr\Training\TrainingController@trainingList')->middleware(['permission:Training List']);
Route::get('hr/training/training_data', 'Hr\Training\TrainingController@getData')->middleware(['permission:Training List']);

Route::get('/hr/training/training_status/{id}/{status}', 'Hr\Training\TrainingController@trainingStatus');
Route::get('hr/training/add_training', 'Hr\Training\TrainingController@showForm')->middleware(['permission:Add Training']);
Route::post('hr/training/add_training', 'Hr\Training\TrainingController@saveTraining')->middleware(['permission:Add Training']);


	
// Assign Training
Route::get('hr/training/assign_list', 'Hr\Training\TrainingAssignController@assignList')->middleware(['permission:Assigned Employee List']);
Route::post('hr/training/assign_data', 'Hr\Training\TrainingAssignController@getData')->middleware(['permission:Assigned Employee List']);

Route::get('hr/training/assign_status/{id}/{status}', 'Hr\Training\TrainingAssignController@assignStatus');
Route::get('hr/training/assign_training', 'Hr\Training\TrainingAssignController@showForm')->middleware(['permission:Assign Training']);
Route::post('hr/training/assign_training', 'Hr\Training\TrainingAssignController@saveTraining')->middleware(['permission:Assign Training']);


//---------HR / Assets Management-----------//

Route::get('hr/assets/assign', 'Hr\Assets\AssetController@showForm');
Route::post('hr/assets/assign', 'Hr\Assets\AssetController@saveData');
Route::get('hr/assets/get-product-by-category-id', 'Hr\Assets\AssetController@getProductByCategoryID');
Route::get('hr/assets/get-product-by-product-id', 'Hr\Assets\AssetController@getAssetByProductID');
Route::get('hr/assets/assign_list', 'Hr\Assets\AssetController@showList');
Route::post('hr/assets/assign_data', 'Hr\Assets\AssetController@getData');
Route::get('hr/assets/assign_edit/{id}', 'Hr\Assets\AssetController@editForm');
Route::post('hr/assets/assign_edit', 'Hr\Assets\AssetController@updateData');


//---------HR / Performance-----------//

Route::get('hr/performance/operation/disciplinary_list', 'Hr\Performance\DisciplinaryRecordController@showList');
Route::get('hr/performance/operation/disciplinary_data', 'Hr\Performance\DisciplinaryRecordController@getData');
Route::get('hr/performance/operation/disciplinary_form', 'Hr\Performance\DisciplinaryRecordController@showForm');
Route::post('hr/performance/operation/disciplinary_form', 'Hr\Performance\DisciplinaryRecordController@saveData');
Route::get('hr/performance/operation/disciplinary_edit/{record_id}', 'Hr\Performance\DisciplinaryRecordController@editForm');
Route::post('hr/performance/operation/disciplinary_edit', 'Hr\Performance\DisciplinaryRecordController@updateData');
Route::get('hr/performance/appraisal', 'Hr\Performance\AppraisalController@showForm');
Route::post('hr/performance/appraisal', 'Hr\Performance\AppraisalController@saveData');
Route::get('hr/performance/appraisal_list', 'Hr\Performance\AppraisalListController@appraisalList');
Route::post('hr/performance/appraisal_list_data', 'Hr\Performance\AppraisalListController@appraisalListData');
Route::get('hr/performance/appraisal_approve/{hr_pa_as_id}', 'Hr\Performance\AppraisalListController@AppraisalView');
Route::post('hr/performance/appraisal_approve/approve_reject', 'Hr\Performance\AppraisalListController@appraisalStatus');


//---------HR / Setup-----------//

//unit
Route::get('hr/setup/unit','Hr\Setup\UnitController@unit');
Route::post('hr/setup/unit','Hr\Setup\UnitController@unitStore');
Route::get('hr/setup/unit/{hr_unit_id}','Hr\Setup\UnitController@unitDelete');
Route::get('hr/setup/unit_update/{hr_unit_id}','Hr\Setup\UnitController@unitUpdate');
Route::post('hr/setup/unit_update','Hr\Setup\UnitController@unitUpdateStore');

//Location
Route::get('hr/setup/location','Hr\Setup\LocationController@location');
Route::post('hr/setup/location','Hr\Setup\LocationController@locationStore');
Route::get('hr/setup/location/{hr_unit_id}','Hr\Setup\LocationController@locationDelete');
Route::get('hr/setup/location_update/{hr_unit_id}','Hr\Setup\LocationController@locationUpdate');
Route::post('hr/setup/location_update','Hr\Setup\LocationController@locationUpdateStore');

//floor
Route::get('hr/setup/floor','Hr\Setup\FloorController@floor');
Route::get('hr/setup/getFloorListByUnitID','Hr\Setup\FloorController@getFloorListByUnitID');
Route::post('hr/setup/floor','Hr\Setup\FloorController@floorStore');
Route::get('hr/setup/floor/{hr_floor_id}','Hr\Setup\FloorController@floorDelete');
Route::get('hr/setup/floor_update/{hr_floor_id}','Hr\Setup\FloorController@floorUpdate');
Route::post('hr/setup/floor_update','Hr\Setup\FloorController@floorUpdateStore');

//line
Route::get('hr/setup/line','Hr\Setup\LineController@line');
Route::get('hr/setup/getLineListByFloorID','Hr\Setup\LineController@getLineListByFloorID');
Route::post('hr/setup/line','Hr\Setup\LineController@lineStore');
Route::get('hr/setup/line/{hr_line_id}','Hr\Setup\LineController@lineDelete');
Route::get('hr/setup/line_update/{hr_line_id}','Hr\Setup\LineController@lineUpdate');
Route::post('hr/setup/line_update','Hr\Setup\LineController@lineUpdateStore');

//shift
Route::get('hr/setup/shift','Hr\Setup\ShiftController@shift');
Route::get('hr/setup/getShiftListByLineID','Hr\Setup\ShiftController@getShiftListByLineID');
Route::post('hr/setup/shift','Hr\Setup\ShiftController@shiftStore');
Route::get('hr/setup/shift/{hr_shift_id}','Hr\Setup\ShiftController@shiftDelete');
Route::get('hr/setup/shift_update/{hr_shift_id}','Hr\Setup\ShiftController@shiftUpdate');
Route::post('hr/setup/shift_update','Hr\Setup\ShiftController@shiftUpdateStore');

//department
Route::get('hr/setup/department','Hr\Setup\DepartmentController@department');
Route::get('hr/setup/getDepartmentListByAreaID','Hr\Setup\DepartmentController@getDepartmentListByAreaID');
Route::post('hr/setup/department','Hr\Setup\DepartmentController@departmentStore');
Route::get('hr/setup/department/{hr_department_id}','Hr\Setup\DepartmentController@departmentDelete');
Route::get('hr/setup/department_update/{hr_department_id}','Hr\Setup\DepartmentController@departmentUpdate');
Route::post('hr/setup/department_update','Hr\Setup\DepartmentController@departmentUpdateStore');

//section
Route::get('hr/setup/section','Hr\Setup\SectionController@section');
Route::get('hr/setup/getSectionListByDepartmentID','Hr\Setup\SectionController@getSectionListByDepartmentID');
Route::post('hr/setup/section','Hr\Setup\SectionController@sectionStore');
Route::get('hr/setup/section/{hr_section_id}','Hr\Setup\SectionController@sectionDelete');
Route::get('hr/setup/section_update/{hr_section_id}','Hr\Setup\SectionController@sectionUpdate');
Route::post('hr/setup/section_update','Hr\Setup\SectionController@sectionUpdateStore');

//subsection
Route::get('hr/setup/subsection','Hr\Setup\SubsectionController@subsection');
Route::get('hr/setup/getSubSectionListBySectionID','Hr\Setup\SubsectionController@getSubSectionListBySectionID');
Route::post('hr/setup/subsection','Hr\Setup\SubsectionController@subsectionStore');
Route::get('hr/setup/subsection/{hr_subsec_id}','Hr\Setup\SubsectionController@subsectionDelete');
Route::get('hr/setup/subsection_update/{hr_subsec_id}','Hr\Setup\SubsectionController@subsectionUpdate');
Route::post('hr/setup/subsection_update','Hr\Setup\SubsectionController@subsectionUpdateStore');

//designation
Route::get('hr/setup/designation','Hr\Setup\DesignationController@designation');
Route::get('hr/setup/getDesignationListByEmployeeTypeID','Hr\Setup\DesignationController@getDesignationListByEmployeeTypeID');
Route::post('hr/setup/designation','Hr\Setup\DesignationController@designationStore');
Route::get('hr/setup/designation/{hr_designation_id}','Hr\Setup\DesignationController@designationDelete');
Route::get('hr/setup/designation_update/{hr_designation_id}','Hr\Setup\DesignationController@designationUpdate');
Route::post('hr/setup/designation_update','Hr\Setup\DesignationController@designationupdateStore');
Route::post('hr/hierarchy', 'Hr\Setup\DesignationController@hierarchy');

//Loan Type
Route::get('hr/setup/loan_type','Hr\Setup\LoanTypeController@addloanType');
Route::post('hr/setup/loan_type','Hr\Setup\LoanTypeController@storeloanType');
Route::get('hr/setup/loan_type/delete/{id}','Hr\Setup\LoanTypeController@loanTypeDelete');
Route::get('hr/setup/loan_type/edit/{id}','Hr\Setup\LoanTypeController@loanTypeEdit');
Route::post('hr/setup/loan_type_update','Hr\Setup\LoanTypeController@updateLoanType');

//Salary Structure
Route::get('hr/setup/salary_structure', 'Hr\Setup\SalaryStructureController@showForm');
Route::post('hr/setup/salary_structure', 'Hr\Setup\SalaryStructureController@save');

//Education Level
Route::get('hr/setup/education_title', 'Hr\Setup\EducationLevelController@showForm');
Route::post('hr/setup/education_title', 'Hr\Setup\EducationLevelController@saveData');
Route::get('hr/setup/education_title/delete/{id}','Hr\Setup\EducationLevelController@degreeDelete');
Route::get('hr/setup/education_title/edit/{id}','Hr\Setup\EducationLevelController@degreeEdit');
Route::post('hr/setup/education_title_update','Hr\Setup\EducationLevelController@degreeUpdate');

//Increment Type
Route::get('hr/setup/increment_type', 'Hr\Setup\IncrementTypeController@showForm');
Route::post('hr/setup/increment_type', 'Hr\Setup\IncrementTypeController@saveData');
Route::get('hr/setup/increment_type_edit/{id}', 'Hr\Setup\IncrementTypeController@incrementTypeEdit');
Route::post('hr/setup/increment_type_update', 'Hr\Setup\IncrementTypeController@incrementTypeUpdate');
Route::get('hr/setup/increment_type_delete/{id}', 'Hr\Setup\IncrementTypeController@incrementTypeDelete');

//Add Other Benifit Item
Route::get('hr/setup/other_benefit_item', 'Hr\Setup\OtherBenefitController@otherBenefit');
Route::post('hr/setup/other_benefit_item', 'Hr\Setup\OtherBenefitController@otherBenefitStore');
Route::get('hr/setup/other_benefit_delete/{id}', 'Hr\Setup\OtherBenefitController@otherBenefitDelete');

//other Benefit
Route::post('hr/payroll/other_benefit', 'Hr\Recruitment\BenefitController@otherBenefitStore');
//Attendance bonus
Route::get('hr/setup/attendance_bonus', 'Hr\Setup\AttendanceBonusController@showForm');
Route::post('hr/setup/attendance_bonus_store', 'Hr\Setup\AttendanceBonusController@attBonusStore');

//Retirement Policy
Route::get('hr/setup/retirement_policy', 'Hr\Setup\RetirementPolicyContorller@index')->middleware(['permission:Retirement']);
Route::post('hr/setup/retirement_policy_save', 'Hr\Setup\RetirementPolicyContorller@saveData')->middleware(['permission:Retirement']);
Route::get('hr/setup/retirement/get_employee_list', 'Hr\Setup\RetirementPolicyContorller@getEmployeeList');
Route::get('hr/setup/retirement/get_employee_details', 'Hr\Setup\RetirementPolicyContorller@getEmployeeDetails');



//---------HR / Profile-----------//


	//File Tag Print
	Route::get('hr/recruitment/file_tag', 'Hr\Reports\FileTagController@showForm');
	Route::post('hr/reports/filetag/search', 'Hr\Reports\FileTagController@fileTagSearch');

	// JOB CARD
	Route::get('hr/operation/job_card', 'Hr\Reports\JobCardController@jobCard')->middleware(['permission:Job Card']);


	//EMPLOYEE
	Route::get('hr/reports/employee_report', 'Hr\Reports\EmployeeReportController@report');

	Route::get('hr/buyermode/job_card', 'Hr\BuyerMode\BmodeJobCardController@jobCard');
	Route::get('hr/buyermode/generate_att_salary', 'Hr\BuyerMode\BmodeJobCardController@generateBuyerModeAttSalary');

	#----------  Reports By Mati-----------#
	//Salary/Wages Increment Status
	Route::get('hr/recruitment/increment_report', 'Hr\Reports\IncrementReportController@incrementReport');
	Route::get('hr/recruitment/nominee', 'Hr\Reports\NomineeController@showForm')->middleware(['permission:Nominee']);
	Route::get('hr/reports/leave_log', 'Hr\Reports\LeaveLogController@showForm')->middleware(['permission:Leave Log']);
	Route::get('hr/reports/leave_check', 'Hr\Reports\LeaveLogController@checkDueLeave');

	Route::get('hr/reports/salary_sheet', 'Hr\Reports\SalarySheetController@showForm')->middleware(['permission:Salary Sheet']);
	Route::any('hr/reports/save_salary_sheet', 'Hr\Reports\SalarySheetController@saveSalarySheet')->middleware(['permission:Salary Sheet']);
	Route::any('hr/reports/print_salary_sheet', 'Hr\Reports\SalarySheetController@printPage')->middleware(['permission:Salary Sheet']);
	
	/*Route::get('hr/reports/salary_sheet_unit_wise', 'Hr\Reports\SalarySheetController@salarySheetUnitWise');
	Route::post('hr/reports/save_salary_sheet_unit_wise', 'Hr\Reports\SalarySheetController@saveSalarySheetUnit');
	Route::get('hr/reports/salary_sheet_unit_wise_day', 'Hr\Reports\SalarySheetController@salarySheetUnitWiseday');

	Route::post('hr/reports/save_salary_sheet_unit_wise_data', 'Hr\Reports\SalarySheetController@saveSalarySheetUnitData');*/
	// warning notice
	Route::get('hr/operation/warning-notice', 'Hr\Operation\WarningNoticeController@index');
	Route::post('hr/operation/warning-notice-first', 'Hr\Operation\WarningNoticeController@firstStep');
	Route::post('hr/operation/warning-notice-second', 'Hr\Operation\WarningNoticeController@secondStep');
	Route::post('hr/operation/warning-notice-third', 'Hr\Operation\WarningNoticeController@thirdStep');

	Route::get('hr/reports/warning-notices', 'Hr\Operation\WarningNoticeController@list');
	Route::get('hr/reports/warning-notice-data', 'Hr\Operation\WarningNoticeController@listData');

	Route::get('hr/operation/payslip', 'Hr\Reports\PayslipController@showForm')->middleware(['permission:Payslip']);

	Route::get('hr/reports/payslip_buyer', 'Hr\BuyerMode\BmodePaySlipController@showForm');

	Route::get('hr/reports/attendance_report', 'Hr\Reports\AttendanceReportController@showForm')->middleware(['permission:Attendance Report']);
	Route::get('hr/reports/floor_ass_by_unit', 'Hr\Reports\IncrementReportController@floorAssByUnit');

	Route::get('hr/reports/attendance_summary_report', 'Hr\Reports\AttendanceReportController@showForm2')->middleware(['permission:Attendance Summary Report']);

	Route::get('hr/reports/get_att_summary', 'Hr\Reports\AttendanceReportController@attSummaryReport')->middleware(['permission:Attendance Summary Report']);
	Route::get('hr/reports/get-att-emp', 'Hr\Reports\AttendanceReportController@getAttEmployee')->middleware(['permission:Attendance Summary Report']);

	Route::get('hr/reports/get-daily-att-excel', 'Hr\Reports\AttendanceReportController@getAttEmployee');

	// reports absent issue
	Route::get('hr/reports/before-absent-after-present', 'Hr\Reports\DailyActivityReportController@beforeAfterStatus');
	Route::get('hr/reports/before-absent-after-present-report', 'Hr\Reports\DailyActivityReportController@beforeAfterReport');

	// yearly acitvity
	Route::get('hr/reports/employee-yearly-activity', 'Hr\Reports\DailyActivityReportController@employeeActivity');
	Route::get('hr/reports/employee-yearly-activity-report', 'Hr\Reports\DailyActivityReportController@employeeActivityReport');
	Route::get('hr/reports/employee-yearly-activity-report-modal', 'Hr\Reports\DailyActivityReportController@employeeActivityReportModal');

	// Daily Attendance Report
	Route::get('hr/reports/daily-attendance-activity', 'Hr\Reports\DailyActivityReportController@attendance');
	Route::get('hr/reports/daily-attendance-activity-report', 'Hr\Reports\DailyActivityReportController@attendanceReport');
	Route::get('hr/reports/daily-present-absent-activity-report', 'Hr\Reports\DailyActivityReportController@presentAbsentReport');
	Route::get('hr/reports/activity-report-excle', 'Hr\Reports\DailyActivityReportController@activityExcle');
	// monthly report
	Route::get('hr/reports/monthly-attendance-activity', 'Hr\Reports\MonthlyActivityReportController@attendance');
	Route::get('hr/reports/monthly-attendance-activity-data', 'Hr\Reports\MonthlyActivityReportController@attendanceData');
	Route::get('hr/reports/monthly-reports', 'Hr\Reports\MonthlyReportController@index');
	Route::get('hr/reports/monthly-maternity-report', 'Hr\Reports\MonthlyReportController@maternity');

	// daily activity audit 
	Route::get('hr/daily-activity-audit', 'Hr\Reports\DailyActivityReportController@attendanceAudit');

	// monthly salary audit 
	Route::get('hr/monthly-salary-audit', 'Hr\Reports\MonthlyActivityReportController@salaryAudit');
	Route::post('hr/operation/salary-audit', 'Hr\Operation\SalaryProcessController@salaryAuditStatus');

	// monthly report
	Route::get('hr/reports/monthly-salary', 'Hr\Reports\MonthlyActivityReportController@salary');
	Route::get('hr/reports/monthly-salary-report', 'Hr\Reports\MonthlyActivityReportController@salaryReport');
	Route::get('hr/reports/employee-yearly-salary-modal', 'Hr\Reports\MonthlyActivityReportController@salaryReportModal');
	Route::get('hr/reports/employee-salary-modal', 'Hr\Reports\MonthlyActivityReportController@empSalaryModal');


	#------------- Search associate with paramenters(unit, floor, line)---------#
	Route::get('hr/reports/search_associate', 'Hr\Reports\IncrementReportController@searchAssociate');
	Route::get('hr/recruitment/background-verification', 'Hr\Reports\BackgroundController@backgroundVerification');
	Route::post('hr/recruitment/background-verification', 'Hr\Reports\BackgroundController@backgroundVerificationStore');

	Route::get('hr/reports/absent_status', 'Hr\Reports\AbsentStatusController@showForm');

	Route::get('hr/reports/line_wise_att', 'Hr\Reports\LineWiseAttController@lineWiseAtt');
	Route::get('hr/reports/line_by_unit','Hr\Reports\LineWiseAttController@getLineByUnit');

	// Test
	Route::get('hr/reports/line_wise_att2', 'Hr\Reports\LineWiseAttController2@lineWiseAtt');
	Route::get('hr/reports/line_by_unit2','Hr\Reports\LineWiseAttController2@getLineByUnit');

	//
	Route::get('hr/reports/monthy_increment', 'Hr\Reports\MonthlyIncrementController@increment')->middleware(['permission:Monthly Increment']);
	Route::get('hr/reports/emp_performance/{associate_id}/{date}', 'Hr\Reports\MonthlyIncrementController@empPerformance');
	Route::post('hr/reports/emp_performance_save', 'Hr\Reports\MonthlyIncrementController@empPerformanceSave');
	Route::get('hr/reports/emp_performance_increment_list/{associate_id}', 'Hr\Reports\MonthlyIncrementController@empPerformanceIncrementList');

	Route::get('hr/reports/absent_status', 'Hr\Reports\AbsentStatusController@showForm');

	// unit wise shift report
	Route::get('hr/reports/unit-wise-shift', 'Hr\Reports\UnitReportsController@shiftIndex');
	Route::get('hr/reports/unit-wise-shift-report', 'Hr\Reports\UnitReportsController@shiftReport');
//Buyer Mode
Route::get('hr/setup/buyermode','Hr\Setup\BuyerModeSetupController@buyerMode');
Route::post('hr/setup/buyermode','Hr\Setup\BuyerModeSetupController@buyerModeStore');
Route::get('hr/setup/getholidays','Hr\Setup\BuyerModeSetupController@getHolidayList');
Route::get('hr/setup/buyer_template_update/{id}', 'Hr\Setup\BuyerModeSetupController@editBuyerModeTemplate');
Route::post('hr/setup/buyer_template_update', 'Hr\Setup\BuyerModeSetupController@editActionBuyerModeTemplate');

	Route::get('hr/reports/bonus_slip', 'Hr\Reports\BonusSlipController@showForm');
	//new bonus slip--------------------------------
	Route::get('hr/operation/bonus-sheet', 'Hr\Reports\BonusSlipController@newShowForm');
	Route::get('hr/reports/bonus-slip-generate', 'Hr\Reports\BonusSlipController@generate');
	Route::post('hr/reports/employee-bonus-disburse', 'Hr\Reports\BonusSlipController@disburse');
	//----------------------------------------------
	Route::get('hr/reports/floor_by_unit', 'Hr\Reports\BonusSlipController@floorByUnit');
	Route::get('hr/reports/bonustype_month_year_by_id', 'Hr\Reports\BonusSlipController@btMonthYear');
	//daily OT Report
	Route::get('hr/reports/daily_ot_report', 'Hr\Reports\DailyOTReportController@dailyOT');

	//worker register
	Route::get('hr/reports/worker_register', 'Hr\Reports\workerRegisterController@workerRegister');
	Route::get('hr/reports/worker_register_table', 'Hr\Reports\workerRegisterController@workerRegisterList');

	Route::get('hr/reports/worker_register_employee', 'Hr\Reports\workerRegisterController@associtaeUnitSearch');

	//Unit Attendance report
	Route::get('hr/reports/unitattendance', 'Hr\Reports\unitAttendanceController@unitAttendance');
	Route::get('hr/reports/unitattendance_table', 'Hr\Reports\unitAttendanceController@unitAttendanceList');

	//Earn Leave Payment Sheet
	Route::get('hr/operation/earn-leave-payment', 'Hr\Reports\earnLeaveController@earnLeavePayment');
	Route::get('hr/reports/earnleavepayment_floor', 'Hr\Reports\earnLeaveController@floor');

	Route::get('hr/reports/earnleavepayment_table', 'Hr\Reports\earnLeaveController@earnLeavePaymentList');

	Route::post('hr/reports/earnleavepaymentstore', 'Hr\Reports\earnLeaveController@earnLeavePaymentStore');


	//Extra OT Sheet

	Route::get('hr/reports/extraotsheet', 'Hr\Reports\extraOtSheetController@extraOtSheet');
	Route::get('hr/reports/extra_ot_list', 'Hr\Reports\extraOtSheetController@extraOtListGenerate');
	// rubel
	Route::get('hr/reports/extra_ot_chank', 'Hr\Reports\extraOtSheetController@extraOtChank');
	Route::get('hr/reports/extra_ot_chank_list', 'Hr\Reports\extraOtSheetController@extraOtChankList');

	// Old Fixed Salary Sheet
	 Route::get('hr/reports/fixed_salary_list', 'Hr\Reports\FixedSalarySheetController@fixedSalaryListGenerate');

	// New Fixed salary sheet
	Route::get('hr/operation/fixed-salary-sheet', 'Hr\Reports\FixedSalarySheet2Controller@showForm')->middleware(['permission:Fixed Salary Sheet']);
	//salary sheet generate

	Route::get('hr/reports/salary-sheet-generate', 'Hr\Reports\SalarySheetGenerateController@index');
	Route::post('hr/reports/salary-sheet-generate', 'Hr\Reports\SalarySheetGenerateController@process');
	Route::post('hr/reports/salary-sheet-generate-process', 'Hr\Reports\SalarySheetGenerateController@processSalary');


	//Multiple Salary Sheet
	
	Route::get('hr/reports/salary-sheet-custom-multi-search', 'Hr\Reports\SalarySheetCustomController@multiSearch');

	Route::get('hr/reports/salary-sheet-custom_buyer', 'Hr\BuyerMode\BmodeSalarySheetController@index');
	Route::get('hr/reports/salary-sheet-custom-multi-search_buyer', 'Hr\BuyerMode\BmodeSalarySheetController@multiSearch');
	Route::get('hr/reports/ajax_get_employees_buyer', 'Hr\BuyerMode\BmodeSalarySheetController@ajaxGetEmployee');

	Route::get('hr/operation/salary-sheet', 'Hr\Reports\SalarySheetCustomController@index')->middleware(['permission:Salary Sheet']);

	//Multiple Salary Sheet ()
	Route::group(['prefix' => 'hr/reports','namespace' => 'Hr\Reports'], function(){
		
		Route::get('salary-sheet-custom-extra-ot', 'SalarySheetCustomController@salary_sheet_extra_ot');
		Route::get('salary-sheet-custom-individual-search', 'SalarySheetCustomController@individualSearch');
		Route::get('salary-sheet-custom-multi-search', 'SalarySheetCustomController@multiSearch');
		Route::post('salary-sheet-employee-wise', 'SalarySheetCustomController@employeeWise');

		Route::get('ajax_get_employees', 'SalarySheetCustomController@ajaxGetEmployee');
		Route::post('employee-salary-disbursed', 'SalarySheetCustomController@empDisbursed');

		Route::post('ajax_get_multi_search_result_chunk', 'SalarySheetCustomController@ajaxGetMultiSearchResultChunk');
		// for extra ot
		Route::post('ajax_get_multi_search_result_extra_ot', 'SalarySheetCustomController@ajaxGetMultiSearchResultExtraOtChunk');
		Route::post('ajax_get_multi_search_result_list', 'SalarySheetCustomController@ajaxGetMultiSearchResultList');
	});



	//Service Book

	Route::get('hr/employee/servicebook', 'Hr\ServiceBook\ServiceBookController@showForm');
	Route::post('hr/operation/servicebookstore', 'Hr\ServiceBook\ServiceBookController@servicebookStore');
	Route::get('hr/operation/servicebookedit/{sb_id?}', 'Hr\ServiceBook\ServiceBookController@servicebookEdit');
	Route::post('hr/operation/servicebookupdate', 'Hr\ServiceBook\ServiceBookController@servicebookUpdate');
	Route::get('hr/operation/servicebookpage', 'Hr\ServiceBook\ServiceBookController@servicebookPage');

	//Cost Mapping
	Route::get('hr/employee/cost-mapping', 'Hr\Recruitment\CostMappingController@showForm')->middleware(['permission:Cost Distribution']);
	Route::get('hr/operation/gross_salary', 'Hr\Recruitment\CostMappingController@getAssGross')->middleware(['permission:Cost Distribution']);

	Route::post('hr/operation/unit_map', 'Hr\Recruitment\CostMappingController@unitMapStore')->middleware(['permission:Cost Distribution']);
	Route::post('hr/operation/area_map', 'Hr\Recruitment\CostMappingController@areaMapStore')->middleware(['permission:Cost Distribution']);
	Route::get('hr/operation/cost_mapping/{id}', 'Hr\Recruitment\CostMappingController@viewMap')->middleware(['permission:Cost Distribution']);
	Route::get('hr/operation/cost_mapping_edit/{id}', 'Hr\Recruitment\CostMappingController@editMap')->middleware(['permission:Cost Distribution']);

	Route::post('hr/operation/unit_map_update', 'Hr\Recruitment\CostMappingController@unitMapUpdate')->middleware(['permission:Cost Distribution']);
	Route::post('hr/operation/area_map_update', 'Hr\Recruitment\CostMappingController@areaMapUpdate')->middleware(['permission:Cost Distribution']);

	Route::get('hr/employee/cost_mapping_list', 'Hr\Recruitment\CostMappingController@mapList')->middleware(['permission:Cost Distribution List']);
	Route::post('hr/operation/cost_mapping_data', 'Hr\Recruitment\CostMappingController@mapData')->middleware(['permission:Cost Distribution List']);

	//Frequent Manual Attendance report
	Route::get('hr/reports/manual_attendance', 'Hr\Reports\manualAttendanceReportController@manualAttendanceForm');
	Route::get('hr/reports/manual_attendance_list', 'Hr\Reports\manualAttendanceReportController@manualAttendanceList');

	// Group Attendance report

	Route::get('hr/reports/group_attendance', 'Hr\Reports\GroupAttendanceController@showForm')->middleware(['permission:Group Attendance']);
	Route::post('hr/reports/group_attendance', 'Hr\Reports\GroupAttendanceController@showForm')->middleware(['permission:Group Attendance']);

	Route::get('hr/logs/desg_update_log', 'Hr\Logs\LogController@designationLog');
	Route::post('hr/logs/desg_update_log_data', 'Hr\Logs\LogController@designationLogData');


	// hr late count default
	Route::get('hr/setup/late_count_default', 'Hr\Setup\HrLateCountController@showForm');
	Route::post('hr/setup/save_late_count_default', 'Hr\Setup\HrLateCountController@saveLateCountDefault');
		//----changed on 17-10-2019----
	Route::post('hr/setup/ajax_get_shifts', 'Hr\Setup\HrLateCountController@ajaxGetLateCountUnitValue');
	Route::post('hr/setup/ajax_get_default_value', 'Hr\Setup\HrLateCountController@ajaxGetLateCountDefaultValue');
	// hr late count customize
	Route::get('hr/setup/late_count_customize', 'Hr\Setup\HrLateCountCustomizeController@showForm');
	Route::get('hr/setup/late_count_customize/get_shifts_by_unit', 'Hr\Setup\HrLateCountCustomizeController@getShiftsByUnit');
	Route::post('hr/setup/save_late_count_customize', 'Hr\Setup\HrLateCountCustomizeController@saveLateCountCustomize');
	Route::get('hr/setup/edit_late_count_customize/{id}', 'Hr\Setup\HrLateCountCustomizeController@editLateCountCustomize');
	Route::post('hr/setup/update_late_count_customize/{id}', 'Hr\Setup\HrLateCountCustomizeController@updateLateCountCustomize');
	Route::get('hr/setup/delete_late_count_customize/{id}', 'Hr\Setup\HrLateCountCustomizeController@deleteLateCountCustomize');



	//New Routes----------------------------------------------------------------------------------------------------------
	//-----Employee unit change route
	Route::get('hr/operation/location_change/list', 'Hr\Operation\LocationChangeController@showList')->middleware(['permission:Outside List']);
	Route::get('hr/operation/location_change/approve', 'Hr\Operation\LocationChangeController@approveLocation')->middleware(['permission:Manage Outside']);
	Route::get('hr/operation/location_change/reject/{id}', 'Hr\Operation\LocationChangeController@rejectLocation')->middleware(['permission:Manage Outside']);
	Route::get('hr/operation/location_change/entry', 'Hr\Operation\LocationChangeController@showForm')->middleware(['permission:Manage Outside']);
	Route::post('hr/operation/location_change/entry', 'Hr\Operation\LocationChangeController@storeData')->middleware(['permission:Manage Outside']);

	

	
	//------Bouns Type Library
	Route::get('hr/setup/bonus_type', 'Hr\Setup\BonusTypeController@index');
	Route::post('hr/setup/bonus_type_save', 'Hr\Setup\BonusTypeController@entrySave');
	Route::get('hr/setup/bonus_type_edit', 'Hr\Setup\BonusTypeController@editDataFetch');
	Route::post('hr/setup/bonus_type_update', 'Hr\Setup\BonusTypeController@entryUpdate');
	Route::get('hr/setup/bonus_type_delete/{id}', 'Hr\Setup\BonusTypeController@entryDelete');

	//----Employee Bonus
	Route::get('hr/operation/employee_bonus', 'Hr\Operation\EmployeeBonusController@index');
	Route::post('hr/operation/employee_bonus_save', 'Hr\Operation\EmployeeBonusController@entrySave');
	Route::get('hr/operation/get_floor', 'Hr\Operation\EmployeeBonusController@getFloorData');
	Route::get('hr/operation/get_line', 'Hr\Operation\EmployeeBonusController@getLineData');
	Route::get('hr/operation/get_department', 'Hr\Operation\EmployeeBonusController@getDepartmentData');
	Route::get('hr/operation/get_section', 'Hr\Operation\EmployeeBonusController@getSectionData');
	Route::get('hr/operation/get_sub_section', 'Hr\Operation\EmployeeBonusController@getSubSectionData');


	//-----------Leave Report
	Route::get('hr/reports/leave_report', 'Hr\Reports\EmployeeLeaveController@index');
	Route::get('hr/reports/leave_report_generate', 'Hr\Reports\EmployeeLeaveController@searchResult');


	//--------Substitude holidays
	Route::get('hr/opration/substitute_holiday', 'Hr\Operation\SubstituteHolidayController@index');
	Route::post('hr/operation/substitute_holiday_save', 'Hr\Operation\SubstituteHolidayController@saveData');






//---------HR / ESS-----------//

// Grievance Appeal
Route::get('hr/ess/grievance/appeal_list', 'Hr\Ess\GrievanceAppealController@showList');
Route::post('hr/ess/grievance/appeal_data', 'Hr\Ess\GrievanceAppealController@getData');
Route::get('hr/ess/grievance/appeal', 'Hr\Ess\GrievanceAppealController@showForm');
Route::post('hr/ess/grievance/appeal', 'Hr\Ess\GrievanceAppealController@saveData');

// Loan Application
Route::get('hr/payroll/loan_list', 'Hr\Ess\LoanApplicationController@loanList');
Route::get('hr/payroll/loan', 'Hr\Ess\LoanApplicationController@loan');
Route::post('hr/payroll/loan', 'Hr\Ess\LoanApplicationController@loanStore');

Route::post('hr/ess/loan_data', 'Hr\Ess\LoanApplicationController@getData');
Route::get('hr/ess/loan_application', 'Hr\Ess\LoanApplicationController@showForm');
Route::post('hr/ess/loan_application', 'Hr\Ess\LoanApplicationController@saveData');
Route::get('hr/ess/loan_history', 'Hr\Ess\LoanApplicationController@loanHistory');
Route::get('hr/ess/loan_status/{id}/{associate_id}', 'Hr\Ess\LoanApplicationController@showLoanStatus');
Route::post('hr/ess/loan_status', 'Hr\Ess\LoanApplicationController@updateLoanStatus');

// Leave Application
Route::get('hr/ess/leave_application', 'Hr\Ess\LeaveApplicationController@showForm');
Route::post('hr/ess/leave_application', 'Hr\Ess\LeaveApplicationController@saveData');
Route::get('hr/ess/leave_history', 'Hr\Ess\LeaveApplicationController@leaveHistory');
Route::post('hr/ess/leave_check', 'Hr\Ess\LeaveApplicationController@leaveCheck');
Route::post('hr/ess/leave_length_check', 'Hr\Ess\LeaveApplicationController@leaveLeangthCheck');
Route::post('hr/ess/associates_leave', 'Hr\Ess\LeaveApplicationController@associatesLeave');
Route::post('hr/ess/attendance_check', 'Hr\Ess\LeaveApplicationController@attendanceCheck');

// Medical Incident
Route::get('hr/employee/medical_incident', 'Hr\Ess\MedicalIncidentController@medicalIncident');
Route::post('hr/ess/medical_incident', 'Hr\Ess\MedicalIncidentController@medicalIncidentStore');
Route::get('hr/employee/medical_incident_edit/{id}', 'Hr\Ess\MedicalIncidentController@medicalIncidentEdit');
Route::post('hr/ess/medical_incident_update', 'Hr\Ess\MedicalIncidentController@update');
Route::get('hr/employee/medical_incident_list', 'Hr\Ess\MedicalIncidentController@medicalIncidentList');
Route::post('hr/ess/medical_incident_data', 'Hr\Ess\MedicalIncidentController@medicalIncidentData');

//Out Side Request

Route::post('hr/ess/out_side_request/entry', 'Hr\Ess\OutsideRequestController@storeData');
Route::get('hr/ess/out_side_request/delete/{id}', 'Hr\Ess\OutsideRequestController@deleteRequest');

// ess menu
Route::get('ess/out_side_request/entry', 'Hr\Ess\OutsideRequestController@showForm');
Route::get('ess/leave_application', 'Hr\Ess\LeaveApplicationController@showForm');
Route::get('ess/loan_application', 'Hr\Ess\LoanApplicationController@showForm');
Route::get('ess/grievance/appeal', 'Hr\Ess\GrievanceAppealController@showForm');




//--------Database Backup---------------//
Route::get('hr/database_backup', 'Hr\DatabaseBackupController@index');
Route::get('hr/db_backup/create', 'Hr\DatabaseBackupController@create');
Route::get('hr/db_file/download/{file_name}', 'Hr\DatabaseBackupController@getDownload');
Route::get('hr/db_file/delete/{file_name}', 'Hr\DatabaseBackupController@deleteFile');
Route::get('hr/db_file/offload/{file_name}', 'Hr\DatabaseBackupController@offloadData');
Route::get('hr/db_file/use_data/{file_name}', 'Hr\DatabaseBackupController@loadData');
///-------



///-----Absent or Present List operation..............
Route::get('hr/reports/attendance-consecutive', 'Hr\Operation\AbsentPresentListController@absentPresentIndex')->middleware(['permission:Attendance Consecutive Report']);
Route::get('hr/operation/attendance_report_data', 'Hr\Operation\AbsentPresentListController@attendanceReportData');
///-----Absent or Present List operation end..............


///---Event-history----///

Route::get("hr/reports/event_history", "Hr\Reports\EventHistoryController@showList")->middleware(['permission:Event History']);
Route::get("hr/reports/event_history_data", "Hr\Reports\EventHistoryController@listData")->middleware(['permission:Event History']);
Route::get("hr/reports/event_history_detail", "Hr\Reports\EventHistoryController@getDetail")->middleware(['permission:Event History']);


// attendance file rollback process
Route::get("hr/operation/attendance-rollback-get-date", "Hr\Operation\AttendanceRollbackController@getDate");
Route::get("hr/operation/attendance-rollback", "Hr\Operation\AttendanceRollbackController@index");
Route::post("hr/operation/attendance-rollback", "Hr\Operation\AttendanceRollbackController@process");



//new operations routes



//system setting routes
Route::get('hr/setup/default_system_setting','Hr\Setup\SystemSettingController@showForm');
Route::post('hr/setup/default_system_setting_save','Hr\Setup\SystemSettingController@saveForm');
Route::get('hr/setup/default_system_setting_update/{id}','Hr\Setup\SystemSettingController@updateForm');
Route::post('hr/setup/default_system_setting_update_data','Hr\Setup\SystemSettingController@updateStore');
Route::get('hr/setup/default_system_setting_delete/{id}','Hr\Setup\SystemSettingController@deleteData');


Route::get('hr/att-json', 'Hr\AttArchiveController@index');
Route::get('hr/line-report', 'Hr\LineReportController@index');
// voucher section
Route::get('hr/operation/voucher', 'Hr\Operation\VoucherController@index');
Route::post('hr/operation/voucher', 'Hr\Operation\VoucherController@voucher');

Route::get('hr/operation/partial-salary', 'Hr\Operation\VoucherController@partial');
Route::post('hr/operation/partial-salary', 'Hr\Operation\VoucherController@partialGenerate');
Route::post('hr/operation/partial-salary/disburse', 'Hr\Operation\VoucherController@disburse');

Route::get('/hr/reports/monthly-ot-report', 'Hr\ReportController@monthlyOT');
Route::get('/hr/reports/monthly-mmr-report', 'Hr\ReportController@monthlyMMR');

Route::get('hr/operation/test', 'Hr\Operation\VoucherController@test');
Route::get('hr/test', 'TestController@test');
Route::get('hr/reports/employee-daily-attendance', 'TestController@exportReport');

// test route
Route::get('hr/check-report', 'TestController@check');
