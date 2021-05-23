@extends('merch.layout')
@section('title', 'Proforma Invoice List')
@section('main-content')
    @push('css')
        <style>
            a[href]:after { content: none !important; }
            thead {display: table-header-group;}
            th{
                font-size: 12px;
                font-weight: bold;
            }
            #example th:nth-child(2) input{
                width: 100px !important;
            }
            #example th:nth-child(3) input{
                width: 90px !important;
            }
            #example th:nth-child(5) select{
                width: 80px !important;
            }
            #example th:nth-child(6) select{
                width: 80px !important;
            }
            /*#example th:nth-child(7) select{
              width: 80px !important;
            }*/
            #example th:nth-child(7) input{
                width: 110px !important;
            }
            #example th:nth-child(8) input{
                width: 70px !important;
            }

            .text-warning {
                color: #c49090!important;
            }
            table.dataTable thead>tr>td.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc {
                padding-right: 16px;
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
					<a href="#">Proforma Invoice</a>
				</li>
				<li class="active">Proforma Invoice List</li>
                <li class="top-nav-btn">
                    <a class="btn btn-sm btn-primary" href="{{ url('merch/proforma_invoice/form') }}"><i class="las la-plus"></i>Add Proforma Invoice</a>
                </li>
			</ul><!-- /.breadcrumb -->
		</div>

        <div class="page-content">
            <div class="">
                @include('inc/message')
                <div class="panel panel-info">
                    <div class="panel-body">
                    <div class="worker-list">
                        <table id="dataTables" class="table table-striped table-bordered" style="white-space:nowrap">
                            <thead>
                                <tr class="warning">
                                    <th>Sl</th>
                                    <th>PI No</th>
                                    <th>Supplier</th>
                                    <th>Booking Ref</th>
                                    <th>PI Qty</th>
                                    <th>Catrgory</th>
                                    <th>Ship Mode</th>
                                    <th>PI Date</th>
                                    <th>PI Last Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div><!-- /.Widget Body -->
            </div><!-- /.row -->
		</div><!-- /.page-content -->
	</div>
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
        ajax: '{!! url("merch/proforma_invoice/getPIListData") !!}',
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
            { data: 'pi_no', name: 'pi_no' },
            { data: 'supplier', name: 'supplier' },
            { data: 'booking', name: 'booking' },
            { data: 'total_pi_qty', name: 'total_pi_qty'},
            { data: 'pi_category', name: 'pi_category' },
            { data: 'ship_mode', name: 'ship_mode' },
            { data: 'pi_date', name: 'pi_date' },
            { data: 'pi_last_date', name: 'pi_last_date' },
            { data: 'pi_status', name: 'pi_status' },
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
        }
    });
});
</script>
@endsection
