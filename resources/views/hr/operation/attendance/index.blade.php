@extends('hr.layout')
@section('title', 'Attendance Operation')
@push('css')
  
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/editor.dataTables.min.css') }}" />
  <style>
    #dataTables th:nth-child(2) input{
      width: 80px !important;
    }
    #dataTables th:nth-child(3) input{
      width: 65px !important;
    }
    #dataTables th:nth-child(4) input{
      width: 65px !important;
    }
    #dataTables th:nth-child(5) input{
      width: 90px !important;
    }
    #dataTables th:nth-child(6) input, #dataTables th:nth-child(7) input{
      width: 62px !important;
    }
    #dataTables th:nth-child(8) input, #dataTables th:nth-child(9) input{
      width: 62px !important;
    }
    #dataTables th:nth-child(11){
      width: 120px !important;
    }
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
                <li class="active"> Attendance Operation</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="attendanceReport" method="get" action="#"> 
                        <div class="panel">
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
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                <option selected="" value="">Choose...</option>
                                                <option value="0">Non-OT</option>
                                                <option value="1">OT</option>
                                            </select>
                                            <label for="otnonot">OT/Non-OT</label>
                                        </div>
                                        <div class="form-group has-float-label">
                                            <input type="number" class="form-control" id="ot_hour" name="ot_range" placeholder="OT hour" value="" min="0" autocomplete="off" />
                                            <label for="ot_hour">OT Hour</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group has-required">
                                            <?php
                                              $type = ['All'=>'Present(All)','Present'=>'Present(Intime & Outtime Not Empty)','Present(Intime Empty)'=>'Present(Intime Empty)','Present(Outtime Empty)'=>'Present(Outtime Empty)','Present (Halfday)'=>'Present (Halfday)','Absent'=>'Absent','Holiday'=>'Holiday','Present (Late)'=>'Late','Present (Late(Outtime Empty))'=>'Late(Outtime Empty)'];
                                            ?>
                                            {{ Form::select('type', $type, null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'type', 'required'=>'required']) }}
                                            <label for="type">Report Type</label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input type="date" class="report_date datepicker form-control" id="report_from" name="report_from" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                            <label for="report_from">From</label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input type="date" class="report_date datepicker form-control" id="report_to" name="report_to" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                            <label for="report_to">To</label>
                                        </div>
                                    </div>   
                                </div>
                                <div class="row">
                                    <div class="offset-8 col-4">
                                        <button class="btn btn-primary nextBtn btn-lg pull-right" id="attendanceReport" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                    <input type="hidden" value="{{ Auth::user()->hasRole('Super Admin')}}" id="super-id">
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col">
                   <input type="hidden" value="1" id="lock_status">
                    <div class="table d-table hide">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head table-responsive w-100" style="">
                             <thead>
                                <tr>
                                   <th>Sl.</th>
                                   <th>Associate ID</th>
                                   <th>Oracle ID</th>
                                   <th>Name</th>
                                   <th>Designation</th>
                                   <th>Shift</th>
                                   <th>Date</th>
                                   <th>In Time</th>
                                   <th>Out Time</th>
                                   <th>OT</th>
                                   <th>Status</th>
                                </tr>
                             </thead>
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
<!-- Datepicker Css -->

<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.cellEdit.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){  
        var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
        
        // change unit
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
                error: function(reject)
                {
                   console.log(reject);
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
               error: function(reject)
               {
                 console.log(reject);
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
               error: function(reject)
               {
                 console.log(reject);
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
               error: function(reject)
               {
                 console.log(reject);
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
             error: function(reject)
             {
               console.log(reject);
             }
           });
        });
        $('#report_from').on('change', function() {
          var dateTo = $(this).val();
          if(dateTo !== '') {
            $('#report_to').val(dateTo);
          }
        });
        var lock = parseInt($("#lock_data").val());

        function myCallbackFunction (updatedCell, updatedRow, oldValue) {
          var index = updatedRow.data().DT_RowIndex;
          if(!(updatedRow.data().out_punch === null)){
            var out_time = updatedRow.data().out_punch;

            var in_time = updatedRow.data().in_punch;
            var associateId = stripHtml(updatedRow.data().associate_id);
             // console.log(updatedRow.data().att_date);
            var ths =$(this);
            var hr_shift_start_time = updatedRow.data().hr_shift_start_time;
            var hr_shift_end_time = updatedRow.data().hr_shift_end_time;
            var hr_shift_break_time = updatedRow.data().hr_shift_break_time;
            var hr_shift_night_flag = updatedRow.data().hr_shift_night_flag;
            var att_date = updatedRow.data().att_date;
            $.ajax({
              url: '/hr/timeattendance/calculate_ot',
              method: "GET",
              dataType: 'json',
              data: {
                'att_date' : att_date,
                'out_time' : out_time,
                'in_time': in_time,
                'associateId' : associateId,
                'hr_shift_start_time' : hr_shift_start_time,
                'hr_shift_end_time' : hr_shift_end_time,
                'hr_shift_break_time' : hr_shift_break_time,
                'hr_shift_night_flag' : hr_shift_night_flag
              },
              success: function(data)
              {
                // console.log(data);
                if(hr_shift_start_time){
                  if(data.s_ot) {
                    // console.log('sss');
                    $('#'+stripHtml(updatedRow.data().associate_id)+index+'_ot').text(data.n_ot);
                    updatedRow.data().ot = data.s_ot;
                  } else {
                    // $('#'+updatedRow.data().associate_id+'_ot').text(0);
                    // updatedRow.data().ot = 0;
                  }
                }
              }
            });
        }

        setTimeout(function () {

         if($('#type').val() != 'Absent'){
           var type = 'out';
           if(updatedRow.data().in_time == null){
             var type = 'in';
           }
           //console.log('{{ Auth::user()->hasRole('super user')}}');

           $.ajax({
             url : "{{ url('hr/attendance/save_from_report') }}",
             type: 'get',
             data: {
               unit : updatedRow.data().as_unit_id,
               associate_id:stripHtml(updatedRow.data().associate_id),
               date:updatedRow.data().att_date,
               in_punch_new:updatedRow.data().in_punch,
               out_punch_new:updatedRow.data().out_punch,
               ot_new:updatedRow.data().ot,
               type:type
             },
             success: function(data)
             {
              console.log(data);
              $('#'+stripHtml(updatedRow.data().associate_id)+index+'_in').css('background-color','yellow');
              $('#'+stripHtml(updatedRow.data().associate_id)+index+'_out').css('background-color','yellow');
              $('#'+stripHtml(updatedRow.data().associate_id)+index+'_ot').css('background-color','yellow');
               toastr.success(' ','Attendance Udated Successfully.');
               // $('#dataTables').DataTable().ajax.reload()
             },
             error: function()
             {
               toastr.error('Please Try Again Later.','Something Went Wrong!!');
             }
           });
         }else{

           $.ajax({
             url : "{{ url('hr/attendance/save_from_report_absent') }}",
             type: 'get',
             data: {
               unit : updatedRow.data().as_unit_id,
               associate_id:stripHtml(updatedRow.data().associate_id),
               date:updatedRow.data().att_date,
               in_punch_new:updatedRow.data().in_punch,
               out_punch_new:updatedRow.data().out_punch,
               ot_new:updatedRow.data().ot
             },
             success: function(data)
             {
              $('#'+stripHtml(updatedRow.data().associate_id)+index+'_in').css('background-color','yellow');
              $('#'+stripHtml(updatedRow.data().associate_id)+index+'_out').css('background-color','yellow');
              $('#'+stripHtml(updatedRow.data().associate_id)+index+'_ot').css('background-color','yellow');
               toastr.success(' ','Attendance Updated Successfully.');
               //$('#dataTables').DataTable().ajax.reload()
             },
             error: function()
             {
               toastr.error('Please Try Again Later.','Something Went Wrong!!');
             }
           });
         }
         },1000);
        }
        var searchable = [1,2,3,4,5,6,7,8];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {};

        var exportColName = ['Sl.','Associate ID','Oracle ID','Name','Designation','Shift', 'Date', 'In Time','Out Time','OT','Status'];
        var exportCol = [0,1,2,3,4,5,6,7,8,9];

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
           url: '{!! url("hr/timeattendance/attendance_report_data") !!}',
           data: function (d) {
             d.associate_id  = $('#associate_id').val(),
             d.report_from = $('#report_from').val(),
             d.report_to   = $('#report_to').val(),
             d.unit        = $('#unit').val(),
             d.otnonot        = $('#otnonot').val(),
             d.floor_id = $("#floor_id").val();
             d.line_id = $("#line_id").val();
             d.area = $("#area").val();
             d.department = $("#department").val();
             d.section = $("#section").val();
             d.subSection = $("#subSection").val();
             d.type = $("#type").val();
             d.ot_hour = $("#ot_hour").val();
             d.condition = $("#condition").val();

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
                      var type = 'Attendance Report -';
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
                  extend: 'excel', 
                  className: 'btn btn-sm btn-warning',
                  title: function () {
                      var type = 'Attendance Report - ';
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
                  extend: 'pdf', 
                  className: 'btn btn-sm btn-primary', 
                  title: function () {
                      var type = 'Attendance Report - ';
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
                       report_from : $('#report_from').val(),
                       report_to   : $('#report_to').val(),
                       otnonot        : '',
                       floor_id : '',
                       line_id : '',
                       area : '',
                       department : '',
                       section : '',
                       subSection : '',
                       type : '',
                       ot_hour : $("#ot_hour").val()
                      }
                      if($('#unit').val() != null){
                         data.unit = $('#unit').select2('data')[0].text; 
                      }
                      if($('#floor_id').val() != null){
                         data.floor_id = $('#floor_id').select2('data')[0].text; 
                      }
                      if($('#line_id').val() != null){
                         data.line_id = $('#line_id').select2('data')[0].text; 
                      }
                      if($('#area').val() != null){
                         data.area = $('#area').select2('data')[0].text; 
                      }
                      if($('#department').val() != null){
                         data.department = $('#department').select2('data')[0].text; 
                      }
                      if($('#section').val() != null){
                         data.section = $('#section').select2('data')[0].text; 
                      }
                      if($('#subSection').val() != null){
                         data.subSection = $('#subSection').select2('data')[0].text; 
                      }
                      if($('#type').val() != null){
                         data.type = 'Attendance Report - '+$('#type').select2('data')[0].text; 
                      }
                      return operationReportHeader(data.type, data);
                    }
              } 
          ],

         columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex' },
           // { data: 'edit_jobcard', name: 'edit_jobcard' },
           { data: 'associate_id',  name: 'associate_id' },
           { data: 'oracle_id',  name: 'oracle_id' },
           { data: 'as_name', name: 'as_name' },
           { data: 'hr_designation_name', name: 'hr_designation_name' },
           { data: 'hr_shift_name', name: 'hr_shift_name' },
           { data: 'att_date', name: 'att_date' },
           { data: 'in_punch', name: 'in_punch' },
           { data: 'out_punch', name: 'out_punch' },
           { data: 'ot', name: 'ot' },
           { data: 'att_status', name: 'att_status' }

         ],

         createdRow: function ( row, data, index ) {
            var td_index = data.DT_RowIndex;
            var associateId = stripHtml(data.associate_id);
            $('td', row).eq(7).attr('id', associateId+td_index+'_in');
            $('td', row).eq(8).attr('id', associateId+td_index+'_out');
            $('td', row).eq(9).attr('id', associateId+td_index+'_ot');
         },
         initComplete: function () {
           var api =  this.api();

           // Apply the search
           api.columns(searchable).every(function () {
             var column = this;
             var input = document.createElement("input");
             input.setAttribute('placeholder', $(column.header()).text());

             $(input).appendTo($(column.header()).empty())
             .on('keyup', function (e) {
              if(e.keyCode == 13){
                column.search($(this).val(), false, false, true).draw();
              }
             });

             $('input', this.column(column).header()).on('click', function(e) {
               e.stopPropagation();
             });
           });

           // each column select list
           api.columns(selectable).every( function (i, x) {
             var column = this;

             var select = $('<select><option value="">'+$(column.header()).text()+'</option></select>')
             .appendTo($(column.header()).empty())
             .on('change', function(e){
               var val = $.fn.dataTable.util.escapeRegex(
                 $(this).val()
               );
               column.search(val ? val : '', true, false ).draw();
               e.stopPropagation();
             });

             // column.data().unique().sort().each( function ( d, j ) {
             // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
             // });
             $.each(dropdownList[i], function(j, v) {
               select.append('<option value="'+v+'">'+v+'</option>')
             });
           });
         }

       });

       dt.MakeCellsEditable({
         "onUpdate": myCallbackFunction,
         "inputCss":'my-input-class',
         "columns": [7,8],
         "allowNulls": {
           "columns": [3],
           "errorClass": 'error'
         },
         "confirmationButton": { // could also be true
           "confirmCss": 'my-confirm-class',
           "cancelCss": 'my-cancel-class'
         },
         "inputTypes": [
           {
             "column": 7,
             "type": "text",
             "options":null
           },
           {
             "column": 8,
             "type": "text",
             "options":null
           }
         ]
       });
       $('#attendanceReport').on('submit', function(e)
       {
         e.preventDefault();
         var from= $("#report_from").val();
         var to= $("#report_to").val();
         var unit= $("#unit").val();
         // var floor_id = $("#floor_id").val();
         // var line_id = $("#line_id").val();
         // var area = $("#area").val();
         // var department = $("#department").val();
         // var section = $("#section").val();
         // var subSection = $("#subSection").val();
         // var type = $("#type").val();
         // var ot_hour = $("#ot_hour").val();
         // setTimeout(function () {
         //   var condition = $("#condition").val();
         // },100);

          if(to == "" || from == "" || unit == "")
          {
            console.log("Please Select Following Field");
          }
          else{
            // check activity lock
            $.ajax({
              type: "get",
              url: '{{ url("hr/operation/unit-wise-activity-lock")}}',
              data: {
                unit: unit
              },
              success: function(response)
              {
                // console.log(response);
                $("#lock_status").val(response);
              },
              error: function (reject) {
                console.log(reject);
              }
            });
            $(".d-table").removeClass('hide');
            dt.draw();
          }
       });
    });
  function stripHtml(html){
    // Create a new div element
    var temporalDivElement = document.createElement("div");
    // Set the HTML content with the providen
    temporalDivElement.innerHTML = html;
    // Retrieve the text property of the element (cross-browser support)
    return temporalDivElement.textContent || temporalDivElement.innerText || "";
  }
  
</script>
@endpush
@endsection