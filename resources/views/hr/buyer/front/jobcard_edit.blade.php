@extends('hr.layout')
@section('title', 'Job Card')
@section('main-content')
@push('css')
<style>
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
   }
   .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .iq-card {
        border: 1px solid #ccc;
    }
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Job Card</li>
            </ul><!-- /.breadcrumb -->
        </div>
    
        <form role="form" method="get" action="{{ url('hrm/timeattendance/attendance_bulk_manual') }}" class="attendanceReport mb-3" id="attendanceReportEmp">
            <div class="panel" style="margin-bottom: 0;">
                
                <div class="panel-body" style="padding-bottom: 5px;">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'allassociates no-select col-xs-12','style', 'required'=>'required']) }}
                                <label  for="associate"> Associate's ID </label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group has-float-label has-required select-search-group">
                                <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                <label  for="year"> Month </label>
                            </div>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </form>

        
        {!!$jobcardview!!}
                
            
        
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">
    function printMe(divName)
    {
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    $(document).ready(function(){ 
        
        // Status Hidden field value change
        $(".manual").on("keyup", function(){ 
            // console.log($(this).val());
            if($(this).val() == '') {
                $(this).val('00:00:00')
            }
            var intime=$(this).parent().parent().find('.intime').val();
            var outtime=$(this).parent().parent().find('.outtime').val();
            if(intime != ''||outtime != ''){
                $(this).parent().parent().find('.att_status').val('P');
            } else {
                $(this).parent().parent().find('.att_status').val('A');
            }
        });

        // excel conversion -->
        $('#excel').click(function(){
            var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
            location.href=url;
            return false;
        });
    });
    
    $(document).ready(function() {
    $(".intime,.outtime").on("keydown", function(event) {
        if (event.keyCode === 38 || event.keyCode === 40) {
            event.preventDefault();
        }
     });
});
$('.intime,.outtime').datetimepicker({
  format:'HH:mm:ss',
  allowInputToggle: false
});
// input focus select all element
$(function () {
    var focusedElement;
    $(document).on('focus', 'input', function () {
        if (focusedElement == this) return;
        focusedElement = this;
        setTimeout(function () { focusedElement.select(); }, 100);
    });
});

$(document).on("click", ".shift_link", function(e) {
    $(".shiftchange").hide();
    $(this).parent().find('.shiftchange').toggle(100);
});

$(document).on("click", ".popover-close", function(e) {
    $(".shiftchange").hide();
});
$(document).on('click', '.shift-change-btn', function(event) {
    let date = $(this).data('date');
    let associate = $(this).data('eaids');
    let asid = $(this).data('asid');
    let oldshift = $("#oldshift-"+date).val();
    let shift = $("#shift-"+date).val();
    let yearmonth = $(this).data('yearmonth');
    if(shift === oldshift){
        $.notify('This Shift Already Assign', 'error');
        setTimeout(function () { $('.popover-close').click(); }, 100);
        
        $('.app-loader').hide();
    }else{
        $('.app-loader').show();
        $.ajax({
            url : "{{ url('hr/operation/single-date-shift-change') }}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: {
               as_id: asid,
               date: date,
               shift: shift,
               associateid: associate
            },
            success: function(data)
            {
                // console.log(data)
                $('.app-loader').hide();
                $.notify(data.msg, data.type);
                if(data.type === 'success'){
                    var shiftData = data.shift;
                    $("#oldshift-"+date).val(shiftData.hr_shift_name);
                    $("#shiftname-"+date).val(shiftData.hr_shift_name);
                    $("#shiftcode-"+date).val(shiftData.hr_shift_code);
                    $("#shiftstart-"+date).val(shiftData.hr_shift_start_time);
                    $("#shiftend-"+date).val(shiftData.hr_shift_end_time);
                    $("#shiftbreak-"+date).val(shiftData.hr_shift_break_time);
                    $("#shiftnight-"+date).val(shiftData.hr_shift_night_flag);
                    $("#billeligible-"+date).val(shiftData.bill_eligible);
                    $("#shiftclick-"+date).html(shiftData.startout);
                    $("#shiftclick-"+date).attr('data-original-title', shiftData.hr_shift_name);
                    if(data.value !== ''){
                        $("#punchintime-"+date).val(data.value.in_time);
                        $("#punchouttime-"+date).val(data.value.out_time);
                        $("#punchot-"+date).html(data.value.ot_hour);
                        $("#totalOtHour").html(data.value.totalOt);
                    }else{
                        console.log('no record');
                    }

                    setTimeout(function() {
                        $("#row-"+date).addClass('highlight');
                        $(".shiftchange").hide();
                        //$("#punchintime-"+date).focus();
                    }, 200);
                }
            },
            error: function(reject)
            {
               $.notify(reject, 'error');
            }
        });
    }
});
    
</script>
@endpush
@endsection