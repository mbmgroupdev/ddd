@extends('merch.index')
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
@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li> 
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">PO Order BOM</a>
				</li>  
				<li class="active">PO Order BOM List</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content">  
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h5>PO Order BOM List</h5>
                </div>
                <div class="panel-body">  
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')

                    <div class="row"> 
                        <div class="col-xs-12 table-responsive"> 
                            <table id="example" class="display stripe row-border order-column custom-font-table table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Order No</th>
                                        <th>PO NO</th>
                                        {{-- <th>Mat. Category</th> --}}
                                        {{-- <th>Mat. Item</th> --}}
                                        <th>Color</th>
                                        <th>Qty</th>
                                        <th>Value</th>
                                        <th>Action</th>
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
@include('merch.common.list-page-freeze')
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 

    var searchable = [4,5];
    var selectable = [1,2,3]; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {
        '1' :[@foreach($orders as $o) <?php echo "'$o'," ?> @endforeach],
        '2' :[@foreach($po_nos as $o) <?php echo "'$o'," ?> @endforeach],
        '3' :[@foreach($colors as $o) <?php echo "'$o'," ?> @endforeach]
    }; 

    $('#example').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        // scrollY:        "300px",
        // scrollX:        true,
        // scrollCollapse: true,
        // ordering:       false,
        // fixedColumns:   {
        //     leftColumns: 0,
        //     rightColumns:1
        // },
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        ajax: {
            url: '{!! url("merch/orders/po_bom_list_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        columns: [   
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'order_code', name: 'order_code' }, 
            { data: 'po_no', name: 'po_no' }, 
            // { data: 'mcat_name',  name: 'mcat_name' }, 
            // { data: 'item_name', name: 'item_name' }, 
            { data: 'clr_name', name: 'clr_name' }, 
            { data: 'precost_req_qty', name: 'precost_req_qty' }, 
            { data: 'precost_value', name: 'precost_value' },  
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Order BOM List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5]
                },
                header: false, 
                footer: true 
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Order BOM List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5]
                },
                header: false, 
                footer: true
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Order BOM List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5]
                },
                header: false, 
                footer: true 
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Order BOM List',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5]
                },
                header: false, 
                footer: true
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: '',
                messageTop: function () {
                  //printCounter++;
                  return '<style>'+
                  'input::-webkit-input-placeholder {'+
                  'color: black;'+
                  'font-weight: bold;'+
                  'font-size: 12px;'+
                  '}'+
                  'input:-moz-placeholder {'+
                  'color: black;'+
                  'font-weight: bold;'+
                  'font-size: 12px;'+
                  '}'+
                  'input:-ms-input-placeholder {'+
                  'color: black;'+
                  'font-weight: bold;'+
                  'font-size: 12px;'+
                  '}'+
                  'th{'+
                  'font-size: 12px !important;'+
                  'color: black !important;'+
                  'font-weight: bold !important;'+
                  '}</style>'+
                  '<h2 class="text-center">MBM Group</h2>'+
                  '<h4 class="text-center">PO Wise BOM List</h4>'+
                  '<h6 style = "margin-left:80%;">'+'Printed on: '+new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate()+'</h6><br>'
                  ;

                },
                messageBottom: null,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5],
                    stripHtml:false

                },
                header: true, 
                footer: false 
            } 
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