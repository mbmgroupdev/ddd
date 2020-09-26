@extends('hr.layout')
@section('title', 'Cost Mapping')
@section('main-content')
@push('css')
<style type="text/css">
    .input-group-addon{

    height: 20px !important;
    padding-bottom: 0px !important;
    padding-top: 3px !important;

    }
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Employee</a>
                </li>
                <li class="active">Cost Mapping</li>
            </ul>
        </div>
         @include('inc/message')
        <div class="panel"> 
            <div class="panel-heading">
                <ul class="nav nav-pills" id="pills-tab" role="tablist" style="    display: inline-flex;">
                  <li class="nav-item">
                     <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#unit_mapping_tab" role="tab" aria-controls="unit_mapping_tab" aria-selected="true">Unit Mapping</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#area_mapping_tab" role="tab" aria-controls="area_mapping_tab" aria-selected="false">Area Mapping</a>
                  </li>
               </ul>
               <a href="{{url('hr/employee/cost_mapping_list')}}" class="btn btn-primary pull pull-right"><i class="las la-list"></i></a>
            </div>
            <div class="panel-body">
                
               <div class="tab-content mb-5" id="pills-tabContent-2">
                    <div class="tab-pane fade active show" id="unit_mapping_tab" role="tabpanel" aria-labelledby="pills-home-tab">
                        {{ Form::open(['url'=>'hr/operation/unit_map', 'method'=>"post", 'class'=>'form-horizontal']) }}
                            <div class="row justify-content-center">
                                <div class="col-sm-3">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates form-control', 'required'=>'required']) }}
                                        <label  for="associate_id"> Associate's ID</label>
                                    </div> 
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group has-float-label">
                                        <input type="text" name="gross_salary" id="gross_salary" placeholder="Associate's Gross Salary" class="form-control" readonly/>
                                        <label  for="gross_salary"> Gross Salary</label>
                                    </div>
                                </div>
                            </div>     
                                

                            <div class="mt-3 mb-3">
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th colspan="3" style="text-align: center;">Unit Cost Distribution(Mapping)</th>
                                        </tr>
                                        <tr>
                                            <th width="33%">Unit</th>
                                            <th width="33%">Floor</th>
                                            <th width="33%">Line</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        @foreach($unitList AS $unit)
                                            <tr>
                                                <td style="margin: 0px; padding: 0px; border-color: #ddd;">
                                                    <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                        <label class="col-xs-8">
                                                            <input name="selected_unit[]" type="checkbox" value="{{ $unit->hr_unit_id }}" class="ace unitCheck" style="margin: 0px; padding: 0px;"/>
                                                            <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $unit->hr_unit_name }}</span>
                                                        </label>
                                                        <div class="input-group input-group-sm col-xs-3">
                                                            <input type="text" name="unit_percent[<?php echo $unit->hr_unit_id; ?>]" readonly class="form-control unit_percent" placeholder="Unit Amount" placeholder="Amount" data-validation="required" value="0" aria-describedby="sizing-addon3">
                                                            <span class="input-group-addon" id="sizing-addon3">%</span>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td  style="margin: 0px; padding: 0px; border-color: #ddd;" colspan="2">
                                                    <table class="table table-bordered "  style="margin: 0px; padding: 0px;">
                                                        <tbody>
                                                            
                                                            @foreach($floorList AS $floor)
                                                            @if($unit->hr_unit_id== $floor->hr_floor_unit_id)
                                                            <tr>
                                                                <td style="margin: 0px; padding: 0px; border-color: #ddd;" width="50%">
                                                                    <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                                        <label class="col-xs-6">
                                                                            <input name="selected_floor[]" type="checkbox" value="{{ $floor->hr_floor_id }}" class="ace floorCheck" style="margin: 0px; padding: 0px;"/>
                                                                            <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $floor->hr_floor_name }}</span>
                                                                        </label>
                                                                        <div class="input-group input-group-sm col-xs-3">
                                                                            <input type="text" name="floor_percent[<?php echo $unit->hr_unit_id; ?>][<?php echo $floor->hr_floor_id; ?>]" readonly class="form-control floor_percent" placeholder="Floor" value="0" data-validation="required" aria-describedby="sizing-addon3">
                                                                            <span class="input-group-addon" id="sizing-addon3">%</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td  style="margin: 0px; padding: 0px; border-color: #ddd;" width="50%">
                                                                    <table class="table table-bordered "  style="margin: 0px; padding: 0px;">
                                                                        <tbody>
                                                                        
                                                                        @foreach($lineList AS $line)
                                                                        @if($line->hr_line_floor_id== $floor->hr_floor_id)
                                                                            <tr>
                                                                                <td style="margin: 0px; padding: 0px; border: 0px">
                                                                                    <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                                                        <label class="col-xs-6">
                                                                                            <input name="selected_line[]" type="checkbox" value="{{ $line->hr_line_id }}" class="ace lineCheck" style="margin: 0px; padding: 0px;"/>
                                                                                            <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $line->hr_line_name }}</span>
                                                                                        </label>
                                                                                        <div class="input-group input-group-sm col-xs-3">
                                                                                            <input type="text" name="line_percent[<?php echo $unit->hr_unit_id ?>][<?php echo $floor->hr_floor_id ?>][<?php echo $line->hr_line_id ?>]" readonly class="form-control line_percent" placeholder="Line" data-validation="required" value="0" aria-describedby="sizing-addon3">
                                                                                            <span class="input-group-addon" id="sizing-addon3">%</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                        @endif
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                           
                                                            @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>

                                            
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                <button class="btn pull-right btn-primary" type="submit" id="submitUnitButton">
                                    <i class=" fa fa-check "></i> Submit
                                </button>
                            </div>
                        {{ Form::close() }}
                    </div>
                    <div class="tab-pane fade" id="area_mapping_tab" role="tabpanel" aria-labelledby="pills-profile-tab">
                        {{ Form::open(['url'=>'hr/operation/area_map', 'method'=>'post', 'class'=>'form-horizontal']) }}
                            <div class="row justify-content-center">
                                <div class="col-sm-3">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('associate_id_area', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id_area', 'class'=> 'associates form-control', 'required'=>'required']) }}
                                        <label  for="associate_id_area"> Associate's ID</label>
                                    </div> 
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group has-float-label">
                                        <input type="text" name="gross_salary_area" id="gross_salary_area" placeholder="Associate's Gross Salary" class="form-control" readonly/>
                                        <label  for="gross_salary_area"> Gross Salary</label>
                                    </div>
                                </div>
                            </div> 
                                 

                                <div class="mt-3 mb-3">
                                    <table class="table table-bordered ">
                                        <thead>
                                            <tr>
                                                <th colspan="4" style="text-align: center;">Area Cost Distribution(Mapping)</th>
                                            </tr>
                                            <tr>
                                                <th width="25%">Area</th>
                                                <th width="25%">Department</th>
                                                <th width="25%">Section</th>
                                                <th width="25%">Sub-Section</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            @foreach($areaList AS $area)
                                                <tr>
                                                    <td style="margin: 0px; padding: 0px; border-color: #ddd;">
                                                        <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                            <label class="col-xs-7">
                                                                <input name="selected_area[]" type="checkbox" value="{{ $area->hr_area_id }}" class="ace areaCheck" style="margin: 0px; padding: 0px;"/>
                                                                <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $area->hr_area_name }}</span>
                                                            </label>
                                                            <div class="input-group input-group-sm col-xs-4">
                                                                <input type="text" name="area_percent[<?php echo $area->hr_area_id ?>]" readonly class="form-control area_percent" placeholder="Area" value="0" data-validation="required" aria-describedby="sizing-addon3">
                                                                <span class="input-group-addon" id="sizing-addon3">%</span>
                                                            </div>

                                                        </div>
                                                    </td>
                                                    <td style="margin: 0px; padding: 0px; border-color: #ddd;" colspan="3">
                                                        <table class="table table-bordered "  style="margin: 0px; padding: 0px;">
                                                            <tbody>
                                                                
                                                                @foreach($deptList AS $dept)
                                                                @if($area->hr_area_id == $dept->hr_department_area_id)
                                                                <tr>
                                                                    <td style="margin: 0px; padding: 0px; border-color: #ddd;" width="33%">
                                                                        <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                                            <label class="col-xs-7">
                                                                                <input name="selected_dept[]" type="checkbox" value="{{ $dept->hr_department_id }}" class="ace deptCheck" style="margin: 0px; padding: 0px;"/>
                                                                                <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $dept->hr_department_name }}</span>
                                                                            </label>
                                                                            <div class="input-group input-group-sm col-xs-4">
                                                                                <input type="text" name="dept_percent[<?php echo $area->hr_area_id ?>][<?php echo $dept->hr_department_id ?>]" readonly class="form-control dept_percent" placeholder="Floor" data-validation="required" value="0" aria-describedby="sizing-addon3">
                                                                                <span class="input-group-addon" id="sizing-addon3">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td  style="margin: 0px; padding: 0px; border-color: #ddd;" colspan="2">
                                                                        <table class="table table-bordered "  style="margin: 0px; padding: 0px;">
                                                                            <tbody>
                                                                            @foreach($sectionList AS $section)
                                                                            @if($section->hr_section_department_id== $dept->hr_department_id)
                                                                                <tr>
                                                                                    <td style="margin: 0px; padding: 0px; border-color: #ddd" width="50%">
                                                                                        <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                                                            <label class="col-xs-7">
                                                                                                <input name="selected_section[]" type="checkbox" value="{{ $section->hr_section_id }}" class="ace sectionCheck" style="margin: 0px; padding: 0px;"/>
                                                                                                <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $section->hr_section_name }}</span>
                                                                                            </label>
                                                                                            <div class="input-group input-group-sm col-xs-4">
                                                                                                <input type="text" name="section_percent[<?php echo $area->hr_area_id ?>][<?php echo $dept->hr_department_id ?>][<?php echo $section->hr_section_id  ?>]" readonly class="form-control section_percent" placeholder="Line" data-validation="required" value="0" aria-describedby="sizing-addon3">
                                                                                                <span class="input-group-addon" id="sizing-addon3">%</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td  style="margin: 0px; padding: 0px; border: 0px;" width="50%">
                                                                                        <table class="table table-bordered "  style="margin: 0px; padding: 0px;">
                                                                                            <tbody>
                                                                                            @foreach($subSecList AS $subSec)
                                                                                            @if($subSec->hr_subsec_section_id== $section->hr_section_id)
                                                                                                <tr>
                                                                                                    <td style="margin: 0px; padding: 0px; border: 0px">
                                                                                                        <div class="checkbox" style="margin: 0px; padding: 2px;">
                                                                                                            <label class="col-xs-7">
                                                                                                                <input name="selected_subSection[]" type="checkbox" value="{{ $subSec->hr_subsec_id }}" class="ace subSectionCheck" style="margin: 0px; padding: 0px;"/>
                                                                                                                <span class="lbl" style="margin: 0px; padding: 0px;"> {{ $subSec->hr_subsec_name }}</span>
                                                                                                            </label>
                                                                                                            <div class="input-group input-group-sm col-xs-4">
                                                                                                                <input type="text" name="subSection_percent[<?php echo $area->hr_area_id ?>][<?php echo $dept->hr_department_id ?>][<?php echo $section->hr_section_id ?>][<?php echo $subSec->hr_subsec_id ?>]" readonly class="form-control subSection_percent" placeholder="Line" data-validation="required" aria-describedby="sizing-addon3" value="0">
                                                                                                                <span class="input-group-addon" id="sizing-addon3">%</span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endif
                                                                                            @endforeach
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                            @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <button class="btn pull-right btn-primary" type="submit" id="submitAreaButton">
                                        <i class=" fa fa-check "></i> Submit
                                    </button>
                                </div>
                            {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function()
{  
    
    $("#associate_id").on("change", function(){
        $.ajax({
            url: '{{ url("hr/operation/gross_salary") }}',
            dataType: 'json',
            delay: 250,
            data:{ associate_id: $(this).val() },
            success: function(data)
            {
                $("#gross_salary ").val(data);
            },
            error: function()
            {
                alert('Gross Salary Not found...');
            }
        });
    });
    //check - uncheck Unit
    $('.unitCheck').click(function(){
       var checked = $(this).prop('checked');
       $(this).parent().parent().parent().parent().find('input:checkbox').prop('checked', checked);
       $(this).parent().parent().parent().parent().find('input:text').attr('readonly', !checked);
        $(this).parent().parent().parent().parent().find(':input[readonly=readonly]').val('0');
    });
    //check - uncheck floor
    $('.floorCheck').click(function(){
        var floor_parent_status= $(this)
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .prev()
            .find(".unitCheck").prop('checked');

       var this_floor_status = $(this).prop('checked');

       $(this).parent().parent().parent().parent().find('input:checkbox').prop('checked', this_floor_status);
       $(this).parent().parent().parent().parent().find('input:text').attr('readonly', !this_floor_status);
       $(this).parent().parent().parent().parent().find(':input[readonly=readonly]').val('0');
      
       if(this_floor_status && !floor_parent_status){
                $(this)
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .prev()
                .find(".unitCheck").prop('checked', true);
                $(this).parent().parent().parent().parent().parent().parent().parent().parent().find(".unit_percent").attr('readonly', false);
            }
    });
    //if floor is checked but Unit is not checked then check the floor
    $('.lineCheck').click(function(){
        var target = $(this).parent().parent().parent().parent().parent().parent().parent();
        var line_parent_status= target.find(".floorCheck").prop('checked');
        var this_line_status= $(this).prop('checked');
        if(this_line_status && !line_parent_status){
            target.prev().find(".floorCheck").prop('checked', true); 
            target.prev().find(".floor_percent").prop('readonly', false);
            var unit_check_status= target.parent().parent().parent().parent().parent().parent().parent().find('.unitCheck').prop("checked");

            if(!unit_check_status){
                target.parent().parent().parent().parent().parent().find('.unitCheck').prop("checked", true);
                target.parent().parent().parent().parent().parent().find('.unit_percent').prop("readonly", false);
            }
        }
        $(this).parent().parent().find('input:text').attr('readonly', !this_line_status);
        $(this).parent().parent().find(':input[readonly=readonly]').val('0');
    });
// Area 
    $("#associate_id_area").on("change", function(){
        $.ajax({
            url: '{{ url("hr/operation/gross_salary") }}',
            dataType: 'json',
            delay: 250,
            data:{ associate_id: $(this).val() },
            success: function(data)
            {
                $("#gross_salary_area ").val(data);
            },
            error: function()
            {
                alert('Gross Salary Not found...');
            }
        });
    });
    //check all area
    $('.areaCheck').click(function(){
       var checked = $(this).prop('checked');
       $(this).parent().parent().parent().parent().find('input:checkbox').prop('checked', checked);
       $(this).parent().parent().parent().parent().find('input:text').attr('readonly', !checked);
       $(this).parent().parent().parent().parent().find(':input[readonly=readonly]').val('0');
    });
    //check - uncheck Department
    $('.deptCheck').click(function(){
        var target= $(this).parent().parent().parent().parent().parent().parent().parent();
        var dept_parent_status= target.prev().find(".areaCheck").prop('checked');
        var this_dept_status = $(this).prop('checked');

        $(this).parent().parent().parent().parent().find('input:checkbox').prop('checked', this_dept_status);
        $(this).parent().parent().parent().parent().find('input:text').attr('readonly', !this_dept_status);      
        $(this).parent().parent().parent().parent().find(':input[readonly=readonly]').val('0');      
       if(this_dept_status && !dept_parent_status){
                target.prev().find(".areaCheck").prop('checked', true);
                target.prev().find(".area_percent").prop('readonly', false);
            }
    });
    //check - uncheck Section
    $('.sectionCheck').click(function(){
        var target= $(this).parent().parent().parent().parent().parent().parent().parent();
        var section_parent_status= target.prev().find(".deptCheck").prop('checked');
        var this_section_status = $(this).prop('checked');
        $(this).parent().parent().parent().parent().find('input:checkbox').prop('checked', this_section_status);
        $(this).parent().parent().parent().parent().find('input:text').attr('readonly', !this_section_status);
        $(this).parent().parent().parent().parent().find(':input[readonly=readonly]').val('0');
       if(this_section_status && !section_parent_status){
                target.prev().find(".deptCheck").prop('checked', true);
                target.prev().find(".dept_percent").prop('readonly', false);
                target.parent().parent().parent().parent().parent().find(".areaCheck").prop('checked', true);
                target.parent().parent().parent().parent().parent().find(".area_percent").prop('readonly', false);
            }
    });
    //check - uncheck Sub-Section
    $('.subSectionCheck').click(function(){
        var target= $(this).parent().parent().parent().parent().parent().parent().parent();
        var subSection_parent_status= target.prev().find(".sectionCheck").prop('checked');
        var this_subSection_status = $(this).prop('checked');
        $(this).parent().parent().parent().parent().find('input:checkbox').prop('checked', this_subSection_status);
        $(this).parent().parent().find('input:text').attr('readonly', !this_subSection_status);
        $(this).parent().parent().find(':input[readonly=readonly]').val('0');
        if(this_subSection_status && !subSection_parent_status){
                target.prev().find(".sectionCheck").prop('checked', true);
                target.prev().find(".section_percent").prop('readonly', false);
                target.parent().parent().parent().parent().parent().find(".deptCheck").prop('checked', true);
                target.parent().parent().parent().parent().parent().find(".dept_percent").prop('readonly', false);
                target.parent().parent().parent().parent().parent().parent().parent().parent().parent().find(".areaCheck").prop('checked', true);
                target.parent().parent().parent().parent().parent().parent().parent().parent().parent().find(".area_percent").prop('readonly', false);
            }

    });
    //unit Submit Calculation check
    $("#submitUnitButton").on("click", function(e){
        var line_sum=0;
        var floor_sum= 0;
        var unit_sum=0;
        var sum=0;
        // summation of unit percentage
        $(".unit_percent").each(function(){
            unit_sum+=parseInt($(this).val());
        });
        //summation of floor percentage
        $(".floor_percent").each(function(){
            floor_sum+=parseInt($(this).val());
        });
        //summation of line percentage
        $(".line_percent").each(function(){
            line_sum+=parseInt($(this).val());
        });
        if(unit_sum != 100){
            alert("Invalid Unit Calculation, Check again!!");
            e.preventDefault();
        }
        else if(floor_sum != 100){
            alert("Invalid Floor Calculation, Check again!!");
            e.preventDefault();
        }
        else if(line_sum != 100){
            alert("Invalid Line Calculation, Check again!!");
            e.preventDefault();
        }
        else if((unit_sum != floor_sum) || (floor_sum!= line_sum) || (line_sum!= unit_sum)){
            alert("Unit,Floor and Line Summation Mismatched, Check again!!");
            e.preventDefault();
        }
        else{
            alert("Congratulations!! valid Calculation, You may proceed");
        }
    });
    //Area Submit Calculation check
    $("#submitAreaButton").on("click", function(e){
        
        var dept_sum= 0;
        var area_sum=0;
        var section_sum=0;
        var subSection_sum=0;
        // summation of area percentage
        $(".area_percent").each(function(){
            area_sum+=parseInt($(this).val());
        });
        //summation of Department percentage
        $(".dept_percent").each(function(){
            dept_sum+=parseInt($(this).val());
        });
        //summation of Section percentage
        $(".section_percent").each(function(){
            section_sum+=parseInt($(this).val());
        });
        //summation of sub Section_percent percentage
        $(".subSection_percent").each(function(){
            subSection_sum+=parseInt($(this).val());
        });
        if(area_sum != 100){
            alert("Invalid Area Calculation, Check again!!");
            e.preventDefault();
        }
        else if(dept_sum != 100){
            alert("Invalid Department Calculation, Check again!!");
            e.preventDefault();
        }
        else if(section_sum != 100){
            alert("Invalid Section Calculation, Check again!!");
            e.preventDefault();
        }
        else if(subSection_sum != 100){
            alert("Invalid Sub-Section Calculation, Check again!!");
            e.preventDefault();
        }
        else if((area_sum != dept_sum) || (dept_sum!= section_sum) || (section_sum!= subSection_sum) || (subSection_sum!=area_sum)){
            alert("Area,Department, Section and Sub-Section Summation Mismatched, Check again!!");
            e.preventDefault();
        }
        else{
            alert("Congratulations!! valid Calculation, You may proceed");
            
        }
    });
}); 
</script> 
@endpush
@endsection