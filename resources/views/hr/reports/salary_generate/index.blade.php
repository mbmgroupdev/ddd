@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        .progress[data-percent]:after {
            color: #000 !important;
        }
        @media only screen and (max-width: 771px) {
            .choice_1_div .col-sm-4 {margin-bottom: 10px;}
            .choice_1_div .col-sm-3 {margin-bottom: 40px;}
        }
        .salary-sheet-content .panel-title {margin-top: 3px; margin-bottom: 3px;}
        .salary-sheet-content .panel-title a{font-size: 15px; display: block;}
        .select2{width: 100% !important;}
        .panel-group { margin-bottom: 5px;}
        .min_sal{height: auto !important;}
        .max_sal{height: auto !important;}
        h3.smaller {font-size: 13px;}
        .header {margin-top: 0;}
    </style>
@endpush

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Operations</a>
                </li>
                <li class="active"> Salary Sheet</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content">
           
            <div id="accordion" class="accordion-style panel-group">
                <div class="panel panel-info">
                    <div class="panel-heading salary-sheet-content">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#multi-search">
                                <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                &nbsp;Salary Sheet Re-Generate
                            </a>
                        </h4>
                    </div>
                    @include('inc.notify')
                    @php
                        $date1 = \Carbon\Carbon::now();
                        $previousMonth = $date1->subMonth()->format('m');
                    @endphp
                    <div class="panel-collapse collapse in" id="multi-search">
                        <div class="panel-body">
                            
                            <div class="form-horizontal">
                                <div class="col-sm-offset-3 col-sm-6 no-padding-left">
                                    <form role="form" method="post" action="{{ url('hr/reports/salary-sheet-generate') }}" id="searchform" >
                                        {{ csrf_field() }} 
                                        <div class="panel panel-info">
                                            <div class="panel-body">
                                                <h3 class="header smaller lighter green">
                                                    <i class="ace-icon fa fa-bullhorn"></i>
                                                    All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                                                </h3>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right align-left" for="month_number">Month <span class="text-red" style="vertical-align: top;">&#42;</span> :</label>
                                                    <div class="col-sm-3">
                                                        {{ Form::select('month', ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'], $previousMonth, ['placeholder'=>'Select Month', 'id'=>'month_number', 'required', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Month field is required', 'required']) }}
                                                        <span class="text-red" id="error_month_s"></span>
                                                    </div>
                                                    <label class="col-sm-3 control-label no-padding-right align-right" for="year">Year <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                                    <div class="col-sm-3">
                                                        
                                                        <input type="number" id="year" class="col-xs-12 yearpicker" placeholder="Enter Year" name="year" value="{{ date('Y') }}" required>
                                                        <span class="text-red" id="error_year_s"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                                    <div class="col-sm-9">
                                                        {{ Form::select('unit', ['1' => 'MBM GARMENTS LTD.', '2' => 'CUTTING EDGE INDUSTRIES LTD.', '8' => 'CUTTING EDGE INDUSTRIES LTD. (WASHING PLANT)', '3' => 'ABSOLUTE QUALITYWEAR LTD.'], null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required', 'required']) }}
                                                        <span class="text-red" id="error_unit_s"></span>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right align-left sr-only" for="disbursed_date">Disbursed Date<span style="color: red">&#42;</span> :</label>
                                                    <div class="col-sm-9">
                                                        <input type="hidden" id="disbursed_date" class="col-xs-12 datepicker" name="disbursed_date" placeholder="Enter Disbursed Date" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-offset-3 col-sm-6 ">
                                                        <button type="submit" class="btn btn-primary btn-xs"
                                                        style=" " ><span class="glyphicon glyphicon-pencil"></span>&nbsp
                                                        Generate</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- <div class="col-sm-6">
                                    <div class="panel panel-warning" style="padding-bottom: 5px;">
                                        <div class="panel-body">
                                            <h3 class="header smaller lighter black">
                                                <i class="ace-icon fa fa-bullhorn"></i>
                                                @php 
                                                    $date = \Carbon\Carbon::now();
                                                    echo $date->subMonth()->format('F'); 
                                                @endphp
                                                Salary Status 
                                            </h3>
                                            <div class="table">
                                                <table class="table table-bordered table-hover">
                                                    <tr class="success">
                                                        <th>SL.</th>
                                                        <th>Unit Name</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>MBM GARMENTS LTD.</td>
                                                        <td><span class="label label-sm label-warning">Pending</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>CUTTING EDGE INDUSTRIES LTD.</td>
                                                        <td><span class="label label-sm label-warning">Pending</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>CUTTING EDGE INDUSTRIES LTD. (WASHING PLANT)</td>
                                                        <td><span class="label label-sm label-warning">Pending</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td>ABSOLUTE QUALITYWEAR LTD.</td>
                                                        <td><span class="label label-sm label-warning">Pending</span>  </td>
                                                    </tr>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>  {{-- page-content end --}}
    </div>  {{-- main-content-inner end --}}
</div>  {{-- main-content end --}}

@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        //month-Year validation------------------
        $('#form-date').on('dp.change',function(){
            $('#to-date').val( $('#form-date').val());    
        });

        $('#to-date, #form-date').on('dp.change',function(){
            var end     = new Date($('#to-date').val()) ;
            var start   = new Date($('#form-date').val());
            if(end < start){
                alert("Invalid!!\n From-Month-Year is latest than To-Month-Year");
                    $('#to-date').val('');
            }
        });
        //manth-Year validation end---------------
    });
</script>

<script>
    var _token = $('input[name="_token"]').val();
    // Reuseable ajax function
    function ajaxOnChange(ajaxUrl, ajaxType, valueObject, successStoreId) {
        $.ajax({
            url : ajaxUrl,
            type: ajaxType,
            data: valueObject,
            success: function(data)
            {
                successStoreId.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    }
    // HR Floor By Unit ID
    var unit = $("#unit");
    var floor = $("#floor")
    unit.on('change', function() {
        ajaxOnChange('{{ url('hr/setup/getFloorListByUnitID') }}', 'get', {unit_id: $(this).val()}, floor);
    });

    //Load Department List By Area ID
    var area = $("#area");
    var department = $("#department");
    area.on('change', function() {
        ajaxOnChange('{{ url('hr/setup/getDepartmentListByAreaID') }}', 'get', {area_id: $(this).val()}, department);
    });

    //Load Section List by department
    var section = $("#section");
    department.on('change', function() {
        ajaxOnChange('{{ url('hr/setup/getSectionListByDepartmentID') }}', 'get', {area_id: area.val(), department_id: $(this).val()}, section);
    });

    //Load Sub Section List by Section
    var subSection = $("#subSection");

    section.on('change', function() {
        ajaxOnChange('{{ url('hr/setup/getSubSectionListBySectionID') }}', 'get', {area_id: area.val(), department_id: department.val(), section_id: $(this).val()}, subSection);
    });
    

    //generate

    
</script>
@endpush


@endsection