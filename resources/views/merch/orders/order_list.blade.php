@extends('merch.index')
@push('css')
<style type="text/css">
    {{-- removing the links in print and adding each page header --}}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}

    </style>

@endpush
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li>
                <li>
                    <a href="#">Order</a>
                </li>
                <li class="active" > Order List</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="panel panel-info">
                    <div class="panel-body">
                        @include('inc/message')
                        <table id="example" class="display stripe row-border order-column custom-font-table table table-bordered" style="width:100%">
                            <thead>
                                <tr class="success">
                                    <th>Sl</th>
                                    <th>Order No</th>
                                    <th>Unit</th>
                                    <th>Buyer</th>
                                    <!-- <th>Brand</th> -->
                                    <th>Season</th>
                                    <th>Style</th>
                                    <th>Amount</th>
                                    <th>Del. Date</th>
                                    {{-- <th>PO</th> --}}
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@include('merch.common.list-page-freeze')
@push('js')

<script type="text/javascript">

$(document).ready(function(){

    var searchable = [6,7];
    var selectable = [3,4,5]; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {
        '2' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '3' :[@foreach($buyerList as $e) <?php echo "'$e'," ?> @endforeach],
        // '4' :[@foreach($brandList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($seasonList as $e) <?php echo "'$e'," ?> @endforeach],
        '5' :[@foreach($styleList as $e) <?php echo "'$e'," ?> @endforeach],
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
        // ordering:       false,
        fixedColumns:   {
            leftColumns: 0,
            rightColumns:1
        },
        ajax: {
            url: '{!! url("merch/orders/order_list_data") !!}',
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
                title: 'Order List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8]
                },
                header: false,
                footer:true

            },
            {
                extend: 'csv',
                className: 'btn-sm btn-success',
                title: 'Order List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8]
                },
                header: false,
                footer:true
            },
            {
                extend: 'excel',
                className: 'btn-sm btn-warning',
                title: 'Order List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8]
                },
                header: false,
                footer:true
            },
            {
                extend: 'pdf',
                className: 'btn-sm btn-primary',
                title: 'Order List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8]
                },
                header: false,
                footer:true
            },
            {
                extend: 'print',
                className: 'btn-sm btn-default',
                title: 'Order List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7,8],
                    stripHtml:false
                },
                footer: false
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'order_code', name: 'order_code' },
            { data: 'hr_unit_name', name: 'hr_unit_name' },
            { data: 'b_name',  name: 'b_name' },
            // { data: 'br_name', name: 'br_name' },
            { data: 'se_name', name: 'se_name' },
            { data: 'stl_no', name: 'stl_no' },
            { data: 'order_qty', name: 'order_qty' },
            { data: 'order_delivery_date', name: 'order_delivery_date' },
            // { data: 'po', name: 'po' },
            { data: 'created_by', name: 'created_by' },
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

            // each column select list
            api.columns(selectable).every( function (i, x) {
                var column = this;

                var select = $('<select style="width: 110px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val.toUpperCase().replace("'S","").replace( /&/g, '&amp;' ): '', true, false ).draw();
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
@endpush
@endsection
