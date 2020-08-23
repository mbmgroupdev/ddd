@extends('hr.layout')
@section('title', 'Promotion')
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
				<li class="active"> Promotion</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            
                        @include('inc/message')
                        <div class="output alert hide"></div>
            @can('Manage Promotion') 
            <div class="panel panel-info">
                <div class="panel panel-heading"><h6>Promotion</h6></div>
                <div class="row" style="padding: 15px; ">
                        <!-- PAGE CONTENT BEGINS -->
                         
                        {{ Form::open(['url'=>'hr/payroll/promotion', 'class'=>'form-horizontal']) }}
                    <div class="col-sm-5">
     
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="previous_designation"> Previous Designation </label>
                                <div class="col-sm-8">
                                    <input type="hidden" name="previous_designation_id">
                                    <input type="text" name="previous_designation" id="previous_designation" placeholder="No Previous Designation Found" class="col-xs-12" data-validation="required" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="current_designation_id"> Promoted Designation </label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('current_designation_id', $designationList, null, ['placeholder'=>'Select Promoted Designation', 'id'=>'current_designation_id', 'class'=> 'col-xs-12',  'data-validation'=>'required']) }}  
                                </div>
                            </div>
                    </div>
                    <div class="col-sm-2"></div>
                    <div class="col-sm-5">

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="eligible_date"> Eligible Date </label>
                                <div class="col-sm-8">
                                    <input type="text" name="eligible_date" palceholder="Y-m-d" id="eligible_date" class="datepicker col-xs-12" data-validation="required" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="effective_date"> Effective Date </label>
                                <div class="col-sm-8">
                                    <input type="text" name="effective_date" id="effective_date" class="datepicker col-xs-12 filter" value="" />
                                </div>
                            </div>
     
                            
                        <!-- PAGE CONTENT ENDS -->
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
                            </div>
                            <!-- /.row --> 
                          
                        {{ Form::close() }}
            </div>   
            </div> 
            @endcan        
            <!-- /.col -->
            <div class="responsive-hundred">
               <div class="widget-box widget-color-blue">
                <table id="dataTables" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Prev. Desg.</th>
                                <th>Curr. Desg</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promotionList AS $promotion)
                            <tr>
                                <td>{{ $promotion->associate_id }}</td>
                                <td>{{ $promotion->as_name }}</td>
                                <td>{{ $promotion->previous_desg }}</td>
                                <td>{{ $promotion->current_desg }}</td>
                                <td>
                                <div class="btn-group">
                                    <a type="button" href="{{ url('hr/payroll/promotion_edit/'.$promotion->id) }}" class='btn btn-xs btn-primary'><i class="fa fa-pencil"></i></a>
                                </div>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
          
          
		</div><!-- /.page-content -->
	</div>
</div> 
<script type="text/javascript">
$(document).ready(function()
{  
    $('#eligible_date').on('dp.change',function(){
        $('#effective_date').val($('#eligible_date').val());    
    });
    $('#effective_date').on('dp.change',function(){
        var end     = new Date($(this).val());
        var start   = new Date($('#eligible_date').val());
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
    $('#dataTables').DataTable({
            pagingType: "full_numbers" ,
    });
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
    // Associate Search
    $('select.associates').select2({
        templateSelection:formatState,
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