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
            
                <div class="row">
                    <div class="col-xs-12">
                        {{ Form::open(['url'=>'hr/performance/operation/disciplinary_form', 'class'=>'form-horizontal']) }}
                    <div class="col-xs-offset-3 col-xs-6">
                        <!-- PAGE CONTENT BEGINS -->
                        </br>

                            <input type="hidden" name="gaid" value="{{ Request::get('gaid') }}"> 
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_offender_id"> Offender ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_offender_id', [(!empty($appeal->hr_griv_appl_offender_as_id)?$appeal->hr_griv_appl_offender_as_id:null) => (!empty($appeal->offender)?$appeal->offender:null)], (!empty($appeal->hr_griv_appl_offender_as_id)?$appeal->hr_griv_appl_offender_as_id:null), ['placeholder'=>'Select Offender\'s Name or ID', 'id'=>'dis_re_offender_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Offender\'s ID field is required']) }} 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_griever_id"> Griever ID (Optional) </label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_griever_id', [(!empty($appeal->hr_griv_associate_id)?$appeal->hr_griv_associate_id:null) => (!empty($appeal->griever)?$appeal->griever:null)], (!empty($appeal->hr_griv_associate_id)?$appeal->hr_griv_associate_id:null), ['placeholder'=>'Select Associate\'s ID', 'id'=>'dis_re_griever_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-optional' => 'true']) }}  

                                </div>
                            </div> 

    						<div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_discussed_date"> Discussed Date <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" name="dis_re_discussed_date" id="dis_re_discussed_date" class="datepicker col-xs-12" data-validation="required" value="{{ (!empty($appeal->hr_griv_appl_discussed_date)?$appeal->hr_griv_appl_discussed_date:null) }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_issue_id">Reason <span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_issue_id', $issueList, (!empty($appeal->hr_griv_appl_issue_id)?$appeal->hr_griv_appl_issue_id:null), ['placeholder'=>'Select Reason', 'id'=>'dis_re_issue_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Reason field is required']) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_req_remedy"> Requested Remedy <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <textarea name="dis_re_req_remedy" id="dis_re_req_remedy" class="col-xs-12" placeholder="Requested Remedy"  data-validation="required length" data-validation-length="2-255" data-validation-allowing=" -" data-validation-error-msg="The Requested Remedy has to be an alphanumeric value between 2-255 characters">{{ (!empty($appeal->hr_griv_appl_req_remedy)?$appeal->hr_griv_appl_req_remedy:null) }}</textarea>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_ac_step_id">Action Steps <span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_ac_step_id', $stepList, null, ['placeholder'=>'Select Action Step', 'id'=>'dis_re_ac_step_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Action Step field is required']) }}
                                </div>
                            </div>

                            <div class="form-group">
    							<label class="col-sm-4 control-label no-padding-right">Date of Execution <span style="color: red; vertical-align: top;">&#42;</span></label>
    							<div class="col-sm-8">
    								<div class="col-sm-6 no-padding-left input-icon">
    									<input type="text" name="dis_re_doe_from" id="dis_re_doe_from" placeholder="From" data-validation="required" class="datepicker col-sm-12">
    								</div>

    								<div class="col-sm-6 no-padding-right no-padding-left responsive-no-margin-left input-icon input-icon-right">
    									<input type="text" name="dis_re_doe_to" id="dis_re_doe_to" placeholder="To" data-validation="required" class="datepicker col-sm-12">
    								</div>
    							</div>
    						</div>


                        <!-- PAGE CONTENT ENDS -->
                    </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-12">
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center" style=" padding-left: 38px;">
                                <button class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-sm" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div>


                    </div>
                  </form>
                </div>
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