@extends('hr.layout')
@section('title', 'Line Change')

@section('main-content')
@push('css')
  <style>
    #dataTables th:nth-child(7) input{
      width: 100px !important;
    }
    #dataTables th:nth-child(8) input{
      width: 100px !important;
    }
    .modal-h3{
      line-height: 15px !important;
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Line Change Reports</li>
            </ul>
        </div>

        <div class="page-content"> 
            <input type="hidden" id="month_year" value="{{ $input['month_year']??date('Y-m')}}">
            <div class="row">
                <div class="col">
                  <div class="iq-card">
                    <div class="iq-card-header d-flex mb-0">
                     <div class="iq-header-title w-100">
                        <div class="row">
                          <div class="col-3">
                              <div class="action-section">
                                  
                              </div>
                          </div>
                          <div class="col-6 text-center">
                            <h4 class="card-title capitalize inline">
                              @php
                                  $associate = request()->associate;
                                  $nextMonth = date('Y-m', strtotime($input['month_year'].' +1 month'));
                                  $prevMonth = date('Y-m', strtotime($input['month_year'].' -1 month'));

                                  $prevUrl = url("hr/reports/line-changes?month_year=$prevMonth");
                                  $nextUrl = url("hr/reports/line-changes?month_year=$nextMonth");
                                  $month = date('Y-m');
                              @endphp
                              <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month Line Change Report" >
                                <i class="las la-chevron-left"></i>
                              </a>

                              <b class="f-16" id="result-head">{{ date('M Y', strtotime($input['month_year'])) }} </b>
                              
                              <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Month Line Change Report" >
                                <i class="las la-chevron-right"></i>
                              </a>
                              
                            </h4>
                          </div>
                          @php 
                              $yearMonth = $input['month_year']; 
                          @endphp
                          <div class="col-3">
                            
                          </div>
                        </div>
                     </div>
                  </div>
                  </div>
                  <div class="table d-table">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head w-100" >
                             <thead>
                                <tr>
                                  <th>Sl.</th>
                                  <th>Picture</th>
                                  <th>Associate ID</th>
                                  <th>Unit</th>
                                  <th>Name - Contact</th>
                                  <th>Current Floor - Line</th>
                                  <th>Changed Floor</th>
                                  <th>Changed  Line</th>
                                  <th>Start Date</th>
                                  <th>End Date</th>
                                  <th width="12%">&nbsp;</th>
                                </tr>
                             </thead>
                          </table>
                       </div>
                     </div>
                   </div>
                </div>
            </div>
        </div><!-- /.page-content -->
        {{-- modal --}}
        <div class="item_details_section">
            <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
              <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
                <div class="fade-box-details fade-box">
                  <div class="inner_gray clearfix">
                    <div class="inner_gray_text text-center" id="heading">
                     <h5 class="no_margin text-white">Back to the previous Line</h5>   
                    </div>
                    <div class="inner_gray_close_button">
                      <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
                    </div>
                  </div>

                  <div class="inner_body" id="modal-details-content" style="display: none">
                    <div class="inner_body_content">
                        <div class="body_top_section">
                          <h3 class="text-center modal-h3"><strong>Name :</strong> <b id="eName"></b></h3>
                          <h3 class="text-center modal-h3"><strong>Id :</strong> <b id="eId"></b></h3>
                          <h3 class="text-center modal-h3"><strong>Floor :</strong> <b id="efloor"></b></h3>
                          <h3 class="text-center modal-h3"><strong>Line :</strong> <b id="eline"></b></h3>
                          <br>
                        </div>
                        <div class="body_content_section">
                          <div class="body_section">
                            {{ Form::open(['url'=>'hr/operation/line-change-close', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                              @csrf
                              <input type="hidden" name="station_id" id="sId" value="">
                              <div class="form-group has-float-label">
                                <input type="datetime-local" name="end_date" id="end_date"  class="datetimepicker form-control" placeholder="End Date" value="{{ date('Y-m-d') }}T{{ date('H:i')}}">
                                <label for="shift_id">End Date </label>
                              </div> 
                              <div class="form-group">
                                  <button class="btn btn-primary" type="submit">
                                      <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                  </button>
                              </div>
                            {{ Form::close() }}
                          </div>
                        </div>
                    </div>
                    <div class="inner_buttons">
                      <a class="cancel_modal_button cancel_details" role="button"> Close </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function(){   
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';

    var searchable = [2,6,7,8,9];
    // var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {};
    var printCounter = 0;
    var dTable =  $('#dataTables').DataTable({

      order: [[ 8, "asc" ]], //reset auto order
      lengthMenu: [[25, 50, 100, -1], [25, 50, 100,"All"]],
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
        url: '{!! url("hr/reports/line-changes-data") !!}',
        data: function (d) {
          
          d.month_year = $("#month_year").val()

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
            columns: [0,1,3,4,5,6,7,8],
            stripHtml: false
          },
        }
      ],

      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'pic', name: 'pic' },
        { data: 'associate_id',  name: 'associate_id' },
        { data: 'hr_unit_name',  name: 'hr_unit_name' },
        { data: 'as_name', name: 'as_name' },
        { data: 'current_line', name: 'current_line' },
        { data: 'changed_floor', name: 'changed_floor' },
        { data: 'changed_line', name: 'changed_line' },
        { data: 'start_date', name: 'start_date' },
        { data: 'end_date', name: 'end_date' },
        { data: 'action', name: 'action' },
        

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
          .on('keyup', function (e) {
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
 });
  
  $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
  $(".overlay-modal, .item_details_dialog").removeAttr("style");
  /*Set min height to 90px after  has been set*/
  detailsheight = $(".item_details_dialog").css("min-height", "115px");
  var months    = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
  $(document).on('click','.changed-action',function(){
    
    let id = $(this).data('id');
    let associateId = $(this).data('asid');
    let name = $(this).data('ename');
    let line = $(this).data('line');
    let floor = $(this).data('floor');

    $("#sId").val(id);
    $("#eName").html(name);
    $("#eId").html(associateId);
    $("#efloor").html(floor);
    $("#eline").html(line);
    /*Show the dialog overlay-modal*/
    $(".overlay-modal-details").show();
    $(".inner_body").show();
    
    /*Animate Dialog*/
    $(".show_item_details_modal").css("width", "225").animate({
      "opacity" : 1,
      height : detailsheight,
      width : "30%"
    }, 600, function() {
      /*When animation is done show inside content*/
      $(".fade-box").show();
    });
      // 
      
  });

  $(".cancel_details").click(function() {
      $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
        /*Remove inline styles*/

        $(".overlay-modal, .item_details_dialog").removeAttr("style");
        $('body').css('overflow', 'unset');
      });
  });

</script>
@endpush
@endsection