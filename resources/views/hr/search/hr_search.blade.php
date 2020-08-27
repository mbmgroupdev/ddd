@extends('hr.layout')
@section('title', 'Query')
@section('main-content')
@push('css')
<style type="text/css">
    
</style>
<link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}" />
@endpush

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Query</a>
                </li>
            </ul>
        </div>

        <div class="page-content">

            <div class="row">
                <div class="col-12">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                </div>
                @php
                    $allQueryStrings = Request::all();
                    $categorySelect = Request::get('category');
                    $typeSelect     = Request::get('type');
                    $dateInput      = Request::get('date');
                    $monthInput     = Request::get('month');
                    $yearInput      = Request::get('year');
                    $rangeFrom      = Request::get('rangeFrom');
                    $rangeTo        = Request::get('rangeTo');
                    $typeDisabled = 'disabled="disabled"';
                    if(!empty($monthInput) || !empty($yearInput) || !empty($rangeFrom) || !empty($rangeTo) || !empty($dateInput)) {
                        $typeDisabled = '';
                    }
                @endphp
                <div class="col-12">
                    <div class="panel">
                        {{ Form::open(['url'=>'#', 'method'=>'get', 'class'=>'form-horizontal', 'autocomplete'=>"off"]) }}
                            <div class="row" id="hr_search_input">
                                <div class="col-3">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <select name="category" id="categorySelect" class="form-control ">
                                            <option value="">Please select one</option>
                                            @if(auth()->user()->can('Query Salary') || auth()->user()->hasRole('Super Admin'))
                                            <option value="salary" {{ sselected($categorySelect,'salary') }}>Salary</option>
                                            @endif
                                            @if(auth()->user()->can('Query Attendance') || auth()->user()->hasRole('Super Admin'))
                                            <option value="attendance" {{ sselected($categorySelect,'attendance') }}>Attendance</option>
                                            @endif
                                            <option value="all_employee" {{ sselected($categorySelect,'all_employee') }}>Floor and Linewise Employee</option>
                                            <option value="outside" {{ sselected($categorySelect,'outside') }}>Outside</option>
                                            <option value="empstatus" {{ sselected($categorySelect,'empstatus') }}>Employee Status</option>
                                            {{-- <option value="absent" {{ sselected($categorySelect,'absent') }}>Absent</option> --}}
                                            {{-- <option value="persent" {{ sselected($categorySelect,'persent') }}>Present</option> --}}
                                            <option value="ot" {{ sselected($categorySelect,'ot') }}>OT</option> 
                                            <option value="leave" {{ sselected($categorySelect,'leave') }}>Leave</option>
                                            <option value="line" {{ sselected($categorySelect,'line') }}>Line Change</option>
                                            <option value="salratio" {{ sselected($categorySelect,'salratio') }}>Salary Ratio</option>
                                            <option value="salincdec" {{ sselected($categorySelect,'salincdec') }}>Salary Increase/Decrease</option>
                                        </select>
                                        <label>Category  </label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <select name="type" id="typeSelect" class="form-control" {{ $typeDisabled }}>
                                            <option value="">Please select one</option>
                                            <option value="yesterday" {{ sselected($typeSelect,'yesterday') }}>Yesterday</option>
                                            <option value="date" {{ sselected($typeSelect,'date') }}>Date</option>
                                            <option value="month" {{ sselected($typeSelect,'month') }}>Month</option>
                                            <option value="year" {{ sselected($typeSelect,'year') }}>Year</option>
                                            <option value="range" {{ sselected($typeSelect,'range') }}>Range</option>
                                        </select>
                                        <label class="col-sm-4 control-label no-padding"> Type</span></label>
                                    </div>
                                </div>
                                <div class="col-3 extra-input" id="dateDiv" style="display: {{ $dateInput!=''?'block':'none' }};">
                                    <div class="has-float-label">
                                        <input type="date" class="form-control " id="dateInput" name="dateInput" value="{{ $dateInput }}" placeholder="Select date"/>
                                        <label>Date</label>
                                    </div>
                                </div>
                                <div class="col-3 extra-input" id="monthDiv" style="display: {{ $monthInput!=''?'block':'none' }};">
                                    <div class="has-float-label">
                                        <input type="month" class="form-control " id="monthInput" name="monthInput" value="{{ $monthInput }}" placeholder="Month-Year"/>
                                        <label>Month</label>
                                    </div>
                                </div>
                                <div class="col-3 extra-input" id="yearDiv" style="display: {{ $yearInput!=''?'block':'none' }};">
                                    <div class="has-float-label">
                                        <input type="year" class="form-control " id="yearInput" name="yearInput" value="{{ $yearInput }}" placeholder="Select year"/>
                                        <label>Year</label>
                                    </div>
                                </div>
                                <div id="rangeSection" class="extra-input col-4" style="display: {{ $rangeFrom!=''?'block':'none' }};">
                                    <div class="row">
                                        <div class="col-sm-6 col-xs-6" id="rangeFromDiv">
                                            <div class="has-float-label">
                                                <input type="month" class="form-control monthYearpicker" id="rangeFrom" name="rangeFrom" placeholder="From" value="{{ $rangeFrom }}"/>
                                                <label>From</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-6">
                                            <div class="has-float-label">
                                                <input type="month" class="form-control monthYearpicker" id="rangeTo" name="rangeTo" placeholder="To" value="{{ $rangeTo }}"/>
                                                <label>To</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-center" id="form_search_div">
                                    <button class="btn btn-primary" id="form_search" type="button" {{ $typeDisabled }}>
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <!-- /.row -->
                        {{ Form::close() }}
                        
                        
                    </div>
                    <div class="progress" id="result-process-bar" style="display: none;">
                        <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress_bar_main" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                          0%
                        </div>
                    </div>

                    <input type="hidden" value="0" id="setFlug" />
                    <div id="searchContent"></div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
    <script src="{{ asset('assets/js/chartjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootbox.js') }}"></script>

    <script type="text/javascript">
        $('#rangeTo, #rangeFrom').on('dp.change',function() {

            var to_date = new Date($('#rangeTo').val());
            var from_date  = new Date($('#rangeFrom').val());
            if(to_date > new Date() || from_date > new Date()){
                alert('You cannot select future date!');
                $(this).val('');
            }else{

                if(from_date > to_date){
                    //console.log(to_date,from_date);
                    alert("Error!! From Date is latest than TO Date");
                    $(this).val('');
                }
            }
        });
        // page loader function
        function preLoader(html='searchContent'){
            //return $('#'+html).html('<center><i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:80px;margin-top:100px;"></i></center>');
        }
        // get page type
        function getPageType(queryStrings){
            var unit = queryStrings.unit;
            var area = queryStrings.area;
            var department = queryStrings.department;
            var floor = queryStrings.floor;
            var section = queryStrings.section;
            var subsection = queryStrings.subsection;
            var view = queryStrings.view;
            var shiftcode= queryStrings.shiftcode;
            var category = queryStrings.category;
            //console.log(queryStrings);
            if(view === 'allunit'){
                return 'unit';
            }else if(view === 'employee'){
                return 'employee';
            }else if(view === 'line'){
                return 'line';
            }else if(view === 'change'){
                return 'change';
            }else if(view === 'all_employee'){
                return 'all_employee';
            }else if(view === 'shift' && typeof unit !== "undefined" && typeof shiftcode === "undefined" && category ==='ot'){
                return 'otshift';
            }else if(typeof section !== "undefined") {
                return 'subsection';
            }else if(category ==='ot' &&  view === "hour"){
                return 'othour';
            }else if(typeof floor !== "undefined"){
                return 'section';
            }else if(typeof department !== "undefined"){
                return 'floor';
            }else if(typeof area !== "undefined"){
                return 'department';
            }else if(typeof unit !== "undefined" && typeof shiftcode === "undefined"){
                return 'area';
            }else{
                return 'all';
            }

        }
        // check date valid or not
        function isValidDate(dateString) {
            var regEx = /^\d{4}-\d{2}-\d{2}$/;
            if(!dateString.match(regEx)) return false;  // Invalid format
            var d = new Date(dateString);
            var dNum = d.getTime();
            if(!dNum && dNum !== 0) return false; // NaN value, Invalid date
            return d.toISOString().slice(0,10) === dateString;
        }

        $(document).ready(function() {
            var allQueryStrings = [];
            allQueryStrings = {!! json_encode($allQueryStrings) !!};
            if(!jQuery.isEmptyObject(allQueryStrings)) {
                var category = allQueryStrings.category;
                sortTypeOption(category);
                var pageType = getPageType(allQueryStrings);
                preLoader();
                var url = getCategoryWiseUrl(allQueryStrings.category,pageType);
                // url = '{{ url('hr/search') }}';
                // attAjaxCall(url,allQueryStrings.date);
                var obj = {
                    'category': allQueryStrings.category,
                    'type': allQueryStrings.type,
                    'date': allQueryStrings.date,
                    'unit': allQueryStrings.unit,
                    'area': allQueryStrings.area,
                    'department': allQueryStrings.department,
                    'floor': allQueryStrings.floor,
                    'section': allQueryStrings.section,
                    'subsection': allQueryStrings.subsection,
                    'attstatus': allQueryStrings.attstatus,
                    'salstatus': allQueryStrings.salstatus,
                    'month': allQueryStrings.month,
                    'year': allQueryStrings.year,
                    'rangeFrom': allQueryStrings.rangeFrom,
                    'rangeTo': allQueryStrings.rangeTo,
                    'view' : allQueryStrings.view,
                    'shiftcode' : allQueryStrings.shiftcode,
                    'leavetype': allQueryStrings.leavetype
                }
                // console.log(obj, url);
                if(allQueryStrings.type == 'date' && typeof allQueryStrings.date !== "undefined" && typeof allQueryStrings.attstatus === "undefined")  {
                    if(!isValidDate(allQueryStrings.date)){
                        $('#searchContent').html('Search paramenters is not valid.');
                    } else {
                        attAjaxCall(url,obj);
                    }
                }
                else if(allQueryStrings.type == 'month' && typeof allQueryStrings.month !== "undefined") {
                    attAjaxCall(url,obj);
                }
                else if(allQueryStrings.type == 'year' && typeof allQueryStrings.year !== "undefined") {
                    attAjaxCall(url,obj);
                }
                else if(allQueryStrings.type == 'range' && typeof allQueryStrings.rangeFrom !== "undefined" && typeof allQueryStrings.rangeTo !== "undefined") {
                        attAjaxCall(url,obj);
                }else if(allQueryStrings.type == 'date' && typeof allQueryStrings.date !== "undefined" && typeof allQueryStrings.attstatus !== "undefined") {
                    if(typeof allQueryStrings.unit !== "undefined") {
                        var url = getCategoryWiseUrl(category,'employee');
                    }
                    else {
                        var url = getCategoryWiseUrl(category,'allemp');
                    }
                    attAjaxCall(url,obj);
                }
                else {
                    $('#searchContent').html('Search paramenters is not valid.');
                }
            }
        });
        // cancle all ajax call request
        $.ajaxQ = (function(){
          var id = 0, Q = {};

          $(document).ajaxSend(function(e, jqx){
            jqx._id = ++id;
            Q[jqx._id] = jqx;
          });
          $(document).ajaxComplete(function(e, jqx){
            delete Q[jqx._id];
          });

          return {
            abortAll: function(){
              var r = [];
              $.each(Q, function(i, jqx){
                r.push(jqx._id);
                jqx.abort();
              });
              return r;
            }
          };
        })();

        // global variables
        var categorySelect = $('#categorySelect').val();
        var typeSelect  = $('#typeSelect').val();
        var dateInput   = $('#dateInput').val();
        var monthInput  = $('#monthInput').val();
        var yearInput   = $('#yearInput').val();
        var rangeFrom   = $('#rangeFrom').val();
        var rangeTo     = $('#rangeTo').val();

        //get url category wise
        function getCategoryWiseUrl(category,pageType) {
            var url = '';
            if(category == 'attendance') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_att_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_att_search') }}'+'_'+pageType;
                }
            } else if(category == 'empstatus') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_empstatus_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_empstatus_search') }}'+'_'+pageType;
                }
            } else if(category == 'salary') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_salary_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_salary_search') }}'+'_'+pageType;
                }
            } else if(category == 'leave') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_leave_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_leave_search') }}'+'_'+pageType;
                }
            } else if(category == 'ot') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_ot_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_ot_search') }}'+'_'+pageType;
                }
            }else if(category == 'line') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_line_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_line_search') }}'+'_'+pageType;
                }
            } else if(category == 'outside') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_outside_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_outside_search') }}'+'_'+pageType;
                }
            }else if(category == 'salratio') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_salratio_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_salratio_search') }}'+'_'+pageType;
                }
            }else if(category == 'all_employee') {
                if(pageType == 'all') {
                    url = '{{ url('hr/search/hr_all_employee_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_all_employee_search') }}'+'_'+pageType;
                }
            }
            else if(category == 'salincdec'){
                if(pageType == 'all'){
                    url = '{{ url('hr/search/hr_salincdec_search') }}';
                } else {
                    url = '{{ url('hr/search/hr_salincdec_search') }}'+'_'+pageType;
                }
            }


            return url;
        }

        // inisical from search from button click
        $('#form_search').on('click', function() {
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            $('#searchContent').hide();
            categorySelect = $('#categorySelect').val();
            typeSelect  = $('#typeSelect').val();
            monthInput  = $('#monthInput').val();
            dateInput   = $('#dateInput').val();
            yearInput   = $('#yearInput').val();
            rangeFrom   = $('#rangeFrom').val();
            rangeTo     = $('#rangeTo').val();
            var url = getCategoryWiseUrl(categorySelect,'all');
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    category: categorySelect,
                    type: typeSelect,
                    date: dateInput,
                    month: monthInput,
                    year: yearInput,
                    rangeFrom: rangeFrom,
                    rangeTo: rangeTo
                },
                success: function(res){
                    // console.log(res);
                    $('#searchContent').html(res.page);
                    window.history.pushState('','',res.url);
                    $('#setFlug').val(1);
                    processbar('success');
                    $('#searchContent').show();
                    // console.log(res);
                },
                error: function(){
                    console.log('error occored');
                    $('#setFlug').val(2);
                    processbar('error');
                }
            });
        });

        /*$(document).on(ace.click_event,'.employee-att-details', function(e) {
            var botboxtitle = '';
            if(categorySelect == 'attendance') {
                botboxtitle = 'Employee Monthly Attendance Details';
            }
            if(categorySelect == 'leave') {
                botboxtitle = 'Employee Monthly Leave Details';
            }
            e.preventDefault();
            var url = $(this).attr('href');
            dialog = bootbox.dialog({
                title: botboxtitle,
                size: 'large',
                message: '<center><i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:80px;margin:50px 0px;"></i></center>',
                closeButton: false,
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: 'btn-danger'
                    }
                }
             });
            dialog.init(function(){
                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(res) {
                        dialog.find('.bootbox-body').html(res);
                        // console.log(res);
                    }
                })
                // setTimeout(function(){
                // }, 3000);
            });
        });*/
        /* ====== attendence search ======= */
        // for group button
        $(document).on('click','.search_all', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                category: categorySelect,
                type: typeSelect,
                date: dateInput,
                month: monthInput,
                year: yearInput,
                rangeFrom: rangeFrom,
                rangeTo: rangeTo
            };
            var url = getCategoryWiseUrl(categorySelect,'all');
            attAjaxCall(url, data);
        });

        $(document).on('click','.search_unit', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var url = getCategoryWiseUrl(categorySelect,'unit');
            attAjaxCall(url);
        });

        $(document).on('click','.search_area', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit')
            };
            var url = getCategoryWiseUrl(categorySelect,'area');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_dept', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                area: $(this).data('area')
            };
            var url = getCategoryWiseUrl(categorySelect,'department');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_floor', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                department: $(this).data('department'),
                unit: $(this).data('unit')
            };
            var url = getCategoryWiseUrl(categorySelect,'floor');
            attAjaxCall(url,data);
        });
        $(document).on('click','.search_line', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                floor: $(this).data('floor'),
                unit: $(this).data('unit')
            };
            var url = getCategoryWiseUrl(categorySelect,'line');
            attAjaxCall(url,data);
        });
        $(document).on('click','.line_change', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                floor: $(this).data('floor'),
                unit: $(this).data('unit'),
                line: $(this).data('line')
            };
            var url = getCategoryWiseUrl(categorySelect,'change');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_section', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                floor: $(this).data('floor')
            };
            var url = getCategoryWiseUrl(categorySelect,'section');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_subsection', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                section: $(this).data('section')
            };
            var url = getCategoryWiseUrl(categorySelect,'subsection');
            attAjaxCall(url,data);
        });



        $(document).on('click','.search_emp', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                area: $(this).data('area'),
                department: $(this).data('department'),
                floor: $(this).data('floor'),
                attstatus: $(this).data('attstatus'),
                section: $(this).data('section'),
                subsection: $(this).data('subsection'),
                salstatus: $(this).data('salstatus'),
                leavetype: $(this).data('leavetype'),
                hour: $(this).data('hour'),
                shiftcode: $(this).data('shiftcode')
            };
            var url = getCategoryWiseUrl(categorySelect,'employee');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_allemp', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                'category': categorySelect,
                'type': typeSelect,
                'date': dateInput,
                attstatus: $(this).data('attstatus'),
            };
            var url = getCategoryWiseUrl(categorySelect,'allemp');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_ot_shift', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                area: $(this).data('area'),
                department: $(this).data('department'),
                floor: $(this).data('floor'),
                section: $(this).data('section'),
                subsection: $(this).data('subsection')
            };
            var url = getCategoryWiseUrl(categorySelect,'otshift');
            attAjaxCall(url,data);
        });
        $(document).on('click','.search_ot_hour', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                shiftcode: $(this).data('shiftcode'),
                unit: $(this).data('unit'),
                area: $(this).data('area'),
                department: $(this).data('department'),
                floor: $(this).data('floor'),
                attstatus: $(this).data('attstatus'),
                section: $(this).data('section'),
                subsection: $(this).data('subsection'),
            };
            var url = getCategoryWiseUrl(categorySelect,'othour');
            attAjaxCall(url,data);
        });

        /* ====== outside search ======= */

        $(document).on('click','.outside_emp', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('id')
            };
            var url = getCategoryWiseUrl(categorySelect,'employee');
            attAjaxCall(url,data);
        });

        $(document).on('click','.outside_emp_date_total', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                location: $(this).data('location'),
                data: $(this).data('request')
            };
            var url = getCategoryWiseUrl(categorySelect,'emp_date_datalist');
            attAjaxCall(url,data);
        });
        
        /* ====== all employee search ======= */

        $(document).on('click','.search_unit_floor', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('id')
            };
            var url = getCategoryWiseUrl(categorySelect,'unit_floor');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_unit_line', function() {
            console.log('hi');
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('id')
            };
            var url = getCategoryWiseUrl(categorySelect,'unit_line');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_floor_line', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('id'),
                floor: $(this).data('floor')

            };
            var url = getCategoryWiseUrl(categorySelect,'floor_line');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_all_employee', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('id'),
                floor: $(this).data('floor'),
                line: $(this).data('line')
            };
            var url = getCategoryWiseUrl(categorySelect,'all_employee');
            attAjaxCall(url,data);
        });

        /* ====== employee status search ======= */

        $(document).on('click','.emp_status', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                status: $(this).data('id')
            };
            var url = getCategoryWiseUrl(categorySelect,'employee');
            attAjaxCall(url,data);
        });

        $(document).on('click','.unit_emp_list', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                status: $(this).data('id'),
                unit: $(this).data('unit')
            };
            var url = getCategoryWiseUrl(categorySelect,'unit_emp_list');
            attAjaxCall(url,data);
        });

        // global ajax call
        function attAjaxCall(url,data,resultDiv='searchContent') {
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            $('#searchContent').hide();
            $.ajax({
                url: url,
                type: 'get',
                data: data,
                success: function(res){
                    $('#'+resultDiv).html(res.page);
                    window.history.pushState('','',res.url);
                    // console.log(res);
                    $('#setFlug').val(1);
                    processbar('success');
                    $('#searchContent').show();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log('Error status: ' + textStatus);
                    console.log('Error message: ' + errorThrown);
                    console.log('error occored on ajax call');
                    $('#setFlug').val(2);
                    processbar('error');
                }
            });
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
                    $("#result-process-bar").css('display', 'none');
                    percentageVaule = 0;
                    percentage = 0;
                    $('#progress_bar_main').html(percentageVaule+'%');
                    $('#progress_bar_main').css({width: percentageVaule+'%'});
                    $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
                }, 1000);
            }else if(parseInt(setFlug) === 2){
                console.log('error processbar');
            }else{
                // set percentage in progress bar
                percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
                $('#progress_bar_main').html(percentage+'%');
                $('#progress_bar_main').css({width: percentage+'%'});
                $('#progress_bar_main').attr('aria-valuenow', percentage+'%');
                if(percentage < 70 ){
                    incValue = 1;
                    // processbar(percentage);
                }else if(percentage < 80){
                    incValue = 0.8;
                }else if(percentage < 90){
                    incValue = 0.5;
                }else if(percentage < 98){
                    incValue = 0.1;
                }else{
                    percentage = 'error';
                }
                setTimeout(() => {
                    processbar(percentage);
                }, 1000);
            }
        }

        // append option in select
        function appendOption(value1) {
            if($('#typeSelect option[value='+value1+']').length == 0) {
                $('#typeSelect').append($('<option>', {
                    value: value1,
                    text: value1.charAt(0).toUpperCase() + value1.slice(1)
                }));
            }
        }

        // sort type option
        function sortTypeOption(category) {
            if(category) {
                $('#typeSelect').removeAttr('disabled');
                if(category=='attendance') {
                    appendOption('date');
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='month']").remove();
                    $("#typeSelect option[value='year']").remove();
                    $("#typeSelect option[value='range']").remove();
                    $("#typeSelect option[value='all']").remove();
                }
                if(category=='outside' || category == 'line' || category == 'salratio') {
                    appendOption('date');
                    appendOption('month');
                    appendOption('year');
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='range']").remove();
                    $("#typeSelect option[value='all']").remove();
                }
                if(category=='salary') {
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='date']").remove();
                    $("#typeSelect option[value='all']").remove();
                    appendOption('month');
                    appendOption('year');
                    appendOption('range');
                }
                if(category=='leave') {
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='range']").remove();
                    $("#typeSelect option[value='year']").remove();
                    $("#typeSelect option[value='all']").remove();
                    appendOption('date');
                    appendOption('month');
                }
                if(category=='ot') {
                    appendOption('yesterday');
                    appendOption('date');
                    appendOption('month');
                    $("#typeSelect option[value='all']").remove();
                    $("#typeSelect option[value='year']").remove();
                    $("#typeSelect option[value='range']").remove();
               }
               if(category=='empstatus') {
                    appendOption('date');
                    appendOption('month');
                    appendOption('year');
                    $("#typeSelect option[value='all']").remove();
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='range']").remove();
                }
               if(category=='all_employee') {
                    appendOption('all');
                    $("#typeSelect option[value='date']").remove();
                    $("#typeSelect option[value='month']").remove();
                    $("#typeSelect option[value='year']").remove();
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='range']").remove();
                }
                if(category=='salincdec') {
                    $("#typeSelect option[value='date']").remove();
                    appendOption('month');
                    appendOption('year');
                    $("#typeSelect option[value='yesterday']").remove();
                    $("#typeSelect option[value='range']").remove();
                }
            } else {
                $('#typeSelect').attr('disabled', 'disabled');
            }
        }



        /*---------custom code for ot-------*/

        /* ====== attendence search ======= */
        $('#categorySelect').on('change', function() {
            var category = $(this).val();
            sortTypeOption(category);
            $("#typeSelect").val($("#typeSelect option:first").val()).change();
            $('#form_search').attr('disabled', 'disabled');
            $('.extra-input').hide();
        });

        $('#typeSelect').on('change', function() {
            var type = $(this).val();
            $('#form_search').removeAttr('disabled');
            if(type=='month') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').show();
                $('#monthDiv input').val('{{ date('M-Y') }}');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
            }
            else if(type=='date') {
                $('#dateDiv').show();
                $('#dateDiv input').val('{{ date('Y-m-d') }}');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
            }
            else if(type=='year') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').show();
                $('#yearDiv input').val('{{ date('Y') }}');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
            }
            else if(type=='range') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').show();
            }
            else {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
            }
        });
    </script>
@endpush
@endsection
