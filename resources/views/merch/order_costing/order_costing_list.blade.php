@extends('merch.layout')
@section('title', 'Order Costing')
@section('main-content')
@push('css')
    <style>
       #dataTables thead input, #dataTables thead select {max-width: unset !important;}
        a[href]:after { content: none !important; }
        
        thead {display: table-header-group;}
        th{
            font-size: 12px;
            font-weight: bold;
        }
        #dataTables th:nth-child(2) input{
          width: 130px !important;
        }
        #dataTables th:nth-child(3) input{
          width: 150px !important;
        } 
        #dataTables th:nth-child(4) select{
          width: 80px !important;
        }
        #dataTables th:nth-child(5) select{
          width: 60px !important;
        }
        #dataTables th:nth-child(6) select{
          width: 70px !important;
        }
        #dataTables th:nth-child(7) input{
          width: 180px !important;
        }
        #dataTables th:nth-child(8) input{
          width: 90px !important;
        }
        #dataTables th:nth-child(9) {
          width: 100px !important;
        }
</style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="#">Merchandising</a>
              </li>
              <li>
                  <a href="#">Order</a>
              </li>
              <li class="active">Order Costing List</li>
              <li class="top-nav-btn">
                <a href="" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Order List</a> &nbsp;
                <a href="" target="_blank" class="btn btn-outline-success btn-sm pull-right"> <i class="fa fa-list"></i> Order BOM</a> &nbsp;
                
                </li>
          </ul><!-- /.breadcrumb -->

        </div>

		<div class="page-content">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="6%">SL</th>
                                        <th width="12%">Order No</th>
                                        <th width="15%">Unit</th>
                                        <th width="8%">Buyer</th>
                                        <th width="8%">Brand</th>
                                        <th width="8%">Season</th>
                                        <th width="15%">Style</th>
                                        <th width="8%">Amount</th>
                                        <th width="18%">Delivery Date</th>
                                        <th width="8%">Status</th>
                                        <th width="8%">Action</th>
                                    </tr>
                                </thead>

                            </table>
                            </table>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){

    var searchable = [1,6,7];
    var selectable = [2,3,4,5]; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {
        '2' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '3' :[@foreach($buyerList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($brandList as $e) <?php echo "'$e'," ?> @endforeach],
        '5' :[@foreach($seasonList as $e) <?php echo "'$e'," ?> @endforeach],
        '6' :[@foreach($styleList as $e) <?php echo "'$e'," ?> @endforeach],
        '9' :['Active','Approval Pending','Costed','Completed','Inactive'],
    };

    var exportColName = ['Order No','Unit','Buyer','Brand', 'Style No', 'Amount','Delivery Date', 'Status'];
        
    var exportCol = [1,2,3,4,5,6,7,8];
    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        language: {
          processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
        },
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers", 
        ajax: {
            url: '{!! url("merch/order_costing/order_costing_data") !!}',
            type: "GET",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        dom: "lBftrip",
        buttons: [   
            {
                extend: 'csv', 
                className: 'btn btn-sm btn-success',
                title: 'Order Costing List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: ''
            }, 
            {
                extend: 'excel', 
                className: 'btn btn-sm btn-warning',
                title: 'Order Costing List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: ''
            }, 
            {
                extend: 'pdf', 
                className: 'btn btn-sm btn-primary', 
                title: 'Order Costing List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: ''
            }, 
            {
                extend: 'print', 
                className: 'btn btn-sm btn-default',
                title: '',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: customReportHeader('Order Costing List', { })
            } 
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'order_code', name: 'order_code' },
            { data: 'hr_unit_name', name: 'hr_unit_name' },
            { data: 'b_name',  name: 'b_name' },
            { data: 'br_name', name: 'br_name' },
            { data: 'se_name', name: 'se_name' },
            { data: 'stl_no', name: 'stl_no' },
            { data: 'order_qty', name: 'order_qty' },
            { data: 'order_delivery_date', name: 'order_delivery_date' },
            { data: 'order_status', name: 'order_status' },
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
                        column.search(val ? '^'+val+'$' : '', true, false ).draw();
                        column.search(val ? val.toUpperCase().replace("'S","").replace( /&/g, '&amp;' ): '', true, false ).draw();
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
