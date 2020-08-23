@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
<style>
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



</style>
@endpush
@section('content')
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
        <li class="active"> Attendance Consecutive Report </li>
      </ul><!-- /.breadcrumb -->
    </div>
    <div class="page-content">
      <div class="page-header">
        <h1>Operation<small> <i class="ace-icon fa fa-angle-double-right"></i>Attendance Consecutive Report</small></h1>
      </div>
      <form class="widget-container-col" role="form" id="attendanceReport" method="get" action="#">
        <div class="widget-box ui-sortable-handle">
          <div class="widget-body">
            <div class="row" style="padding: 10px 20px">
              <div class="col-md-12">
                <!-- <div class="col-sm-3">
                <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="associate_id">Associate</label>
                <div class="col-sm-8">
                {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12']) }}
              </div>
            </div>
          </div> -->
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
              $type = ['Present'=>'Present','Intime-Outtime Empty'=>'Intime-Outtime Empty','Present(Intime Empty)'=>'Present (In Time Empty)','Present(Outtime Empty)'=>'Present (Out Time Empty)','Present (Halfday)'=>'Present (Halfday)','Absent'=>'Absent','Leave'=>'Leave','Present (Late)'=>'Late','Present (Late(Outtime Empty))'=>'Late (Out Time Empty)'];

              // $type = ['Absent'=>'Absent'];

              ?>
              {{ Form::select('type', $type, 'Absent', ['placeholder'=>'Select Report Type ', 'id'=>'type', 'style'=> 'width:100%', 'data-validation'=>'required',  'data-validation-error-msg'=>'This field is required']) }}
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <br>
          <div class="col-sm-3">
            <div class="form-group">
              <label class="col-sm-5 control-label no-padding-right" for="report_from"> From <span style="color: red; vertical-align: text-top;">*</span></label>
              <div class="col-sm-7 no-padding-right">
                <input name="report_from" id="report_from" placeholder="Y-m-d" class="col-xs-12 datepicker form-control" data-validation="required" data-validation-format="yyyy-mm-dd" style="height: 30px; font-size: 12px;" />
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label class="col-sm-5 control-label no-padding-right" for="report_to"> To <span style="color: red; vertical-align: text-top;">*</span></label>
              <div class="col-sm-7 no-padding-right">
                <input  name="report_to" id="report_to" placeholder="Y-m-d" class="col-xs-12 datepicker form-control" data-validation-format="yyyy-mm-dd" data-validation="required" style="height: 30px; font-size: 12px;" />
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label class="col-sm-5 control-label no-padding-right" for="report_to">Salary Range<span style="color: red; vertical-align: text-top;">*</span></label>
              <div class="col-sm-7 no-padding-right">
                  <input type="number" class="col-xs-12 form-control" name="min_salary" id="min_salary" value="50000" required="required" placeholder="MIN Salary" style="font-size: 12px;" />
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <div class="col-sm-7 no-padding-right">
                  <input type="number" class="col-xs-12 form-control" name="max_salary" id="max_salary"  required="required"  placeholder="MAX Salary" value="{{$data['salaryMax']}}" style="font-size: 12px;" />
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
      <button type="submit" class="btn btn-primary btn-sm attendanceReport">
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
            <th>Associate ID</th>
            <th>Unit</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Section</th>
            <th>Designation</th>
            <th>Dates</th>
            <th>Total</th>
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
</div>

<script type="text/javascript">



$(document).ready(function(){


  var searchable = [1,5,6,7,8];
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
      url: '{!! url("hr/operation/attendance_report_data") !!}',
      data: function (d) {
        d.associate_id  = $('#associate_id').val(),
        d.report_from = $('#report_from').val(),
        d.report_to   = $('#report_to').val(),
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
        d.min_salary = $("#min_salary").val();
        d.max_salary = $("#max_salary").val();

      },
      type: "get",
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    },

    dom: 'lBfrtip',
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
          columns: [0,1,3,4,5,6,7,8],
          stripHtml: false
        },
      }
    ],

    columns: [
      // { data: 'DT_RowIndex', name: 'DT_RowIndex' },
      { data: 'pic', name: 'pic' },
      { data: 'associate_id',  name: 'associate_id' },
      { data: 'hr_unit_name',  name: 'hr_unit_name' },
      { data: 'as_name', name: 'as_name' },
      { data: 'cell', name: 'cell' },
      { data: 'section', name: 'section' },
      { data: 'hr_designation_name', name: 'hr_designation_name' },
      { data: 'dates', name: 'dates' },
      { data: 'absent_count', name: 'absent_count' },
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

  $('body').on('change','#min_salary, #max_salary', function(){
      var min = parseFloat($('#min_salary').val());
      var max = parseFloat($('#max_salary').val());

      if(min > max){
        alert('Minimum Salry is Greater than Maximum Salary');
        $('#min_salary').val('');
        $('#max_salary').val('');
      }
  });

  $('#attendanceReport').on('submit', function(e)
  {
    e.preventDefault();
//-------------------------------
    var min = parseFloat($('#min_salary').val());
    var max = parseFloat($('#max_salary').val());

    if(min > max){
      alert('Minimum Salry is Greater than Maximum Salary');
      $('#min_salary').val('');
      $('#max_salary').val('');
    }
//-------------------------------

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
        $("#subSection").html(data);
      },
      error: function()
      {
        alert('failed...');
      }
    });
  });
});
</script>
@endsection
