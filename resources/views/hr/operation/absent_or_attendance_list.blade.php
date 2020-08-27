@extends('hr.layout')
@section('title', 'Attendance Consecutive')
@push('css')
   <link rel="stylesheet" href="{{ asset('assets/css/reports.css')}}">
   <style>
    table.dataTable {
      border-spacing: 1px;
    }
    #dataTables th:nth-child(2) input{
      width: 100px !important;
    }
    #dataTables th:nth-child(4){
      width: 120px !important;
    }
    
    #dataTables th:nth-child(6) input{
      width: 90px !important;
    }
    #dataTables th:nth-child(7) input{
      width: 120px !important;
    }
    #dataTables th:nth-child(8) input{
      width: 190px !important;
    }
    #dataTables th:nth-child(9) input{
      width: 62px !important;
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
                <li class="active"> Attendance Consecutive</li>
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
                                        <div class="form-group has-float-label has-required">
                                          <input type="date" class="report_date datepicker form-control" id="report_from" name="report_from" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                          <label for="report_from">Report From</label>
                                        </div>
                                        <div class="row">
                                          <div class="col-5 pr-0">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date min_sal form-control" id="min_salary" name="min_salary" placeholder="Min Salary" required="required" value="50000" min="{{$data['salaryMin']}}" max="{{$data['salaryMax']}}" autocomplete="off" />
                                              <label for="min_salary">Range From</label>
                                            </div>
                                          </div>
                                          <div class="col-1 p-0">
                                            <div class="c1DHiF text-center">-</div>
                                          </div>
                                          <div class="col-6">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date max_sal form-control" id="max_salary" name="max_salary" placeholder="Max Salary" required="required" value="{{$data['salaryMax']}}" min="{{$data['salaryMin']}}" max="{{$data['salaryMax']}}" autocomplete="off" />
                                              <label for="max_salary">Range To</label>
                                            </div>
                                          </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <?php
                                                $type = ['Present'=>'Present','Intime-Outtime Empty'=>'Intime-Outtime Empty','Present(Intime Empty)'=>'Present (In Time Empty)','Present(Outtime Empty)'=>'Present (Out Time Empty)','Present (Halfday)'=>'Present (Halfday)','Absent'=>'Absent','Leave'=>'Leave','Present (Late)'=>'Late','Present (Late(Outtime Empty))'=>'Late (Out Time Empty)'];
                                            ?>
                                            {{ Form::select('type', $type, 'Absent', ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'type', 'required'=>'required']) }}
                                            <label for="type">Report Type</label>
                                        </div>
                                        
                                        <div class="form-group has-float-label has-required">
                                          <input type="date" class="report_date datepicker form-control" id="report_to" name="report_to" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                          <label for="report_to">Report To</label>
                                        </div>
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" id="attendanceReport"><i class="fa fa-save"></i> Generate</button>
                                        </div>
                                    </div>   
                                </div>
                                
                            </div>
                        </div>
                        <div class="single-employee-search" id="single-employee-search" style="display: none;">
                          <div class="form-group">
                            <input type="text" name="employee" class="form-control" placeholder="Search Employee Associate ID..." id="searchEmployee">
                          </div>
                        </div>
                    </form>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col">
                  <div class="table d-table hide">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head table-responsive w-100" >
                             <thead>
                                <tr>
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
                     </div>
                   </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){   
        var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
        
        // change from data action
        $('#report_from').on('change', function() {
          if($(this).val() !== '') {
            $('#report_to').val($(this).val());
          }
        });
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

    });
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
          d.floor_id = $("#floor_id").val(),
          d.line_id = $("#line_id").val(),
          d.area = $("#area").val(),
          d.department = $("#department").val(),
          d.section = $("#section").val(),
          d.subSection = $("#subSection").val(),
          d.type = $("#type").val(),
          d.ot_hour = $("#ot_hour").val(),
          d.condition = $("#condition").val(),
          d.min_salary = $("#min_salary").val(),
          d.max_salary = $("#max_salary").val()

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

    
</script>
@endpush
@endsection