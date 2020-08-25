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
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content"> 
            <div class="panel panel-success">
                <div class="panel-heading page-headline-bar">
                    <h6>
                        Leave Entry
                        <a href="{{ url('hr/timeattendance/all_leaves') }}" class="btn btn-primary pull-right" rel='tooltip' data-tooltip-location='left' data-tooltip='Leave List'>
                            <i class="fa fa-list"> Leave List</i>
                        </a>
                    </h6>
                 </div> 
                <div class="panel-body">
                    <div class="row">
                        @include('inc/message')
                        <div class="col-6" style="padding-top: 20px;border-right: 1px solid #d1d1d1;">
                            {{ Form::open(['url'=>'hr/timeattendance/leave_worker', 'class'=>'form-horizontal', 'files' => true]) }}
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('leave_ass_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'leave_ass_id', 'class'=> 'associates form-control', 'required'=>'required']) }}  
                                    <label for="leave_ass_id"> Associate's ID </label>
                                </div>
                                <div class="form-group has-required has-float-label select-search-group">
                                    <select name="leave_type" id="leave_type" class="form-control"  required="required" >
                                        <option value="">Select Leave Type</option>
                                        <option value="Casual">Casual</option>
                                        <option value="Earned">Earned</option>
                                        <option value="Sick">Sick</option> 
                                        <option value="Maternity">Maternity</option>
                                        <option value="Special">Special</option> 
                                    </select>
                                    <label for="leave_type">Leave Type</label>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        
                                        <div class="form-group has-required has-float-label mb-0">
                                            <input type="text" name="leave_from" id="leave_from" class="form-control" required />
                                            <label  for="leave_from">Leave From </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div id="multipleDateAccept" class="form-group has-required has-float-label mb-0">
                                            <input type="text" name="leave_to" id="leave_to" class="form-control" required />
                                            <label  for="leave_from">Leave From </label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-12">
                                        <p id="select_day" style="font-size:12px;width: 100%;"></p>
                                        <p style="font-size:12px;width: 100%;">Date format must be <span style="color: red">YYYY-MM-DD</span></p>
                                    </div>
                                </div>
                                <div class="form-group has-required has-float-label">
                                    <label  for="leave_applied_date"> Applied Date  </label>
                                    <input type="date" name="leave_applied_date" id="leave_applied_date" class="form-control" required placeholder="YYYY-MM-DD"   value="{{date('Y-m-d')}}"/>
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
                        <div class="col-6">
                            <div class="center" id="associate-leave">
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
                                <h5 class="center">Leave log {{date('Y')}}</h5>
                                <table class="table table-bordered table-stripped" >
                                    <thead>
                                    <tr>
                                        <th >Leave Type</th>
                                        <th >Total</th>
                                        <th >Taken</th>
                                        <th >Due</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th >Casual</th>
                                        <td >10</td>
                                        <td >0</td>
                                        <td >10</td>
                                    </tr>
                                    <tr>
                                        <th >Earned</th>
                                        <td >0</td>
                                        <td >0</td>
                                        <td >0</td>
                                    </tr>
                                    <tr>
                                        <th >Sick</th>
                                        <td >14</td>
                                        <td >0</td>
                                        <td >14</td>
                                    </tr>
                                    <tr>
                                        <th >Special</th>
                                        <td > - </td>
                                        <td >0</td>
                                        <td > - </td>
                                    </tr>
                                    <tr>
                                        <th >Maternity</th>
                                        <td >112</td>
                                        <td >0</td>
                                        <td >112</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #efefef;"> 
                                            <th >Subtotal</th>
                                            <td >136</td>
                                            <td >0</td>
                                            <td >136</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
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
                        var oCode = '';
                        if(item.as_oracle_code !== null){
                            oCode = item.as_oracle_code + ' - ';
                        }
                        return {
                            text: oCode + item.associate_name,
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    })
                };
          },
          cache: true
        }
    }); 
    // Select Multiple Dates
    /*var multipleDate = $("#multipleDate");
    var multipleDateAccept = $("#multipleDateAccept");
    multipleDate.on('click', function(){
        //multipleDateAccept.children().val('');
        multipleDateAccept.toggleClass('hide');
    });*/

    $("#leave_type").on("change", function(e){
        if(!($("#leave_ass_id").val())){
            toastr.options.progressBar = true ;
            toastr.options.positionClass = 'toast-top-center';
            toastr.error('Please Select Associates!');
            $(this).val(0);
            $("#leave_type").val('');
        }
        else{
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
                    if(data.stat == 'false'){
                        var leave = $("#leave_type").val();
                        $("#leave_type").val('');
                        toastr.options.progressBar = true ;
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('This Employee is not allowed to take '+leave+' Leave');
                    }
                },
                error: function(xhr)
                {
                    alert('failed...');
                }
            });
        }
        $('#leave_to').val('');
        $('#leave_from').val('');
        $('#select_day').html('');
        $('#leave_entry').attr("disabled",true);
    }); 
    $("#leave_ass_id").on("change", function(e){
        if(($("#leave_ass_id").val())){
            $.ajax({
                url: '{{ url("hr/ess/associates_leave") }}',
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    associate_id: $("#leave_ass_id").val()
                },
                success: function(res)
                {
                    console.log(res);
                    $('#associate-leave').html(res);
                    $("#leave_type").val('');
                    $('#leave_to').val('');
                    $('#leave_from').val('');
                    $('#select_day').html('');
                    $('#leave_entry').attr("disabled",true);
                    //console.log(res);
                },
                error: function(xhr)
                {
                    console.log(xhr);
                }
            });
        }
    }); 


    $('#leave_from').on('keyup',function(){
        var leave_from = $('#leave_from').val();
        //validate date format using regex
        if(/[12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])/.test(leave_from)){
            $('#leave_to').val(leave_from);
            $('#leave_applied_date').val(leave_from);
        }else{
            $('#leave_to').val('');
            $('#leave_applied_date').val('{{date("Y-m-d")}}');
        }
    });
    $(document).on('keyup','#leave_from,#leave_to', function(){
        var formval = $('#leave_from').val();
        var lv_to_date = $('#leave_to').val();
        var associate_id = $("#leave_ass_id").val();
        var l_type = $('#leave_type').val();
        if(associate_id && l_type){
            //validate date format
            if((/[12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])/).test(formval) && (/[12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])/).test(lv_to_date) && formval && lv_to_date){
                const from_date = new Date(formval);
                const to_date   = new Date(lv_to_date); 
                if(from_date > to_date){
                    $(this).val(formval);
                    toastr.options.progressBar = true ;
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('From date is later than To date'); 
                }
                    const from = new Date($('#leave_from').val());
                    const to   = new Date($('#leave_to').val());
                    const diffTime = Math.abs(to.getTime() - from.getTime());
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if(isNaN(diffDays)){
                        $('#select_day').html('');
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
                                sel_days: diffDays,
                                from_date: $('#leave_from').val(),
                                to_date: $('#leave_to').val()
                            },
                            success: function(data)
                            {
                                if(data.stat == 'false'){
                                    $('#select_day').html('<span style="color:#da0000;">'+data.msg+'</div>');
                                    $('#leave_entry').attr("disabled",true);
                                }else{
                                    $('#leave_entry').attr("disabled",false);
                                }
                            },
                            error: function(xhr)
                            {
                                console.log(xhr);
                            }
                        });
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
                                                $('#leave_from').val('');
                                                $('#leave_to').val('');
                                                $('#leave_entry').attr("disabled",true);
                                        }
                                    });
                                }
                                //console.log(data);
                            },
                            error: function(xhr)
                            {
                                alert('failed...');
                            }
                        });
                    }
            }else{
                $('#select_day').html('');
            }
        }else{
            $('#select_day').html('');
            $('#leave_to').val('');
            $('#leave_from').val('');
            toastr.options.progressBar = true ;
            toastr.options.positionClass = 'toast-top-center';
            toastr.error('Please select associates and leave type!');
        }
    });

   //file upload validation
    $("#leave_supporting_file").change(function () {
        var fileExtension = ['pdf','doc','docx','jpg','jpeg','png'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error').show();
            $(this).val('');
        }
        else{
                $('#file_upload_error').hide();
            }
    });
});
</script>
@endpush
@endsection