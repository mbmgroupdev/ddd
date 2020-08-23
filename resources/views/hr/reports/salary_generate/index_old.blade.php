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
                                &nbsp;Salary Sheet Generate
                            </a>
                        </h4>
                    </div>
                    @include('inc.notify')
                    <div class="panel-collapse collapse in" id="multi-search">
                        <div class="panel-body">
                            <h3 class="header smaller lighter green">
                                <i class="ace-icon fa fa-bullhorn"></i>
                                All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                            </h3>
                            <form role="form" method="post" action="{{ url('hr/reports/salary-sheet-generate') }}" id="searchform" >
                                {{ csrf_field() }} 
                                <div class="form-horizontal col-xs-12">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding align-left" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                            <div class="col-sm-9">
                                                {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required', 'required']) }}
                                                <span class="text-red" id="error_unit_s"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding align-left" for="floor"> Floor :</label>
                                            <div class="col-sm-9">
                                                {{ Form::select('floor', !empty(Request::get('unit'))?$floorList:[], Request::get('floor'), ['placeholder'=>'Select Floor', 'id'=>'floor', 'style'=>'width:100%']) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding align-left" for="area"> Area : </label>
                                            <div class="col-sm-9">
                                                {{ Form::select('area', $areaList, Request::get('area'), ['placeholder'=>'Select Area', 'id'=>'area', 'style'=> 'width:100%', 'data-validation-error-msg'=>'The Area field is required']) }}
                                                <span class="text-red" id="error_area_s"></span>
                                            </div>
                                        </div>
                                        

                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding align-left" for="department">Department :</label>
                                            <div class="col-sm-8">
                                                {{ Form::select('department', !empty(Request::get('area'))?$deptList:[], Request::get('department'), ['placeholder'=>'Select Department ', 'id'=>'department', 'style'=> 'width:100%', 'data-validation-error-msg'=>'The Department field is required']) }}
                                                <span class="text-red" id="error_department_s"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 no-padding control-label  align-left" for="department">Section : </label>
                                            <div class="col-sm-8">
                                                {{ Form::select('section', !empty(Request::get('department'))?$sectionList:[], Request::get('section'), ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding align-left" for="department">Sub-Section : </label>
                                            <div class="col-sm-8">
                                                {{ Form::select('sub_section', !empty(Request::get('section'))?$subSectionList:[], Request::get('subSection'), ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                            </div>
                                        </div>
                                        

                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right align-left" for="month_number">Month <span class="text-red" style="vertical-align: top;">&#42;</span> :{{-- <span style="color: red">&#42;</span> --}}</label>
                                            <div class="col-sm-9">
                                                <select id="month_number" name="month" class="col-xs-12 month_number" required>
                                                    <option value="">Select Month</option>
                                                    <option value="01">January</option>
                                                    <option value="02">February</option>
                                                    <option value="03">March</option>
                                                    <option value="04">April</option>
                                                    <option value="05">May</option>
                                                    <option value="06">June</option>
                                                    <option value="07">July</option>
                                                    <option value="08">August</option>
                                                    <option value="09">September</option>
                                                    <option value="10">October</option>
                                                    <option value="11">November</option>
                                                    <option value="12">December</option>
                                                </select>
                                                <span class="text-red" id="error_month_s"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right align-left" for="year">Year <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                            <div class="col-sm-9">
                                                
                                                <input type="number" id="year" class="col-xs-12 yearpicker" placeholder="Enter Year" name="year" value="{{ date('Y') }}" required>
                                                <span class="text-red" id="error_year_s"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right align-left" for="disbursed_date">Disbursed Date<span style="color: red">&#42;</span> :</label>
                                            <div class="col-sm-9">
                                                <input type="date" id="disbursed_date" class="col-xs-12 datepicker" name="disbursed_date" placeholder="Enter Disbursed Date" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 col-sm-offset-4">
                                        <div class="col-sm-8 col-sm-offset-2">
                                            <button type="submit" class="btn btn-primary pull-right"
                                            style=" height: 28px;
                                            margin-right: 8%;
                                            border: none;
                                            color: white;
                                            text-align: center;
                                            text-decoration: none;
                                            display: inline-block;
                                            padding-top: 3px;
                                            font-size: 12px;
                                            cursor: pointer;" ><span class="glyphicon glyphicon-pencil"></span>&nbsp
                                            Generate</button>
                                        </div>
                                    </div>
                                </div> 
                            </form>
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