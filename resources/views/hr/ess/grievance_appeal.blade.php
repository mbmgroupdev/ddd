@extends('user.layout')
@section('title', 'User Dashboard')
@section('main-content')
@push('css')
<style type="text/css">
    .tags{
        width:100%;
    }
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="#">Ess</a>
                </li>
                <li>
                    <a href="#">Grievance</a>
                </li>
                <li class="active">Appeal</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            @include('inc/message')
            
            <div class="panel panel-info">
               <div class="panel-heading"><h6>Appeal <a href="{{ url('hr/ess/grievance/appeal_list')}}" class="pull-right btn btn-xx btn-info">Appeal list</a></h6></div> 
                 <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-offset-3 col-xs-6">
                            <!-- PAGE CONTENT BEGINS -->

                            <!-- Display Erro/Success Message -->

                            {{ Form::open(['url'=>'hr/ess/grievance/appeal', 'class'=>'form-horizontal']) }}
         
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_griv_associate_id"> Griever ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8"> 
                                        {{ Form::select('hr_griv_associate_id', [], null, ['placeholder'=>'Select Griever ID', 'id'=>'hr_griv_associate_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Griever ID field is required']) }}  
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_griv_appl_issue_id">Grievance Issue <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        {{ Form::select('hr_griv_appl_issue_id', $issueList, null, ['placeholder'=>'Select Grievance Issue', 'id'=>'hr_griv_appl_issue_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Greivance Issue field is required']) }} 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_griv_appl_step"> Grievance Step </label>
                                    <div class="col-sm-8">
                                        <textarea name="hr_griv_appl_step" id="hr_griv_appl_step" class="col-xs-12" placeholder="Grievance Step"  data-validation="length" data-validation-length="0-255" data-validation-optional="true" data-validation-allowing=" -" data-validation-error-msg="The Grievance Step has to be an alphanumeric value between 2-255 characters"></textarea>
                                    </div>
                                </div> 


                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_griv_appl_discussed_date"> Discussed Date <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="hr_griv_appl_discussed_date" id="hr_griv_appl_discussed_date" class="col-xs-12 datepicker" data-validation="required"data-validation-error-msg="The Discussed Date field is required" placeholder="Y-m-d" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_griv_appl_req_remedy"> Requested Remedy <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <textarea name="hr_griv_appl_req_remedy" id="hr_griv_appl_req_remedy" class="col-xs-12" placeholder="Requested Remedy"  data-validation="required length" data-validation-length="2-255" data-validation-allowing=" -" data-validation-error-msg="The Requested Remedy has to be an alphanumeric value between 2-255 characters"></textarea>
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_griv_appl_offender_as_id"> Offender ID(s) </label>
                                    <div class="col-sm-8">
                                        <textarea multiple="multiple" name="hr_griv_appl_offender_as_id" id="hr_griv_appl_offender_as_id" class="tagsinput col-xs-12" placeholder="Offender ID(s)"  data-validation="required length" data-validation-length="8-255" data-validation-allowing=" -" data-validation-error-msg="The Offender ID(s) has to be an alphanumeric value between 8-255 characters"></textarea>
                                    </div>
                                </div>


                            <!-- /.row --> 
                            

                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <div class="col-sm-12 responsive-hundred">
                            
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 32px;">
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
                            {{ Form::close() }}
                        <!-- /.col -->
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
        placeholder: 'Select Associate\'s ID',
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


    // Offender's List  
    var offender = $('.tagsinput');
    var path = "{{ url('hr/associate-tags') }}";
    var query = $(this).val();
    offender.tag({
        placeholder : 'Select Offender\'s Name',
        source:  function (query, process) {
            return $.get(path, { keyword: query }, function (data) {
                console.log(process(data))
                return process(data);
            });
        }
    }); 
 

});
</script>
@endsection