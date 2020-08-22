@extends('hr.layout')
@section('title', 'Assign Training')
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
                    <a href="#">Training</a>   
                </li>
                <li class="active">Assign Training</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                    @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>Assign Training<a href="{{ url('hr/training/assign_list')}}" class="pull-right btn btn-xx btn-info">Assign List</a></h6>
                </div>
                <div class="panel-body">

                    <div class="row">
                      {{ Form::open(['url'=>'hr/training/assign_training', 'class'=>'form-horizontal']) }}
                        <div class=" col-sm-offset-3 col-sm-6">
                            <!-- PAGE CONTENT BEGINS -->
                            <!-- <h1 align="center">Add New Employee</h1> -->
                            </br> 
                            <!-- Display Erro/Success Message -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="training_list"> Training List <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8"> 
                                         {{ Form::select('tr_as_tr_id', $trainingList, null, ['placeholder'=>'Select Training List', 'id'=>'tr_as_tr_id', 'class'=> 'col-xs-12 responsive-no-padding-right', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Training List field is required']) }}   
                                    </div>
                                </div>

                                <div class="form-group"> 
                                    <label class="col-sm-3 control-label no-padding-right"  for="tr_as_ass_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('tr_as_ass_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'tr_as_ass_id', 'class'=> 'associates no-select col-xs-12 responsive-no-padding-right', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-12 responsive-hundred">
                                <div class="clearfix form-actions">
                                    <div class="col-md-offset-4 col-md-4 text-center"> 
                                        <button class="btn btn-sm btn-success" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>

                                        &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-sm" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                        </button>
                                    </div>
                                </div>

                            <!-- /.row --> 
                            <hr /> 
                            <!-- PAGE CONTENT ENDS -->
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
});
</script>

@endsection














