@extends('hr.layout')
@section('title', '')
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
    .dataTables_wrapper .dt-buttons {
        text-align: center;
        padding-left: 425px;
    }
    .dataTables_length{
        float: left;
    }
    .dataTables_filter{
        float: right;
    }
    .dataTables_processing {
        top: 200px !important;
        z-index: 11000 !important;
        border: 0px !important;
        box-shadow: none !important;
        background: transparent !important;
    }
</style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Home</a>
				</li> 
				<li>
					<a href="#">Recruitment</a>
				</li>
				<li>
					<a href="#">Employer</a>
				</li>
				<li class="active">Employee Hierarchy</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content">  
 			<div class="panel panel-success">
              {{-- <div class="panel-heading"><h6>Employee Hierarchy</h6></div>  --}}
                <div class="panel-body">          
        			<div class="row"> 
        				<div class="col-xs-12 worker-list">
        					<table id="dataTables" class="table table-striped table-bordered">
        						<thead>
        							<tr>
        								<th>Name</th>
        								<th>Department</th>
        								<th>Designation</th> 
        								<th>Unit</th> 
        								<th>Employee Type</th>
        							</tr>
        						</thead>
        						<tfoot>
        							<tr>
        								<th>Name</th>
        								<th>Department</th>
        								<th>Designation</th> 
        								<th>Unit</th> 
        								<th>Employee Type</th>
        							</tr>
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
$(document).ready(function()
{  
	var searchable = [];
	var selectable = [4]; 
	var dropdownList = {
		'4' :[@foreach($employeeTypes as $e) <?php echo "'$e'," ?> @endforeach],
	};

    $('#dataTables').DataTable({
    	order: [],  
	    processing: true,
	    responsive: true,
	    serverSide: true,
        pagingType: "full_numbers",
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        }, 
        dom: "lBftrip", 
	    ajax: '{!! url("hr/recruitment/employee/hierarchy_data") !!}',
	    columns: [    
	        {data:'name',  name: 'name', orderable: false}, 
	        {data:'hr_department_name', name: 'hr_department_name', orderable: false}, 
	        {data:'hr_designation_name', name: 'hr_designation_name', orderable: false}, 
	        {data:'hr_unit_name', name: 'hr_unit_name', orderable: false}, 
	        {data:'hr_emp_type_name', name: 'hr_emp_type_name', orderable: false}, 
	    ],  
        buttons: [  
            {
            	extend: 'copy', 
            	className: 'btn-sm btn-info',
            	title: 'Emplyee Hierarchy List',
            	header: false,
            	footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
            	extend: 'csv', 
            	className: 'btn-sm btn-success',
            	title: 'Emplyee Hierarchy List',
            	header: false,
            	footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
            	extend: 'excel', 
            	className: 'btn-sm btn-warning',
            	title: 'Emplyee Hierarchy List',
            	header: false,
            	footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
            	extend: 'pdf', 
            	className: 'btn-sm btn-primary', 
            	title: 'Emplyee Hierarchy List',
            	header: false,
            	footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
            	extend: 'print', 
            	className: 'btn-sm btn-default',
            	title: 'Emplyee Hierarchy List',
            	header: true,
            	footer: false,
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                }
            } 
        ], 
        initComplete: function () {   
        	var api =  this.api();

			// Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

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

			    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
			        .appendTo($(column.header()).empty())
			        .on('change', function(e){
			            var val = $.fn.dataTable.util.escapeRegex(
			                $(this).val()
			            );
			            column.search(val ? val : '', true, false ).draw();
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
@endsection