@extends('hr.layout')
@section('title', 'Monthly Salary')

@section('main-content')

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
                <li class="active"> Monthly Salary</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="unitWise"> 
                        <div class="panel">
                            
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
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
                                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="floor_id">Floor</label>
                                        </div>
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
                                    </div> 
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="line_id">Line</label>
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
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required">
                                          <input type="month" class="report_date form-control" id="report-date" name="month" placeholder=" Month-Year"required="required" value="{{ date('Y-m')}}"autocomplete="off" />
                                          <label for="report-date">Month</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <?php
                                              $status = ['1'=>'Active','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left'];
                                            ?>
                                            {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus']) }}
                                            <label for="estatus">Status</label>
                                        </div>
                                        <div class="form-group">
                                          <button onclick="multiple()" class="btn btn-primary nextBtn btn-lg pull-right choice_2_generate_btn" type="button" id="choice_2_generate_btn" name="choice_2_generate_btn"><i class="fa fa-save"></i> Generate</button>
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
            <div class="row">
                <div class="col h-min-400">
                    <input type="hidden" value="0" id="setFlug">
            
                    <div class="progress" id="result-process-bar" style="display: none;">
                        <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress_bar_main" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                          0%
                        </div>
                    </div>
                    {{-- result of list --}}
                    <div class="panel panel-success" id="salary-sheet-result" style="display: none">
                        <div class="panel-heading" id="salary-sheet-result-inner">Salary sheet result  &nbsp;<button rel='tooltip' data-tooltip-location='left' data-tooltip='Salary sheet result print' type="button" onClick="printMe1('result-show')" class="btn btn-primary btn-xs text-right"><i class="fa fa-print"></i> Print</button></div>
                        <div class="panel-body" id="result-show"></div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
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
                url: '{{ url("/hr/reports/salary-sheet-custom-individual-search") }}',
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
                url: '{{ url("hr/reports/salary-sheet-employee-wise") }}',
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
                    url: '{{ url("/hr/reports/ajax_get_employees") }}',
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