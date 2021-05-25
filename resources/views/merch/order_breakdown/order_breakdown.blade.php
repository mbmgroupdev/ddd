@extends('merch.layout')
@section('title', 'Order Breakdown')

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



<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Order Break Down List</a>
				</li>
				<li class="active">Order Color & Size Break Down</li>
			</ul><!-- /.breadcrumb -->
		</div>
        @include('inc/message')
		<div class="page-content">
<div>
    <div class="panel panel-info">
        <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered" style="display: block; overflow-x: auto; width: 100%;" border="1">
                        <thead>
                        <tr>
                            <th >SL</th>
                            <th >Order No</th>
                            <th width="15%">Unit</th>
                            <th width="15%">Buyer</th>
                            <!-- <th width="15%">Brand</th> -->
                            <th width="15%">Season</th>
                            <th width="15%">Style No</th>
                            <th width="15%">Order Quantity</th>
                            <th width="15%">Delivery Date</th>
                            <th width="15%">Action</th>
                        </tr>
                        </thead>
                        
                    </table>
                
    
            </div>
            <!-- Display Erro/Success Message -->

            
        </div> {{-- panel-body --}}
        
    </div> {{-- panel panel-info --}}
</div>
        </div>{{-- end-page-content --}}


	{{-- end main-content-inner --}}
</div>  {{-- end main-content --}}



<script type="text/javascript">
	 $(document).ready(function(){

//            var searchable = [2,4,5];
//            var selectable = [1,3,6];

            $('#dataTables').DataTable({
                //reset auto order
                processing: true,
                responsive: false,
                serverSide: true,
                pagingType: "full_numbers",
               
                dom: "lBftrip",
                ajax: {
                    url: '{!!  url("merch/getOrder") !!}',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'order_code',  name: 'order_code'},
                    {data: 'hr_unit_name', name: 'hr_unit_name'},
                    {data: 'b_name', name: 'b_name'},
                    // {data: 'br_name',  name: 'br_name'},
                    {data: 'se_name', name: 'se_name'},
                    {data: 'stl_no', name: 'stl_no'},
                    {data: 'order_qty', name: 'order_qty'},
                    {data: 'order_delivery_date', name: 'order_delivery_date'},

                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn-sm btn-info',
                        title: 'Order Breakdown List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                        header: true,
                        footer: false
                    },
                    {
                        extend: 'csv',
                        className: 'btn-sm btn-success',
                        title: 'Order Breakdown List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                        header: true,
                        footer: false
                    },
                    {
                        extend: 'excel',
                        className: 'btn-sm btn-warning',
                        title: 'Order Breakdown List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                        header: true,
                        footer: false
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-sm btn-primary',
                        title: 'Order Breakdown List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                        header: true,
                        footer: false
                    },
                    {
                        extend: 'print',
                        autoPrint: true,
                        className: 'btn-sm btn-default',
                        title: 'Order Breakdown List',
                        exportOptions: {
                            // columns: ':visible'
                            columns: [0,1,2,3,4,5,6,7,8],
                            stripHtml: false
                        },
                        header: true,
                        footer: false
                    }
                ],

                initComplete: function () {
                    var api =  this.api();

//                    // Apply the search
//                    api.columns(searchable).every(function () {
//                        var column = this;
//                        var input = document.createElement("input");
//                        input.setAttribute('placeholder', $(column.header()).text());
//                        input.setAttribute('style', 'width: 110px; height:40px; border:1px solid whitesmoke;');
//
//                        $(input).appendTo($(column.header()).empty())
//                            .on('keyup', function () {
//                                column.search($(this).val(), false, false, true).draw();
//                            });
//
//                        $('input', this.column(column).header()).on('click', function(e) {
//                            e.stopPropagation();
//                        });
//                    });

//                    // each column select list
//                    api.columns(selectable).every( function (i, x) {
//                        var column = this;
//
//                        var select = $('<select style="width: 110px; height:40px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
//                            .appendTo($(column.header()).empty())
//                            .on('change', function(e){
//                                var val = $.fn.dataTable.util.escapeRegex(
//                                    $(this).val()
//                                );
//                                column.search(val ? val : '', true, false ).draw();
//                                e.stopPropagation();
//                            });
//
//                        $.each(dropdownList[i], function(j, v) {
//                            select.append('<option value="'+v+'">'+v+'</option>')
//                        });
//                    });
                }
            });
        });
</script>



@endsection


