@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        .progress[data-percent]:after {
            color: #000 !important;
        }
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
                <li class="active"> Extra OT Sheet</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <?php $type='extra_ot_link_radio'; ?>
                @include('hr/reports/operations_radio')
            <div class="page-header">
                <h1>Operations<small><i class="ace-icon fa fa-angle-double-right"></i> Extra OT Sheet  </small></h1>
            </div>
            {{-- Choice 2 --}}
            <div class="panel panel-info">
                <div class="panel-body" >
                    <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
                        {{Form::open( ["url"=>"#", "class"=>"form-horizontal col-xs-12"] )}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right align-left" for="unit"> Unit<span class="text-red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                        <span class="text-red" id="error_unit_s"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right align-left" for="floor"> Floor </label>
                                    <div class="col-sm-9">
                                        {{ Form::select('floor', !empty(Request::get('unit'))?$floorList:[], Request::get('floor'), ['placeholder'=>'Select Floor', 'id'=>'floor', 'style'=>'width:100%']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right align-left" for="area"> Area<span class="text-red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        {{ Form::select('area', $areaList, Request::get('area'), ['placeholder'=>'Select Area', 'id'=>'area', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}
                                        <span class="text-red" id="error_area_s"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right align-left" for="salary_range">Sal. Range  {{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-9">
                                        <div class="col-sm-6 no-padding">
                                            <input type="number" name="min_sal" id="min_sal" class="col-xs-12 min_sal" placeholder="Min Salary" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}">
                                        </div>
                                        <div class="col-sm-6 no-padding">
                                            <input type="number" name="max_sal" id="max_sal" class="col-xs-12 max_sal" placeholder="Max Salary" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}">
                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-righta align-left" for="department">Department<span class="text-red">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('department', !empty(Request::get('area'))?$deptList:[], Request::get('department'), ['placeholder'=>'Select Department ', 'id'=>'department', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Department field is required']) }}
                                        <span class="text-red" id="error_department_s"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right align-left" for="department">Section </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('section', !empty(Request::get('department'))?$sectionList:[], Request::get('section'), ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right align-left" for="department">Sub-Section </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('sub_section', !empty(Request::get('section'))?$subSectionList:[], Request::get('subSection'), ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right align-left" for="ot_range" >OT Range </label>
                                    <div class="col-sm-8">
                                        <input type="number" id="ot_range" class="col-xs-12" placeholder="OT Range" name="ot_range" min="0" value="0">
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right align-left" for="month_number">Month<span class="text-red">&#42;</span> {{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-7">
                                        <select id="month_number" name="month_number" class="col-xs-12 month_number">
                                            <option value="">Select Month</option>
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                        <span class="text-red" id="error_month_s"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right align-left" for="year">Year<span class="text-red">&#42;</span> {{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-7">
                                        <input type="number" id="year" class="col-xs-12 yearpicker" placeholder="Enter Year" name="year">
                                        <span class="text-red" id="error_year_s"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right align-left" for="disbursed_date">Disbursed Date{{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-7">
                                        <input type="date" id="disbursed_date" class="col-xs-12 disbursed_date" name="disbursed_date" placeholder="Enter Disbursed Date" name="year" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right align-left" for="employee_status_id" > Employee Status<span class="text-red">&#42;</span>{{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-7">
                                        <select id="employee_status" name="employee_status" class="col-xs-12 employee_status">
                                            <option value="">Select EMP. Status</option>
                                            <option value="1">Active</option>
                                            <option value="2">Resign</option>
                                            <option value="3">Terminate</option>
                                            <option value="4">Suspend</option>
                                        </select>
                                        <span class="text-red" id="error_status_s"></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{-- Form End --}}
                        <div class="row">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4">
                                <div class="col-sm-5"></div>
                                <div class="col-sm-7">
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
                    </div>
                </div>
            </div>

            <div class="progress progress-striped active pos-rel" id="progress_bar_main" data-percent="10%" style="display: none">
                <div class="progress-bar" id="progress_bar" style="width:10%;"></div>
            </div>
            <div class="row text-center" id="loader"></div>
            {{-- result of list --}}
            <div class="panel panel-warning" id="salary-sheet-result" style="display: none">
                <div class="panel-heading" id="salary-sheet-result-inner">Salary sheet result  &nbsp;<button rel='tooltip' data-tooltip-location='left' data-tooltip='Salary sheet result print' type="button" onClick="printMe('result-show')" class="btn btn-primary btn-xs text-right"><i class="fa fa-print"></i> Print</button></div>
                <div class="panel-body" id="result-show"></div>
            </div>


      </div>  {{-- page-content end --}}
  </div>  {{-- main-content-inner end --}}
</div>  {{-- main-content end --}}

@push('js')
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
        var year        = $('input[name="year"]').val();
        var disbursed_date  = $('input[name="disbursed_date"]').val();
        var employee_status = $('select[name="employee_status"]').val();

        var flug = new Array();
        flug.push(errorMsgRepeter('error_unit_s',unit,'Unit not empty'));
        flug.push(errorMsgRepeter('error_area_s',area,'Area not empty'));
        flug.push(errorMsgRepeter('error_month_s',month,'Month not empty'));
        flug.push(errorMsgRepeter('error_department_s',department,'Department not empty'));
        flug.push(errorMsgRepeter('error_year_s',year,'Year not empty'));
        flug.push(errorMsgRepeter('error_status_s',employee_status,'Status not empty'));

        // console.log(flug);
        if(jQuery.inArray(false, flug) === -1){
            // remove all append message
            $('.prepend').remove();
            $("#salary-sheet-result").show();
            $("#salary-sheet-result-inner").hide();
            $("#result-show").html('');

            $("#choice_2_generate_btn").attr('disabled','disabled');

            $('html, body').animate({
                scrollTop: $("#result-show").offset().top
            }, 2000);
            // show loader
            $("#result-show").html('<div class="loader-cycle"><img src="'+loaderPath+'" /></div>');
            // show message(data faching)
            $('#progress_bar_main').before('<h3 class="text-center prepend" id="data_fach_update">Data Fetching</h3>');
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
                disbursed_date : disbursed_date
            };
            setTimeout(() => {
                $.ajax({
                    url: url+'/hr/reports/ajax_get_employees',
                    type: "GET",
                    data: dataObj,
                    success: function(response){
                        if(response.status) {
                            if(response.count > 100) {
                                // show amount of data found
                                // $('#progress_bar_main').before('<h3 class="text-center prepend" id="dataFoundCount">Total Data Found: 0</h3>');
                                // get data packet wise
                                var data = {
                                    pageHead: {},
                                    group1: [],
                                    group2: [],
                                    group3: []
                                };
                                var dataFoundCount = 0;
                                for(var i = 0; i < response.empcount; i++) {
                                    (function(i){
                                        setTimeout(function(){
                                            $.ajax({
                                                url: '{{ url('hr/reports/ajax_get_multi_search_result_extra_ot') }}',
                                                type: 'POST',
                                                datatype: 'json',
                                                data: {input: dataObj, employeechunk: response.result[i]},
                                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                success: function(res) {
                                                    if(i == 0) {
                                                        $("#result-show").html('');
                                                        $('#progress_bar_main').show();
                                                        $('#data_fach_update').text('Data Sorting...');
                                                        data.pageHead = res.pageHead;
                                                    }
                                                    if(res.group1.length != 0){
                                                        data.group1 = $.merge(data.group2, res.group2);
                                                        dataFoundCount = data.group1.length;
                                                    }
                                                    if(res.group2.length != 0){
                                                        data.group2 = $.merge(data.group2, res.group2);
                                                        dataFoundCount = data.group2.length;
                                                    }
                                                    if(res.group3.length != 0){
                                                        data.group3 = $.merge(data.group2, res.group2);
                                                        dataFoundCount = data.group3.length;
                                                    }
                                                    // update amount of data found
                                                    // $('#dataFoundCount').html('Total Data Found: '+dataFoundCount);
                                                    // calculate percentage
                                                    percentage = parseInt((i-0)*100/(response.empcount-1));
                                                    // set percentage in progress bar
                                                    $('#progress_bar').css({width: percentage+'%'});
                                                    $('#progress_bar_main').attr('data-percent', percentage+'%');
                                                    // console.log(i, percentage, res, data);
                                                    if(percentage == 100) {
                                                        $.ajax({
                                                            url: '{{ url('hr/reports/ajax_get_multi_search_result_list') }}',
                                                            type: 'POST',
                                                            dataType: 'json',
                                                            data: {viewdata: JSON.stringify(data)},
                                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                            success: function(resView){
                                                                // console.log(resView);
                                                                $("#salary-sheet-result-inner").show();
                                                                // hide progress bar
                                                                $('#progress_bar_main').hide();
                                                                // remove all append message
                                                                $('.prepend').remove();
                                                                // set html data
                                                                $("#result-show").html(resView);
                                                                // remove grnerate button disabled attribute
                                                                $("#choice_2_generate_btn").removeAttr('disabled');
                                                            }, error: function(){
                                                                console.log('Error Occurred3');
                                                            }
                                                        });
                                                    }

                                                }, error: function() {
                                                    console.log('Error Occurred2');
                                                }
                                            });
                                        }, 1000*i);
                                    })(i);
                                }
                            } else {
                                // get all data single action
                                // less than 100 data found
                                $.ajax({
                                    url: '{{ url('hr/reports/ajax_get_multi_search_result_chunk') }}',
                                    type: 'POST',
                                    datatype: 'json',
                                    data: {input: dataObj, employeechunk: response.result},
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    success: function(res) {
                                        data.pageHead = res.pageHead;
                                        if(res.group1.length != 0){
                                            data.group1 = $.merge(data.group2, res.group2);
                                            dataFoundCount = data.group1.length;
                                        }
                                        if(res.group2.length != 0){
                                            data.group2 = $.merge(data.group2, res.group2);
                                            dataFoundCount = data.group2.length;
                                        }
                                        if(res.group3.length != 0){
                                            data.group3 = $.merge(data.group2, res.group2);
                                            dataFoundCount = data.group3.length;
                                        }
                                        $.ajax({
                                            url: '{{ url('hr/reports/ajax_get_multi_search_result_list') }}',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {viewdata: JSON.stringify(data)},
                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                            success: function(resView){
                                                // console.log(resView);
                                                $("#salary-sheet-result-inner").show();
                                                // hide progress bar
                                                $('#progress_bar_main').hide();
                                                // remove all append message
                                                $('.prepend').remove();
                                                // set html data
                                                $("#result-show").html(resView);
                                                // remove grnerate button disabled attribute
                                                $("#choice_2_generate_btn").removeAttr('disabled');
                                            }, error: function(){
                                                console.log('Error Occurred3');
                                            }
                                        });

                                    }, error: function() {
                                        console.log('Error Occurred2');
                                    }
                                });
                                console.log('less than 100 data found');
                            }
                        } else {
                            // hide progress bar
                            $('#progress_bar_main').hide();
                            // remove all append message
                            $('.prepend').remove();
                            // set html data
                            $("#result-show").html('No Employee Found');
                            // remove grnerate button disabled attribute
                            $("#choice_2_generate_btn").removeAttr('disabled');
                            // console.log('error occured1');
                        }
                    }, error: function() {
                        console.log('no employee found');
                    }
                });
            }, 1000);
        }
    }
    function attLocation(loc){
        window.location = loc;
       }
</script>
@endpush


@endsection