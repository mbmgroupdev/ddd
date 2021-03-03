<style type="text/css">
	.emp-pagination a {
        border-right: 1px solid;
        padding: 0 10px;
    }
    .emp-pagination a.active{
        font-weight: bold;
    }
</style>
<div class="emp-pagination">
    <a class="@if($act_page == 'basic')active @endif" href="{{ url("hr/recruitment/employee/edit/$associate_id") }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Edit Basic Information'>Basic</a>
    <a class="@if($act_page == 'advance')active @endif" href="{{ url("hr/recruitment/operation/advance_info_edit/$associate_id") }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Edit Advance Information'>Advance</a>
    <a class="@if($act_page == 'medical')active @endif" href="{{url("hr/recruitment/operation/medical_info_edit/$associate_id")}}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Edit Medical Information'>Medical</a>
    <a class="@if($act_page == 'benefit')active @endif" href="{{ url("hr/payroll/employee-benefit?associate_id=$associate_id") }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Edit Benefit Information'>Benefit</a>
    <a href="{{ url("hr/recruitment/operation/advance_info_edit/$associate_id") }}#bangla" data-toggle="tooltip" data-placement="top" title="" data-original-title='Edit Bangla Information'>Bangla</a>
    <a href="{{ url("hr/recruitment/operation/advance_info_edit/$associate_id") }}#education" data-toggle="tooltip" data-placement="top" title="" data-original-title='Edit Education Information'>Education</a>
</div>