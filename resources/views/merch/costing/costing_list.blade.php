@extends('merch.layout')
@section('title', 'Costing Compare')
@section('main-content')
    @push('css')
        <style>
            a[href]:after {
                content: none !important;
            }

            thead {
                display: table-header-group;
            }

            th {
                font-size: 12px;
                font-weight: bold;
            }

            #example th:nth-child(2) input {
                width: 100px !important;
            }

            #example th:nth-child(3) input {
                width: 90px !important;
            }

            #example th:nth-child(5) select {
                width: 80px !important;
            }

            #example th:nth-child(6) select {
                width: 80px !important;
            }

            /*#example th:nth-child(7) select{
              width: 80px !important;
            }*/
            #example th:nth-child(7) input {
                width: 110px !important;
            }

            #example th:nth-child(8) input {
                width: 70px !important;
            }

            .text-warning {
                color: #c49090 !important;
            }

            table.dataTable thead > tr > td.sorting, table.dataTable thead > tr > td.sorting_asc, table.dataTable thead > tr > td.sorting_desc, table.dataTable thead > tr > th.sorting, table.dataTable thead > tr > th.sorting_asc, table.dataTable thead > tr > th.sorting_desc {
                padding-right: 16px;
            }
        </style>
    @endpush
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#"> Merchandising </a>
                    </li>
                    <li>
                        <a href="#"> Costing Compare </a>
                    </li>

                    <li class="active">Costing List</li>
                </ul><!-- /.breadcrumb -->
            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            @include('inc/message')
                            <div class="worker-list row">
                                <div class="col-sm-12">
                                    <table id="dataTables" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Order No</th>
                                            <th>Unit</th>
                                            <th>Buyer</th>
                                            <th>Style</th>
                                            <th>Amount</th>
                                            <th>Style Cost</th>
                                            <th>Order Cost</th>
                                            <th>PO Cost</th>
                                            <th>PI Cost</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div><!-- /.Widget Body -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            var searchable = [1, 7, 8];
            var selectable = [2, 3, 4]; //use 4,5,6,7,8,9,10,11,....and * for all
            var dropdownList = {
                '2': [@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
                '3': [@foreach($buyerList as $e) <?php echo "'$e'," ?> @endforeach],
                '4': [@foreach($styleList as $e) <?php echo "'$e'," ?> @endforeach]
            };

            $('#dataTables').DataTable({
                order: [], //reset auto order
                processing: true,
                responsive: false,
                serverSide: true,
                pagingType: "full_numbers",
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
                ajax: {
                    url: '{!! url("merch/costing/list-data") !!}',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'order_code', name: 'order_code'},
                    {data: 'unit.hr_unit_name', name: 'unit.hr_unit_name'},
                    {data: 'buyer.b_name', name: 'buyer.b_name'},
                    {data: 'style.stl_no', name: 'style.stl_no'},
                    {data: 'order_qty', name: 'order_qty'},
                    {data: 'style_cost', name: 'style_cost'},
                    {data: 'order_cost', name: 'order_cost'},
                    {data: 'po_cost', name: 'po_cost'},
                    {data: 'pi_cost', name: 'pi_cost'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn-sm btn-info',
                        title: 'Order Costing List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'csv',
                        className: 'btn-sm btn-success',
                        title: 'Order Costing List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'excel',
                        className: 'btn-sm btn-warning',
                        title: 'Order Costing List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-sm btn-primary',
                        title: 'Order Costing List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'print',
                        className: 'btn-sm btn-default',
                        title: 'Order Costing List',
                        orientation: 'landscape',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            stripHtml: false
                        },
                        header: true,
                        footer: false
                    }
                ],
                initComplete: function () {
                    var api = this.api();

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

                        $('input', this.column(column).header()).on('click', function (e) {
                            e.stopPropagation();
                        });
                    });

                    // each column select list
                    api.columns(selectable).every(function (i, x) {
                        var column = this;

                        var select = $('<select style="width: 110px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">' + $(column.header()).text() + '</option></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function (e) {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                                column.search(val ? val : '', true, false).draw();
                                e.stopPropagation();
                            });

                        $.each(dropdownList[i], function (j, v) {
                            select.append('<option value="' + v + '">' + v + '</option>')
                        });
                    });
                }
            });
        });
    </script>
@endsection
