@extends('hr.layout')
@section('title', 'Disciplinary Record')
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
                    <a href="#">Performance </a>
                </li>
                <li class="active"> Disciplinary Record</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Disciplinary Record<a href="{{ url('hr/performance/operation/disciplinary_list')}}" class="pull-right btn btn-xx btn-primary">Record List</a></h6></div> 
                <div class="panel-body"> 
                    {{ Form::open(['url'=>'hr/performance/operation/disciplinary_form', 'class'=>'form']) }}

                        <input type="hidden" name="gaid" value="{{ Request::get('gaid') }}"> 
                        <div class="row justify-content-center">
                            <div class="col-sm-9">
                                <div class="row">
                                    
                                    <div class="col-sm-6">
                                        
                                        <div class="user-details-block mb-3 mt-custom-4">
                                            <div class="user-profile text-center mt-0">
                                                <img id="off_avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                            </div>
                                            <div class="text-center mt-3">
                                             <h4><b id="off_name">-------------</b></h4>
                                             <p class="mb-0" id="off_designation">
                                                --------------------------</p>
                                             <p class="mb-0" >
                                                Oracle ID: <span id="off_oracle_id" class="text-success">-------------</span>
                                             </p>
                                             <p  class="mb-0">Department: <span id="off_department" class="text-success">------------------------</span> </p>
                                             
                                             </div>
                                        </div>
                                        <div class="form-group has-float-label has-required select-search-group ml-custom-5 mr-custom-5">
                                            {{ Form::select('dis_re_offender_id', [(!empty($appeal->hr_griv_appl_offender_as_id)?$appeal->hr_griv_appl_offender_as_id:null) => (!empty($appeal->offender)?$appeal->offender:null)], (!empty($appeal->hr_griv_appl_offender_as_id)?$appeal->hr_griv_appl_offender_as_id:null), ['placeholder'=>'Select Offender\'s Name or ID', 'id'=>'dis_re_offender_id', 'class'=> 'associates  ', 'required'=>'required', 'data-type'=>'off_']) }}
                                            <label  for="dis_re_offender_id"> Offender ID </label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        
                                        <div class="user-details-block mb-3">
                                            <div class="user-profile text-center mt-0 mt-custom-4">
                                                <img id="gri_avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                            </div>
                                            <div class="text-center mt-3">
                                             <h4><b id="gri_name">-------------</b></h4>
                                             <p class="mb-0" id="gri_designation">
                                                --------------------------</p>
                                             <p class="mb-0" >
                                                Oracle ID: <span id="gri_oracle_id" class="text-success">-------------</span>
                                             </p>
                                             
                                             <p  class="mb-0">Department: <span id="gri_department" class="text-success">------------------------</span> </p>
                                             
                                             </div>
                                        </div>
                                        <div class="form-group has-float-label has-required select-search-group ml-custom-5 mr-custom-5">
                                            {{ Form::select('dis_re_griever_id', [(!empty($appeal->hr_griv_associate_id)?$appeal->hr_griv_associate_id:null) => (!empty($appeal->griever)?$appeal->griever:null)], (!empty($appeal->hr_griv_associate_id)?$appeal->hr_griv_associate_id:null), ['placeholder'=>'Select Associate\'s ID', 'id'=>'dis_re_griever_id', 'class'=> 'associates ', 'data-type'=>'gri_']) }}  
                                            <label  for="dis_re_griever_id"> Griever ID (Optional) </label>
                                        </div> 
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-float-label has-required ">
                                    <input type="date" name="dis_re_discussed_date" id="dis_re_discussed_date" class="form-control" required="required" value="{{ (!empty($appeal->hr_griv_appl_discussed_date)?$appeal->hr_griv_appl_discussed_date:null) }}" />
                                    <label for="dis_re_discussed_date"> Discussed Date </label>
                                </div>
                                <div class="form-group has-float-label has-required select-search-group">
                                    {{ Form::select('dis_re_issue_id', $issueList, (!empty($appeal->hr_griv_appl_issue_id)?$appeal->hr_griv_appl_issue_id:null), ['placeholder'=>'Select Reason', 'id'=>'dis_re_issue_id', 'class'=> 'form-control', 'required'=>'required']) }}
                                    <label  for="dis_re_issue_id">Reason </label>
                                </div>
                                <div class="form-group has-float-label has-required">
                                    <textarea name="dis_re_req_remedy" id="dis_re_req_remedy" class="form-control" placeholder="Requested Remedy"  required="required">{{ (!empty($appeal->hr_griv_appl_req_remedy)?$appeal->hr_griv_appl_req_remedy:null) }}</textarea>
                                    <label  for="dis_re_req_remedy"> Requested Remedy  </label>
                                </div>
                                <div class="form-group has-float-label has-required select-search-group">
                                    {{ Form::select('dis_re_ac_step_id', $stepList, null, ['placeholder'=>'Select Action Step', 'id'=>'dis_re_ac_step_id', 'class'=> 'col-xs-12', 'required'=>'required']) }}
                                    <label  for="dis_re_ac_step_id">Action Steps </label>
                                </div>
                                <div class="form-group has-float-label has-required">
                                    <label >Date of Execution From </label>
                                    <input type="date" name="dis_re_doe_from" id="dis_re_doe_from" placeholder="From" required="required" class="form-control">
                                </div>
                                <div class="form-group has-float-label has-required">
                                    <input type="date" name="dis_re_doe_to" id="dis_re_doe_to" placeholder="To" required="required" class="form-control">
                                    <label >Date of Execution To</label>
                                </div>
                                <div class="form-group has-float-label">
                                    <button class="btn pull-right btn-primary" type="submit">
                                        <i class="fa fa-check"></i> Submit
                                    </button>

                                </div>
                            </div>
                        </div>
                       
                    {{Form::close()}}
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function()
{
    $('body').on('change', '.associates', function(){
        var type = $(this).data('type');
        $.ajax({
            url: '{{ url("hr/associate") }}',
            dataType: 'json',
            data: {associate_id: $(this).val()},
            success: function(data)
            {
                $('#'+type+'oracle_id').text(data.as_oracle_code);
                $('#'+type+'name').text(data.as_name);
                $('#'+type+'department').text(data.hr_department_name);
                $('#'+type+'designation').text(data.hr_designation_name);
                if(data.as_pic == null){
                    if(data.as_gender == 'Male'){
                        $('#'+type+'avatar').attr('src',url+'/assets/images/user/09.jpg');   
                    }
                    else{
                        $('#'+type+'avatar').attr('src',url+'/assets/images/user/1.jpg');   
                    }
                }
                else{
                    $('#'+type+'avatar').attr('src', url+data.as_pic);  
                }
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    });


    //date validation------------------
    $('#dis_re_doe_from').on('dp.change',function(){
        $('#dis_re_doe_to').val($('#dis_re_doe_from').val());    
    });

    $('#dis_re_doe_to').on('dp.change',function(){
        var end     = new Date($(this).val());
        var start   = new Date($('#dis_re_doe_from').val());
        if(start == '' || start == null){
            alert("Please enter From-Date first");
            $('#dis_re_doe_to').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n From-Date is latest than To-Date");
                $('#dis_re_doe_to').val('');
            }
        }
    });
    //date validation end---------------
});
</script>
@endpush
@endsection