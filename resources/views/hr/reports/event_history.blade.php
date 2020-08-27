@extends('hr.layout')
@section('title', 'Event History')
@push('css')
  <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.css')}}">
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
                <li class="active"> Event History</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                  <div class="iq-card">
                    
                    <div class="iq-card-body">
                       <table id="dataTables" class="table table-hover table-borderd table-head">
                          <thead>
                             <tr>
                                <th>SL</th>
                                <th>Associate ID</th>
                                <th>Type</th>
                                <th>Changed At</th>
                                <th>Changed By</th>
                                <th>Modified Status</th>
                                {{-- <th>Previous Event</th> --}}
                                <th>View</th>
                            </tr>
                          </thead>
                       </table>
                    </div>
                 </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- Modal -->
            
            <div class="item_details_section">
              <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
                <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
                  <div class="fade-box-details fade-box">
                    <div class="inner_gray clearfix">
                      <div class="inner_gray_text text-center" id="heading">
                       <h5 class="no_margin text-white">Change Details</h5>   
                      </div>
                      <div class="inner_gray_close_button">
                        <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
                      </div>
                    </div>

                    <div class="inner_body" id="modal-details-content" style="display: none">
                      <div class="inner_body_content">
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-3">Employee ID:</div>
                            <div class="col-sm-6" id="employee_id" style="font-weight: bold;">Enter</div>
                          </div>
                          <div class="row">
                              <div class="col-sm-12">
                              <table class="table table-bordered">
                                  <tr>
                                      <td></td>
                                      <td style="font-weight: bold;">Before Status</td>
                                      <td style="font-weight: bold;">After Status</td>
                                  </tr>
                                  <tr>
                                      <td style="font-weight: bold;">In Time</td>
                                      <td id="in_time_before"></td>
                                      <td id="in_time_after"></td>
                                  </tr>
                                  <tr>
                                      <td style="font-weight: bold;">Out Time</td>
                                      <td id="out_time_before"></td>
                                      <td id="out_time_after"></td>
                                  </tr>
                                  <tr>
                                      <td style="font-weight: bold;">OT Hour</td>
                                      <td id="ot_hour_before"></td>
                                      <td id="ot_hour_after"></td>
                                  </tr>
                                  <tr>
                                      <td style="font-weight: bold;">Late Status</td>
                                      <td id="late_status_before"></td>
                                      <td id="late_status_after"></td>
                                  </tr>
                              </table>
                              </div>
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
            <!--Modal end-->
        </div><!-- /.page-content -->
    </div>
</div>
   
@push('js')
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function()
{
    var searchable = [1,3,4,5];
    var selectable = [2];
    var dropdownList = {
        '2' :['In-time/Out-time Modify','Absent to Present','Present to Absent','Made Halfday'],
    }; 

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/reports/event_history_data") !!}',
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
               columns: [0,1,2,5,6,7,8,9],
               stripHtml: false
             },

           }
         ],
        columns: [ 
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, 
            { data: 'user_id',  name: 'user_id' }, 
            { data: 'type',  name: 'type' }, 
            { data: 'created_at', name: 'created_at' },
            { data: 'created_by', name: 'created_by' },
            { data: 'modified_event', name: 'modified_event' }, 
            // { data: 'previous_event', name: 'previous_event' }, 
            { data: 'action', name: 'action' }, 
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; height:25px; border:1px solid whitesmoke;');

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

                var select = $('<select style="width: 110px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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

    var loaderModal = '<td class="text-center" colspan="6"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:50px;"></i></td>';
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    
    $(document).on('click','.log-details',function(e){
        var id = $(this).data('book-id');
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        // console.log(id);
        $.ajax({
            url : "{{ url('hr/reports/event_history_detail') }}",
            type: 'get',
            data: {id:id},
            dataType: 'json',
            success: function(data)
            {
              // console.log(data);
                // if(typeof variable !== 'undefined')
                var before = JSON.parse(data.previous_event);
                var after = JSON.parse(data.modified_event);
                console.log(after['associate_id']);
                $('#employee_id').text(data.user_id);
                $('#in_time_before').text(before['in_time']);
                $('#in_time_after').text(after['in_punch_new']);
                $('#out_time_before').text(before['out_time']);
                $('#out_time_after').text(after['out_punch_new']);
                $('#ot_hour_before').text(before['ot_hour']);
                $('#ot_hour_after').text(after['ot_new']);
                $('#late_status_before').text(before['late_status']);
                $('#late_status_after').text(after['late_status']);
                
            }
        });
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "40%"
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

}); 
</script>
@endpush
@endsection