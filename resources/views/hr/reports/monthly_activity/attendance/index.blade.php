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
      width: 70px !important;
    }
    #dataTables th:nth-child(5) input{
      width: 120px !important;
    }
    #dataTables th:nth-child(6) input{
      width: 120px !important;
    }
    #dataTables th:nth-child(7) input{
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
    div.dataTables_wrapper div.dataTables_processing {
      top: 10% !important;
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Monthly Attendance</li>
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
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit" >
                                                <option selected="" value="">Choose...</option>
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="location" class="form-control capitalize select-search" id="location">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($locationList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="location">Location</label>
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
                                        
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="department">Department</label>
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
                                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="floor_id">Floor</label>
                                        </div>
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
                                        
                                        
                                    </div>
                                    <div class="col-3">
                                        <div class="row">
                                          <div class="col-5 pr-0">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                              <label for="min_sal">Range From</label>
                                            </div>
                                          </div>
                                          <div class="col-1 p-0">
                                            <div class="c1DHiF text-center">-</div>
                                          </div>
                                          <div class="col-6">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                              <label for="max_sal">Range To</label>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                          <input type="month" class="report_date form-control" id="month" name="month" placeholder=" Month-Year"required="required" value="{{ date('Y-m') }}"autocomplete="off" />
                                          <label for="month">Month</label>
                                        </div>
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <?php
                                              $status = ['1'=>'Active','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left', '6'=>'Maternity'];
                                            ?>
                                            {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'employee_status', 'required']) }}
                                            <label for="employee_status">Status</label>
                                        </div>
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" id="attendanceReport" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                        </div>
                                    </div>   
                                </div>
                                
                            </div>
                        </div>
                        
                    </form>
                    
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col">
                    <div class="table d-table hide">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head table-responsive w-100" style="">
                             <thead>
                                <tr>
                                   <th>Sl.</th>
                                   <th>Photo</th>
                                   <th>Oracle ID</th>
                                   <th>Associate ID</th>
                                   <th>Name</th>
                                   <th>Designation</th>
                                   <th>Present</th>
                                   <th>Absent</th>
                                   <th>Leave</th>
                                   <th>Holiday</th>
                                   <th>OT Hour</th>
                                   <th>Total Day</th>
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

        var searchable = [2,3,4,5,6,7,8];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {};

        var exportColName = ['Sl.','','Oracle ID','Associate ID','Name','Designation', 'Present', 'Absent', 'Leave', 'Holiday', 'OT Hour', 'Total Day'];
        var exportCol = [2,3,4,5,6,7,8,9, 10];

        var dTable =  $('#dataTables').DataTable({

         order: [], //reset auto order
         lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
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
           url: '{!! url("hr/reports/monthly-attendance-activity-data") !!}',
           data: function (d) {
             d.associate_id  = $('#associate_id').val(),
             d.unit          = $('#unit').val(),
             d.otnonot       = $('#otnonot').val(),
             d.floor_id      = $("#floor_id").val();
             d.line_id       = $("#line_id").val();
             d.area          = $("#area").val();
             d.department    = $("#department").val();
             d.section       = $("#section").val();
             d.subSection    = $("#subSection").val();
             d.month         = $("#month").val();
             d.min_sal       = $("#min_sal").val();
             d.max_sal       = $("#max_sal").val();
             d.employee_status = $("#employee_status").val();

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
                      var type = 'Attendance Report';
                      
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
                      var type = 'Attendance Report';
                      
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
                       location : '',
                       otnonot : '',
                       floor_id : '',
                       line_id : '',
                       area : '',
                       department : ''
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
                      
                      return operationReportHeader(data.type, data);
                    }
              } 
          ],

         columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex' },
           { data: 'pic', name: 'pic' },
           { data: 'oracle_id',  name: 'oracle_id' },
           { data: 'associate_id',  name: 'associate_id' },
           { data: 'as_name', name: 'as_name' },
           { data: 'hr_designation_name', name: 'hr_designation_name' },
           { data: 'present', name: 'present' },
           { data: 'absent', name: 'absent' },
           { data: 'leave', name: 'leave' },
           { data: 'holiday', name: 'holiday' },
           { data: 'ot_hour', name: 'ot_hour' },
           { data: 'total_day', name: 'total_day' }

         ],

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
      $('#attendanceReport').on('submit', function(e)
       {
         e.preventDefault();
         var location= $("#location").val();
         var unit= $("#unit").val();
         
          if(location == "" && unit == "")
          {
            $.notify("Please Select Following Field", 'error');
          }
          else{
            
            $(".d-table").removeClass('hide');
            dTable.draw();
          }
       });
       
       
    });
</script>
@endpush
@endsection