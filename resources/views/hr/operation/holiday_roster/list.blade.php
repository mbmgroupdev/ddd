@extends('hr.layout')
@section('title', 'Holiday Roster List')
@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.css')}}">
  <style>
    
    table.dataTable {
      border-spacing: 1px;
    }
    .badge {
      font-size: 100%;
    }
  </style>
@endpush
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
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Holiday Roster Assign List</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12"> 
                    <form class="" role="form" id="holidayRosterReport" method="get" action="#">
                        <div class="panel">
                          <div class="panel-heading">
                            <h6>Holiday Roster <a href="{{ url('/hr/operation/holiday-roster')}}" class="btn btn-success btn-sm pull-right"> <i class="fa fa-plus"></i> Holiday Roster assign</a></h6>
                          </div>
                          <div class="panel-body">
                              <div class="row">
                                  <div class="col-3">
                                      <div class="form-group has-float-label has-required select-search-group">
                                          <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                              <option selected="" value="">Choose...</option>
                                              @foreach($unitList as $key => $value)
                                              <option value="{{ $key }}">{{ $value }}</option>
                                              @endforeach
                                          </select>
                                        <label for="unit">Unit</label>
                                      </div>
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="area" class="form-control capitalize select-search" id="area">
                                              <option selected="" value="">Choose...</option>
                                              @foreach($areaList as $key => $value)
                                              <option value="{{ $key }}">{{ $value }}</option>
                                              @endforeach
                                          </select>
                                          <label for="area">Area</label>
                                      </div>
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                              <option selected="" value="">Choose...</option>
                                          </select>
                                          <label for="department">Department</label>
                                      </div>
                                  </div>
                                  <div class="col-3">
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                              <option selected="" value="">Choose...</option>
                                          </select>
                                          <label for="floor_id">Floor</label>
                                      </div>
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                              <option selected="" value="">Choose...</option>
                                          </select>
                                          <label for="section">Section</label>
                                      </div>
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                              <option selected="" value="">Choose...</option> 
                                          </select>
                                          <label for="subSection">Sub Section</label>
                                      </div>
                                  </div> 
                                  <div class="col-3">
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                              <option selected="" value="">Choose...</option>
                                          </select>
                                          <label for="line_id">Line</label>
                                      </div>
                                      <div class="form-group has-float-label">
                                          <input type="date" class="report_date datepicker form-control" id="date" name="report_to" placeholder="Y-m-d" value="" autocomplete="off" />
                                          <label for="date">Date</label>
                                      </div>
                                      <div class="form-group has-float-label select-search-group">
                                          {{ Form::select('day', ['Sat' => 'Saturday', 'Sun' => 'Sunday', 'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednsday', 'Thu' => 'Thursday', 'Fri' => 'Friday'], null, ['placeholder'=>'Select Day', 'id'=>'day', 'class'=> 'form-control select-search']) }}
                                          <label for="day">Day</label>
                                      </div>
                                  </div>
                                  <div class="col-3">
                                      <div class="form-group has-float-label select-search-group has-required">
                                          <?php
                                            $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT'];
                                           // $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT','Substitute'=>'Substitute'];
                                          ?>
                                          {{ Form::select('type', $types, null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'type', 'required'=>'required']) }}
                                          <label for="type">Day Type</label>
                                      </div>
                                      <div class="form-group has-float-label has-required">
                                        <input type="month" class="report_date form-control" id="month" name="month" placeholder=" Month-Year"required="required" value="{{ date('Y-m')}}"autocomplete="off" />
                                        <label for="month">Month</label>
                                      </div>
                                      <div class="form-group">
                                        <button class="btn btn-primary nextBtn btn-lg pull-right" id="attendanceReport" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                      </div>
                                  </div>   
                              </div>
                          </div>
                        </div>
                        
                    </form>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col h-min-400">
                    <div class="table d-table hide">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head table-responsive w-100" style="">
                             <thead>
                                <tr>
                                  <th>Sl. No</th>
                                  <th>Picture</th>
                                  <th>Oracle ID</th>
                                  <th>Associate ID</th>
                                  {{-- <th>Unit</th> --}}
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
                     </div>
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
                                      {{-- <div class="widget-toolbar">
                                          <a href="#" data-action="collapse">
                                              <i class="ace-icon fa fa-chevron-down"></i>
                                          </a>
                                      </div> --}}
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
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<!-- Datepicker Css -->

<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script>
  
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

  var searchable = [2,3,5,6,7,8];
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
        // d.associate_id  = $('#associate_id').val(),
        d.month = $('#month').val(),
        d.day   = $('#day').val(),
        d.unit        = $('#unit').val(),
        d.floor_id = $("#floor_id").val();
        d.line_id = $("#line_id").val();
        d.area = $("#area").val();
        d.department = $("#department").val();
        d.section = $("#section").val();
        d.subSection = $("#subSection").val();
        d.type = $("#type").val();
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
      { data: 'DT_RowIndex', name: 'DT_RowIndex' },
      { data: 'pic', name: 'pic' },
      { data: 'as_oracle_code',  name: 'as_oracle_code' },
      { data: 'associate_id',  name: 'associate_id' },
      // { data: 'hr_unit_name',  name: 'hr_unit_name' },
      { data: 'as_name', name: 'as_name' },
      { data: 'cell', name: 'cell' },
      { data: 'section', name: 'section' },
      { data: 'hr_designation_name', name: 'hr_designation_name' },
      { data: 'dates', name: 'dates' },
      { data: 'day_count', name: 'day_count' },
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
          if(e.keyCode == 13){
            column.search($(this).val(), false, false, true).draw();
          }
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
    var to= $("#report_to").val();
    var unit= $("#unit").val();
    // var floor_id = $("#floor_id").val();
    // var line_id = $("#line_id").val();
    // var area = $("#area").val();
    // var department = $("#department").val();
    // var section = $("#section").val();
    // var subSection = $("#subSection").val();
    // var type = $("#type").val();
    

    if(to == ""  || unit == "")
    {
      $.notify('Select required fields', 'error');
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
        $('#floor_id').removeAttr('disabled');
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
        $('#line_id').removeAttr('disabled');
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
        $('#department').removeAttr('disabled');
        $("#department").html(data);
      },
      error: function()
      {
        $.notify('failed', 'error');
      }
    });
  });
  

  //Load Section List By department ID
  $('#department').on("change", function(){
    $.ajax({
      url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
      type: 'get',
      data: {area_id: $("#area").val(), department_id: $(this).val()},
      success: function(data)
      {
        $('#section').removeAttr('disabled');
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
        $('#subSection').removeAttr('disabled');
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