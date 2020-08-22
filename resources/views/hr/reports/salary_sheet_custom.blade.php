@extends('hr.layout')
@section('title', 'Salary Sheet')
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
                        <h2 class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#individual" aria-expanded="false">
                                <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                &nbsp;Employee Wise
                            </a>
                        </h2>
                    </div>
                
                    <div class="panel-collapse collapse" id="individual" aria-expanded="false" style="height: 0px;">
                        <div class="panel-body">
                            <h3 class="header smaller lighter green">
                                <i class="ace-icon fa fa-bullhorn"></i>
                                All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                            </h3>
                            <form action="#" id="employee-wise">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <div class="form-group no-padding">
                                            <label class="col-sm-2 control-label no-padding align-right" for="emp_id">Employees <span class="text-red" style="vertical-align: top;">&#42;</span> : {{-- <span style="color: red">&#42;</span> --}}</label>
                                            <div class="col-sm-10">
                                                 {{ Form::select('as_id[]', [],'', ['id'=>'as_id', 'class'=> 'associates no-select col-xs-12', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                <span class="text-red" id="error_ac_id_f"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group no-padding">
                                            <label class="col-sm-4 control-label no-padding align-right" for="form-date">Month <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="month" id="form-date" class="col-xs-12 monthYearpicker" value="" data-validation="required" placeholder=" Month-Year" />
                                                <span class="text-red" id="error_form_date_f"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-4 col-sm-offset-4">
                                        <div class="col-sm-8 col-sm-offset-2">
                                            <button onclick="employeeWise()" class="btn btn-primary choice_1_generate_btn" id="choice_1_generate_btn" type="button" name="choice_1_generate_btn" style="height: 28px;
                                            border: none;
                                            color: white;
                                            text-align: center;
                                            text-decoration: none;
                                            display: inline-block;
                                            padding-top: 3px;
                                            font-size: 12px;
                                            cursor: pointer;" ><span class="glyphicon glyphicon-pencil"></span>&nbsp Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading salary-sheet-content">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#multi-search">
                                <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                &nbsp;Unit Wise
                            </a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse in" id="multi-search">
                        <div class="panel-body">
                            <h3 class="header smaller lighter green">
                                <i class="ace-icon fa fa-bullhorn"></i>
                                All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                            </h3>
                            {{-- {{Form::open( ["url"=>"#", "class"=>"form-horizontal col-xs-12"] )}} --}}
                            <div class="form-horizontal col-xs-12">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding align-left" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                        <div class="col-sm-9">
                                            {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
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
                                            {{ Form::select('area', $areaList, Request::get('area'), ['placeholder'=>'Select Area', 'id'=>'area', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}
                                            <span class="text-red" id="error_area_s"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding align-left" for="salary_range">Range : {{-- <span style="color: red">&#42;</span> --}}</label>
                                        <div class="col-sm-9">
                                            <div class="col-xs-5 no-padding">
                                                <input type="number" name="min_sal" id="min_sal" class="col-xs-12 min_sal" placeholder="Min Salary" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}">
                                            </div>
                                            <div class="col-xs-2">
                                               <div class="c1DHiF">-</div>
                                            </div>
                                            <div class="col-xs-5 no-padding">
                                                <input type="number" name="max_sal" id="max_sal" class="col-xs-12 max_sal" placeholder="Max Salary" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}">
                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding align-left" for="department">Department :</label>
                                        <div class="col-sm-8">
                                            {{ Form::select('department', !empty(Request::get('area'))?$deptList:[], Request::get('department'), ['placeholder'=>'Select Department ', 'id'=>'department', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Department field is required']) }}
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
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right align-left" for="ot_range" >Disbursed : </label>
                                        <div class="col-sm-8">
                                            <select id="disbursed" name="disbursed" class="col-xs-12 disbursed">
                                                <option value=""> - Select - </option>
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right align-left" for="month_number">Month <span class="text-red" style="vertical-align: top;">&#42;</span> :{{-- <span style="color: red">&#42;</span> --}}</label>
                                        <div class="col-sm-9">
                                            <select id="month_number" name="month_number" class="col-xs-12 month_number">
                                                <option value="">Select Month</option>
                                                <option value="01" {{sselected(date('F'),'January')}}>January</option>
                                                <option value="02" {{sselected(date('F'),'February')}}>February</option>
                                                <option value="03" {{sselected(date('F'),'March')}}>March</option>
                                                <option value="04" {{sselected(date('F'),'April')}}>April</option>
                                                <option value="05" {{sselected(date('F'),'May')}}>May</option>
                                                <option value="06" {{sselected(date('F'),'June')}}>June</option>
                                                <option value="07" {{sselected(date('F'),'July')}}>July</option>
                                                <option value="08" {{sselected(date('F'),'August')}}>August</option>
                                                <option value="09" {{sselected(date('F'),'September')}}>September</option>
                                                <option value="10" {{sselected(date('F'),'October')}}>October</option>
                                                <option value="11" {{sselected(date('F'),'November')}}>November</option>
                                                <option value="12" {{sselected(date('F'),'December')}}>December</option>
                                            </select>
                                            <span class="text-red" id="error_month_s"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right align-left" for="year">Year <span class="text-red" style="vertical-align: top;">&#42;</span> : {{-- <span style="color: red">&#42;</span> --}}</label>
                                        <div class="col-sm-9">
                                            <select id="year" name="year" class="col-xs-12 year">
                                                @foreach($getYear as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                            <!-- <input type="number" id="year" class="col-xs-12 yearpicker" placeholder="Enter Year" name="year" value="{{ date('Y') }}"> -->
                                            <span class="text-red" id="error_year_s"></span>
                                        </div>
                                    </div>
                                    <div class="form-group sr-only">
                                        <label class="col-sm-3 control-label no-padding-right align-left" for="disbursed_date">Disbursed Date{{-- <span style="color: red">&#42;</span> --}}</label>
                                        <div class="col-sm-9">
                                            <input type="date" id="disbursed_date" class="col-xs-12 disbursed_date" name="disbursed_date" placeholder="Enter Disbursed Date" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right align-left" for="employee_status_id" > Status <span class="text-red" style="vertical-align: top;">&#42;</span> : {{-- <span style="color: red">&#42;</span> --}}</label>
                                        <div class="col-sm-9">
                                            <select id="employee_status" name="employee_status" class="col-xs-12 employee_status">
                                                <option value="1">Active</option>
                                                <option value="2">Resign</option>
                                                <option value="3">Terminate</option>
                                                <option value="4">Suspend</option>
                                                <option value="5">Left</option>
                                            </select>
                                            <span class="text-red" id="error_status_s"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding align-left" for="asOT"> OT/Non-OT :</label>
                                        <div class="col-sm-9">
                                            {{ Form::select('as_ot', ['1' =>'OT', '0'=>'Non-OT'], null, ['placeholder'=>'Select OT Status', 'id'=>'asOT', 'style'=>'width:100%']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-4">
                                    <div class="col-sm-8 col-sm-offset-2">
                                        <button onclick="multiple()" class="btn btn-primary choice_2_generate_btn pull-right" id="choice_2_generate_btn" name="choice_2_generate_btn"
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
                            {{-- {{Form::close()}} --}}  
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" value="0" id="setFlug">
            
            <div class="progress" id="result-process-bar" style="display: none;">
                <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress_bar_main" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                  0%
                </div>
            </div>
            {{-- result of list --}}
            <div class="panel panel-success" id="salary-sheet-result">
                <div class="panel-heading" id="salary-sheet-result-inner" style="display: none">Salary sheet result  &nbsp;<button rel='tooltip' data-tooltip-location='left' data-tooltip='Salary sheet result print' type="button" onClick="printMe1('result-show')" class="btn btn-primary btn-xs text-right"><i class="fa fa-print"></i> Print</button></div>
                <div class="panel-body" id="result-show"></div>
            </div>

        </div>  {{-- page-content end --}}
    </div>  {{-- main-content-inner end --}}
</div>  {{-- main-content end --}}

@push('js')
<script type="text/javascript">
    var loader = '<img src=\'{{ asset("assets/img/loader-box.gif")}}\' class="center-loader">';
    $(document).ready(function(){
        //salary range validation------------------
        $('#min_sal').on('change',function(){
            $('#max_sal').val('');

            if($('#min_sal').val() < 0){
                $('#min_sal').val('');
            }    
        });

        $('#max_sal').on('change',function(){
            if($('#max_sal').val() < 0){
                $('#max_sal').val('');
            }
            else{
                var end     = $(this).val();
                var start   = $('#min_sal').val();
                console.log('min:'+start+' '+'max:'+end);
                if(start == '' || start == null){
                    alert("Please enter Min-Salary first");
                    $('#max_sal').val('');
                }
                else{
                     if(parseFloat(end) < parseFloat(start)){
                        alert("Invalid!!\n Min-Salary is greater than Max-Salary");
                        $('#max_sal').val('');
                    }
                }
            }
        });
        //salary range validation end-----------------

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

{{-- submit individual --}}
<script>
    var _token = $('input[name="_token"]').val();
    // show error message
    function errorMsgRepeter(id, check, text){
        var flug1 = false;
        if(check == ''){
            $('#'+id).html('<label class="control-label status-label" for="inputError">* '+text+'<label>');
            flug1 = false;
        }else{
            $('#'+id).html('');
            flug1 = true;
        }
        return flug1;
    }

    function formatState (state) {
        //console.log(state.element);
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "/user/pages/images/flags";
        var $state = $(
        '<span><img /> <span></span></span>'
        );
        // Use .text() instead of HTML string concatenation to avoid script injection issues
        var targetName = state.name;
        $state.find("span").text(targetName);
        // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
        return $state;
    };

    $('select.associates').select2({
        templateSelection:formatState,
        placeholder: 'Select Name or Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    })
                };
          },
          cache: true
        }
    });

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

    function isNotNullNorUndefined (o) {
        return (typeof (o) !== 'undefined' && o !== null);
    };
    // 
    function individual() {
        var as_id       = $('select[name="as_id"]').val();
        var form_date   = $('input[name="form-date"]').val();
        var to_date     = $('input[name="to-date"]').val();
        var flug        = new Array();
        flug.push(errorMsgRepeter('error_ac_id_f', as_id, 'Employee required'));
        flug.push(errorMsgRepeter('error_form_date_f', form_date, 'From date required'));
        flug.push(errorMsgRepeter('error_to_date_f', to_date, 'To date required'));
        //console.log(as_id);
        if(jQuery.inArray(false, flug) === -1){
            $('.prepend').remove();
            // $("#salary-sheet-result").show();
            $("#salary-sheet-result-inner").hide();
            $("#result-show").html(loader);
            $('html, body').animate({
                scrollTop: $("#result-show").offset().top
            }, 2000);
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            $.ajax({
                url: url+'/hr/reports/salary-sheet-custom-individual-search',
                type: "GET",
                data: {
                  _token : _token,
                  as_id : as_id,
                  form_date : form_date,
                  to_date : to_date
                },
                success: function(response){
                    // console.log(response);
                    if(response !== 'error'){
                        $('#setFlug').val(1); 
                        processbar('success');
                        $('.prepend').remove();
                        setTimeout(() => {
                            $("#result-show").html(response);
                            $("#salary-sheet-result-inner").show();
                            // remove grnerate button disabled attribute
                            $("#choice_1_generate_btn").removeAttr('disabled');
                        }, 1000);
                        
                        
                    }else{
                        $('#setFlug').val(2); 
                        processbar('error');
                    }
                }, error: function() {
                    processbar('error');
                    $('#setFlug').val(2); 
                }
            });
            
        }
    }

    function employeeWise() {
        var as_id = $('select[name="as_id"]').val();
        var month = $('input[name="month"]').val();
        var flug  = new Array();
        flug.push(errorMsgRepeter('error_ac_id_f', as_id, 'Employee required'));
        flug.push(errorMsgRepeter('error_form_date_f', month, 'Month required'));
        //console.log(as_id);
        if(jQuery.inArray(false, flug) === -1){

            var form = $("#employee-wise");
            $('.prepend').remove();
            // $("#salary-sheet-result").show();
            $("#salary-sheet-result-inner").hide();
            $("#result-show").html(loader);
            $('html, body').animate({
                scrollTop: $("#result-show").offset().top
            }, 2000);
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            $.ajax({
                type: "post",
                url: url+'/hr/reports/salary-sheet-employee-wise',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), // serializes the form's elements.
                success: function(response){
                    // console.log(response.length);
                    if(response !== 'error'){
                        $('#setFlug').val(1); 
                        processbar('success');
                        $('.prepend').remove();
                        setTimeout(() => {
                            $("#result-show").html(response);
                            $("#salary-sheet-result-inner").show();
                            
                        }, 1000);
                        

                    }else{
                        $('#setFlug').val(2); 
                        processbar('error');
                    }
                }, error: function() {
                    processbar('error');
                    $('#setFlug').val(2); 
                }
            });
        }    
    }
    //multiple salary sheet
    function multiple() {
        var unit        = $('select[name="unit"]').val();
        var floor       = $('select[name="floor"]').val();
        var area        = $('select[name="area"]').val();
        var department  = $('select[name="department"]').val();
        var sectionF    = $('select[name="section"]').val();
        var sub_section = $('select[name="sub_section"]').val();
        var ot_range    = $('input[name="ot_range"]').val();
        var month       = $('select[name="month_number"]').val();
        var min_sal     = $('input[name="min_sal"]').val();
        var max_sal     = $('input[name="max_sal"]').val();
        var year        = $('select[name="year"]').val();
        var as_ot        = $('select[name="as_ot"]').val();
        var disbursed        = $('select[name="disbursed"]').val();
        // var year        = $('input[name="year"]').val();
        var disbursed_date  = $('input[name="disbursed_date"]').val();
        var employee_status = $('select[name="employee_status"]').val();
        var flug = new Array();
        flug.push(errorMsgRepeter('error_unit_s',unit,'Unit not empty'));
        // flug.push(errorMsgRepeter('error_area_s',area,'Area not empty'));
        flug.push(errorMsgRepeter('error_month_s',month,'Month not empty'));
        // flug.push(errorMsgRepeter('error_department_s',department,'Department not empty'));
        flug.push(errorMsgRepeter('error_year_s',year,'Year not empty'));
        // flug.push(errorMsgRepeter('error_status_s',employee_status,'Status not empty'));

       // console.log(flug);
        if(jQuery.inArray(false, flug) === -1){
            // remove all append message
            $('.prepend').remove();
            // $("#salary-sheet-result").show();
            $("#salary-sheet-result-inner").hide();
            $("#result-show").html(loader);

            $("#choice_2_generate_btn").attr('disabled','disabled');

            $('html, body').animate({
                scrollTop: $("#result-show").offset().top
            }, 2000);
            
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            var dataObj = {
                token : _token,
                unit : unit,
                floor : floor,
                area : area,
                department : department,
                section : sectionF,
                sub_section : sub_section,
                ot_range : ot_range,
                month : month,
                year : year,
                employee_status : employee_status,
                min_sal : min_sal,
                max_sal : max_sal,
                as_ot : as_ot,
                disbursed : disbursed,
                disbursed_date : disbursed_date
            };
            setTimeout(() => {
                $.ajax({
                    url: url+'/hr/reports/ajax_get_employees',
                    type: "GET",
                    dataType : 'html',
                    data: dataObj,
                    success: function(response){
                        // console.log(response.length);
                        if(response !== 'error'){
                            $('#setFlug').val(1); 
                            processbar('success');
                            $('.prepend').remove();
                            setTimeout(() => {
                                $("#result-show").html(response);
                                $("#salary-sheet-result-inner").show();
                                // remove grnerate button disabled attribute
                                $("#choice_2_generate_btn").removeAttr('disabled');
                                
                            }, 1000);
                            

                        }else{
                            $('#setFlug').val(2); 
                            processbar('error');
                        }
                    }, error: function() {
                        processbar('error');
                        $('#setFlug').val(2); 
                    }
                });
            }, 1000);
        }
    }

    var incValue = 1;
    
    function processbar(percentage) {
        var setFlug = $('#setFlug').val();
        if(parseInt(setFlug) === 1){
            var percentageVaule = 99;
            $('#progress_bar_main').html(percentageVaule+'%');
            $('#progress_bar_main').css({width: percentageVaule+'%'});
            $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
            setTimeout(() => {
                percentageVaule = 0;
                percentage = 0;
                $('#progress_bar_main').html(percentageVaule+'%');
                $('#progress_bar_main').css({width: percentageVaule+'%'});
                $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
                $("#result-process-bar").css('display', 'none');
            }, 1000);
        }else if(parseInt(setFlug) === 2){
            console.log('error');
        }else{
            // set percentage in progress bar
            percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
            $('#progress_bar_main').html(percentage+'%');
            $('#progress_bar_main').css({width: percentage+'%'});
            $('#progress_bar_main').attr('aria-valuenow', percentage+'%');
            if(percentage < 40 ){
                incValue = 1;
                // processbar(percentage);
            }else if(percentage < 60){
                incValue = 0.8;
            }else if(percentage < 75){
                incValue = 0.5;
            }else if(percentage < 85){
                incValue = 0.2;
            }else if(percentage < 98){
                incValue = 0.1;
            }else{
                return false;
            }
            setTimeout(() => {
                processbar(percentage);
            }, 1000);
        }

    }
    
    function attLocation(loc){
        window.location = loc;
   }

   function printMe1(divName)
{   
    

    var mywindow=window.open('','','width=800,height=800');
    
    mywindow.document.write('<html><head><title>Print Contents</title>');
    mywindow.document.write('<style>@page {size: landscape; color: color;} </style>');
    mywindow.document.write('</head><body>');
    mywindow.document.write(document.getElementById(divName).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close();  
    mywindow.focus();           
    mywindow.print();
    mywindow.close();
}
</script>
@endpush


@endsection