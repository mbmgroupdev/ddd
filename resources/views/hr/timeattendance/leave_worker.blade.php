@extends('hr.layout')
@section('title', 'Employees Leave')
@section('main-content')
@push('css')
<style type="text/css">
    .widget-box{border-radius: 5px;}
    #toast-container>div{opacity: 0.95!important;}
    .history-title{
        box-shadow: 0px 4px 10px 5px #ece7e7;
        padding: 5px;
    }
</style>
<link href="{{ asset('assets/css/sweetalert.min.css')}}" rel="stylesheet"/>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Time & Attendance </a>
                </li>
                <li class="active"> Employee's Leave </li>
                <li class="top-nav-btn">
                    <a href="{{ url('hr/timeattendance/all_leaves') }}" class="btn btn-sm btn-primary pull-right" rel='tooltip' data-tooltip-location='left' data-tooltip='Leave List'>
                            List <i class="fa fa-list"> </i>
                        </a>
                </li>
            </ul>
        </div>
        <div class="page-content"> 
            <div class="panel panel-success">
                <div class="panel-body">
                    <div class="row">
                        @include('inc/message')
                        <div class="col-sm-5" style="padding-top: 10px;border-right: 1px solid #d1d1d1;">
                            {{ Form::open(['url'=>'hr/timeattendance/leave_worker', 'class'=>'form-horizontal needs-validation', 'files' => true, 'novalidate']) }}
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('leave_ass_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'leave_ass_id', 'class'=> 'associates form-control', 'required'=>'required']) }}  
                                    <label for="leave_ass_id"> Associate's ID </label>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <div class="form-group has-required has-float-label select-search-group">
                                            <select name="leave_type" id="leave_type" class="form-control"  required="required" >
                                                <option value="">Select Leave Type</option>
                                                <option value="Casual">Casual</option>
                                                <option value="Earned">Earned</option>
                                                <option value="Sick">Sick</option> 
                                                <option value="Special">Special</option> 
                                            </select>
                                            <label for="leave_type">Leave Type</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group has-required has-float-label">
                                            <input type="date" name="leave_applied_date" id="leave_applied_date" class="form-control" required placeholder="YYYY-MM-DD"   value="{{date('Y-m-d')}}"/>
                                            <label  for="leave_applied_date"> Applied Date  </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        
                                        <div class="form-group has-required has-float-label mb-0">
                                            <input type="date" name="leave_from" id="leave_from" class="form-control" required />
                                            <label  for="leave_from">Leave From </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div id="multipleDateAccept" class="form-group has-required has-float-label mb-0">
                                            <input type="date"  name="leave_to" id="leave_to" class="form-control" required />
                                            <label  for="leave_from">Leave From </label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-12">
                                        <p id="select_day" class="text-success"></p>
                                        <p id="error_leave_text" class="text-danger"></p>
                                    </div>
                                </div>
                                
                                <div class="form-group  file-zone mb-0">
                                    <label  for="file"> Supporting File </label>
                                    <input type="file" name="leave_supporting_file" class="file-type-validation" data-file-allow='["docx","doc","pdf","jpeg","png","jpg"]' autocomplete="off" />
                                    <div class="invalid-feedback" role="alert">
                                        <strong>Select a file</strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <span id="file_upload_error" class="text-danger" style="; font-size: 12px;">Only <strong>docx, doc, pdf, jpeg, jpg or png</strong> file supported(<1 MB).</span>
                                    
                                </div>
                                
                                <div class="form-group has-float-label">
                                    <label for="leave_comment"> Note </label>
                                    <textarea name="leave_comment" id="leave_comment" class="form-control" placeholder="Description"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn  btn-primary" type="submit" id="leave_entry" disabled="disabled">
                                        <i class="fa fa-check bigger-110"></i> Submit
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn " type="reset">
                                        <i class="fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            {{ Form::close() }}
                        </div>
                        <div class="col-sm-7 pt-3">
                            <div class="row" id="associates_leave">
                                <div class="col-sm-5">
                                    <div class="user-details-block benefit-employee">
                                        <div class="user-profile text-center mt-0">
                                            <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                        </div>
                                        <div class="text-center mt-3">
                                            <h4><b id="user-name">Selected User</b></h4>
                                            <p class="mb-0" id="designation">
                                                Associate ID: ----------</p>
                                            <p class="mb-0" id="designation">
                                                Oracle ID: ----------</p>
                                             
                                          </div>
                                    </div>
                                </div>
                                        
                                
                                <div class="col-sm-7">
                                    <ul class="speciality-list m-0 p-0">
                                        <li class="d-flex mb-4 align-items-center">
                                           <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-calendar-day"></i></a></div>
                                           <div class="media-support-info ml-3">
                                              <h6>Casual Leave</h6>
                                              <p class="mb-0">Total:  <span class="text-danger" id="total_earn_leave">10</span class="text-danger"> Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">0</span > Remained: <span class="text-success" id="remained_earn_leave">0</span></p>
                                           </div>
                                        </li>
                                        <li class="d-flex mb-4 align-items-center">
                                           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-stethoscope"></i></a></div>
                                           <div class="media-support-info ml-3">
                                              <h6>Sick Leave</h6>
                                              <p class="mb-0">Total:  <span class="text-danger" id="total_earn_leave">14</span class="text-danger"> Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">0</span > Remained: <span class="text-success" id="remained_earn_leave">0</span></p>
                                           </div>
                                        </li>
                                        
                                        <li class="d-flex mb-4 align-items-center">
                                           <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-dollar-sign"></i></a></div>
                                           <div class="media-support-info ml-3">
                                              <h6>Earned Leave</h6>
                                              <p class="mb-0">Total:  <span class="text-danger" id="total_earn_leave">0</span class="text-danger"> Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">0</span > Remained: <span class="text-success" id="remained_earn_leave">0</span></p>
                                           </div>
                                        </li>
                                        <li class="d-flex mb-4 align-items-center">
                                           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-gift "></i></a></div>
                                           <div class="media-support-info ml-3">
                                              <h6>Special Leave</h6>
                                              <p class="mb-0">---</p>
                                           </div>
                                        </li>
                                     </ul>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
<script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function()
{
    

    $("#leave_type").on("change", function(e){
        if(!($("#leave_ass_id").val())){
            $.notify('Please Select Associates!','error');
            $(this).val(0);
            $("#leave_type").val('');
        }
        else{
            $('.app-loader').show();
            $.ajax({
                url: '{{ url("hr/ess/leave_check") }}',
                type: 'post',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    associate_id: $("#leave_ass_id").val(), 
                    leave_type: $(this).val()
                },
                success: function(data)
                {
                    $('.app-loader').hide();
                    if(data.stat == 'false'){
                        $("#leave_type").val('');
                        $.notify(data.msg,'error');
                    }
                },
                error: function(xhr)
                {
                    $.notify('failed...');
                }
            });
        }
        $('#leave_to').val('');
        $('#leave_from').val('');
        $('#select_day').html('');
        $('#error_leave_text').html('');
        $('#leave_entry').attr("disabled",true);
    }); 

    $("#leave_ass_id").on("change", function(e){
        if(($("#leave_ass_id").val())){
            $('.app-loader').show();
            $.ajax({
                url: '{{ url("hr/ess/associates_leave") }}',
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    associate_id: $("#leave_ass_id").val()
                },
                success: function(res)
                {
                    $('#associates_leave').html(res);
                    $("#leave_type").val('');
                    $('#leave_to').val('');
                    $('#leave_from').val('');
                    $('#select_day').html('');
                    $('#error_leave_text').html('');
                    $('#leave_entry').attr("disabled",true);
                    $('.app-loader').hide();
                },
                error: function(xhr)
                {
                    
                }
            });
        }
    }); 


    $(document).on('change','#leave_from',function(){
        var leave_from = $('#leave_from').val();
        $('#leave_to').val(leave_from).attr('min',leave_from);
    });

    $(document).on('change','#leave_from,#leave_to', function(){
        var formval = $('#leave_from').val();
        var lv_to_date = $('#leave_to').val();
        var associate_id = $("#leave_ass_id").val();
        var l_type = $('#leave_type').val();
        if(associate_id && l_type){
            const from_date = new Date(formval);
            const to_date   = new Date(lv_to_date); 
            if(from_date > to_date){
                $(this).val(formval);
                $.notify('From date is later than To date'); 
            }
            const from = new Date($('#leave_from').val());
            const to   = new Date($('#leave_to').val());
            const diffTime = Math.abs(to.getTime() - from.getTime());
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if(isNaN(diffDays)){
                $('#select_day').html('');
                $('#error_leave_text').html('');
            }else{
                $('#select_day').html('You have selected  <span style="color: #ff0909;font-weight:600;">'+(diffDays+1)+'</span> day(s).');
                $.ajax({
                    url: '{{ url("hr/ess/leave_length_check") }}',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        associate_id: associate_id, 
                        leave_type: l_type,
                        sel_days: diffDays+1,
                        from_date: $('#leave_from').val(),
                        to_date: $('#leave_to').val()
                    },
                    success: function(data)
                    {
                        if(data.stat == 'false'){
                            $.notify(data.msg, 'error');
                            $('#error_leave_text').html('<span style="color:#da0000;">'+data.msg+'</div>');
                            $('#leave_entry').attr("disabled",true);
                        }else{
                            $('#leave_entry').attr("disabled",false);
                            $.ajax({
                                url: '{{ url("hr/ess/attendance_check") }}',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    associate_id: associate_id,
                                    from_date: $('#leave_from').val(),
                                    to_date: $('#leave_to').val()
                                },
                                success: function(data)
                                {
                                    if(data.stat == false){
                                        swal(data.msg+'. Do you want to delete attendance?', {
                                            buttons: {
                                                cancel: "Cancel",
                                                catch: {
                                                    text: "OK",
                                                    value: "catch",
                                                },
                                            },
                                        })
                                        .then((value) => {
                                            switch (value) {
                                                case "catch":
                                                    break;
                                                default:
                                                    $('#select_day').html('');
                                                    $('#error_leave_text').html('');
                                                    $('#leave_from').val('');
                                                    $('#leave_to').val('');
                                                    $('#leave_entry').attr("disabled",true);
                                            }
                                        });
                                    }
                                },
                                error: function(xhr)
                                {
                                }
                            });
                        }
                    },
                    error: function(xhr)
                    {
                        
                    }
                });

                            
            }
        }else{
            $('#select_day').html('');
            $('#error_leave_text').html('');
            $('#leave_to').val('');
            $('#leave_from').val('');
            $.notify('Please select associates or leave type!','error');
        }
    });


});
</script>
@endpush
@endsection