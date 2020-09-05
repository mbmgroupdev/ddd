@extends('hr.layout')
@section('title', 'Shift Roster Summary')
@push('css')
  <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.css')}}">
  {{-- <link rel="stylesheet" href="{{ asset('assets/css/editor.dataTables.min.css') }}" /> --}}
  
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
                <li class="active"> Shift Roster Summary</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form role="form" method="get" action="#" id="shiftRoasterForm"> 
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
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area" required="">
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
                                            <select name="subsection" class="form-control capitalize select-search" id="subSection" disabled>
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
                                        <div class="form-group has-float-label select-search-group">
                                            
                                            {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=>'form-control capitalize select-search', 'id'=>'emp_type']) }}
                                            <label for="emp_type">Employee Type</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        
                                        <div class="form-group has-float-label has-required">
                                            <input type="month" class="report_date form-control" id="month" name="month" placeholder=" Month-Year"required="required" value="{{ date('Y-m')}}"autocomplete="off" />
                                            <label for="month">Month</label>
                                        </div>
                                        
                                        <br>
                                        <br>
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" id="shiftRoasterBtn" type="submit" ><i class="fa fa-save"></i> Generate</button>
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
            <div class="panel">
               <div class="panel-body worker-list">
                 <ul class="color-bar mb-3">
                    <li><span class="color-label lib-roster"></span><span class="lib-label"> Change Shift</span></li>
                    <li><span class="color-label lib-default"></span><span class="lib-label">  Default Shift</span></li>

                    <li><span class="color-label lib-roster-holiday"></span><span class="lib-label"> Roster (Day Off)</span></li>
                    <li><span class="color-label lib-roster-general"></span><span class="lib-label"> Roster (General)</span></li>
                    <li><span class="color-label lib-roster-ot"></span><span class="lib-label"> Roster (OT)</span></li>
                    <li><span class="color-label lib-holiday"></span><span class="lib-label"> Holiday/Weekend</span></li>
                    <li><span class="color-label lib-ot"></span><span class="lib-label"> OT</span></li>
                 </ul>
                 <table id="dataTablesShift" class="table table-bordered table-striped" style="width: 100%; overflow-x: auto; display: block; ">
                     <thead>
                         <tr>
                             <th>Id</th>
                             <th>Name</th>
                             <th>Associate Id</th>
                             <th>Designation</th>
                             <th>Line</th>
                             <th>Floor</th>
                             <th>Day 1</th>
                             <th>Day 2</th>
                             <th>Day 3</th>
                             <th>Day 4</th>
                             <th>Day 5</th>
                             <th>Day 6</th>
                             <th>Day 7</th>
                             <th>Day 8</th>
                             <th>Day 9</th>
                             <th>Day 10</th>
                             <th>Day 11</th>
                             <th>Day 12</th>
                             <th>Day 13</th>
                             <th>Day 14</th>
                             <th>Day 15</th>
                             <th>Day 16</th>
                             <th>Day 17</th>
                             <th>Day 18</th>
                             <th>Day 19</th>
                             <th>Day 20</th>
                             <th>Day 21</th>
                             <th>Day 22</th>
                             <th>Day 23</th>
                             <th>Day 24</th>
                             <th>Day 25</th>
                             <th>Day 26</th>
                             <th>Day 27</th>
                             <th>Day 28</th>
                             <th>Day 29</th>
                             <th>Day 30</th>
                             <th>Day 31</th>
                         </tr>
                     <thead>
                     <tbody></tbody>
                 </table>
               </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<!-- Datepicker Css -->

<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.cellEdit.js') }}"></script>
<script type="text/javascript">
  function getCellId(iteration){
      return 5+iteration;
  }

    $(document).ready(function(){  
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });




        $('#shiftRoasterForm').on('submit', function(e) {
            e.preventDefault();
            $('.app-loader').show();
            if($('#unit').val()) {
                $(".d-table").removeClass('hide');
                /*datatable.draw();*/
                
              var searchable = [];
              var selectable = []; 
              var dropdownList = {};
              var td = 0;
              var datatable = $('#dataTablesShift').DataTable({
                  order: [], //reset auto order
                  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                  processing: true,
                  responsive: false,
                  serverSide: true,
                  cache: false,/*
                  language: {
                    processing: function(){
                        $('.app-loader').html();
                    },
                  },*/
                  scroller: {
                    loadingIndicator: false
                  },
                  pagingType: "full_numbers",
                  ajax: {
                    url: '{!! url('hr/timeattendance/shift_roaster_datatable') !!}',
                    beforeSend: function(){
                        //$('.app-loader').show();
                    },
                    data: function (d) {
                      d.month         = $('#month').val(),
                      // d.year          = $('#year').val(),
                      d.unit          = $('#unit').val(),
                      d.otnonot       = $('#otnonot').val(),
                      d.floor_id      = $("#floor_id").val(),
                      d.line_id       = $("#line_id").val(),
                      d.area          = $("#area").val(),
                      d.department    = $("#department").val(),
                      d.section       = $("#section").val(),
                      d.subsection    = $("#subsection").val(),
                      d.emptype       = $("select[name=emp_type]").val()
                    },
                    type: "get",
                    headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                  },

                  dom: "lBftrip",
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
                    }
                  ],

                  columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'associate', name: 'associate' },
                    { data: 'designation', name: 'designation' },
                    { data: 'line', name: 'line' },
                    { data: 'floor', name: 'floor' },
                    { data: 'day_1', name: 'day_1' },
                    { data: 'day_2', name: 'day_2' },
                    { data: 'day_3', name: 'day_3' },
                    { data: 'day_4', name: 'day_4' },
                    { data: 'day_5', name: 'day_5' },
                    { data: 'day_6', name: 'day_6' },
                    { data: 'day_7', name: 'day_7' },
                    { data: 'day_8', name: 'day_8' },
                    { data: 'day_9', name: 'day_9' },
                    { data: 'day_10', name: 'day_10' },
                    { data: 'day_11', name: 'day_11' },
                    { data: 'day_12', name: 'day_12' },
                    { data: 'day_13', name: 'day_13' },
                    { data: 'day_14', name: 'day_14' },
                    { data: 'day_15', name: 'day_15' },
                    { data: 'day_16', name: 'day_16' },
                    { data: 'day_17', name: 'day_17' },
                    { data: 'day_18', name: 'day_18' },
                    { data: 'day_19', name: 'day_19' },
                    { data: 'day_20', name: 'day_20' },
                    { data: 'day_21', name: 'day_21' },
                    { data: 'day_22', name: 'day_22' },
                    { data: 'day_23', name: 'day_23' },
                    { data: 'day_24', name: 'day_24' },
                    { data: 'day_25', name: 'day_25' },
                    { data: 'day_26', name: 'day_26' },
                    { data: 'day_27', name: 'day_27' },
                    { data: 'day_28', name: 'day_28' },
                    { data: 'day_29', name: 'day_29' },
                    { data: 'day_30', name: 'day_30' },
                    { data: 'day_31', name: 'day_31' }
                  ],

                  rowCallback: function(row, data, index){
                      for(var i=1; i<=31; i++){
                          // get row cell id
                          td = getCellId(i);
                          if((data['day_'+i]!=null && data['day_'+i].indexOf('Weekend') != -1) || (data['day_'+i]!=null && data['day_'+i].indexOf('Holiday') != -1) ||  typeof data['hPlanner'+i] != "undefined"){
                              if(data['hRoster'+i]) {
                                  // roster data found
                                  if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('Holiday') != -1) {
                                      $(row).find('td:eq('+td+')').css({'background-color': '#e17055', 'color': '#fff', 'font-weight': 'bold'});
                                  }
                                  if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('General') != -1) {
                                      $(row).find('td:eq('+td+')').css({'background-color': '#16a085', 'color': '#fff', 'font-weight': 'bold'});
                                  }
                                  if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('OT') != -1) {
                                      $(row).find('td:eq('+td+')').css({'background-color': '#f39c12', 'color': '#fff', 'font-weight': 'bold'});
                                  }
                              } else {
                                  // set cell color red
                                  $(row).find('td:eq('+td+')').css({'background-color': '#dc3545', 'color': '#fff', 'font-weight': 'bold'});
                              }
                          } else if(data['day_'+i]!=null && data['day_'+i].indexOf('OT') != -1) {
                              // set cell color orange
                              $(row).find('td:eq('+td+')').css({'background-color': '#ffc107', 'color': '#fff', 'font-weight': 'bold'});
                          } else if(data['day_'+i]) {
                              if(data['defaultDay'+i]) {
                                  if(data['hRoster'+i]) {
                                      // roster data found
                                      if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('Holiday') != -1) {
                                          $(row).find('td:eq('+td+')').css({'background-color': '#e17055', 'color': '#fff', 'font-weight': 'bold'});
                                      }
                                      if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('General') != -1) {
                                          $(row).find('td:eq('+td+')').css({'background-color': '#16a085', 'color': '#fff', 'font-weight': 'bold'});
                                      }
                                      if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('OT') != -1) {
                                          $(row).find('td:eq('+td+')').css({'background-color': '#f39c12', 'color': '#fff', 'font-weight': 'bold'});
                                      }
                                  } else {
                                      // default shift day
                                      $(row).find('td:eq('+td+')').css({'font-weight': 'bold'});
                                  }
                              } else {
                                  if(data['hRoster'+i]) {
                                      // roster data found
                                      if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('Holiday') != -1) {
                                          $(row).find('td:eq('+td+')').css({'background-color': '#e17055', 'color': '#fff', 'font-weight': 'bold'});
                                      }
                                      if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('General') != -1) {
                                          $(row).find('td:eq('+td+')').css({'background-color': '#16a085', 'color': '#fff', 'font-weight': 'bold'});
                                      }
                                      if(data['hRoster'+i]!=null && data['hRoster'+i].indexOf('OT') != -1) {
                                          $(row).find('td:eq('+td+')').css({'background-color': '#f39c12', 'color': '#fff', 'font-weight': 'bold'});
                                      }
                                  } else {
                                      // roster shift day
                                      // set cell color green
                                      $(row).find('td:eq('+td+')').css({'background-color': '#28a745', 'color': '#fff', 'font-weight': 'bold'});
                                  }
                              }
                          }
                      }
                  },

                  createdRow: function( row, data, dataIndex ) {
                      for(var i=7; i<=37; i++){
                          $(row).children(':nth-child('+i+')').addClass('tr_eachrow');
                      }
                  },

                  initComplete: function () {
                    var api =  this.api();

                    // Apply the search
                    api.columns(searchable).every(function () {
                      var column = this;
                      var input = document.createElement("input");
                      input.setAttribute('placeholder', $(column.header()).text());

                      $(input).appendTo($(column.header()).empty())
                      .on('keyup', function () {

                        column.search($(this).val(), false, false, true).draw();
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
                    $('.app-loader').hide();
                  }
              });
               
            }
        });

        // sum two time ex: 12:00:00+11:30:00
        function additionTime() {
            var arr = [];
            $.each(arguments, function() {
                $.each(this.split(':'), function(i) {
                    arr[i] = arr[i] ? arr[i] + (+this) : +this;
                });
            })
            return arr.map(function(n) {
                return n < 10 ? '0'+n : n;
            }).join(':');
        }

        // convert min to hour:min
        function convertMinsToHrsMins(mins) {
            let h = Math.floor(mins / 60);
            let m = mins % 60;
            h = h < 10 ? '0' + h : h;
            m = m < 10 ? '0' + m : m;
            return `${h}:${m}`;
        }

        $(document).on('mouseover', '.tr_eachrow', function(e) {
            // console.log($(this).text());
            var shift_code = $(this).text();
            // if (shift_code.indexOf('-') == 1) {
            //     shift_code = shift_code[1];
            // }
            shift_code = shift_code.split('-');
            if (typeof shift_code[1] !== "undefined") {
                shift_code = shift_code[1];
            } else {
                shift_code = shift_code[0];
            }
            var unit_id = $('#unit').val();
            var that = $(this);
            that.attr('data-tooltip', 'Please Wait....');
            $.ajax({
                'url': '{{ url('hr/shift_roaster/ajax_get_sfhift_details') }}',
                'type': 'get',
                'dataType': 'json',
                data:{
                    shift_code: shift_code,
                    unit_id: unit_id
                },
                success: function (data) {
                    if(data['hr_shift_name']) {
                        var breakTime = data['hr_shift_break_time'];
                        var endTime = data['hr_shift_end_time'];
                        var sum = additionTime(endTime,convertMinsToHrsMins(breakTime));

                        var show = "Shift Name: "+data['hr_shift_name']+
                        "\nIn-Time: "+data['hr_shift_start_time']+
                        "\n Break-Time: "+data['hr_shift_break_time']+" Min"+
                        "\nOut-Time: "+sum;
                        that.attr('data-tooltip', show);
                    } else {
                        that.attr('data-tooltip', shift_code);
                    }
                }
            }, function(){
                //This function is for unhover.
            });
        });
    
      });
</script>
@endpush
@endsection