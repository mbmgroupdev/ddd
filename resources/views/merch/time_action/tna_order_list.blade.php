@extends('merch.index')
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
                    <a href="#"> Time & Action </a>
                </li>
                  
                <li class="active">Order TNA List</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>Order TNA List</h6>
                </div>
                <div class="panel-body">
                    <div class="widget-header text-right">
                    <div class="col-sm-12">
                        <a href="{{ url('merch/time_action/tna_order') }}" class="btn btn-primary btn-xs">
                            New Order TNA
                        </a>
                        <a href="{{ url('merch/time_action/library') }}" class="btn btn-warning btn-xs">
                            Library
                        </a>
                        <a href="{{ url('merch/time_action/tna_template') }}" class="btn btn-info btn-xs" >
                           TNA Template
                        </a>
                      
                    </div>
                </div>
          
                                   
                <div class="row"> 
                    <div class="col-xs-12 worker-list">
                        <table id="dataTables" class="table table-striped table-bordered" style="display: block; overflow-x: auto; width: 100%; white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th width="15%">SL</th>
                                    <th width="15%">MBM Order</th>
                                    <th width="15%">Confirm Date</th>
                                    <th width="15%">Lead Days </th>
                                    <th width="15%">Tolerance Days</th>
                                    <th width="15%">TNA Type </th>
                                    <th width="15%">OK to Begin</th>
                                    <th width="15%">Rev OK to Begin </th>
                                    <th width="15%">Action</th>
                            </thead>  
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>MBM Order</th>
                                    <th>Confirm Date</th>
                                    <th>Lead Days </th>
                                    <th>Tolerance Days</th>
                                    <th>TNA Type </th>
                                    <th>OK to Begin</th>
                                    <th>Rev OK to Begin </th>
                                    <th>Action</th>
                            </tfoot>  
                        </table>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                </div>
                
            </div>

            
		</div><!-- /.page-content -->
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

    var searchable = [1,2,3,4,5,6,7];
    var selectable = [];

    var dropdownList = { 
        
        
    };

    $('#dataTables').DataTable({
    	order: [], //reset auto order
	    processing: true,
	    responsive: false,
	    serverSide: true,
        pagingType: "full_numbers",
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
	  
        ajax: {
            url: "{!! url('merch/time_action/tna_order_list_data') !!}",
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
	    columns: [   
	        {data: 'serial_no', name: 'serial_no'}, 
	        {data: 'order_code',  name: 'order_code'}, 
	        {data: 'confirm_date', name: 'confirm_date'}, 
	        {data: 'lead_days', name: 'lead_days'}, 
	        {data: 'tolerance_days',  name: 'tolerance_days'}, 
	        {data: 'tna_temp_name', name: 'tna_temp_name'},
            {data: 'begin_date', name: 'begin_date'}, 
            {data: 'revise_begin_date', name: 'revise_begin_date'},
	        {data: 'action', name: 'action', orderable: false, searchable: false}
	    ],  
        buttons: [  
            {
            	extend: 'copy', 
            	className: 'btn-sm btn-info',
                title: 'Order Time and Action(TNA) list',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7]
                },
                header: false, 
                footer: true 
            }, 
            {
            	extend: 'csv', 
            	className: 'btn-sm btn-success',
                title: 'Order Time and Action(TNA) list',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7]
                },
                header: false, 
                footer: true
            }, 
            {
            	extend: 'excel', 
            	className: 'btn-sm btn-warning',
                title: 'Order Time and Action(TNA) list',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7]
                },
                header: false, 
                footer: true 
            }, 
            {
            	extend: 'pdf', 
            	className: 'btn-sm btn-primary', 
                title: 'Order Time and Action(TNA) list',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7]
                },
                header: false, 
                footer: true
            }, 
            {
            	extend: 'print', 
            	className: 'btn-sm btn-default',
                title: 'Order Time and Action(TNA) list',
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4,5,6,7],
                    stripHtml: false
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
                input.setAttribute('style', 'width: 110px; height:40px; border:1px solid whitesmoke;');

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

                var select = $('<select style="width: 110px; height:40px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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
@endsection