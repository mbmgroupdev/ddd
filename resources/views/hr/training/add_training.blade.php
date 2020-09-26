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
                    <h6>Add Training<a href="{{ url('hr/training/training_list')}}" class="pull-right btn btn-primary">Training List</a></h6>
                </div>
                <div class="panel-body"> 
                    {{ Form::open(['url'=>'hr/training/add_training', 'class'=>'form-horizontal']) }}
                    <div class="row justify-content-center">
                        
                        <div class="col-sm-4 add_training">

                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('tr_as_tr_id', $trainingNames, null, ['placeholder'=>'Select Training List', 'id'=>'tr_as_tr_id', 'class'=> 'form-control', 'required'=>'required']) }}  
                                <label for="training_list"> Training List  </label>
                            </div>

                            <div class="form-group has-required has-float-label"> 
                                <input name="tr_trainer_name" type="text" id="tr_trainer_name" placeholder="Trainer Name" class="form-control" required="required"/>
                                <label for="tr_trainer_name"> Trainer Name  </label>
                            </div> 
     
                            <div class="form-group has-required has-float-label">
                                <textarea name="tr_description" id="tr_description" class="form-control" placeholder="Description"  required="required"></textarea>
                                <label for="tr_description"> Description  </label>
                            </div> 
                            <div class="form-group">
                                <label for="tr_status"> Status </label>
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="multipleDate">
                                    <label class="custom-control-label" for="multipleDate" data-on-label="On" datoff-label="Off">Multiple Date</label>
                               </div>

                           </div>

                            <div class="form-group has-required has-float-label">
                                <input type="date" name="tr_start_date" id="tr_start_date" placeholder="Start Date" class="form-control" required="required"  />
                                <label for="tr_start_date">Start Date </label>
                            </div>
                            <div id="multi-date" class="form-group has-required has-float-label hide" >
                                <label for="tr_end_date">End Date </label>
                                <input type="date" name="tr_end_date" id="tr_end_date" placeholder="End Date" class="form-control" />
                            </div> 
     
      
                            <div class="form-group has-float-label">
                                <label for="tr_start_time">Start Time</label>
                                    <input type="time" name="tr_start_time" id="tr_start_time"  class="form-control" />
                            </div>
                            <div class="form-group has-float-label">
                                <label for="tr_start_time">End Time</label>
                                <input type="time" name="tr_end_time" id="tr_end_time" placeholder="End Time" class="form-control" required="required"  />
                            </div> 
                            <div class="form-group">
                                <button class="btn btn-primary pull-right" type="submit">
                                    <i class="fa fa-check "></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div> 
        
		</div><!-- /.page-content -->
	</div>
</div> 

@push('js')
<script type="text/javascript">
    $(document).on('click',"#multipleDate" ,function(){
        if($("#multi-date").hasClass('hide')){
            $("#multi-date").removeClass('hide');
        }else{
            $("#multi-date").addClass('hide');
        }
        $("#multi-date").children().val('');
    }); 
$(document).ready(function(){
    

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
@endpush
@endsection














