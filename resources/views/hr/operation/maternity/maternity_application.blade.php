@extends('hr.layout')
@section('title', 'Maternity Leave Application')
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
                    <a href="#"> Operation </a>
                </li>
                <li>
                    <a href="#"> Maternity Leave </a>
                </li>
                <li class="active">Application </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-sm btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </li>
            </ul>
        </div>

        
        @include('inc/message')
        <div class="panel panel-success" style="">
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-4">
                        {{Form::open(['url'=>'hr/operation/maternity-leave', 'class'=>'form-horizontal needs-validation', 'novalidate', "enctype" => "multipart/form-data"])}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group has-required has-float-label  select-search-group">
                                        
                                        {{ Form::select('associate',  [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'femaleassociates', 'class'=> 'female-associates form-control', 'required'=>'required']) }}
                                        <label >Employee</label>
                                        
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label ">
                                        <input id="applied_date" type="date" name="applied_date" class="form-control" required placeholder="Enter baby no" value="{{date('Y-m-d')}}">
                                        <label for="applied_date">Applied Date</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group has-float-label ">
                                        <input id="edd" type="date" name="edd" class="form-control" required placeholder="Enter EDD" value="">
                                        <label for="applied_date">EDD</label>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label ">
                                        <input id="no_of_son" type="text" name="no_of_son" class="form-control" required placeholder="Enter no of son" value="0">
                                        <label for="no_of_son">Son</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label ">
                                        <input id="no_of_daughter" type="text" name="no_of_daughter" class="form-control" required placeholder="Enter no of daughter" value="0">
                                        <label for="no_of_daughter">Daughter</label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label ">
                                        <input id="last_child_age" type="text" name="last_child_age" class="form-control" required placeholder="Enter no of daughter" value="0">
                                        <label for="last_child_age">Last Child Age</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group has-required has-float-label ">
                                        <input id="husband_name" type="text" name="husband_name" class="form-control" required placeholder="Enter husban name" >
                                        <label for="husband_name">Husband Name</label>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label ">
                                        <input id="husband_occupasion" type="text" name="husband_occupasion" class="form-control" required placeholder="Enter husband occupation" >
                                        <label for="husband_occupasion">Occupation</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group has-float-label ">
                                        <input id="husband_age" type="text" name="husband_age" class="form-control"  placeholder="Enter husband age" >
                                        <label for="husband_age">Age</label>
                                    </div>
                                </div>
                            </div>    
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group  has-float-label">
                                        <input id="usg_report" type="file"  name="usg_report"  >
                                        <label for="usg_report" >USG Report</label><br>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" >Proceed to Checkup!</button>
                                </div>
                            </div>
                            
                        {{Form::close()}}
                    </div>
                    <div class="col-8">
                        <div class=" panel-info" id="basic_info_div">
                            <div class="panel-body">
                                <div class="row">
                                    
                                    <div class="col-sm-6">
                                        
                                        <div class="user-details-block" style="border-left: 1px solid #d1d1d1;padding-top: 3.5rem;">
                                            <div class="user-profile text-center mt-0">
                                                <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/1.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/1.jpg") }}';">
                                            </div>
                                            <div class="text-center mt-3">
                                             <h4><b id="name">-------------</b></h4>
                                             <p class="mb-0" id="designation">
                                                --------------------------</p>
                                             <p class="mb-0" >
                                                Oracle ID: <span id="oracle_id" class="text-success">-------------</span>
                                             </p>
                                             <p class="mb-0" >
                                                Associate ID: <span id="associate_id" class="text-success">-------------</span>
                                             </p>
                                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <ul class="speciality-list m-0 p-0">
                                            
                                            <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-city"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Unit</h6>
                                                  <p id="unit" class="mb-0">------------------------</p>
                                               </div>
                                            </li>
                                            <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-city"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Department</h6>
                                                  <p id="department" class="mb-0">------------------------</p>
                                               </div>
                                            </li>
                                            <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-city"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Line</h6>
                                                  <p id="line" class="mb-0">------------------------</p>
                                               </div>
                                            </li>
                                             <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-city"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Section</h6>
                                                  <p id="section" class="mb-0">------------------------</p>
                                               </div>
                                            </li>
                                            <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-dollar-sign"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Salary</h6>
                                                  <p class="mb-0">Gross:  <span class="text-danger" id="gross_salary">0</span> Basic: <span class="text-success" id="basic_salary">0</span></p>
                                               </div>
                                            </li>
                                            
                                         </ul>
                                        
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

@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        const baseurl = window.location.protocol + "//" + window.location.host + "/",
          loader = '<p class="display-1 m-5 p-5 text-center text-warning">'+
                        '<i class="fas fa-circle-notch fa-spin "></i>'+
                    '</p>';
         $('#femaleassociates').select2({
            placeholder: 'Select Employee',
            ajax: {
                url: baseurl+'hr/adminstrator/employee/female-associates',
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
                                text: item.user_name,
                                id: item.associate_id
                            }
                        }) 
                    };
                },
                cache: true
            }
        });
        $('#femaleassociates').on('change', function(){
            var emp_id = $(this).val();
            if(emp_id != ""){
                $('.app-loader').show();
                var url = '{{url('')}}';
                $.ajax({
                    url : "{{ url('hr/associate') }}",
                    type: 'get',
                    dataType : 'json',
                    data: { 
                        associate_id : emp_id
                    },
                    success: function(data)
                    {
                        $('#associate_id').text(data['associate_id']);
                        $('#oracle_id').text(data['as_oracle_code']);
                        $('#name').text(data['as_name']);
                        $('#unit').text(data['hr_unit_name']);
                        $('#department').text(data['hr_department_name']);
                        $('#line').text(data['hr_line_name']);
                        $('#section').text(data['hr_section_name']);
                        $('#designation').text(data['hr_designation_name']);
                        $('#gross_salary').text(data['ben_current_salary']);
                        $('#basic_salary').text(data['ben_basic']);
                        $('#husband_name').val(data['emp_adv_info_spouse']);
                        
                        if(data['as_pic'] == null){
                            $('#avatar').attr('src',url+'/assets/images/user/1.jpg'); 
                        }
                        else{
                            $('#avatar').attr('src', url+data['as_pic']);
                        } 
                        $('.app-loader').hide();
                    },
                    error: function(data)
                    {
                        $.notify('failed...','error');
                        $('.app-loader').hide();
                    }
                });
            }
            else{
                $('#associate_id').text('-------------');
                $('#oracle_id').text('-------------');
                $('#name').text('-------------');
                $('#unit').text('------------------------');
                $('#department').text('------------------------');
                $('#designation').text('------------------------');
                $('#doj').text('------------------------');
                $('#avatar').attr('src','/assets/images/user/09.jpg'); 
                $('#service_Y').html('0');
                $('#service_m').html('0');
                $('#service_d').html('0');
                $('#gross_salary').text('0');
                $('#basic_salary').text('0');
                $('#total_earn_leave').text('0');
                $('#enjoyed_earn_leave').text('0');
                $('#remained_earn_leave').text('0');
                $('.app-loader').hide();
            }

        });

        $('body').on('change','#usg_report',function () {
            // console.log($(this).val());
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $.notify("Please Upload only pdf/doc/docx/jpg/jpeg/png type file.", 'error');
                $(this).val('');
            }
        });

    });
</script>
    
@endpush
@endsection