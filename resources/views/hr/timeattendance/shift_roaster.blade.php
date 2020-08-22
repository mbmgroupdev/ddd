@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
    .lib{color:#fff;text-align: center;}
    .lib-default{background-color:#f8f9fa;}
    .lib-roster{background-color:#28a745;}
    .lib-holiday{background-color:#dc3545;}
    .lib-roster-holiday{background-color:#e17055;}
    .lib-roster-general{background-color:#16a085;}
    .lib-roster-ot{background-color:#f39c12;}
    .lib-ot{background-color:#ffc107;}
    ul.color-bar {
        list-style: none;
        display: block;
        height: auto;
        border: 1px solid #d1d1d1;
        padding: 10px;
        margin: 5px auto;
        text-align: center;
    }
    .color-bar li {
        display: inline-block;
        vertical-align: middle;
        width: 13.5%;
        height: 20px;
        font-weight: bold;
        font-size: 15px;
    }
    span.color-label {
        width: 40px;
        display: block;
        height: 20px;
        float: left;
        margin: 0 5px;
    }
    span.lib-label {
        text-align: left;
        display: grid;
        padding-top: 2px;
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
                    <a href="#"> Reports </a>
                </li>
                <li class="active"> Shift Roster Summary</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i>Shift Roster</small></h1>
            </div>

            <div class="row">
                <form role="form" method="get" action="#" id="shiftRoasterForm">
                    {{-- ShiftRoasterController@getRoaster --}}
                    <div class="col-xs-12">
                        <div class="col-sm-4">
                            <div class="form-group required">
                                <label class="col-sm-3 control-label no-padding" for="year"> Year</label>
                                <div class="col-sm-9">
                                    <input type="text" name="year" id="year" placeholder="Select Year" class="col-xs-12 yearpicker" value="{{ date('Y') }}" data-validation="required" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group required">
                                <label class="col-sm-3 control-label no-padding" for="month"> Month</label>
                                <div class="col-sm-9">
                                    <input type="text" name="month" id="month" placeholder="Select Month" class="col-xs-12 monthpicker" value="{{ date('F') }}" data-validation="required"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group required">
                                <label class="col-sm-3 control-label no-padding" for="unit"> Unit </label>
                                <div class="col-sm-9">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" style="margin-top:20px">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding" for="floor_id"> Floor </label>
                                <div class="col-sm-9">
                                    {{ Form::select('floor_id',$floorList , null, ['placeholder'=>'Select Floor', 'id'=>'floor_id', 'class'=> 'col-xs-12']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding" for="line_id"> Line </label>
                                <div class="col-sm-9">
                                    {{ Form::select('line_id', $lineList, null, ['placeholder'=>'Select Line', 'id'=>'line_id', 'class'=> 'col-xs-12']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding" for="area"> Area </label>
                                <div class="col-sm-9">
                                    {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area', 'id'=>'area','class'=> 'col-xs-12','style'=> 'width:100%']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" style="margin-top:20px">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding" for="department"> Department </label>
                                <div class="col-sm-9">
                                    {{ Form::select('department', $deptList, null, ['placeholder'=>'Select Department ', 'id'=>'department','class'=> 'col-xs-12', 'style'=> 'width:100%']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding" for="section"> Section </label>
                                <div class="col-sm-9">
                                    {{ Form::select('section', $sectionList, null, ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding" for="subsection"> Sub-Section </label>
                                <div class="col-sm-9">
                                    {{ Form::select('subsection', $subSectionList,null, ['placeholder'=>'Select Sub-Section ', 'id'=>'subsection', 'style'=> 'width:100%']) }}
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="col-xs-12" style="margin-top:20px">
                        <div class="col-sm-4">
                            <div class="form-group">
                              <label class="col-sm-3 control-label no-padding-right" for="emp_type">Employee Type </label>
                              <div class="col-sm-9" style="">
                                  {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'id'=>'emp_type','class'=> 'form-control ']) }}
                              </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="otnonot">OT/Non-OT </label>
                                <div class="col-sm-9">
                                    <select name="otnonot" id="otnonot" class="form-control">
                                        <option value="">Select OT/Non-OT</option>
                                        <option value="0">Non-OT</option>
                                        <option value="1">OT</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 pull-right" style="margin-top:10px">
                        <div class="col-sm-offset-10 col-sm-2">
                            <button type="submit" class="btn btn-primary btn-xs col-sm-10 pull-right" id="shiftRoasterBtn" style="margin-right: 12px;">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="space-20"></div>
            <div class="row" style="margin-top:20px">
                <div class="col-xs-12 worker-list d-table hide">
                    <ul class="color-bar">
                       <li><span class="color-label lib-roster"></span><span class="lib-label"> Change Shift</span></li>
                       <li><span class="color-label lib-default"></span><span class="lib-label">  Default Shift</span></li>
                       <li><span class="color-label lib-roster-holiday"></span><span class="lib-label"> Roster (Day Off)</span></li>
                       <li><span class="color-label lib-roster-general"></span><span class="lib-label"> Roster (General)</span></li>
                       <li><span class="color-label lib-roster-ot"></span><span class="lib-label"> Roster (OT)</span></li>
                       <li><span class="color-label lib-holiday"></span><span class="lib-label"> Holiday/Weekend</span></li>
                       <li><span class="color-label lib-ot"></span><span class="lib-label"> OT</span></li>
                    </ul>
                    <table id="dataTables" class="table table-bordered table-striped" style="width: 100%; overflow-x: auto; display: block; ">
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
                $("#floor_id").html(data);
            },
            error: function()
            {
                alert('failed...');
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
                alert('failed...');
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
                alert('failed...');
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
                $("#section").html(data);
            },
            error: function()
            {
                alert('failed...');
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
                $("#subsection").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    var searchable = [];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {};
    var td = 0;
    var datatable = $('#dataTables').DataTable({
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
          url: '{!! url('hr/timeattendance/shift_roaster_datatable') !!}',
          beforeSend: function(){
            // Here, manually add the loading message.
            $('#dataTables > tbody').html(
              '<div class="row"><div class="col-sm-12 text-center"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div></div>'
            );
          },
          data: function (d) {
            d.month         = $('#month').val(),
            d.year          = $('#year').val(),
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
          type: "post",
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        },

        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp",
        buttons: [
          {
            extend: 'copy',
            className: 'btn-sm btn-info text-center',
            exportOptions: {
              columns: ':visible'
            }
          },
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
            console.log(data);
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
        }
    });

    $('#shiftRoasterForm').on('submit', function(e) {
        e.preventDefault();
        if($('#unit').val()) {
            $(".d-table").removeClass('hide');
            datatable.draw();
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
