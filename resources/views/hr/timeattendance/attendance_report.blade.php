@extends('hr.layout')
@section('title', 'Attendance Operation')
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
   div.dataTables_wrapper div.dataTables_processing { top: 100px;}
</style>
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
               <a href="#"> Operation</a>
            </li>
            <li class="active"> Attendance Operation </li>
         </ul>
         <!-- /.breadcrumb -->
      </div>
      <div class="page-content">
         <form class="widget-container-col" role="form" id="attendanceReport" method="get" action="#">
            <div class="widget-box ui-sortable-handle">
               <div class="widget-body">
                  <div class="row" style="padding: 10px 20px">
                     <div class="col-md-3">
                        <!-- <div class="col-sm-3">
                           <div class="form-group">
                           <label class="col-sm-4 control-label no-padding-right" for="associate_id">Associate</label>
                           <div class="col-sm-8">
                        {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12']) }}
                           </div>
                           </div>
                           </div> -->

                          <div class="form-group row">
                            <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit: <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8 no-padding-right">
                               {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                            </div>
                          </div>
                          <div class="form-group row" >
                             <label class="col-sm-4 control-label no-padding-right" for="area"> Area: </label>
                             <div class="col-sm-8 no-padding-right" >
                                {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area', 'id'=>'area','class'=> 'col-xs-12','style'=> 'width:100%', 'data-validation-error-msg'=>'The Area field is required']) }}
                             </div>
                          </div>
                          <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Department:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('department', $deptList, null, ['placeholder'=>'Select Department ', 'id'=>'department','class'=> 'col-xs-12', 'style'=> 'width:100%','data-validation-error-msg'=>'The Department field is required']) }}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Section:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('section', $sectionList, null, ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                           </div>
                        </div>
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Sub Section:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('subSection', $subSectionList,null, ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                           </div>
                        </div>
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Floor:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('floor_id',$floorList , null, ['placeholder'=>'Select Floor', 'id'=>'floor_id', 'class'=> 'col-xs-12']) }}
                           </div>
                        </div>
                        
                     </div>
                    <div class="col-sm-3">
                      <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Line:  </label>
                         <div class="col-sm-8 no-padding-right" >
                            {{ Form::select('line_id', $lineList, null, ['placeholder'=>'Select Line', 'id'=>'line_id', 'class'=> 'col-xs-12']) }}
                         </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="otnonot">OT/Non-OT: </label>
                          <div class="col-sm-8 no-padding-right">
                             <select name="otnonot" id="otnonot" class="form-control">
                                <option value="">Select OT/Non-OT</option>
                                <option value="0">Non-OT</option>
                                <option value="1">OT</option>
                             </select>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="ot_hour"> OT Hour: </label>
                          <div class="col-sm-8 no-padding-right">
                             <input  name="ot_range" id="ot_hour" placeholder="OT hour" class="col-xs-12  form-control" style="height: 30px; font-size: 12px;" />
                          </div>
                       </div>
                       
                        @hasanyrole("Super Admin")
                        <input name="auth" id="auth" type="hidden" value="super_user" />
                        @endhasanyrole
                    </div>
                    <div class="col-sm-3">
                       <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="type"> Report Type: <span style="color: red; vertical-align: text-top;">*</span> </label>
                           <div class="col-sm-8 no-padding-right" >
                              <?php
                                 $type = ['All'=>'Present(All)','Present'=>'Present(Intime & Outtime Not Empty)','Present(Intime Empty)'=>'Present(Intime Empty)','Present(Outtime Empty)'=>'Present(Outtime Empty)','Present (Halfday)'=>'Present (Halfday)','Absent'=>'Absent','Holiday'=>'Holiday','Present (Late)'=>'Late','Present (Late(Outtime Empty))'=>'Late(Outtime Empty)'];
                                 ?>
                              {{ Form::select('type', $type, null, ['placeholder'=>'Select Report Type ', 'id'=>'type', 'style'=> 'width:100%', 'data-validation'=>'required',  'data-validation-error-msg'=>'This field is required']) }}
                           </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="report_from"> From: <span style="color: red; vertical-align: text-top;">*</span></label>
                          <div class="col-sm-8 no-padding-right">
                             <input name="report_from" id="report_from" placeholder="Y-m-d" class="report_date col-xs-12 datepicker form-control" data-validation="required" data-validation-format="yyyy-mm-dd" style="height: 30px; font-size: 12px;" />
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="report_to"> To: <span style="color: red; vertical-align: text-top;">*</span></label>
                          <div class="col-sm-8 no-padding-right">
                             <input  name="report_to" id="report_to" placeholder="Y-m-d" class="report_date col-xs-12 datepicker form-control" data-validation-format="yyyy-mm-dd" data-validation="required" style="height: 30px; font-size: 12px;" />
                          </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="padding-top: 12px;">
                      <div class="col-sm-3 ot"></div>
                      <div class="col-sm-7">
                         <span style="font-size: 16px; font-weight: bold; color: red;" id="over_time"></span>
                      </div>
                      <div class="col-sm-2 text-right">
                         <button type="submit" class="btn btn-primary btn-sm attendanceReport">
                         <i class="fa fa-search"></i>
                         Search
                         </button>
                      </div>
                    </div>
                  </div>
               </div>
            </div>
         </form>
         <input type="hidden" value="{{ Auth::user()->hasRole('Super Admin')}}" id="super-id">

         <div class="row">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="col-sm-12">
               <!-- PAGE CONTENT BEGINS -->
               <br>
               @php $lock = salary_lock_date();@endphp
               <input type="hidden" value="{{ $lock }}" id="lock_data">
               <div class="table d-table hide">
                  <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
                     <thead>
                        <tr>
                           <th>Sl. No</th>
                           <th>Job Card</th>
                           <th>Associate ID</th>
                           <th>Oracle ID</th>
                           <th>Name</th>
                           <th>Designation</th>
                           <th>Shift</th>
                           <th>Date</th>
                           <th>In Time</th>
                           <th>Out Time</th>
                           <th>Over Time</th>
                           <th>Attendance Status</th>
                        </tr>
                     </thead>
                  </table>
               </div>
               <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
         </div>
         <!-- div for summary -->
      </div>
      <!-- /.page-content -->
   </div>
</div>
@push('js')
<script type="text/javascript">
  var lock = parseInt($("#lock_data").val());

  function myCallbackFunction (updatedCell, updatedRow, oldValue) {
    var index = updatedRow.data().DT_RowIndex;
    if(!(updatedRow.data().out_punch === null)){
      var out_time = updatedRow.data().out_punch;

      var in_time = updatedRow.data().in_punch;
      var associateId = updatedRow.data().associate_id;
       console.log(updatedRow.data().att_date);
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
              $('#'+updatedRow.data().associate_id+index+'_ot').text(data.n_ot);
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
           associate_id:updatedRow.data().associate_id,
           date:updatedRow.data().att_date,
           in_punch_new:updatedRow.data().in_punch,
           out_punch_new:updatedRow.data().out_punch,
           ot_new:updatedRow.data().ot,
           type:type
         },
         success: function(data)
         {
          console.log(data);
          $('#'+updatedRow.data().associate_id+index+'_in').css('background-color','yellow');
          $('#'+updatedRow.data().associate_id+index+'_out').css('background-color','yellow');
          $('#'+updatedRow.data().associate_id+index+'_ot').css('background-color','yellow');
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
           associate_id:updatedRow.data().associate_id,
           date:updatedRow.data().att_date,
           in_punch_new:updatedRow.data().in_punch,
           out_punch_new:updatedRow.data().out_punch,
           ot_new:updatedRow.data().ot
         },
         success: function(data)
         {
          $('#'+updatedRow.data().associate_id+index+'_in').css('background-color','yellow');
          $('#'+updatedRow.data().associate_id+index+'_out').css('background-color','yellow');
          $('#'+updatedRow.data().associate_id+index+'_ot').css('background-color','yellow');
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

   $(document).ready(function(){

    // change from data action
    $('#report_from').on('dp.change', function() {
      var report_from = $(this).val();
      var report_to = $('#report_to').val();
      if(report_to == '') {
        $('#report_to').val(report_from);
      }
    });

    // change from data action
    $('#report_to').on('dp.change', function() {
      var report_to = $(this).val();
      var report_from = $('#report_from').val();
      if(report_from == '') {
        $('#report_from').val(report_to);
      }
    });
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

   $('select.associates').select2({
     placeholder: 'Select Associate\'s ID',
     ajax: {
       url: '{{ url("hr/associate-search") }}',
       dataType: 'json',
       delay: 250,
       data: function (params) {
         return {
           keyword: params.term
         };
       },
       processResults: function (data) {
         return {
           results:  $.map(data, function (item) {
             return {
               text: item.associate_name,
               id: item.associate_id
             }
           })
         };
       },
       cache: true
     }
   });

   var searchable = [2,3,4,5,6,7,8,9];
   var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
   var dropdownList = {};

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
         title: '<h2 class="text-center">'+ $("#type").val() +'</h2><br>',
         orientation: 'landscape',
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
               '<h2 class="text-center">'+'Unit: '+$("#unit option:selected").text()+'</h2>'+
               '<h4 class="text-center">'+'Report Type: '+$("#type option:selected").text()+'</h2>'+
               '<h4 class="text-center">'+'Report Date: '+$("#report_from").val()+' '+'To'+' '+$("#report_to").val()+'</h4>'+
               '<h4 class="text-center">'+'Total: '+dTable.data().length+'</h4>'+
               '<h4 class="text-center">'+'Printed At: '+new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate()+'</h4><br>'
               ;

         // if ( printCounter === 1 ) {
         //     return $("#type").val();
         // }
         // else {
         //     return 'You have printed this document '+printCounter+' times';
         // }
     },
     messageBottom: null,
         exportOptions: {
           columns: [0,2,5,6,7,8,9,10],
           stripHtml: false
         },

       }
     ],

     columns: [
       { data: 'DT_RowIndex', name: 'DT_RowIndex' },
       { data: 'edit_jobcard', name: 'edit_jobcard' },
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
        $('td', row).eq(8).attr('id', data.associate_id+td_index+'_in');
        $('td', row).eq(9).attr('id', data.associate_id+td_index+'_out');
        $('td', row).eq(10).attr('id', data.associate_id+td_index+'_ot');
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

   dTable.MakeCellsEditable({
     "onUpdate": myCallbackFunction,
     "inputCss":'my-input-class',
     "columns": [8,9],
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
         "column": 8,
         "type": "text",
         "options":null
       },
       {
         "column": 9,
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
         console.log('failed...');
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
         console.log('failed...');
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
         console.log('failed...');
       }
     });
   });
   $('#type').on("change", function(){
     if($('#type').val() == 'Absent'){
       $('.ot').empty();
     }

   });
   $("#ot_hour").bind("keyup change", function(e) {

     var data =
     '<div class="form-group">'+
     '<label class="col-sm-5 control-label no-padding-right" for="condition"> Condition <span style="color: red; vertical-align: text-top;">*</span> </label>'+

     '<div class="col-sm-7 no-padding-right" >'+
     '<?php
      $conditions = ['Equal'=>'Equal','Less Than'=>'Less Than','Greater Than'=>'Greater Than'];
      ?>'+
     '{{ Form::select('condition', $conditions,null, ['placeholder'=>'Select Condition', 'id'=>'condition', 'style'=> 'width:100%', 'data-validation'=>'required',  'data-validation-error-msg'=>'This field is required']) }}'+
     '</div>'+
     '</div>';
     if($('#ot_hour').val() > 0 && $('#type').val() != 'Absent'){
       $('.ot').empty();
       $('.ot').append(data);
     }else{
       $('.ot').empty();
     }


   })
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
         console.log('failed...');
       }
     });
   });
   $('#dataTables').on('click', '.make-absent', function (e) {

     var associate_id = $(this).parent().parent().find('td').eq(2).html();
     var date = $(this).parent().parent().find('td').eq(7).html();
     var ths =$(this);
     var attdate = new Date(date);
     var currentdate = new Date();

     if((currentdate.getDate() <= lock || attdate.getMonth() == currentdate.getMonth() ) && (attdate.getMonth() == (currentdate.getMonth() == 0 ? 11 :currentdate.getMonth()-1) || attdate.getMonth() == currentdate.getMonth())){

       swal({
         title: "Are you sure?",
         icon: "warning",
         buttons: true,
         dangerMode: false,
       })
       .then((willDelete) => {
         if (willDelete) {
           $.ajax({
             url : "{{ url('hr/timeattendance/make_absent') }}",
             type: 'get',
             data: {
               associate_id: associate_id,
               date: date
             },
             success: function(data)
             {
                // console.log(data);
                if(data === 'success'){
                  toastr.success(' ','Attendance Updated Successfully.');
                   var row = ths.closest('tr');
                   row.fadeOut(400, function () {
                     $('#dataTables').DataTable().row(row).remove()
                   });
                 }else{
                    toastr.error('Please Try Again Later.','Something Went Wrong!!');
                 }
             },
             error: function()
             {
               toastr.error('Please Try Again Later.','Something Went Wrong!!');
             }
           });
         } else {

         }
       });
     }else{
       swal("Sorry!", "You can only change previous month's data on or before "+lock+" day of current month!","info")
     }
   });

   $('#dataTables').on('click', '.make-halfday', function () {

     var associate_id = $(this).parent().parent().find('td').eq(2).html();
     var date = $(this).parent().parent().find('td').eq(7).html();
     var ths =$(this);
     var attdate = new Date(date);
     var currentdate = new Date();

     if((currentdate.getDate() <= lock || attdate.getMonth() == currentdate.getMonth() ) && (attdate.getMonth() == (currentdate.getMonth() == 0 ? 11 :currentdate.getMonth()-1) || attdate.getMonth() == currentdate.getMonth())){
       swal({
         title: "Are you sure?",
         icon: "warning",
         buttons: true,
         dangerMode: false,
       })
       .then((willDelete) => {
         if (willDelete) {
           $.ajax({
             url : "{{ url('hr/timeattendance/make_halfday') }}",
             type: 'get',
             data: {
               associate_id: associate_id,
               date: date
             },
             success: function(data)
             {
                if(data === 'success'){
                  toastr.success(' ','Attendance Updated Successfully.');
                  var row = ths.closest('tr');

                  row.fadeOut(400, function () {
                    $('#dataTables').DataTable().row(row).remove()
                  });
                }else{
                  toastr.error('Please Try Again Later.','Something Went Wrong!!');
                }
             },
             error: function()
             {
               toastr.error('Please Try Again Later.','Something Went Wrong!!');
             }
           });
         } else {

         }
       });
     }else{
       swal("Sorry!", "You can only change previous month's data on or before 5th day of current month!","info")
     }
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
         console.log('failed...');
       }
     });
   });
   });
</script>
@endpush
@endsection
