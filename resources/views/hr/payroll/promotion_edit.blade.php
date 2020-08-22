@extends('hr.layout')
@section('title', 'Add Role')
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
					<a href="#">Payroll</a>
				</li>
				<li class="active"> Promotion Edit</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Payroll<small> <i class="ace-icon fa fa-angle-double-right"></i> Promotion Edit</small></h1>
            </div>

            <div class="row">
                    {{ Form::open(['url'=>'hr/payroll/promotion_update', 'class'=>'form-horizontal']) }}
                    @include('inc/message')
                <div class="col-xs-offset-3 col-xs-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    </br>
                    <!-- Display Erro/Success Message -->
                    <div class="output alert hide"></div> 

                        <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="associate_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-9">
                                <input type="text" name="associate_id" id="associate_id" value="{{ $promotion->associate_id }}" class="col-xs-12" readonly> 
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="previous_designation"> Previous Designation </label>
                            <div class="col-sm-9">
                                <input type="hidden" name="previous_designation_id" value="{{ $promotion->previous_designation_id }}">
                                <input type="text" name="previous_designation" id="previous_designation" value="{{ $promotion->prev_desg }}" class="col-xs-12" data-validation="required" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="current_designation_id"> Promoted Designation </label>
                            <div class="col-sm-9"> 
                                {{ Form::select('current_designation_id', $designationList, $promotion->current_designation_id, ['placeholder'=>'Select Promoted Designation', 'id'=>'current_designation_id', 'class'=> 'col-xs-12',  'data-validation'=>'required']) }}  
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="eligible_date"> Eligible Date </label>
                            <div class="col-sm-9">

                                <input type="text" name="eligible_date" id="eligible_date" class="datepicker col-xs-12" value="{{ $promotion->eligible_date }}" data-validation="required" readonly />

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="effective_date"> Effective Date </label>
                            <div class="col-sm-9">
                                <input type="text" name="effective_date" id="effective_date" class="datepicker col-xs-12 filter" value="{{ $promotion->effective_date }}" />

                            </div>
                        </div>
                </div>
                <div class="col-xs-12">
                        
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
		</div><!-- /.page-content -->
	</div>
</div> 
<script type="text/javascript">
$(document).ready(function()
{   
    $('#eligible_date').on('dp.change',function(){
        var eligible_date = '{{ $promotion->eligible_date }}';
        if(!eligible_date) {
            $('#effective_date').val('');    
        }
    });
    $('#effective_date').on('dp.change',function(){
        var end     = $(this).val();
        var start   = $('#eligible_date').val();
        if(start == '' || start == null){
            alert("Please enter Start-Date first");
            $('#effective_date').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n Start-Date is latest than End-Date");
                $('#effective_date').val('');
            }
        }
    });
    // Associate Search
    $('select.associates').select2({
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/payroll/promotion-associate-search") }}',
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

    //Associate Information 
    $("body").on('change', ".associates", function(){
        $.ajax({
            url: '{{ url("hr/payroll/promotion-associate-info") }}',
            type: 'get',
            dataType: 'json',
            data: {associate_id: $(this).val()},
            success: function(data)
            { 
                if (data.status)
                { 
                    $("select[name=current_designation_id").html("").append(data.designation);
                    $('select[name=current_designation_id').trigger('change'); 

                    $("input[name=eligible_date]").val(data.eligible_date);
                    $("input[name=previous_designation]").val(data.previous_designation);
                    $("input[name=previous_designation_id]").val(data.previous_designation_id);
                    $(".output").addClass("hide");
                }
                else
                {
                    $("input[name=eligible_date]").val(""); 
                    $("input[name=previous_designation]").val("");
                    $("input[name=previous_designation_id]").val("");
                    $(".output").removeClass("hide").addClass("alert-danger").html(data.error);
                }         
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });        
    });

});
</script>
@endsection