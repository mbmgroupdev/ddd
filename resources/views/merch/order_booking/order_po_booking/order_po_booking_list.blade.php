@extends('merch.layout')
@section('title', 'Order Booking')

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
					<a href="#">Order Booking</a>
				</li>
				<li class="active">Order Booking List</li>
                <li class="top-nav-btn">
                    <a type="button" class="btn btn-primary btn-xs" href="{{ url('merch/order_po_booking/showForm') }}">Add Order Booking</a>
                </li>
			</ul><!-- /.breadcrumb -->
		</div>

        @include('inc/message')
        <div class="panel">
            <!-- Widget Body -->
            <div class="panel-body">

                <div class="worker-list">
                    <table id="dataTables" class="table table-striped table-bordered table-responsive" style="display: block; white-space: nowrap; overflow-x: auto; width: 100%;">
                        <thead>
                            <tr class="warning">
                                <th width="5%">Sl</th>
                                <th width="10%">Booking Reference</th>
                                <th>Buyer</th>
                                <th>Supplier</th>
                                <th>Unit</th>
                                {{-- <th>Order & PO</th> --}}
                                <th>Booking Quantity</th>
                                <th>Delivery Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div><!-- /.Widget Body -->
        </div><!-- /.row -->
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    var searchable = [1,2,3];
    // var selectable = [2];

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: '{!! url("merch/order_po_booking/getPoOrderListData") !!}',
        dom: "lBftrip",
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
            { data: 'booking_ref_no', name: 'booking_ref_no' },
            { data: 'buyer', name: 'buyer' },
            { data: 'supplier', name: 'supplier' },
            { data: 'unit', name: 'unit' },
            // { data: 'orderPo', name: 'orderPo' },
            { data: 'bookingQty', name: 'bookingQty' },
            { data: 'deliveryDate', name: 'deliveryDate' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        initComplete: function () {
            var api =  this.api();

            // Apply the search
            api.columns(searchable).every(function () {
                var column = this;
                var input = document.createElement("input");
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; height:40px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });

            //each column select list
            // api.columns(selectable).every( function (i, x) {
            //     var column = this;

            // var select = $('<select style="width: 110px; height:40px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;" ><option value="">'+$(column.header()).text()+'</option></select>')
            //     .appendTo($(column.header()).empty())
            //     .on('change', function(e){
            //         var val = $.fn.dataTable.util.escapeRegex(
            //             $(this).val()
            //         );
            //         column.search(val ? val : '', true, false ).draw();
            //         e.stopPropagation();
            //     });
            // });
        }
    });
});
</script>
@endsection
