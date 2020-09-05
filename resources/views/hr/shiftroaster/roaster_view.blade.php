@extends('hr.layout')
@section('title', 'Holiday Roster List')
@section('main-content')
@push('css')
<style>
.fc-event-container a{margin-top: 10px; padding: 10px;}
.widget-box {
    border-radius: 5px;
}
.fc-day.selected {
   background: #ff6;
}
.fc-today.selected {
    background: #ff6 !important;
}
.widget-body { background-color: transparent;}
.ui-datepicker-calendar {
    display: none;
    }
.dataTables_wrapper .dt-buttons {
  float:right;
  text-align:center;
}
.dataTables_length{
  float:left;
}
.dataTables_filter{
  display: none;
}
.dataTables_processing {
  /* top: 85% !important; */
  z-index: 11000 !important;
  border: 0px !important;
  box-shadow: none !important;
  background: transparent !important;
}
.my-input-class {
  padding: 3px 6px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.my-confirm-class {
  padding: 3px 6px;
  font-size: 12px;
  color: white;
  text-align: center;
  vertical-align: middle;
  border-radius: 4px;
  background-color: #337ab7;
  text-decoration: none;
}
.my-cancel-class {
  padding: 3px 6px;
  font-size: 12px;
  color: white;
  text-align: center;
  vertical-align: middle;
  border-radius: 4px;
  background-color: #a94442;
  text-decoration: none;
}
.error {
  border: solid 1px;
  border-color: #a94442;
}
.destroy-button{
  padding:5px 10px 5px 10px;
  border: 1px blue solid;
  background-color:lightgray;
}
.toast-top-right{
  background-color:lightgray !important;
}
.swal-footer {
  text-align: center !important;
}
.swal-text {
  text-align: center !important;
}
.swal-modal {
  width: 410px !important;
  height: 330px !important;
}
.fc-prev-button,.fc-next-button,.fc-today-button{
  display: none;

}


</style>
<link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}">
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
            <a href="#"> Operations </a>
        </li>
        <li class="active"> Holiday Roster </li>
      </ul><!-- /.breadcrumb -->
    </div>
    <div class="page-content">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h6>Holiday Roster <a href="{{ url('/hr/operation/holiday-roster')}}" target="_blank" class="btn btn-info btn-sm pull-right"> <i class="fa fa-plus"></i> Holiday Roster assign</a></h6>
        </div>
        <div class="panel-body">
          <form class="row" role="form" id="holidayRosterReport" method="get" action="#">
            <div class="widget-box ui-sortable-handle">
              <div class="widget-body">
                <div class="row" style="padding: 10px 20px">
                  <div class="col-md-12">
                    
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="col-sm-5 control-label no-padding-right" for="unit"> Unit <span style="color: red; vertical-align: text-top;">*</span></label>
                        <div class="col-sm-7 no-padding-right">
                          {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="area"> Floor  </label>
                      <div class="col-sm-7 no-padding-right" >
                        {{ Form::select('floor_id',$floorList , null, ['placeholder'=>'Select Floor', 'id'=>'floor_id', 'class'=> 'col-xs-12']) }}
                      </div>
                    </div>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="area"> Line  </label>
                      <div class="col-sm-7 no-padding-right" >
                        {{ Form::select('line_id', $lineList, null, ['placeholder'=>'Select Line', 'id'=>'line_id', 'class'=> 'col-xs-12']) }}
                      </div>
                    </div>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="area"> Area </label>
                      <div class="col-sm-7 no-padding-right" >
                        {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area', 'id'=>'area','class'=> 'col-xs-12','style'=> 'width:100%', 'data-validation-error-msg'=>'The Area field is required']) }}
                      </div>
                    </div>
                  </div>
                  <br><br>
                  <div class="col-md-12">
                    <br>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="area"> Department  </label>
                      <div class="col-sm-7 no-padding-right" >
                        {{ Form::select('department', $deptList, null, ['placeholder'=>'Select Department ', 'id'=>'department','class'=> 'col-xs-12', 'style'=> 'width:100%','data-validation-error-msg'=>'The Department field is required']) }}
                      </div>
                    </div>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="area"> Section  </label>
                      <div class="col-sm-7 no-padding-right" >
                        {{ Form::select('section', $sectionList, null, ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                      </div>
                    </div>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="area"> Sub Section  </label>
                      <div class="col-sm-7 no-padding-right" >
                        {{ Form::select('subSection', $subSectionList,null, ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                      </div>
                    </div>
                    <div class="col-sm-3 " >
                      <label class="col-sm-5 control-label no-padding-right" for="type"> Report Type <span style="color: red; vertical-align: text-top;">*</span> </label>
                      <div class="col-sm-7 no-padding-right" >
                        <?php
                          $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT','Substitute'=>'Substitute']
                        ?>
                          {{ Form::select('type', $types, null, ['placeholder'=>'Select Type','id'=>'type', 'class'=> 'form-control', 'data-validation'=> 'required']) }}
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <br>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="col-sm-5 control-label no-padding-right" for="month"> Month <span style="color: red; vertical-align: text-top;">*</span></label>
                        <div class="col-sm-7 no-padding-right">
                          @php
                            $month = date('m');
                            $year = date('Y');
                          @endphp
                          <!-- <input name="report_from" id="report_from" placeholder="m" autocomplete="off" class="col-xs-12 date-picker form-control" data-validation="required" data-validation-format="mm" style="height: 30px; font-size: 12px;" /> -->
                          {{ Form::select('month', ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'], $month, ['placeholder'=>'Select Month', 'id'=>'month', 'required','class'=> 'form-control', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Month field is required', 'required']) }}
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="col-sm-5 control-label no-padding-right" for="year"> Year <span style="color: red; vertical-align: text-top;">*</span></label>
                        <div class="col-sm-7 no-padding-right">
                          <input  name="year" id="year" autocomplete="off" placeholder="Select Year" class="col-xs-12 yearpicker form-control" data-validation-format="yyyy-mm-dd" data-validation="required" value="{{ $year }}" style="height: 30px; font-size: 12px;" />
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="col-sm-5 control-label no-padding-right" for="report_to"> Day </label>
                        <div class="col-sm-7 no-padding-right">
                          {{ Form::select('day', ['Sat' => 'Saturday', 'Sun' => 'Sunday', 'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednsday', 'Thu' => 'Thursday', 'Fri' => 'Friday'], null, ['placeholder'=>'Select Day', 'id'=>'day', 'class'=> 'form-control','data-validation-error-msg'=>'The Month field is required']) }}
                        </div>
                      </div>
                    </div>

                    <!-- <div class="col-sm-3 ot_hour_div">
                    <div class="form-group">
                    <label class="col-sm-5 control-label no-padding-right" for="ot_hour"> OT Hour </label>
                    <div class="col-sm-7 no-padding-right">
                    <input  name="ot_range" id="ot_hour" placeholder="OT hour" class="col-xs-12  form-control" style="height: 30px; font-size: 12px;" />
                  </div>
                </div>
                </div>  -->

                <div class="col-sm-3 ot">
                  <!-- <div class="form-group">
                      <label class="col-sm-5 control-label no-padding-right" for="ot_hour"> OT Hour </label>
                      <div class="col-sm-7 no-padding-right">
                      <input  name="ot_range" id="ot_hour" placeholder="OT hour" class="col-xs-12  form-control" style="height: 30px; font-size: 12px;" />
                    </div>
                  </div> -->
                  <div class="form-group">
                    <label class="col-sm-5 control-label no-padding-right" for="date"> Date </label>
                    <div class="col-sm-7 no-padding-right">
                      <input  name="report_to" id="date" placeholder="Y-m-d" class="col-xs-12 datepicker form-control" data-validation-format="yyyy-mm-dd" style="height: 30px; font-size: 12px;" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
      <div class="">
        <h4 class="row" style="padding:0px 53px">
          <div class="col-sm-11 text-right" style="">
            <span style="font-size: 16px; font-weight: bold; color: red;" id="over_time"></span>
          </div>

          <div class="col-sm-1">
            <button type="submit" class="btn btn-primary btn-sm holidayRosterReport">
              <i class="fa fa-search"></i>
              Search
            </button>
          </div>
        </h4>
      </div>
    </div>
  </form>

  <div class="row">
    <!-- Display Erro/Success Message -->
    @include('inc/message')
    <div class="col-sm-12">
      <!-- PAGE CONTENT BEGINS -->
      <br>
      <div class="table d-table hide table-responsive" >
        <table id="dataTables" class="table table-striped table-bordered" style="display: auto; overflow-x: auto;white-space: nowrap; width: 100% !important;">
          <thead>
            <tr>
              <!-- <th>Sl. No</th> -->
              <th>Picture</th>
              <th>Oracle ID</th>
              <th>Associate ID</th>
              <th>Unit</th>
              <th>Name</th>
              <th>Contact</th>
              <th>Section</th>
              <th>Designation</th>
              <th>Dates</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </div>
      <!-- PAGE CONTENT ENDS -->
    </div>
    <!-- /.col -->
  </div>
  <!-- div for summary -->
  </div><!-- /.page-content -->
</div>
<div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="calendarModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calendarModalLabel">Dates</h5>

      </div>
      <div class="modal-body">
        <div class="row" >

          <div class="row" style="padding: 10px 20px">
            <div class="col-md-12">
              <div class="col-sm-6 " >
                <label class="col-sm-5 control-label no-padding-right" for="typem"> Report Type* </label>
                <div class="col-sm-7 no-padding-right" >
                  <?php
                    $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT']
                  ?>
                    {{ Form::select('typem', $types, null, ['placeholder'=>'Select Type','id'=>'typem', 'class'=> 'form-control']) }}
                </div>

              </div>
              <div class="col-sm-6 " >
                <label class="col-sm-4 control-label no-padding-right" for="comment"> Comment </label>
                <div class="col-sm-7 no-padding-right" >
                  <input type="text" name="comment" id="comment" class="form-control" value="" placeholder="Comment">
                </div>

              </div>
            </div>

        </div>



          <div class="col-md-12">
            <div class="widget-box widget-color-blue3">
                <div class="widget-header">
                    <h4 class="widget-title smaller">
                      Calendar
                    </h4>
                    <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                            <i class="ace-icon fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>


                <div class="widget-body">
                    <div class="widget-main padding-16">
                        <div id="event-calendar">

                        </div>

                    </div>
                </div>
            </div>
            <input type="hidden" name="as_id" id="as_id" value="">
            <input type="hidden" name="previousDates" id="previousDates" value="">
            <input type="hidden" name="previousDatesChanged" id="previousDatesChanged" value="">
          </div>

    </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="saveDates" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
<script type="text/javascript">

var pdates=[];
var multiselect = [];
var singleselect = [];
$(document).ready(function(){
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };

  var searchable = [1,2,6,7,8,9];
  // var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
  var dropdownList = {};
  var printCounter = 0;
  var dTable =  $('#dataTables').DataTable({

    order: [], //reset auto order
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    processing: true,
    responsive: false,
    serverSide: true,
    cache: false,
    language: {
      processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
    },
    scroller: {
      loadingIndicator: false
    },
    pagingType: "full_numbers",
    ajax: {
      url: '{!! url("hr/shift_roaster/roaster_view_data") !!}',
      data: function (d) {
        d.associate_id  = $('#associate_id').val(),
        d.month = $('#month').val(),
        d.year   = $('#year').val(),
        d.day   = $('#day').val(),
        d.unit        = $('#unit').val(),
        d.floor_id = $("#floor_id").val();
        d.line_id = $("#line_id").val();
        d.area = $("#area").val();
        d.department = $("#department").val();
        d.section = $("#section").val();
        d.subSection = $("#subSection").val();
        d.type = $("#type").val();
        d.ot_hour = $("#ot_hour").val();
        d.condition = $("#condition").val();
        d.date = $("#date").val();

      },
      type: "get",
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    },

    dom: 'lBfrtip',
    buttons: [
      {
        extend: 'csv',
        className: 'btn-sm btn-success',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'excel',
        className: 'btn-sm btn-warning',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'pdf',
        className: 'btn-sm btn-primary',
        exportOptions: {
          columns: ':visible'
        }
      },
      {

        extend: 'print',
        className: 'btn-sm btn-default print',
        title: '',
        orientation: 'portrait',
        pageSize: 'LEGAL',
        alignment: "center",
        // header:true,
        messageTop: function () {
          //printCounter++;
          return '<style>'+
          'input::-webkit-input-placeholder {'+
          'color: black;'+
          'font-weight: bold;'+
          'font-size: 12px;'+
          '}'+
          'input:-moz-placeholder {'+
          'color: black;'+
          'font-weight: bold;'+
          'font-size: 12px;'+
          '}'+
          'input:-ms-input-placeholder {'+
          'color: black;'+
          'font-weight: bold;'+
          'font-size: 12px;'+
          '}'+
          'th{'+
          'font-size: 12px !important;'+
          'color: black !important;'+
          'font-weight: bold !important;'+
          '}</style>'+
          '<h2 class="text-center">Consecutive ' +$("#type option:selected").text()+' Report</h2>'+
          '<h3 class="text-center">'+'Unit: '+$("#unit option:selected").text()+'</h3>'+
          '<h5 class="text-center">(From '+$("#report_from").val()+' '+'To'+' '+$("#report_to").val()+') </h5>'+
          '<h5 class="text-center">'+'Total: '+dTable.data().length+'</h5>'+
          '<h6 style = "margin-left:80%;">'+'Printed on: '+new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate()+'</h6><br>'
          ;

        },
        messageBottom: null,
        exportOptions: {
          columns: [0,1,3,4,5,6,7,8,9],
          stripHtml: false
        },
      }
    ],

    columns: [
      // { data: 'DT_RowIndex', name: 'DT_RowIndex' },
      { data: 'pic', name: 'pic' },
      { data: 'as_oracle_code',  name: 'as_oracle_code' },
      { data: 'associate_id',  name: 'associate_id' },
      { data: 'hr_unit_name',  name: 'hr_unit_name' },
      { data: 'as_name', name: 'as_name' },
      { data: 'cell', name: 'cell' },
      { data: 'section', name: 'section' },
      { data: 'hr_designation_name', name: 'hr_designation_name' },
      { data: 'dates', name: 'dates' },
      { data: 'absent_count', name: 'absent_count' },
      { data: 'actions', name: 'actions' },
      // {
      //     "render": function(data, type, row){
      //         return data.split(";").join("<br/>");
      //     }
      // }

    ],
    initComplete: function () {
      var api =  this.api();

      // Apply the search
      api.columns(searchable).every(function () {
        var column = this;
        var input = document.createElement("input");
        input.setAttribute('placeholder', $(column.header()).text());
        input.setAttribute('style', 'width: 80px; height:32px; border:1px solid whitesmoke; color: black;');

        $(input).appendTo($(column.header()).empty())
        .on('keyup', function () {

          column.search($(this).val(), false, false, true).draw();
        });

        $('input', this.column(column).header()).on('click', function(e) {
          e.stopPropagation();
        });
      });

    }

  });

  $('#calendarModal').on('click','#saveDates',function(){

    console.log($('#as_id').val(),multiselect,singleselect);
     //$('#typem').val()
    if(multiselect.length == 0){
      var req = 1;
      var dates = singleselect;
      var selectType = 'single';
    }else if (singleselect.length == 0) {
        var req = 1;
      var dates = multiselect;
      selectType = 'multi';
    }else{
     var dates = [];
    }
    //console.log($('#previousDates').val());


   pdates =[];
    $('#event-calendar').find('.previous').each(function(k){
      var expdata = $(this).data('date').split('-');
      pdates.push(expdata[2]);
    });
    console.log(pdates,$('#previousDates').val());
    $('previousDatesChanged').val(pdates);
    console.log(dates.length);
    if(req==1){

      $.ajax({
        url : "{{ url('hr/shift_roaster/roaster_save_changes') }}",
        type: 'get',
        data: {
                as_id : $('#as_id').val(),
                dates:dates,
                type:$('#typem').val(),
                year:$('#year').val(),
                month:$('#month').val(),
                previous:$('#previousDates').val(),
                previousDateChanged:pdates,
                selectType:selectType,
                comment:$('#comment').val()
              },
        success: function(data)
        {
          //$("#floor_id").html(data);
          toastr.success(' ','Attendance Update Successfully.');
          $('#calendarModal').modal('hide');
          dTable.draw();
        },
        error: function()
        {
          $.notify('failed', 'error');
        }
      });
      $.notify('failed', 'error');
    }else{
      alert('Please Select Report Type');
    }




  });

$('#dataTables tbody').on('click','#calendar-view',function(e){
  //console.log('lk');
  //console.log($(this).parent().parent().find('td').eq(7).html());
  multiselect = [];
  singleselect=[];
  var dates = '';
  dates = $(this).parent().parent().find('td').eq(7).html().split(',');
  var asId = $(this).parent().parent().find('td').eq(1).html();
  $('#as_id').val(asId);
  console.log($('#type').val());
  var type = $('#type').val();
  //$('#typem').val(type);
  // $('#typem option[text="Holiday"').attr('selected','selected');
  dates.pop();
  console.log(dates);
  $('#previousDates').val(dates);
  setTimeout(function(){
  $('#event-calendar').find('.fc-day').each(function(k){
    console.log($(this).data('date'));

//      .each(function(v){
// console.log(v);
//   });
    $(this).removeClass('selected').removeClass('multi').removeClass('single').removeClass('previous');
    for (var i = 0; i < dates.length; i++) {
      var clss = $(this).attr('class').split(' ');
      if(!clss.includes("fc-other-month")){
         var cdate = $(this).data('date').split('-');
         if(cdate[2] == dates[i]){
           $(this).addClass('selected');
           $(this).addClass('previous');
         }
      }
      //console.log(dates[i]);
    }

  });
}, 1000);
});
setTimeout(function(){

  $('#event-calendar').on('click', '.fc-day-header', function(e) {

   let day = $(this).text().toLowerCase();
   //console.log($('.fc-day').filter('.fc-' + day));
   multiselect = [];
   singleselect=[];
   $('.fc-day').filter('.fc-' + day).each(function() {
       // if(this.value != "on")
       // {
       //     checkedBoxes.push($(this).val());
       //     checkedIds.push($(this).data('id'));
       // }
       var clss = $(this).attr('class').split(' ');
       if(!clss.includes("fc-other-month")){
         //console.log($(this).data('date'));
         multiselect.push($(this).data('date'));
       }

   });
   //console.log(multiselect);

   $('#multi_select_dates').val(multiselect);
   $('.fc-day').removeClass('selected')
               .removeClass('single')
               .removeClass('previous')
               .filter('.fc-' + day)
               .addClass('selected')
               .addClass('multi');
   $('.fc-other-month').removeClass('selected')
               .removeClass('multi');

  });

  $('#event-calendar').on('click', '.fc-day', function(e) {

  let day = $(this).text().toLowerCase();

  var clss = $(this).attr('class').split(' ');


  //console.log(singleselect);
  if(clss.includes("selected") && clss.includes("single")){
  singleselect.pop($(this).data('date'))
  $(this).removeClass('selected')
              .removeClass('single')
              .removeClass('previous');
  }else{
  $(this).addClass('selected');
  $(this).addClass('single');
  $('.multi').removeClass('selected').removeClass('multi');
  $('.fc-other-month').removeClass('selected')
              .removeClass('single');
  multiselect = [];
  //singleselect=[];
  if(!clss.includes("fc-other-month")){
    //console.log($(this).data('date'));
    singleselect.push($(this).data('date'));
  }
  $('#single_select_dates').val(singleselect);
  }

  });
  $('#event-calendar').on('click', '.fc-day-number', function(e) {

  let day = $(this).data('date');
  var clss = $(this).attr('class').split(' ');
  // console.log(day);
  var currentEl = $(this).parent().parent().parent().parent().parent().find('.fc-bg').find("tr").find("[data-date='" + day + "']");

  if(clss.includes("selected") && clss.includes("single")){
  currentEl.removeClass('selected')
             .removeClass('single');
  }else{

  currentEl.addClass('selected');
  currentEl.addClass('single');

  $('.multi').removeClass('selected').removeClass('multi');
  $('.fc-other-month').removeClass('selected')
             .removeClass('single');
  }


  });


}, 1000);


  $(window).on('shown.bs.modal', function () {

    var ricksDate = new Date($('#year').val(),$('#month').val()-1, 1);
    //console.log(ricksDate);
    $("#event-calendar").fullCalendar('destroy');
    $("#event-calendar").fullCalendar({
       defaultDate: ricksDate
     });
  });

  $('#holidayRosterReport').on('submit', function(e)
  {
    e.preventDefault();
    var from= $("#report_from").val();
    var to= $("#report_to").val();
    var unit= $("#unit").val();
    var floor_id = $("#floor_id").val();
    var line_id = $("#line_id").val();
    var area = $("#area").val();
    var department = $("#department").val();
    var section = $("#section").val();
    var subSection = $("#subSection").val();
    var type = $("#type").val();
    var ot_hour = $("#ot_hour").val();
    setTimeout(function () {
      var condition = $("#condition").val();
    },100);

    if(to == "" || from == "" || unit == "")
    {
      //alert("Please Select Following Field");

    }
    else{
      $(".d-table").removeClass('hide');
      dTable.draw();
    }
  });

  $('#unit').on("change", function(){
    $.ajax({
      url : "{{ url('hr/attendance/floor_by_unit') }}",
      type: 'get',
      data: {unit : $(this).val()},
      success: function(data)
      {
        $("#floor_id").html(data);
      },
      error: function()
      {
        $.notify('failed', 'error');
      }
    });

    //Load Line List By Unit ID
    $.ajax({
      url : "{{ url('hr/reports/line_by_unit') }}",
      type: 'get',
      data: {unit : $(this).val()},
      success: function(data)
      {
        $("#line_id").html(data);
      },
      error: function()
      {
        $.notify('failed', 'error');
      }
    });
  });
  //Load Department List By Area ID
  $('#area').on("change", function(){
    $.ajax({
      url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
      type: 'get',
      data: {area_id : $(this).val()},
      success: function(data)
      {
        $("#department").html(data);
      },
      error: function()
      {
        $.notify('failed', 'error');
      }
    });
  });
  $('#type').on("change", function(){
    if($('#type').val() == 'Absent'){
      $('.ot').empty();
    }

  });

  //Load Section List By department ID
  $('#department').on("change", function(){
    $.ajax({
      url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
      type: 'get',
      data: {area_id: $("#area").val(), department_id: $(this).val()},
      success: function(data)
      {
        $("#section").html(data);
      },
      error: function()
      {
        $.notify('failed', 'error');
      }
    });
  });


  //Load Sub Section List by Section
  $('#section').on("change", function(){
    $.ajax({
      url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
      type: 'get',
      data: {
        area_id: $("#area").val(),
        department_id: $("#department").val(),
        section_id: $(this).val()
      },
      success: function(data)
      {
        $("#subSection").html(data);
      },
      error: function()
      {
        $.notify('failed', 'error');
      }
    });
  });
});
</script>
@endpush
@endsection
