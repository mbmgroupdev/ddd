@extends('merch.layout')
@section('title', 'PI List')


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
                    <a class="btn btn-sm btn-primary" href="{{ url('merch/proforma_invoice/form') }}"><i class="las la-plus"></i> Create PI</a>
                </li>
			</ul><!-- /.breadcrumb -->
		</div>

{{--		<div class="page-content">--}}
{{--            <div class="row">--}}
{{--                <!-- Widget Header -->--}}

{{--                <!-- Widget Body -->--}}
{{--                <div class="panel-body">--}}

{{--                    @include('inc/message')--}}
{{--                    <div class="widget-header text-right">--}}
{{--                        <a type="button" class="btn btn-primary btn-xs" href="{{ url('merch/proforma_invoice/form') }}">Add Proforma Invoice</a>--}}
{{--                    </div>--}}
{{--                    <br>--}}
{{--                    <div class="worker-list">--}}
        <div class="row">
            <div class="col-sm-12 col-lg-12">
{{--                <input type="hidden" value="1" id="lock_status">--}}
                <div class="table d-table ">
                    <div class="iq-card">
                        <div class="iq-card-body">
                            <div class="panel">
                                <div class="panel-heading">
                                    <h6>
                                        Proforma Invoice List
{{--                                        <a href="{{url('/pmddatadownload')}}" class="btn btn-primary pull-right">Create PI</a>--}}
                                    </h6>
                                </div>

{{--                                <div class="panel-heading">--}}
{{--                                    <h6 >PMD Data For H&M--}}
{{--                                        <a href="{{url('/pmddatadownload')}}" class="btn btn-primary pull-right">Download</a>--}}
{{--                                    </h6>--}}
{{--                                </div>--}}
                                <div class="panel-body">
                                    <table id="dataTables" class="table table-striped table-bordered"
                                            style="display: block;overflow-x: auto;width: 100%;" border="1">
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
        // dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp",
        dom: 'lBfrtip',
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
