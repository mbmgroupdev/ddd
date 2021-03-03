@extends('hr.layout')
@section('title', 'Payslip')
@section('main-content')
@push('css')
  <style>
    html {
     scroll-behavior: smooth;
    }
    #load{
        width:100%;
        height:100%;
        position:fixed;
        z-index:9999;
        background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 75%  rgba(192,192,192,0.1);
        visibility: hidden;
    }

    @media only screen and (max-width: 1199px) {
        .pay_slip_fields .col-sm-3{width: 33%;}
}
#header{
     display: none;
}
@media only screen and (max-width: 767px) {
        .pay_slip_fields .col-sm-3{width: 50%;}
        .pay_slip_fields .col-sm-8{padding-left: 0px !important;}
}
@media print {
  #header {
    display: table-header-group;
      /* display: block; */
      text-align: center;

  }


  .pageprint{
     display:block;
     page-break-before: always !important;
  }
  .pageprint :first-of-type{
     page-break-before: avoid;
  }
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
                <li class="active"> Pay Slip</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <form role="form" method="get" action="{{ url('hr/operation/payslip') }}" id="searchform" class="form-horizontal panel pay_slip_fields">

            <div class="panel-body">
                <div class="row">
                    <div class="col-3">
                        <div class="form-group has-float-label select-search-group">
                            <select name="unit" class="form-control capitalize select-search" id="unit" >
                                <option selected="" value="">Choose...</option>
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
                            <?php
                              $payType = ['all'=>'All', 'cash'=>'Cash', 'rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Duch-Bangla Bank Limited.'];
                            ?>
                            {{ Form::select('pay_status', $payType, 'all', ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType']) }}
                            <label for="paymentType">Payment Type</label>
                        </div>
                        
                    </div> 
                    <div class="col-3">
                        <div class="form-group has-float-label select-search-group">
                            <select name="line" class="form-control capitalize select-search" id="line" disabled >
                                <option selected="" value="">Choose...</option>
                            </select>
                            <label for="line">Line</label>
                        </div>
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
                        <div class="form-group has-float-label has-required">
                          <input type="number" class="perpage form-control" id="perpage" name="perpage" placeholder="Per Page" required="required" value="4" min="0" autocomplete="off" />
                          <label for="perpage">Per Page</label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group has-float-label has-required">
                          <input type="month" class="report_date form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime('-1 month')) }}"autocomplete="off" />
                          <label for="month">Month</label>
                        </div>
                        <div class="form-group has-float-label select-search-group">
                            <?php
                              $status = ['1'=>'Active','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left', '6'=> 'Maternity'];
                            ?>
                            {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus']) }}
                            <label for="estatus">Status</label>
                        </div>
                        <div class="form-group has-float-label select-search-group">
                            <?php
                              $status = ['0'=>'No','1'=>'Yes'];
                            ?>
                            {{ Form::select('disbursed', $status, null, ['placeholder'=>'Select Salary Status ', 'class'=>'form-control capitalize select-search', 'id'=>'disbursed']) }}
                            <label for="disbursed">Disbursed</label>
                        </div>
                        <div class="form-group">
                          <button onclick="multiple()" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-save"></i> Generate</button>
                        </div>
                    </div>   
                </div>
                
            </div>

                  {{-- </div> --}}

                 <div class="col-sm-12" >
                    <div class="col-sm-12 align-right no-padding-right no-padding-left">

                            @if (!empty(request()->has('unit')))
                              <button type="button" onClick="printPayslip('html-2-pdfwrapper')" class="btn btn-warning btn-sm" title="Print" style="margin-bottom: 20px;">
                                 <i class="fa fa-print"></i>
                              </button>
                              <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" style="margin-bottom: 20px;">
                                <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                              </button>
                              <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF" style="margin-bottom: 20px;">
                                  <i class="fa fa-file-pdf-o"></i>
                              </a>
                            @endif
                    </div>
                 </div>
        </form>
        <div id="result-show"></div>
            
    </div>
</div>

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
<script type="text/javascript">

$(document).ready(function(){
    // Reuseable ajax function
    var _token = $('input[name="_token"]').val();

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



        // excel conversion -->
        $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
        location.href=url
        return false
        })

    });


    //multiple salary sheet
    function multiple() {
        var form = $(".pay_slip_fields");
        var unit = $("#unit").val();
        var location = $('select[name="location"]').val();
        var month = $("#month").val();
        if((unit !== '' || location !== '') && month !== ''){
            $("#result-process-bar").show();
            $("#result-show").html(loader);
            $('#setFlug').val(0);
            processbar(0);
            $.ajax({
                type: "get",
                url: '{{ url("hrm/operation/unit-wise-pay-slip")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                    if(response !== 'error'){
                        $('#setFlug').val(1); 
                        processbar('success');
                        setTimeout(() => {
                            $("#result-show").html(response.view);
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
            if((unit === '' || location === '')){
                $.notify("Select Unit Or Location", 'error');
            }
            if(month === ''){
                $.notify("Please Select Month", 'error');
            }
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

    function printPayslip(divName)
    {
        var myWindow=window.open('','PRINT','width=800,height=800');
        myWindow.document.write('<html><head></head><body>');
        myWindow.document.write(document.getElementById(divName).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }


    function attLocation(loc){
    window.location = loc;
   }
</script>
@endsection
