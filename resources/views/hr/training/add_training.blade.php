@extends('hr.layout')
@section('title', 'Add Training')
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
				<li class="active">Add Training</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
                @include('inc/message')

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>Add Training<a href="{{ url('hr/training/training_list')}}" class="pull-right btn btn-xx btn-info">Training List</a></h6>
                </div>
                <div class="panel-body"> 
                    <div class="col-sm-12">  
                    {{ Form::open(['url'=>'hr/training/add_training', 'class'=>'form-horizontal']) }}
                    <div class=" col-sm-offset-3 col-sm-6 add_training">
                        <!-- PAGE CONTENT BEGINS -->
                        <!-- <h1 align="center">Add New Employee</h1> -->
                        </br> 

                        <!-- Display Erro/Success Message -->


                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="training_list"> Training List <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('tr_as_tr_id', $trainingNames, null, ['placeholder'=>'Select Training List', 'id'=>'tr_as_tr_id', 'class'=> 'col-xs-12 responsive-no-padding-right', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Training List field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group"> 
                                <label class="col-sm-3 control-label no-padding-right" for="tr_trainer_name"> Trainer Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input name="tr_trainer_name" type="text" id="tr_trainer_name" placeholder="Trainer Name" class="col-xs-12" data-validation="required length custom"  data-validation-length="3-128" data-validation-error-msg="The Trainer Name has to be an alphabet value between 3-128 characters" />
                                </div>
                            </div> 
     
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_description"> Description <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <textarea name="tr_description" id="tr_description" class="col-xs-12 " placeholder="Description"  data-validation="required length" data-validation-length="3-1024" data-validation-allowing=" -" data-validation-error-msg="The Description has to be an alphanumeric value between 3-1024 characters"></textarea>
                                </div>
                            </div>  

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="multipleDate"> Continue</label>
                                <div class="col-sm-9"> 
                                    <input id="multipleDate" class="ace ace-switch ace-switch-6" type="checkbox">
                                    <span class="lbl" style="margin:6px 0 0 0"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_start_date">Schedule Date <span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                        <div class="col-sm-6 no-padding-left input-icon">
                                        <input type="text" name="tr_start_date" id="tr_start_date" placeholder="Start Date" class="col-xs-12 datepicker" data-validation="required" data-validation-format="yyyy-mm-dd" data-validation-error-msg="The Start Date field is required" />
                                        </div>
                                        <div class="col-sm-6 no-padding-right input-icon-right hide" id="multipleDateAccept">
                                        <input type="text" name="tr_end_date" id="tr_end_date" placeholder="End Date" class="col-xs-12 datepicker" data-validation-format="yyyy-mm-dd"/> 
                                        </div>
                                </div>
                            </div> 
     
      
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_start_time">Schedule Time <span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    
                                    <div class="col-sm-6 no-padding-left ">
                                        <input type="time" name="tr_start_time" id="tr_start_time" placeholder="function(){
                                        alert($(this).val());
                                    }S;tart Time" class="col-xs-12" tr_end_time data-validation="required"  data-validation-error-msg="The Start Time field is required" /== '' || == />
                                    </div>
                                    {{-- </span>
                                    &nbsp &nbsp &nbsp &nbsp
                                    <span class="input-icon" style="width: 155px !important;"> --}}
                                    <div class="col-sm-6  no-padding-left">
                                       <input type="time" name="tr_end_time" id="tr_end_time" placeholder="End Time" class="col-xs-12" data-validation="required"  data-validation-error-msg="The End Time field is required" /> 
                                   </div>
                                    
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_status"> Status </label>
                                <div class="col-sm-9">
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('tr_status', 'Active', true, ['class'=>'ace' ,'data-validation'=>'required']) }}
                                            <span class="lbl" value="Active"> Active</span>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('tr_status', 'Inactive', false, ['class'=>'ace']) }}
                                            <span class="lbl" value="Inactive"> Inactive</span>
                                        </label>
                                    </div>
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
                    </div>   
                </div>
            </div> 
                <!-- /.col -->
        
		</div><!-- /.page-content -->
	</div>
</div> 

<script type="text/javascript">
$(document).ready(function(){
   $('select.associates').select2({
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
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    }) 
                };
          },
          cache: true
        }
    });

        var multipleDate = $("#multipleDate");
    var multipleDateAccept = $("#multipleDateAccept");
    multipleDate.on('click', function(){
        multipleDateAccept.children().val('');
        multipleDateAccept.toggleClass('hide');
    }); 

    //date validation------------------
    $('#tr_start_date').on('dp.change',function(){
        $('#tr_end_date').val( $('#tr_start_date').val());    
    });

    $('#tr_end_date').on('dp.change',function(){
        var end     = new Date($(this).val());
        var start   = new Date($('#tr_start_date').val());
        if(start == '' || start == null){
            alert("Please enter Start-Date first");
            $('#tr_end_date').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n Start-Date is latest than End-Date");
                $('#tr_end_date').val('');
            }
        }
    });
    //date validation end---------------
    //Time validation------------------------------
    // $('#tr_start_time').on('change',function(){
    //     $('#tr_end_time').val('');    
    // });

    // $('#tr_end_time').on('change',function(){
    //     var  end_time  = $(this).val();
    //     var  st_time   = $('#tr_start_time').val();
    //     if(st_time == '' || st_time == null){
    //         alert("Please enter Start-time first");
    //         $('#tr_end_time').val('');
    //     }
    //     else{
    //          if(end_time < st_time){
    //             console.log( st_time +'\n'+ end_time );
    //          //    alert("Invalid!!\n Start-time is latest than End-time");
    //          //    $('#tr_end_time').val('');
    //         }
    //     }
    // });
    //Time validation end ------------------------

});
</script>

@endsection














