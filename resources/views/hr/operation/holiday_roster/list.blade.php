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
                <li class="top-nav-btn">
                  <a href="{{ url('/hr/operation/holiday-roster')}}" class="btn btn-success btn-sm pull-right"> <i class="fa fa-plus"></i> Holiday Roster Assign</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12"> 
                    <form class="" role="form" id="holidayRosterReport" method="get" action="#">
                        <div class="panel">
                          {{-- <div class="panel-heading">
                            <h6>Holiday Roster List <a href="{{ url('/hr/operation/holiday-roster')}}" class="btn btn-success btn-sm pull-right"> <i class="fa fa-plus"></i> Holiday Roster Assign</a></h6>
                          </div> --}}
                          <div class="panel-body pb-0">
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
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                              <option selected="" value="">Choose...</option>
                                          </select>
                                          <label for="floor_id">Floor</label>
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
              <div class="table-area hide h-min-400">
                <div class="iq-card">
                  <div class="iq-card-body">
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%!important;">
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
                      <div class="col-12">
                        <div class="form-group has-required has-float-label select-search-group">
                            <?php
                                $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT']
                              ?>
                                {{ Form::select('typem', $types, null, ['placeholder'=>'Select Type','id'=>'typem', 'class'=> 'form-control']) }} 
                            <label  for="typem', 'class'=> 'form-control']) }} " style="color: maroon;">Type </label>
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group has-required has-float-label select-search-group">
                            <input type="text" name="selected_dates" id="selected_dates" value="" class="form-control">
                             
                            <label  for="selected_dates', 'class'=> 'form-control']) }} " style="color: maroon;">Dates </label>
                        </div>
                      </div>
                      
                      <div class="col-md-12">
                        
                        <input type="hidden" name="as_id" id="as_id" value="">
                        {{-- <input type="text" name="previousDates" id="previousDates" value=""> --}}
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
  var exportColName = ['Sl.','Picture','Oracle ID','Associate ID','Name','Contact', 'Section', 'Designation','Days','Total'];
  var exportCol = [0,2,3,4,5,6,7,8,9];

  var dt =  $('#dataTables').DataTable({

    order: [], //reset auto order
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    processing: true,
    responsive: false,
    serverSide: true,
    cache: false,
    language: {
      processing: '<i class="fa fa-spinner fa-spin f-60"></i><span class="sr-only">Loading...</span> '
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
          className: 'btn btn-sm btn-success',
          title: function () {
              var type = 'Holiday Roster -';
              if($('#type').val() != ''){
                 type += $('#type').select2('data')[0].text; 
              }
              return type;
          },
          header: true,
          footer: false,
          exportOptions: {
              columns: exportCol,
              format: {
                  header: function ( data, columnIdx ) {
                      return exportColName[columnIdx];
                  }
              }
          },
          "action": allExport,
          messageTop: ''
      }, 
      {
          extend: 'excel', 
          className: 'btn btn-sm btn-warning',
          title: function () {
              var type = 'Holiday Roster - ';
              if($('#type').val() != ''){
                 type += $('#type').select2('data')[0].text; 
              }
              return type;
          },
          header: true,
          footer: false,
          exportOptions: {
              columns: exportCol,
              format: {
                  header: function ( data, columnIdx ) {
                      return exportColName[columnIdx];
                  }
              }
          },
          "action": allExport,
          messageTop: ''
      }, 
      {
          extend: 'pdf', 
          className: 'btn btn-sm btn-primary', 
          title: function () {
              var type = 'Holiday Roster - ';
              if($('#type').val() != null){
                 type += $('#type').select2('data')[0].text; 
              }
              return type;
          },
          header: true,
          footer: false,
          exportOptions: {
              columns: exportCol,
              format: {
                  header: function ( data, columnIdx ) {
                      return exportColName[columnIdx];
                  }
              }
          },
          "action": allExport,
          messageTop: ''
      }, 
      {
          extend: 'print', 
          className: 'btn btn-sm btn-default',
          title: '',
          header: true,
          footer: false,
          exportOptions: {
              columns: exportCol,
              format: {
                  header: function ( data, columnIdx ) {
                      return exportColName[columnIdx];
                  }
              }
          },
          "action": allExport,
          messageTop: function () {
              var data = {
               unit : '',
               month : $('#month').val(),
               date  : $('#report_to').val(),
               day    : '',
               floor_id : '',
               line_id : '',
               area : '',
               department : '',
               section : '',
               subSection : '',
               type : '',
              }
              if($('#unit').val() != ''){
                 data.unit = $('#unit').select2('data')[0].text; 
              }
              if($('#floor_id').val() != ''){
                 data.floor_id = $('#floor_id').select2('data')[0].text; 
              }
              if($('#line_id').val() != ''){
                 data.line_id = $('#line_id').select2('data')[0].text; 
              }
              if($('#area').val() != ''){
                 data.area = $('#area').select2('data')[0].text; 
              }
              if($('#department').val() != ''){
                 data.department = $('#department').select2('data')[0].text; 
              }
              if($('#section').val() != ''){
                 data.section = $('#section').select2('data')[0].text; 
              }
              if($('#subSection').val() != ''){
                 data.subSection = $('#subSection').select2('data')[0].text; 
              }
              if($('#day').val() != ''){
                 data.day = $('#day').select2('data')[0].text; 
              }
              if($('#type').val() != ''){
                 data.type = 'Holiday Roster - '+$('#type').select2('data')[0].text; 
              }
              return rosterReportHeader(data.type, data);
            }
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
      $.ajax({
        url : "{{ url('hr/shift_roaster/roaster_updated_changes') }}",
        type: 'get',
        data: {
          as_id : $('#as_id').val(),
          type:$('#typem').val(),
          year_month:$('#month').val(),
          select_dates:$('#selected_dates').val()
        },
        success: function(response)
        {
          // console.log(response);

          $.notify(response.msg, response.status);
          dt.draw();
          $('#calendarModal').modal('hide');
        },
        error: function()
        {
          $.notify('failed', 'error');
        }
      });
  });

$('#dataTables tbody').on('click','#calendar-view',function(e){
  multiselect = [];
  singleselect=[];
  var dates = '';
  dates = $(this).parent().parent().find('td').eq(8).html().split(',');
  var asId = $(this).parent().parent().find('td').eq(3).html();
  $('#as_id').val(asId);
  // console.log($('#type').val());
  var type = $('#type').val();
  $('#typem').val(type).trigger('change', 'select2');
  dates.pop();
  // console.log(dates);
  $('#selected_dates').val(dates);
});



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
      $(".table-area").removeClass('hide');
      dt.draw();
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