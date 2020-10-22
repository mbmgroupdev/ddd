@extends('hr.layout')
@section('title', 'Increment')
@section('main-content')
@push('css')
    <style type="text/css">
        {{-- removing the links in print and adding each page header --}}
        a[href]:after { content: none !important; }
        thead {display: table-header-group;}

        /*.form-group {overflow: hidden;}*/
        table.header-fixed1 tbody {max-height: 240px;  overflow-y: scroll;}

    </style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#"> Human Resource </a>
				</li> 
				<li>
					<a href="#"> Payroll </a>
				</li>
				<li class="active"> Increment </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/payroll/increment')}}" class="btn btn-sm btn-primary">Add Increment</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="panel panel-success">
                <div class="panel-body">
                    
                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th>Sl.</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Inc. Type</th>
                                <th>Inc. Amount</th>
                                <th>Effective Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
<script type="text/javascript"> 
$(document).ready(function(){
    var totalempcount = 0;
    var totalemp = 0;
    var searchable = [1,2];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {};
    var exportColName = ['Sl.','Associate ID','Name','Increment Type','Increment Amount'];
        var exportCol = [0,1,2,3,4,5];
    var dt =  $('#dataTables').DataTable({
           order: [], //reset auto order
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            ajax: {
                url: '{!! url("hr/payroll/increment-list-data") !!}',
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    "action": allExport,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    "action": allExport,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    "action": allExport,
                    className: 'btn-sm btn-primary',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    "action": allExport,
                    className: 'btn-sm btn-default print',
                    title: '',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: false
                    },
                    title: '',
                    messageTop: function () {
                        return  '<h3 class="text-center">Increment List</h3>';
                               
                    }

                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'associate_id',  name: 'associate_id' },
                { data: 'as_name', name: 'as_name'},
                { data: 'increment_type', name: 'increment_type'},
                { data: 'increment_amount',  name: 'increment_amount' },
                { data: 'effective_date',  name: 'effective_date' },
                { data: 'action',  name: 'action' }
            ],
            initComplete: function () {
                var api =  this.api();

                // Apply the search
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());

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

                    var select = $('<select><option value="">'+$(column.header()).text()+'</option></select>')
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