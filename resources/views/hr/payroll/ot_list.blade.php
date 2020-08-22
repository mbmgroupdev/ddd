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
                <li>
                    <a href="#"> Payroll </a>
                </li>
                <li class="active"> OT List </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>OT List<a href="{{ url('hr/payroll/ot')}}" class="pull-right btn btn-xx btn-info">OT</a></h6>
                </div>
                <div class="panel-body">

                    <div class="row">
                        <!-- Display Erro/Success Message -->
                        <div class="col-xs-12  no-padding-left no-padding-right">
                            <!-- PAGE CONTENT BEGINS --> 
                            <div class="col-xs-12 worker-list">
                                <table id="dataTables" class="table table-striped table-bordered"  style="display:table;overflow-x: auto;white-space: nowrap; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>SL. No.</th>
                                            <th>Associate ID</th>
                                            <th>Date</th>
                                            <th>OT Hour(s)</th>
                                            <th>Remarks</th>
                                            <th>Created by</th>
                                            <th>Created at</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <!--  <tfoot>
                                       <tr>
                                           <th>SL. No.</th>
                                           <th>Associate ID</th>
                                           <th>Date</th>
                                           <th>OT Hour(s)</th>
                                           <th>Remarks</th>
                                           <th>Created by</th>
                                           <th>Created at</th>
                                           {{-- <th>Action</th> --}}
                                       </tr>
                                                                </tfoot>  -->
                                </table>
                            </div>

                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
            </div>
        </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{   
    var searchable = [1,2,3,4,5,6];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
 

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/payroll/ot_list_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Overtime List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Overtime List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Overtime List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Overtime List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Overtime List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, 
            { data: 'hr_ot_as_id',  name: 'hr_ot_as_id' }, 
            { data: 'hr_ot_date',  name: 'hr_ot_date' }, 
            { data: 'hr_ot_hour', name: 'hr_ot_hour' }, 
            { data: 'hr_ot_remarks', name: 'hr_ot_remarks' }, 
            { data: 'hr_ot_created_by', name: 'hr_ot_created_by' }, 
            { data: 'hr_ot_created_at', name: 'hr_ot_created_at' }
            // { data: 'action', name: 'action', orderable: false, searchable: false }
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


@endsection