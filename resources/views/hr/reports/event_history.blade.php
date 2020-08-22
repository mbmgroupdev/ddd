
@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
<style type="text/css">
    {{-- removing the links in print and adding each page header --}}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}

    /*making place holder custom*/
    input::-webkit-input-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    input:-moz-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    input:-ms-input-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    th{
        font-size: 12px;
        font-weight: bold;
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
                <li class="active">Event History</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12 worker-list">
                    <!-- PAGE CONTENT BEGINS --> 
                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100%; overflow-x: auto;">
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

                    <!-- PAGE CONTENT ENDS -->
                </div>
                        <!-- Modal -->
                        <div id="myModal" class="modal fade" role="dialog">
                          <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Detail</h4>
                              </div>
                              <div class="modal-body">
                                    <div class="row">
                                      <div class="col-sm-2"></div>
                                      <div class="col-sm-2">Employee ID:</div>
                                      <div class="col-sm-6" id="employee_id" style="font-weight: bold;">Enter</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-offset-2 col-sm-8">
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
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!--Modal end-->
            </div>
            <br>
        </div><!-- /.page-content -->
    </div>
</div>
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
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Event History',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Event History',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Event History',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Event History',
                header: false,
                footer: true,
                pageSize: 'A4',
                orientation: 'landscape', 
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Event History',
                header: true,
                footer: false,
                pageSize: 'A4',
                orientation: 'landscape',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ],
                    stripHtml: false
                } 
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

});
</script>

<script type="text/javascript">
$(document).ready(function()
{
    $('#myModal').on('shown.bs.modal', function (e) {
      
        var id = $(e.relatedTarget).data('book-id');
        console.log(id);
        $.ajax({
            url : "{{ url('hr/reports/event_history_detail') }}",
            type: 'get',
            data: {id:id},
            dataType: 'json',
            success: function(data)
            {
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
    })
});
</script>
@endsection