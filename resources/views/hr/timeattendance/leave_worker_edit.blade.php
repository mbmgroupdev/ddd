@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
    .widget-box{border-radius: 5px;}
    #toast-container>div{opacity: 0.95!important;}
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
                <li class="active"> Worker Leave Edit</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- Display Erro/Success Message -->
                    <div class="col-sm-12 widget-box widget-color-green2">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">
                                Leave Edit ( <span style="color:black; font-size: 12px; font-weight: 700;">{{$leave_data->as_name}}</span> )

                                <a href="{{url('hr/timeattendance/all_leaves')}}" class="btn btn-xs btn-info btn-round pull-right" style="margin-top: 10px;">
                                    <i class="fa fa-list bigger-100"></i>
                                </a>
                            </h5>
                        </div>
                        <div class="widget-body" >
                            <div class="widget-main" >
                            @include('inc/message')
                            {{ Form::open(['url'=>'hr/timeattendance/leave_update', 'class'=>'form-horizontal', 'files' => true]) }}
                                <input type="hidden" name="hidden_id" id="hidden_id" value="{{$leave_data->id}}">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="leave_ass_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="col-xs-12" name="leave_ass_id" id="leave_ass_id" value="{{$leave_data->leave_ass_id}}" readonly="readonly" >
                                        {{-- {{ Form::select('leave_ass_id', [], $leave_data->leave_ass_id, ['placeholder'=>'Select Associate\'s ID', 'id'=>'leave_ass_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }} --}}  

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="leave_type">Leave Type <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <select name="leave_type" id="leave_type" class="col-xs-12 no-select"  data-validation="required" data-validation-error-msg="Leave type is required" >
                                            <option value="">Select Leave Type</option>
                                            <option value="Casual" <?php if($leave_data->leave_type == 'Casual') echo "selected" ?> >Casual</option>
                                            <option value="Earned" <?php if($leave_data->leave_type == 'Earned') echo "selected" ?>>Earned</option>
                                            <option value="Sick" <?php if($leave_data->leave_type == 'Sick') echo "selected" ?>>Sick</option> 
                                            <option value="Special" <?php if($leave_data->leave_type == 'Special') echo "selected" ?>>Special</option> 
                                            <option value="Maternity" <?php if($leave_data->leave_type == 'Maternity') echo "selected" ?>>Maternity</option> 
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="multipleDate"> Multiple Date</label>
                                    <div class="col-sm-8"> 
                                        <input id="multipleDate" class="ace ace-switch ace-switch-6" type="checkbox">
                                        <span class="lbl" style="margin:6px 0 0 0"></span>
                                    </div>
                                </div> --}}
                                <div class="form-group">

                                    <label class="col-sm-4 control-label no-padding-right" for="leave_from">Leave Date <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                            <div class="col-sm-6 no-padding-left input-icon">
                                            <input type="text" name="leave_from" id="leave_from" class="col-xs-12 " data-validation="required date" placeholder="YYYY-MM-DD"  data-validation-format="yyyy-mm-dd" value="{{$leave_data->leave_from}}" />
                                            </div>
                                            <div class="col-sm-6 no-padding-right input-icon-right " id="multipleDateAccept">
                                            <input type="text" name="leave_to" id="leave_to" class="col-xs-12 " data-validation="required date" placeholder="YYYY-MM-DD"  data-validation-format="yyyy-mm-dd" value="{{$leave_data->leave_to}}" /> 
                                            </div>
                                            <label id="select_day" style="font-size:12px;width: 100%;"></label>
                                            <label style="font-size:12px;width: 100%;">Date format must be <span style="color: red">YYYY-MM-DD</span></label>

                                    </div>
                                </div>
                                <div class="form-group">

                                    <label class="col-sm-4 control-label no-padding-right" for="leave_applied_date"> Applied Date <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="leave_applied_date" id="leave_applied_date" class="col-xs-12 text-center " data-validation="required date" placeholder="YYYY-MM-DD"  data-validation-format="yyyy-mm-dd" value="{{$leave_data->leave_applied_date}}" />

                                    </div>
                                </div>

                                <div class="form-group" style="padding-top: 10px;">
                                    <label class="col-sm-4 control-label no-padding-right no-padding-top" for="leave_supporting_file">Supporting File<br> <span>(pdf|doc|docx|jpg|jpeg|png)</span> </label>
                                    <div class="col-sm-8">
                                        <input type="file" name="leave_supporting_file" id="leave_supporting_file" data-validation="mime size" data-validation-allowing="docx,doc,pdf,jpeg,png,jpg" data-validation-max-size="1M"
                                        data-validation-error-msg-size="You can not upload file larger than 1MB" data-validation-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file" value="{{$leave_data->leave_supporting_file}}">
                                        <span id="file_upload_error" class="red" style="display: none; font-size: 14px;">Only <strong>docx, doc, pdf, jpeg, jpg or png</strong> file supported(<1 MB).</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="leave_comment"> Note </label>
                                    <div class="col-sm-8">
                                        <textarea name="leave_comment" id="leave_comment" class="col-xs-12" placeholder="Description"  data-validation="length" data-validation-length="0-1024" data-validation-allowing=" -" data-validation-error-msg="The Description has to be an alphanumeric value between 2-1024 characters" value="{{$leave_data->leave_comment}}">{{$leave_data->leave_comment}}</textarea>
                                    </div>
                                </div>

                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="clearfix form-actions">
                                    <div class=" text-center" style="padding-left: 53px;">
                                        <button class="btn btn-sm btn-success" type="submit" id="leave_entry">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>

                                        &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-sm" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                        </button>
                                    </div>
                                </div>


                            {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
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

  

    // Select Multiple Dates
    var multipleDate = $("#multipleDate");
    var multipleDateAccept = $("#multipleDateAccept");
    multipleDate.on('click', function(){
        multipleDateAccept.children().val('');
        multipleDateAccept.toggleClass('hide');
    });

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
                    $('#leave_to').val('');
                    $('#leave_from').val('');

                },
                error: function(xhr)
                {
                    alert('failed...');
                }
            });
        }
        $('#select_day').html('');
        $('#leave_entry').attr("disabled",true);
    });

//Dates entry alerts....

    var start_form = $('#leave_from').val();
    var start_to = $('#leave_to').val();

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
                    $('#leave_to').val(formval);
                    toastr.options.progressBar = true ;
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('From date is later than To date'); 

                    /*$('#select_day').html('You have selected  <span style="color: #ff0909;font-weight:600;">1</span> day.');*/
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
                                to_date: $('#leave_to').val(),
                                leave_id : '{{$leave_data->id}}'
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
//Dates entry alerts end...

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
@endsection