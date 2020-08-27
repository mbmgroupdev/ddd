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
                <div class="panel-heading"><h6>Disciplinary Record<a href="{{ url('hr/performance/operation/disciplinary_list')}}" class="pull-right btn btn-xx btn-info">Record List</a></h6></div> 
                <div class="panel-body"> 
                    {{ Form::open(['url'=>'hr/performance/operation/disciplinary_form', 'class'=>'form']) }}

                        <input type="hidden" name="gaid" value="{{ Request::get('gaid') }}"> 
                        <div class="row justify-content-center">
                            <div class="col-sm-4">
                                
                                <div class="user-details-block mb-3">
                                    <div class="user-profile text-center mt-0">
                                        <img id="off_avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                    </div>
                                    <div class="text-center mt-3">
                                     <h4><b id="off_name">-------------</b></h4>
                                     <p class="mb-0" id="designation">
                                        --------------------------</p>
                                     <p class="mb-0" >
                                        Oracle ID: <span id="off_oracle_id" class="text-success">-------------</span>
                                     </p>
                                     <p class="mb-0" >
                                        Associate ID: <span id="off_associate_id" class="text-success">-------------</span>
                                     </p>
                                     <p  class="mb-0">Department: <span id="off_department" class="text-success">------------------------</span> </p>
                                     
                                     </div>
                                </div>
                                <div class="form-group has-float-label has-required select-search-group ml-4 mr-4">
                                    {{ Form::select('dis_re_offender_id', [(!empty($appeal->hr_griv_appl_offender_as_id)?$appeal->hr_griv_appl_offender_as_id:null) => (!empty($appeal->offender)?$appeal->offender:null)], (!empty($appeal->hr_griv_appl_offender_as_id)?$appeal->hr_griv_appl_offender_as_id:null), ['placeholder'=>'Select Offender\'s Name or ID', 'id'=>'dis_re_offender_id', 'class'=> 'associates  ', 'required'=>'required']) }}
                                    <label  for="dis_re_offender_id"> Offender ID </label>
                                </div>
                                
                            </div>
                            <div class="col-sm-4">
                                
                                <div class="user-details-block mb-3">
                                    <div class="user-profile text-center mt-0">
                                        <img id="gri_avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                    </div>
                                    <div class="text-center mt-3">
                                     <h4><b id="gri_name">-------------</b></h4>
                                     <p class="mb-0" id="designation">
                                        --------------------------</p>
                                     <p class="mb-0" >
                                        Oracle ID: <span id="gri_oracle_id" class="text-success">-------------</span>
                                     </p>
                                     <p class="mb-0" >
                                        Associate ID: <span id="gri_associate_id" class="text-success">-------------</span>
                                     </p>
                                     <p  class="mb-0">Department: <span id="gri_department" class="text-success">------------------------</span> </p>
                                     
                                     </div>
                                </div>
                                <div class="form-group has-float-label has-required select-search-group ml-4 mr-4">
                                    {{ Form::select('dis_re_griever_id', [(!empty($appeal->hr_griv_associate_id)?$appeal->hr_griv_associate_id:null) => (!empty($appeal->griever)?$appeal->griever:null)], (!empty($appeal->hr_griv_associate_id)?$appeal->hr_griv_associate_id:null), ['placeholder'=>'Select Associate\'s ID', 'id'=>'dis_re_griever_id', 'class'=> 'associates ']) }}  
                                    <label  for="dis_re_griever_id"> Griever ID (Optional) </label>
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
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <i class="fa fa-check bigger-110"></i> Submit
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
<script type="text/javascript">
$(document).ready(function()
{
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
@endsection