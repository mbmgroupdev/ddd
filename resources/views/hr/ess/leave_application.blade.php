@extends('user.layout')
@section('title', 'User Dashboard')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<a href="#"> ESS </a>
				</li>
				<li class="active"> Leave Application</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            @include('inc/message')
            <div class="panel panel-success">
              <div class="panel-heading"><h6>Leave Application</h6></div> 
                <div class="panel-body">

                    <div class="row">
                        
            
                        <div class="col-sm-5" style="padding-top: 20px;border-right: 1px solid #d1d1d1;">
                            {{ Form::open(['url'=>'hr/ess/leave_application', 'class'=>'form-horizontal pad', 'files' => true]) }}
                                <!-- PAGE CONTENT BEGINS -->
                               
                            <div class="form-group has-required has-float-label select-search-group">
                                
                                <select name="leave_type" id="leave_type" class="col-xs-12 no-select"  data-validation="required" data-validation-error-msg="Leave type is required" >
                                    <option value="">Select Leave Type</option>
                                    <option value="Casual">Casual</option>
                                    <option value="Earned">Earned</option>
                                    <option value="Sick">Sick</option> 
                                    <option value="Maternity">Maternity</option>
                                </select>
                                <label  for="leave_type">Leave Type</label>
                            </div>

                            <!-- <div class="form-group">
                                <label  for="multipleDate"> Multiple Date</label>
                                <div class="col-sm-7"> 
                                    <input id="multipleDate" class="ace ace-switch ace-switch-6" type="checkbox">
                                    <span class="lbl" style="margin:6px 0 0 0"></span>
                                </div>
                            </div> -->
     
                            <div class="form-group">
                                <label  for="leave_from">Leave Date </label>
                                <div class="row">
                                    
                                    <div class="col-sm-6 input-icon no-padding-left">
                                        <input type="text" name="leave_from" id="leave_from" class="datepicker form-control" placeholder="Y-m-d" data-validation="required" data-validation-error-msg="The Start Date field is required" autocomplete="off"/>
                                    </div> 
                                    <div class="col-sm-6 input-icon input-icon-right  no-padding-right" id="multipleDateAccept">
                                        <input type="text" placeholder="Y-m-d" name="leave_to" id="leave_to" class=" datepicker form-control" data-validation="required" data-validation-error-msg="The End Date field is required" autocomplete="off"/> 
                                    </div> 
                                    <label id="select_day" style="font-size:12px;width: 100%;"></label>
                                </div>
                            </div>

                            <input type="hidden" name="leave_applied_date" id="leave_applied_date" value="<?php echo date('Y-m-d'); ?>" class="col-xs-10 col-sm-5 " data-validation="required"/>

                            <div class="form-group" >
                                <label  for="leave_supporting_file">Supporting File <br> <span>(pdf|doc|docx|jpg|jpeg|png)</span> </label><br>
                                    <input type="file" name="leave_supporting_file" id="leave_supporting_file" data-validation="mime size" data-validation-allowing="docx,doc,pdf,jpeg,png,jpg" data-validation-max-size="1M"
                                    data-validation-error-msg-size="You can not upload file larger than 1MB">
                                    <span id="file_upload_error" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png type</strong> file supported(<1 MB).</span>
                            </div>      
                            <div class="form-group">
                                <button class="btn  btn-primary" type="submit" id="leave_entry" disabled="disabled">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>
                            </div>           
                            
                            {{ Form::close() }}
                        </div>
                            <!-- /.col -->
                        <div class="col-sm-7 ">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Applied Date</th>
                                        <th>Leave From</th>
                                        <th>Leave To</th>
                                        <th>File</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="leaveHistory"> 
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Applied Date</th>
                                        <th>Leave From</th>
                                        <th>Leave To</th>
                                        <th>File</th>
                                        <th>Status</th>
                                    </tr>
                                </tbody> 
                            </table>
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
    var urlD = '{{ url('/') }}';
    // console.log(urlD);
    var associate_id = '{{ auth()->user()->associate_id }}';
    $.ajax({
        url: '{{ url("hr/ess/leave_history") }}',
        dataType: 'json',
        data: {associate_id: associate_id},
        success: function(history)
        {
            var html = "";
            $.each(history, function(i, v)
            {
                var file = v.leave_supporting_file;
                if(file){
                    var extension = file.substr(file.indexOf('.')+1, file.length-1 );
                }
                // console.log(extension);
                html += "<tr>"+
                    "<td>"+v.leave_type+"</td>"+
                    "<td>"+v.leave_applied_date+"</td>"+
                    "<td>"+(v.leave_from) +"</td>"+
                    "<td>"+v.leave_to+"</td>";
                if(file){
                    if(extension == 'jpg' || extension == 'jpeg' || extension == 'png' ){
                        show = 'fa fa-image';    
                    }
                    else{
                        show = 'fa fa-file';
                    }
                html+= '<td><a href="'+urlD+v.leave_supporting_file+'"><i class="'+show+' bigger-200"></i></a></td>';
                }
                else{
                    html+= '<td>No File</td>';
                }
                html+="<td>"+v.leave_status+"</td>"+"</tr>";
            });
            $("#leaveHistory").html(html);

        },
        error: function(xhr)
        {
            alert('failed...');
        }
    });


   /* var multipleDate = $("#multipleDate");
    var multipleDateAccept = $("#multipleDateAccept");
    multipleDate.on('click', function(){
        multipleDateAccept.children().val('');
        $('#select_day').html('');
        multipleDateAccept.toggleClass('hide');
    })
*/

    $("#leave_type").on("change", function(e){
        $.ajax({
            url: '{{ url("hr/ess/leave_check") }}',
            type: 'post',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                associate_id: associate_id, 
                leave_type: $(this).val()
            },
            success: function(data)
            {
                if(data.stat == 'false'){
                    var leave = $("#leave_type").val();
                    $("#leave_type").val('');
                    toastr.options.progressBar = true ;
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('You are allowed to take '+leave+' Leave');
                }


            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
        $('#leave_to').val('');
        $('#leave_from').val('');
        $('#select_day').html('');
        $('#leave_entry').attr("disabled",true);
    });  

    //Dates entry alerts......................................
    $('#leave_from').on('dp.change',function(){
        $('#leave_to').val( $('#leave_from').val());    
    });

    $(document).on('dp.change','#leave_from,#leave_to', function(){
        var formval = $('#leave_from').val();
        var lv_to_date = $('#leave_to').val();
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
                                usertype : 'ess'
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
            toastr.error('Please select leave type!');
        }
    });
    //Dates entry alerts end..............................................

    //File upload validation....
    $('#leave_supporting_file').on('change', function(){
        var fileExtension = ['docx','doc','pdf','jpeg','png','jpg'];
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
                    