@php
	$pageHead = $pageHead;
@endphp
@if(count($group1) > 0)
	@php
		$group1Flug = 0;
		$unit 		= App\Models\Hr\Unit::where('hr_unit_id', $group1[0]->employee->as_unit_id)->first();
		$location 	= App\Models\Hr\Unit::where('hr_unit_id', $group1[0]->employee->as_location)->first();
		$title 		= 'Unit : '.($unit!==NULL?$unit->hr_unit_name_bn:'').' - Location : '.($location!==NULL?$location->hr_unit_name_bn:'');
		$getSalaryList = $group1;
	@endphp
	@include('hr.common.view_salary_sheet_list')
@else
	@php
		$group1Flug = 1;
	@endphp
@endif
@if(count($group2) > 0)

	@php
		$group2Flug = 0;
		$unit 		= App\Models\Hr\Unit::where('hr_unit_id', $group2[0]->employee->as_unit_id)->first();
		$location 	= App\Models\Hr\Unit::where('hr_unit_id', $group2[0]->employee->as_location)->first();
		$title 		= 'Unit : '.($unit!==NULL?$unit->hr_unit_name_bn:'').' - Location : '.($location!==NULL?$location->hr_unit_name_bn:'');
		$title 		= '';
		$getSalaryList = $group2;
	@endphp
	@include('hr.common.view_salary_sheet_list')
@else
	@php $group2Flug = 1; @endphp
@endif

@if(count($group3) > 0)

	@php
		$group3Flug = 0;
		$unit 		= App\Models\Hr\Unit::where('hr_unit_id', $group3[0]->employee->as_unit_id)->first();
		$location 	= App\Models\Hr\Unit::where('hr_unit_id', $group3[0]->employee->as_location)->first();
		$title 		= 'Unit : '.($unit!==NULL?$unit->hr_unit_name_bn:'').' - Location : '.($location!==NULL?$location->hr_unit_name_bn:'');
		$getSalaryList = $group3;
	@endphp
	@include('hr.common.view_salary_sheet_list')
@else
	@php $group3Flug = 1; @endphp
@endif

@if($group1Flug == 1 && $group2Flug == 1 && $group3Flug == 1)
	@php
		$title = '';
		$getSalaryList = [];
	@endphp
	@include('hr.common.view_salary_sheet_list')
@endif