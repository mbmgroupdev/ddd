@extends('hr.layout')
@section('title', 'Outside Entry')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner col-sm-12">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active">Outside Entry</li>
                <li class="top-nav-btn">
                    <a class="pull-right btn btn-sm btn-primary" href="{{url('hr/operation/location_change/list')}}"><i class="fa fa-list"></i> Outside List</a>
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="panel panel-info">
            {{--  --}}
            <div class="panel-body">
                {{ Form::open(['url'=>'hr/operation/location_change/entry', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                    <div class="row"> 
                        <div class="col-sm-3">
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('employee_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'employee_id', 'class'=> 'associates no-select form-control', 'required'=>'required']) }}  
                                <label for="associate_id"> Associate's ID </label>
                            </div> 

                            
                            <div class="form-group has-float-label select-search-group">
                                <select class="col-xs-12 requested_location " id="requested_location" name="requested_location" required="required">
                                    <option value="">Select Location</option>
                                     @if($locationList)
                                        @foreach($locationList as $key =>  $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No Data</option>
                                    @endif
                                </select>
                                <label> Requested Location </label>
                            </div> 
                            <div class="form-group has-required has-float-label select-search-group">
                                <select class="col-xs-12 type" id="type" name="type" required="required">
                                    <option value="">Select Type</option>
                                    <option value="1">Full Day</option>
                                    <option value="2">1st Half</option>
                                    <option value="3">2nd Half</option>
                                </select>
                                <label for="floor_id">Type </label>
                            </div> 
                            
                            <div class="form-group has-float-label">
                                <textarea type="text" name="comment" class="form-control " ></textarea>
                                <label> Comment </label>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                            </div> 
                            

                        </div>
                        <div class="col-sm-3">  
                            <div class="form-group has-float-label">
                                <input name="previous_unit" type="text" id="unit" class="form-control" readonly>
                                <label> Unit </label>
                            </div> 
                            <div class="form-group has-float-label">
                                <input type="text" id="requested_place" name="requested_place" class="form-control" readonly placeholder="Enter outside location">
                                <label> Requested Place </label>
                            </div> 
                            <div class="form-group has-float-label">
                                <input type="date" name="from_date" id="start_date" class="datetimepicker form-control " placeholder="Start Date" required="required">
                                <label for="shift_id">From Date </label>
                            </div>
                            <div class="form-group has-float-label">
                                <input type="date" name="to_date" id="end_date"  class="datetimepicker form-control" placeholder="End Date" required="required">
                                <label for="shift_id">To Date </label>
                            </div> 
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="user-details-block" style="padding-top: 1rem;">
                                <div class="user-profile text-center mt-0">
                                    <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                </div>
                                <div class="text-center mt-3">
                                 <h4><b id="name">-------------</b></h4>
                                 <p class="mb-0" id="designation">
                                    --------------------------</p>
                                 <p class="mb-0" >
                                    Oracle ID: <span id="oracle_id" class="text-success">-------------</span>
                                 </p>
                                 <p class="mb-0" >
                                    Associate ID: <span id="associate_id_emp" class="text-success">-------------</span>
                                 </p>
                                 <p  class="mb-0">Department: <span id="department" class="text-success">------------------------</span> </p>
                                 
                                </div>
                            </div>
                        </div>  
               
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>   
</div> 
@push('js')
<script type="text/javascript">
    $(document).ready(function(){ 

        

        function handleInput(elm) {
          tmpval = elm.val();
          if (tmpval == '') {
            elm.removeClass('active')
              .siblings('label').removeClass('active');
          } else {
            elm.addClass('active')
              .siblings('label').addClass('active');
          }
        }
        //datepicker end

        $(document).on('change', '#employee_id', function(){ 
            var url = '{{url("/")}}'; 
            if( $(this).val() != ''){
                $.ajax({
                    url : "{{ url('hr/timeattendance/station_as_info') }}",
                    type: 'json',
                    method: 'get',
                    data: {associate_id: $(this).val()},
                    success: function(data)
                    {
                        $("#unit").val(data.unit);
                        $('#associate_id_emp').text(data['associate_id']);
                        $('#oracle_id').text(data['as_oracle_code']);
                        $('#name').text(data['as_name']);
                        $('#department').text(data['hr_department_name']);
                        $('#designation').text(data['hr_designation_name']);
                        
                        $('#avatar').attr('src', url+data['as_pic']); 
                    },
                    error: function()
                    {
                    }
                });
            }

        });


        $('body').on('change', '.to_date', function(){           

            var to_dt = $(this).val();
            var frm_dt = $(this).parent().prev().find('.from_date').val();
            // console.log("From: ",frm_dt, "To:",to_dt );

            if(frm_dt == '' || frm_dt == null){ 
                    alert("Please Enter From Date"); $(this).val(null);
                }
            else{

                if(frm_dt>to_dt){
                    alert("Please Enter To Date Properly (From date is greater than To Date)"); $(this).val(null);   
                }
            }
        });


     
        //on select outside location make place name mandatory
        $("body").on("change", ".requested_location", function(){
            
            if($(this).val()== "Outside"){
                $("#requested_place").prop("required", true);
                $("#requested_place").removeAttr("readonly");
            }
            else{
                $("#requested_place").removeAttr("required", true);
                $("#requested_place").prop("readonly", "readonly");
            }
        });
  
    });
</script>
@endpush
@endsection