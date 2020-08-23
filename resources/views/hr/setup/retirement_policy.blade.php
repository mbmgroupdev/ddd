@extends('hr.layout')
@section('title', '')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active">Retirement Policy </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i>Retirement Policy</small></h1>
            </div>

            <div class="row">
                <div class="panel panel-success" style="">
                    <div class="panel-body">
                        <!-- Display Erro/Success Message -->
                        @include('inc/message')
                        
                        <div class="row">
                            <div class="col-sm-7">
                                {{Form::open(['url'=>'hr/setup/retirement_policy_save', 'class'=>'form-horizontal'])}}
                                    <div class="row no-padding no-margin">
                                        <div class="form-group">
                                            <label class="col-sm-4" >Employee Type</label>
                                            <div class="col-sm-8">
                                                <select class="col-sm-12" id="employee_type_id" name="employee_type_id">
                                                    <option value="">Select Employee Type</option>
                                                    <option value="1">Managment</option>
                                                    <option value="2">Staff</option>
                                                    <option value="3">Worker</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4" >Employee</label>
                                            <div class="col-sm-8 ff" style="pointer-events: none;">
                                                <select class="col-sm-12" id="employee_id" name="employee_id" >
                                                    <option value="">Select Emp Type first</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4" >Service Days <small style="font-size: 9px; color: maroon;">(Till Today)</small></label>
                                            <div class="col-sm-8">
                                                <div class="col-sm-2 no-margin no-padding">
                                                    <input class="col-xs-12" type="text" name="service_year" id="service_year" placeholder="Y" readonly="readonly">
                                                </div>
                                                <div class="col-sm-2"><label style="font-size: 12px; margin-top: 10px;">Year/s</label></div>
                                                
                                                <div class="col-sm-2 no-margin no-padding">
                                                    <input class="col-xs-12" type="text" name="service_month" id="service_month" placeholder="M" readonly="readonly">
                                                </div>
                                                <div class="col-sm-2"><label style="font-size: 12px; margin-top: 10px;">Month/s</label></div>
                                                
                                                <div class="col-sm-2 no-margin no-padding">
                                                    <input class="col-xs-12" type="text" name="service_days" id="service_days" placeholder="D" readonly="readonly">
                                                </div>
                                                <div class="col-sm-2"><label style="font-size: 12px; margin-top: 10px;">Day/s</label></div>
                                                {{-- <input type="number" name="year_of_service" id="year_of_service" class="col-xs-12" style="height: auto;" data-validation="required" placeholder="1Y ~ 100Y"> --}}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4" >% of Basic</label>
                                            <div class="col-sm-8">
                                                <input type="number" name="percent_of_basic" id="percent_of_basic" class="col-xs-12" style="height: auto;" data-validation="required" placeholder="1% ~ 100%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row no-padding no-margin">
                                        <button type="submit" class="btn btn-sm btn-primary pull-right" id="save_button" style="border-radius: 2px;" disabled="disabled">Save</button>
                                    </div>
                                    
                                {{Form::close()}}
                            </div>
                            <div class="col-sm-5">
                                <div class="panel panel-info">
                                    <div class="panel-heading"><h5>Basic Information</h5></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6 no-margin no-padding pull-right" id="picture">
                                                
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <table class="table table-striped table-responsive">
                                                <tbody>
                                                    <tr>
                                                       <td>Associate ID</td> 
                                                       <td id="associate_id"></td> 
                                                    </tr>
                                                    <tr>
                                                       <td>Unit</td> 
                                                       <td id="unit"></td> 
                                                    </tr>
                                                    {{-- <tr>
                                                       <td>Location</td> 
                                                       <td id="location"></td> 
                                                    </tr> --}}
                                                    <tr>
                                                       <td>Department</td> 
                                                       <td id="department"></td> 
                                                    </tr>
                                                    <tr>
                                                       <td>Designation</td> 
                                                       <td id="designation"></td> 
                                                    </tr>
                                                    <tr>
                                                       <td>Date of Joining</td> 
                                                       <td id="doj"></td> 
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#percent_of_basic').on('keyup',function(){
            
            if($('#percent_of_basic').val()<1){
                $('#percent_of_basic').val(1);
            }
           
            if($('#percent_of_basic').val()>100){
                $('#percent_of_basic').val(100);
            }
        });

        //Unique Check
        $('#employee_type_id').on('change', function(){
             var emp_type_id = $(this).val();
             // var yr_of_service = $('#year_of_service').val();
                //ajax call to check the existance of emp_type and service year.
                // console.log(emp_type_id,yr_of_service );
            $('#associate_id').text('');
            $('#unit').text('');
            // $('#location').text('');
            $('#department').text('');
            $('#designation').text('');
            $('#doj').text('');
            $('#picture').html('');
            $('#service_year').val('');
            $('#service_month').val('');
            $('#service_days').val('');

            if(emp_type_id == ""){
                $('.ff').attr('style', 'pointer-events:none;');
                $('#employee_id').html('<option value="">Select Emp Type first</option>');
            }
            else{
                $.ajax({
                    url : "{{ url('hr/setup/retirement/get_employee_list') }}",
                    type: 'get',
                    dataType : 'json',
                    data: { 
                        emp_type_id : emp_type_id
                    },
                    success: function(data)
                    {
                         $('.ff').removeAttr('style');
                         $('#employee_id').html(data);   
                    },
                    error: function(data)
                    {
                        alert('failed...');
                       
                    }
                });
            }
            
        });

        $('#employee_id').on('change', function(){
            var emp_id = $(this).val();
             // var yr_of_service = $('#year_of_service').val();
                //ajax call to check the existance of emp_type and service year.
                // console.log(emp_type_id,yr_of_service );
            
            if(emp_id != ""){
                var url = '{{url('')}}';
                console.log(url);
                $.ajax({
                    url : "{{ url('hr/setup/retirement/get_employee_details') }}",
                    type: 'get',
                    dataType : 'json',
                    data: { 
                        emp_id : emp_id
                    },
                    success: function(data)
                    {
                        // console.log(data);
                        $('#associate_id').text(data['associate_id']);
                        $('#unit').text(data['hr_unit_name']);
                        // $('#location').text(data['hr_location_name']);
                        $('#department').text(data['hr_department_name']);
                        $('#designation').text(data['hr_designation_name']);
                        $('#doj').text(data['as_doj']);
                        if(data['as_pic'] == null){
                            if(data['as_gender'] == 'Male'){
                                $('#picture').html('<img  src=\"'+url+'/assets/images/employee/male.jpg\" style="height: 80px; width: 60px;" class="pull-right">');   
                            }
                            else{
                                $('#picture').html('<img  src=\"'+url+'/assets/images/employee/female.jpg\" style="height: 80px; width: 60px;" class="pull-right">');   
                            }
                        }
                        else{
                            $('#picture').html('<img  src='+url+data['as_pic']+' style="height: 80px; width: 60px;" class="pull-right">');   
                        }
                        $('#service_year').val(data['service_years']);
                        $('#service_month').val(data['service_months']);
                        $('#service_days').val(data['service_days']);
                    },
                    error: function(data)
                    {
                        alert('failed...');
                       
                    }
                });
            }
            else{
                $('#associate_id').text('');
                $('#unit').text('');
                // $('#location').text('');
                $('#department').text('');
                $('#designation').text('');
                $('#doj').text('');
                $('#picture').html('');
                $('#service_year').val('');
                $('#service_month').val('');
                $('#service_days').val('');
            }

        });
    });
</script>
@endsection