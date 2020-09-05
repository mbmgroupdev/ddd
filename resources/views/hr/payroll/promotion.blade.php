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
            @can('Manage Promotion') 
            <div class="panel">
                <div class="panel-heading">
                    <h6>Promotion
                        {{-- <a href="" class="btn btn-primary pull-right">Promotion List</a> --}}
                    </h6>
                </div>
                         
                {{ Form::open(['url'=>'hr/payroll/promotion', 'class'=>'form-horizontal p-3']) }}
                    <div class="row justify-content-center">
                        
                        <div class="col-4">
         
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'img-associates']) }}
                                <label  for="associate_id"> Associate's ID </label>
                            </div>

                            <div class="form-group has-float-label has-required ">
                                <input type="hidden" name="previous_designation_id">
                                <input type="text" name="previous_designation" id="previous_designation" placeholder="No Previous Designation Found"  readonly  class="form-control" />
                                <label  for="previous_designation"> Previous Designation </label>
                            </div>

                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('current_designation_id', $designationList, null, ['placeholder'=>'Select Promoted Designation', 'id'=>'current_designation_id']) }}  
                                <label for="current_designation_id"> Promoted Designation </label>
                            </div>

                            <div class="form-group has-float-label  has-required">
                                <input type="date" name="eligible_date" palceholder="Y-m-d" id="eligible_date" class="form-control "  readonly />
                                <label  for="eligible_date"> Eligible Date </label>
                            </div>

                            <div class="form-group has-float-label has-required">
                                <input type="date" name="effective_date" id="effective_date" class=" form-control filter" value="" />
                                <label  for="effective_date"> Effective Date </label>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-check"></i> Save
                                </button>
                            </div>
     
                        </div>
                        <div class="col-4 benefit-employee">
                            <div class="user-details-block">
                                  <div class="user-profile text-center">
                                        <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                  </div>
                                  <div class="text-center mt-3">
                                     <h4><b id="user-name">Selected User</b></h4>
                                     <p class="mb-0" id="designation">
                                        Employee designation</p>
                                     
                                  </div>
                               </div>
                        </div>
                    </div>
                          
                {{ Form::close() }}
            </div>
            @endcan        
            <!-- /.col -->
            {{-- <div class="responsive-hundred">
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
            </div> --}}
          
          
		</div><!-- /.page-content -->
	</div>
</div> 
@push('js')
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
    

    //Associate Information 
    $("body").on('change', ".img-associates", function(){
        $.ajax({
            url: '{{ url("hr/payroll/promotion-associate-info") }}',
            type: 'get',
            dataType: 'json',
            data: {associate_id: $(this).val()},
            success: function(data)
            { 
                console.log(data);
                if (data.status)
                { 
                    $('#avatar').attr('src',data.as_pic);
                    $('#user-name').text(data.as_name);
                    $('#designation').text(data.previous_designation);

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
@endpush
@endsection