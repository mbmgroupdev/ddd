@extends('hr.layout')
@section('title', 'Promotion Edit')
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
                <li class="top-nav-btn"><a href="{{url('hr/payroll/promotion-list')}}" class="btn btn-primary pull-right">Promotion List</a></li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content">
            @can('Manage Promotion') 
            <div class="panel @if($isLatest == 0) p-3 m-3 @endif">
                @if($isLatest == 1)
                {{ Form::open(['url'=>'hr/payroll/promotion_update', 'class'=>'form-horizontal p-3 m-3']) }}
                @endif
                    <div class="row justify-content-center">
                        
                        <div class="col-sm-3">
                            <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                            <div class="form-group has-float-label has-required ">
                                <input type="text" name="associate_id" id="associate_id" value="{{ $promotion->associate_id }}" class="form-control" readonly>
                                <label  for="associate_id"> Associate's ID </label>
                            </div>

                            <div class="form-group has-float-label has-required ">
                                <input type="hidden" name="previous_designation_id" value="{{ $promotion->previous_designation_id }}">
                                <input type="text" name="previous_designation" id="previous_designation" placeholder="No Previous Designation Found"  readonly  class="form-control" value="{{ $designation[$promotion->previous_designation_id]['hr_designation_name'] }}"/>
                                <label for="previous_designation"> Previous Designation </label>
                            </div>
                            @php
                                if($isLatest == 1){
                                    $disabled = '';
                                }
                                else{
                                    $disabled = 'disabled';
                                }

                            @endphp
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('current_designation_id', $designationList, $promotion->current_designation_id , ['placeholder'=>'Select Promoted Designation', 'id'=>'current_designation_id', $disabled]) }}  
                                <label for="current_designation_id"> Promoted Designation </label>
                            </div>


                            <div class="form-group has-float-label has-required">
                                <input type="date" name="effective_date" id="effective_date" class=" form-control filter" value="{{ $promotion->effective_date }}" {{$disabled}}/>
                                <label  for="effective_date"> Effective Date </label>
                            </div>
                            @if($isLatest == 1)
                            <div class="form-group text-center mt-3">
                                <button class="btn btn-primary " type="submit">
                                    <i class="fa fa-check"></i> Save
                                </button>
                            </div>
                            @endif
     
                        </div>
                        <div class="col-sm-4 benefit-employee">
                            <div class="user-details-block">
                                  <div class="user-profile text-center">
                                        <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($promotion)  }}">
                                  </div>
                                  <div class="text-center mt-3">
                                     <h4><b id="user-name">{{$promotion->as_name}}</b></h4>
                                     <p class="mb-0" > <span id="designation">
                                        {{$designation[$promotion->as_designation_id]['hr_designation_name']}}</span>, <span id="section">{{$promotion->hr_section_name}}</p>
                                     
                                  </div>
                               </div>
                            
                        </div>
                        <div class="col-sm-4 benefit-employee border-left">
                            <strong>Prmotion History</strong><hr>
                            <div class="promotion-history">
                                {!!$historyview!!}
                            </div>
                        </div>
                        
                    </div>
                @if($isLatest == 1)        
                {{ Form::close() }}
                @endif
            </div>
            @endcan      
          
          
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