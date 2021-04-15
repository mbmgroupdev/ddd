@extends('hr.layout')
@section('title', 'Bonus Disburse')

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
                <li class="active"> Bonus Disburse</li>
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
                              <form class="" role="form" id="employeeWiseSalary">
                                  <div class="panel">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                        {{ Form::select('as_id[]', [],'', ['id'=>'as_id', 'class'=> 'allassociates form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                        <label for="as_id">Employees</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                  <div class="form-group has-float-label select-search-group">
                                                    <select name="formattype" class="form-control capitalize select-search" id="formattype" >
                                                        <option value="0" selected>Single</option>
                                                        <option value="1">Combine</option>
                                                    </select>
                                                    <label for="formattype">Format</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="month" class="report_date form-control" id="emp-month" name="emp_month_year" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime('-1 month')) }}"autocomplete="off" />
                                                      <label for="emp-month">Month</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
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
                <div class="iq-card iq-accordion-block accordion-active">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Unit Wise </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                        <div class="col-12">
                            <form class="" role="form" id="unitWiseSalary"> 
                                <div class="panel mb-0">
                                    
                                    <div class="panel-body pb-0">
                                        <div class="row">
                                            <div class="col-3">
                                              
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="unit" class="form-control capitalize select-search" id="unit" >

                                                        <option selected="" value="">Choose...</option>
                                                        @if(array_intersect(auth()->user()->unit_permissions(), [1,4,5]))
                                                            <option value="145">MBM + MFW + SRT</option>
                                                            <option value="14">MBM + MFW </option>
                                                            <option value="15">MBM + SRT </option>
                                                        @endif
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
                                                        <option selected="" value="">Choose...</option>
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
                                                        <option selected="" value="">Choose...</option>
                                                        <option value="0">Non-OT</option>
                                                        <option value="1">OT</option>
                                                    </select>
                                                    <label for="otnonot">OT/Non-OT</label>
                                                </div>
                                                <div class="row">
                                                  <div class="col-5 pr-0">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                                      <label for="min_sal">Range From</label>
                                                    </div>
                                                  </div>
                                                  <div class="col-1 p-0">
                                                    <div class="c1DHiF text-center">-</div>
                                                  </div>
                                                  <div class="col-6">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                                      <label for="max_sal">Range To</label>
                                                    </div>
                                                  </div>
                                                </div>
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $payType = ['cash'=>'Cash', 'rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Duch-Bangla Bank Limited.'];
                                                    ?>
                                                    {{ Form::select('pay_status', $payType, null, ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType']) }}
                                                    <label for="paymentType">Payment Type</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $status = ['0'=>'No','1'=>'Yes'];
                                                    ?>
                                                    {{ Form::select('disbursed', $status, null, ['placeholder'=>'Select Salary Status ', 'class'=>'form-control capitalize select-search', 'id'=>'disbursed']) }}
                                                    <label for="disbursed">Disbursed</label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group has-float-label select-search-group">
                                                  {{ Form::select('bonus_type', $bonus_type, null, ['placeholder'=>'Select Bonus Type', 'class'=>'form-control capitalize select-search', 'id'=>'bonusFor']) }}
                                                    <label for="bonusFor">Bonus For</label>
                                                </div>
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                  {{ Form::select('bonus_year', $year, null, ['placeholder'=>'Select Year', 'class'=>'form-control capitalize select-search', 'id'=>'bonusFor']) }}
                                                    <label for="bonusFor">Year</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $status = ['1'=>'Active', '6'=> 'Maternity'];
                                                    ?>
                                                    {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus']) }}
                                                    <label for="estatus">Status</label>
                                                </div>
                                                
                                                
                                                <div class="form-group">
                                                  <button onclick="multiple()" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-save"></i> Generate</button>
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
</script>

<script>
    var _token = $('input[name="_token"]').val();
    function printDiv(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
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
        var form = $("#employeeWiseSalary");
        var employee = $("#as_id").val();
        var month = $("#emp-month").val();
        
        if(employee.length > 0 && month !== ''){
            $('.app-loader').show()
            $("#result-process-bar").show();
            $('#setFlug').val(0);
            processbar(0);
            $.ajax({
                type: "get",
                url: '{{ url("hr/operation/employee-wise-salary-sheet")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                    if(response !== 'error'){
                        setTimeout(() => {
                            $('#setFlug').val(1); 
                            processbar('success');
                            $("#result-show").html(response);
                            $('.app-loader').hide();
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
        var year = $('#bonus_year').val();
        var bonus_type = $("#bonus_type").val();
        if(year !== '' && bonus_type !== ''){
            $(".app-loader").show();
            $.ajax({
                type: "get",
                url: '{{ url("hr/operation/unit-wise-bonus-sheet")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), 
                success: function(response)
                {

                  setTimeout(() => {
                      $("#result-show").html(response);
                      $("#result-process-bar").show()
                      $(".app-loader").hide();
                  }, 500);

                  
                },
                error: function (reject) {
                  $(".app-loader").hide();
                }
            });
        }else{
            $("#result-process-bar").hide();
            $.notify("Select Bonus tpe and year", 'error');
            
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