@extends('hr.layout')
@section('title', 'Bill Announcement')

@section('main-content')
@push('js')
    <style>
       table tr p span{
            font-size: 10px !important;
        }
        table td, table th{
            vertical-align: top !important;
        }
        .table td {
            padding: 5px;
        }
        .panel-body {
            padding: 10px 8px;
        }
        h3 {
            font-size: 1rem;
        }
        h2, h2 b {
            font-size: 1.5rem;
        }
        #top-tab-list li a {
            border: 1px solid;
        }
        .iq-accordion.career-style .iq-accordion-block {
            margin-bottom: 15px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-top: 10px;
        }
        p span, p span font{
            font-size: 10px;
        }
        .iq-accordion-block{
            padding: 10px 0;
        } 
        .custom-control-label:after, .custom-control-label:before {
            top: .50rem;
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
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Bill Announcement</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Employee Wise </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                          <div class="col-12">
                              <form class="" role="form" id="employeeWiseBill">
                                  <div class="panel">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                        {{ Form::select('as_id[]', [],'', ['id'=>'as_id', 'class'=> 'allassociates form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                        <label for="as_id">Employees</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="emp-form-range" name="date_type" class="" value="range">
                                                <input type="hidden" class="report_date form-control" id="emp-month-year" name="month_year" placeholder=" Month-Year" value="{{ date('Y-m') }}"autocomplete="off" />
                                                <div class="col-sm-2">
                                                    <div class="form-group has-float-label has-required">
                                                        <input type="date" class="report_date datepicker form-control" id="associate_from_date" name="from_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                        <label for="associate_from_date">From Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group has-float-label has-required">
                                                        <input type="date" class="report_date datepicker form-control" id="associate_to_date" name="to_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                        <label for="associate_to_date">To Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                  <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $status = ['0'=>'Unpaid','1'=>'Paid'];
                                                    ?>
                                                    {{ Form::select('pay_status', $status, 0, ['placeholder'=>'Select Payable Status ', 'class'=>'form-control capitalize select-search', 'id'=>'associate_pay_status']) }}
                                                    <label for="associate_pay_status">Payable Status</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-2">
                                                  <div class="form-group has-float-label select-search-group">
                                                    
                                                    {{ Form::select('bill_type', $billType, 0, ['placeholder'=>'Select Payable Status ', 'class'=>'form-control capitalize select-search', 'id'=>'associate_bill_type']) }}
                                                    <label for="associate_bill_type">Bill Type</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-1 no-padding">
                                                    <button onclick="individual()" type="button" class="btn btn-primary btn-sm" id="individualBtn"><i class="fa fa-save"></i> Generate</button>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                  </div>
                              </form>
                          </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card iq-accordion-block accordion-active ">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">Filter </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                        <div class="col-12">
                            <form class="" role="form" id="unitWiseSalary"> 
                                <div class="panel mb-0">
                                    @php
                                      $mbmFlag = 0;
                                      $mbmAll = [1,4,5];
                                      $permission = auth()->user()->unit_permissions();
                                      $checkUnit = array_intersect($mbmAll,$permission);
                                      if(count($checkUnit) > 2){
                                        $mbmFlag = 1;
                                      }
                                    @endphp
                                    <div class="panel-body pb-0">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group has-float-label select-search-group has-required">
                                                    <select name="unit" class="form-control capitalize select-search" id="unit" required>
                                                        <option value=""> - Select - </option>
                                                        
                                                        @foreach($unitList as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                  <label for="unit">Unit</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="location" class="form-control capitalize select-search" id="location">
                                                        <option selected="" value="">Choose Location...</option>
                                                        @foreach($locationList as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                  <label for="location">Location</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="area" class="form-control capitalize select-search" id="area">
                                                        <option value="">Choose...</option>
                                                        @foreach($areaList as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="area">Area</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="department">Department</label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="section">Section</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                                        <option selected="" value="">Choose...</option> 
                                                    </select>
                                                    <label for="subSection">Sub Section</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="floor" class="form-control capitalize select-search" id="floor" disabled >
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="floor">Floor</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="line" class="form-control capitalize select-search" id="line" disabled >
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="line">Line</label>
                                                </div>
                                            </div> 
                                            <div class="col-3">
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                        <option  value="">Choose...</option>
                                                        <option value="0">Non-OT</option>
                                                        <option value="1" selected="">OT</option>
                                                    </select>
                                                    <label for="otnonot">OT/Non-OT</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $status = ['0'=>'Unpaid','1'=>'Paid'];
                                                    ?>
                                                    {{ Form::select('pay_status', $status, 0, ['placeholder'=>'Select Payable Status ', 'class'=>'form-control capitalize select-search', 'id'=>'pay_status']) }}
                                                    <label for="pay_status">Payable Status</label>
                                                </div>
                                                <div class="form-group has-float-label has-required select-search-group">
                                                    <?php
                                                      $status = ['1'=>'Active','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left', '6'=>'Maternity'];
                                                    ?>
                                                    {{ Form::select('as_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus']) }}
                                                    <label for="estatus">Status</label>
                                                </div>
                                                <div class="form-group has-float-label has-required select-search-group">
                                                    <?php
                                                      $payType = ['all'=>'All','cash' => 'Cash','dbbl'=>'DBBL','rocket'=>'Rocket'];
                                                    ?>
                                                    {{ Form::select('pay_status', $payType, null, ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType', 'required']) }}
                                                    <label for="paymentType">Payment Type</label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    
                                                    {{ Form::select('bill_type', $billType, 0, ['placeholder'=>'Select Payable Status ', 'class'=>'form-control capitalize select-search', 'id'=>'bill_type']) }}
                                                    <label for="bill_type">Bill Type</label>
                                                </div>
                                                <div class="form-inline mb-3 mt-10">
                                                    
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                       <input type="radio" id="form-range" name="date_type" class="date_type custom-control-input" value="range" checked="">
                                                       <label class="custom-control-label cursor-pointer" for="form-range"> Range </label>
                                                    </div>
                                                    {{-- <div class="custom-control custom-radio custom-control-inline">
                                                       <input type="radio" id="cash_amount" name="date_type" class="date_type custom-control-input" value="Cash">
                                                       <label class="custom-control-label" for="cash_amount"> Date </label>
                                                    </div> --}}
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                       <input type="radio" id="form-month" name="date_type" class="date_type custom-control-input" value="month" >
                                                       <label class="custom-control-label cursor-pointer" for="form-month"> Month </label>
                                                    </div>
                                                </div>
                                                <div id="month-form" style="display: none;">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="month" class="report_date form-control" id="month-year" name="month_year" placeholder=" Month-Year" value="{{ date('Y-m') }}"autocomplete="off" />
                                                      <label for="month-year">Month</label>
                                                    </div>
                                                </div>
                                                <div class="row" id="range-form">
                                                  <div class="col pr-0">
                                                      <div class="form-group has-float-label has-required">
                                                          <input type="date" class="report_date datepicker form-control" id="from_date" name="from_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                          <label for="from_date">From Date</label>
                                                      </div>
                                                  </div>
                                                  <div class="col">
                                                      <div class="form-group has-float-label has-required">
                                                          <input type="date" class="report_date datepicker form-control" id="to_date" name="to_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                          <label for="to_date">To Date</label>
                                                      </div>
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                  <button onclick="multiple()" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-search"></i> Generate</button>
                                                </div>
                                            </div>   
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </form>
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                   </div>
                </div>
                
             </div>
            <input type="hidden" value="0" id="setFlug">
            <div class="row">
                <div class="col h-min-400">
                    <div id="result-process-bar" style="display: none;">
                        <div class="iq-card">
                            <div class="iq-card-body">
                                
                                <div class="" id="result-show">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                          0%
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
    
    var loader = '<div class="progress"><div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div>';
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
        //month-Year validation end---------------
    });
    $(document).on('change','.date_type', function(){

        if($(this).val() == 'month'){
            $("#month-form").show();
            $("#range-form").hide();
        }else if($(this).val() == 'range'){
            $("#month-form").hide();
            $("#range-form").show();
        }
    });
</script>

<script>
    var _token = $('input[name="_token"]').val();
    

    // Reusable ajax function
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
        $( "#floor" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getFloorListByUnitID') }}', 'get', {unit_id: $(this).val()}, floor);
        // line
        $.ajax({
           url : "{{ url('hr/reports/line_by_unit') }}",
           type: 'get',
           data: {unit : $(this).val()},
           success: function(data)
           {
                $('#line').removeAttr('disabled');
                $("#line").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });


    //Load Department List By Area ID
    var area = $("#area");
    var department = $("#department");
    area.on('change', function() {
        $( "#department" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getDepartmentListByAreaID') }}', 'get', {area_id: $(this).val()}, department);
    });

    //Load Section List by department
    var section = $("#section");
    department.on('change', function() {
        $( "#section" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getSectionListByDepartmentID') }}', 'get', {area_id: area.val(), department_id: $(this).val()}, section);
    });

    //Load Sub Section List by Section
    var subSection = $("#subSection");
    section.on('change', function() {
        $( "#subSection" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getSubSectionListBySectionID') }}', 'get', {area_id: area.val(), department_id: department.val(), section_id: $(this).val()}, subSection);
    });

    //individual salary sheet
    function individual() {
        var form = $("#employeeWiseBill");
        var employee = $("#as_id").val();
        var from_date = $("#associate_from_date").val();
        var to_date = $("#associate_to_date").val();
        
        if(employee.length > 0 && from_date !== '' && to_date !== ''){
            
            $("#result-process-bar").show();
            $('#setFlug').val(0);
            processbar(0);
            $.ajax({
                type: "get",
                url: '{{ url("hr/operation/filter-wise-bill-announcement-sheet")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                  // console.log(response);
                    if(response !== 'error'){
                        setTimeout(() => {
                            $('#setFlug').val(1); 
                            processbar('success');
                            $("#result-show").html(response);
                            
                        }, 1000);
                    }else{
                        $('#setFlug').val(2); 
                        $('.app-loader').hide();
                        processbar('error');
                    }
                },
                error: function (reject) {
                    processbar('error');
                    $('#setFlug').val(2); 
                }
            });
        }else{
            $("#result-process-bar").hide();
            if(employee.length === 0){
                $.notify("Please Select At Least One Employee", 'error');
            }
            if(month === null){
                $.notify("Please Select Month", 'error');
            }
        }
    }

    //multiple salary sheet
    function multiple() {
        var form = $("#unitWiseSalary");
        var unit = $("#unit").val();
        var location = $('select[name="location"]').val();
        var area = $('select[name="area"]').val();
        var department = $('select[name="department"]').val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var dateType = $('input[name="date_type"]').val();
        var flag = 0;
        var msg = '';
        if(dateType == 'range'){
            if(fromDate == '' && toDate == ''){
                flag = 1;
                msg = 'Date Range Not Null!';
            }
        }else if(dateType == 'month'){
            if($("#month-year").val() == ''){
                flag = 1;
                msg = 'Month Not Null!';
            }
        }

        if((unit === '' && location === '')){
            flag = 1;
            msg = 'Select Unit/Location';
        }

        if(flag === 0){
            $("#result-process-bar").show();
            $("#result-show").html(loader);
            $('#setFlug').val(0);
            processbar(0);
            $.ajax({
                type: "get",
                url: '{{ url("hr/operation/filter-wise-bill-announcement-sheet")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                    // console.log(response)
                    if(response !== 'error'){
                        $('#setFlug').val(1); 
                        processbar('success');
                        setTimeout(() => {
                            $("#result-show").html(response);
                        }, 1000);
                    }else{
                        $('#setFlug').val(2); 
                        processbar('error');
                    }
                },
                error: function (reject) {
                    processbar('error');
                    $('#setFlug').val(2); 
                }
            });
        }else{
            $("#result-process-bar").hide();
            $.notify(msg, 'error');
            
        }
    }

    var incValue = 1;
    
    function processbar(percentage) {
        var setFlug = $('#setFlug').val();
        if(parseInt(setFlug) === 1){
            var percentageVaule = 99;
            $('#progress-bar').html(percentageVaule+'%');
            $('#progress-bar').css({width: percentageVaule+'%'});
            $('#progress-bar').attr('aria-valuenow', percentageVaule+'%');
            setTimeout(() => {
                percentageVaule = 0;
                percentage = 0;
                $('#progress-bar').html(percentageVaule+'%');
                $('#progress-bar').css({width: percentageVaule+'%'});
                $('#progress-bar').attr('aria-valuenow', percentageVaule+'%');
                //$("#result-process-bar").css('display', 'none');
            }, 1000);
        }else if(parseInt(setFlug) === 2){
            console.log('error');
        }else{
            // set percentage in progress bar
            percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
            $('#progress-bar').html(percentage+'%');
            $('#progress-bar').css({width: percentage+'%'});
            $('#progress-bar').attr('aria-valuenow', percentage+'%');
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
</script>
@endpush
@endsection