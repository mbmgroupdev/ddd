@extends('merch.layout')
@section('title', 'Style BOM')

@push('css')
<style type="text/css">
    {{-- removing the links in print and adding each page header --}}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}
    th{
        font-size: 12px;
        font-weight: bold;
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
                    <a href="#">Merchandising</a>
                </li>
                <li class="active">Reservation List</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <!-- page-content -->
        <div class="page-content"> 
            <!-- row -->
            <div class="row">
                <!-- Widget Header -->
                {{-- <div class="widget-header text-right">
                    <a type="button" class="btn btn-primary btn-xs" href="{{ url('merch/reservation/reservation') }}">Add Reservation</a>
                </div> --}}
                <!-- /.Widget Header-->
                <div class="panel panel-success">
                    <div class="panel-heading"><h6>Reservation List
                        <a href="{{ url('merch/reservation/reservation') }}" rel='tooltip' data-tooltip-location='left' data-tooltip='Add New Reservation' type="button" class="btn btn-info btn-xx pull-right">
                        <i class="glyphicon  glyphicon-plus"></i>
                        </a>
                    </h6>
                    </div>

                    <!-- Widget Body -->
                    <div class="panel-body">
                        @include('inc/message')
                            <table id="example" class="display stripe row-border order-column custom-font-table table table-bordered" style="width:100%">
                                <thead>
                                    <tr class="warning">
                                        <th>Sl</th>
                                        <th>Unit</th>
                                        <th>Buyer Name</th>
                                        <th>Month-Year</th>
                                        <th>Product Type</th>
                                        <th>SAH</th>
                                        <th>Projection</th>
                                        <th>Confirmed</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th width="150px">Action</th>
                                    </tr>
                                    
                                </thead>
                                 
                            </table>
                    </div><!-- /.Widget Body -->
                </div>
                

            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div>
</div>
@include('merch.common.list-page-freeze')
@push('js')
<script type="text/javascript">
$(document).ready(function(){
    var searchable = [3,5,6,7];
    var selectable = [1,2,4]; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {
        '1' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '2' :[@foreach($buyerList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($prdtypList as $e) <?php echo "'$e'," ?> @endforeach],
    };  

    $('#example').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        scrollY:        "450px",
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:   {
            leftColumns: 0,
            rightColumns:1
        },
        ajax: '{!! url("merch/reservation/reservation_list_data") !!}',
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Reservation List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
                footer:true,
                header:false
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Reservation List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
                footer: true,
                header:false
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Reservation List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
                footer: true,
                header:false
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Reservation List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
                footer: true,
                header:false
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Reservation List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8,9],
                    stripHtml: false
                },
                footer: false 
            } 
        ], 
        columns: [ 
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'hr_unit_name', name: 'hr_unit_name' }, 
            { data: 'b_name',  name: 'b_name' }, 
            { data: 'month_year', name: 'month_year' }, 
            { data: 'prd_type_name', name: 'prd_type_name' }, 
            { data: 'res_sah', name: 'res_sah' }, 
            { data: 'projection', name: 'projection' }, 
            { data: 'confirmed', name: 'confirmed' }, 
            { data: 'balance', name: 'balance' }, 
            { data: 'status', name: 'status',orderable: false, searchable: false }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this;  
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });
 
            //each column select list
            api.columns(selectable).every( function (i, x) {
                var column = this; 

                var select = $('<select style="width: 110px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;" ><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val : '', true, false ).draw();
                        e.stopPropagation();
                    });
                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                }); 
            });
        }  
    }); 
});
</script>
@endpush
@endsection
