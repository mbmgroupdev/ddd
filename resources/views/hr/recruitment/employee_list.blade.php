@extends('hr.layout')
@section('title', 'Employee List')
@section('main-content')
@push('css')
<style type="text/css">
    .dataTables_wrapper .dataTables_processing {
        position: absolute;
        top: 30%;
        left: 50%;
        width: 30%;
        height: 80px;
        margin-left: -20%;
        margin-top: -25px;
        padding-top: 20px;
        text-align: center;
        font-size: 1.7em;
        background-color:White;
    }
    /*.dataTables_wrapper .dt-buttons {
        float:right;
        text-align:center;
    }
    .dataTables_length{
        float:left;
    }
    .dataTables_filter{
        display: none;
    }*/
</style>
@endpush
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
					<i class="fa fa-home home-icon"></i>
					<a href="#">Home</a>
				</li>
				<li>
					<a href="#">Employee</a>
				</li>
				<li class="active">Employee List</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">
            @include('inc/message')
 			<div class="panel ">
                
                <div class="panel-body pb-0">
			 <!-- Display Erro/Success Message -->
					<form class="row" role="form" id="empFilter" method="get" action="#">
                        <div class="col-3">
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('unit', $allUnit, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'form-control']) }}
                                <label  for="unit"> Unit </label>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group has-float-label select-search-group">
                                <select name="otnonot" id="otnonot" class="form-control filter">
                                    <option value="">Select OT/Non-OT</option>
                                    <option value="0">Non-OT</option>
                                    <option value="1">OT</option>
                                </select>
                                <label  for="otnonot">OT/Non-OT </label>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group has-float-label select-search-group">
                                {{ Form::select('emp_type', $empTypes, null, ['placeholder'=>'Select Employee Type', 'id'=>'emp_type',  'class'=>'form-control']) }}
                                <label  for="emp_type"> Employee Type </label>
                            </div>
                        </div>

                        <div class="col-2">
                            <button type="button" id="" class="btn btn-primary  empFilter">
                            <i class="fa fa-search"></i>
                            Search
                            </button>
                        </div>
		            </form>
		        </div>
		    </div>

			<div class="panel ">	
				<div class="panel-heading"><h6>Employee List</h6></div> 	
				<div class="col-12 pt-3 pb-3">
					<div class="widget-header">
						<div class="row">
							<ul class="list-unstyled col-4">
								<li><strong>Total Employee:</strong>
								{{ ($reportCount->employee->total?$reportCount->employee->total:0) }}</li>
								<li><strong>Today's Join:</strong>
								{{ ($reportCount->employee->todays_join?$reportCount->employee->todays_join:0) }}</li>
							</ul>
							<ul class="list-unstyled col-4">
								<li><strong>Males:</strong>
								{{ ($reportCount->employee->males?$reportCount->employee->males:0) }}</li>
								<li><strong>Females:</strong>
								{{ ($reportCount->employee->females?$reportCount->employee->females:0) }}</li>
							</ul>
							<ul class="list-unstyled col-4">
								<li><strong>Non OT:</strong>
								{{ ($reportCount->employee->non_ot?$reportCount->employee->non_ot:0) }}</li>
								<li><strong>OT:</strong>
								{{ ($reportCount->employee->ot?$reportCount->employee->ot:0) }}</li>
							</dl>
						</div>
					</div>
				</div>
				
				<div class="col-12 worker-list">
					<table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
						<thead>
							<tr>
								<th>ID</th>
								<th>Action</th>
								<th>Status</th>
								<th>Associate ID</th>
								<th>Oracle ID</th>
								<th>RFID</th>
								<th>Name</th>
								<th>Employee Type</th>
								<th id="floor">Floor</th>
								<th id="line">Line</th>
								<th>Department</th>
								<th>Designation</th>
								<th>Gender</th>
								<th>OT Status</th>
							</tr>
						</thead>
		
					</table>
				</div>
			</div>

		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function()
{
	
		var searchable = [3,4,5,6];
		var selectable = [2,7,8,9,10,12,13]; 

		var dropdownList = {
			'2':['Active', 'Resign', 'Terminate', 'Suspend','Left'],
			'7' :[@foreach($employeeTypes as $emp) <?php echo "'$emp'," ?> @endforeach],
			'8' :[@foreach($floorList as $floor) <?php echo "'$floor'," ?> @endforeach],
			'9' :[@foreach($lineList as $e) <?php echo "'$e'," ?> @endforeach],
			'10' :[@foreach($departmentList as $e) <?php echo "'$e'," ?> @endforeach],
			'12':['Female','Male'],
			'13':['OT','Non OT']
		};

		$("#unit").change(function(){
		  var unit=$(this).val();
		  $.ajax({
            url : "{{ url('hr/recruitment/employee/dropdown_data') }}",
            type: 'get',
            data: {
              unit: unit      
            },
            success: function(data)
            {  
              	dropdownList[8] = data.floorList;
              	dropdownList[9] = data.lineList;
            },
            error: function()
            {
              alert('Please Select Unit');
            }
          });
		});
		

		

	    var dTable = $('#dataTables').DataTable({

	    	order: [],
	    	lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		    processing: true,
		    responsive: false,
		    serverSide: true,
	        processing: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
	        pagingType: "full_numbers",
	        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>tip",
	        //dom: 'lBfrtip',
	        ajax: {
	            url: '{!! url("hr/recruitment/employee/employee_data") !!}',
	            type: "get",
	            data: function (d) {
	                d.unit  = $('#unit').val(),
	                d.emp_type = $('#emp_type').val(),
	                d.otnonot = $('#otnonot').val()
	            },
	            headers: {
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	            }
	        },
		    columns: [
		        {data:'serial_no', name: 'serial_no'},
		        {data:'action', name: 'action', orderable: false, searchable: false},
		        {data:'as_status', name: 'as_status'},
		        {data:'associate_id', name: 'associate_id'},
		        {data:'as_oracle_code', name: 'as_oracle_code'},
		        {data:'as_rfid_code', name: 'as_rfid_code'},
		        {data:'as_name',  name: 'as_name'},
		        {data:'hr_emp_type_name', name: 'hr_emp_type_name', orderable: false},
		        {data:'hr_floor_name', name: 'hr_floor_name', orderable: false},
		        {data:'hr_line_name',  name: 'hr_line_name', orderable: false},


		        {data:'hr_department_name',  name: 'hr_department_name', orderable: false},
		        {data:'hr_designation_name', name: 'hr_designation_name', orderable: false},

		        {data:'as_gender', name: 'as_gender', orderable: false,},
		        {data:'as_ot', name: 'as_ot', orderable: false}



		    ],
	        buttons: [
	            {
	            	extend: 'copy',
	            	className: 'btn-sm btn-info',
	            	title: 'Employee List',
	            	header: false,
	            	footer: true,
	                exportOptions: {
	                    // columns: ':visible'
	                    columns: [0,2,3,4,5,6,7,8,9,10,11,12,13]
	                }
	            },
	            {
	            	extend: 'csv',
	            	className: 'btn-sm btn-success',
	            	title: 'Employee List',
	            	header: false,
	            	footer: true,
	                exportOptions: {
	                    // columns: ':visible'
	                    columns: [0,2,3,4,5,6,7,8,9,10,11,12,13]
	                }
	            },
	            {
	            	extend: 'excel',
	            	className: 'btn-sm btn-warning',
	            	title: 'Employee List',
	            	header: false,
	            	footer: true,
	                exportOptions: {
	                    // columns: ':visible',
	                    columns: [0,2,3,4,5,6,7,8,9,10,11,12,13]
	                }
	            },
	            {
	            	extend: 'pdf',
	            	className: 'btn-sm btn-primary',
	            	title: 'Employee List',
	            	pageSize: 'A2',
	            	header: false,
	            	footer: true,
	                exportOptions: {
	                    // columns: ':visible'
	                    columns: [0,2,3,4,5,6,7,8,9,10,11,12,13]
	                }
	            },
	            {
	            	extend: 'print',
	            	className: 'btn-sm btn-default',
	            	title: 'Employee List',
	            	// orientation:'landscape',
	            	pageSize: 'A2',
	            	header: true,
	            	footer: false,
	            	orientation: 'landscape',
	                exportOptions: {
	                    // columns: ':visible',
	                    columns: [0,2,3,4,5,6,7,8,9,10,11,12,13],
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
		    	//console.log(dropdownList);
				api.columns(selectable).every( function (i, x) {
				    var column = this;

				    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
				        .appendTo($(column.header()).empty())
				        .on('change', function(e){
				            var val = $.fn.dataTable.util.escapeRegex(
				                $(this).val()
				            );
				            column.search(val ? '^'+val+'$' : '', true, false ).draw();
				            e.stopPropagation();
				        });

					// column.data().unique().sort().each( function ( d, j ) {
					// if(d) select.append('<option value="'+d+'">'+d+'</option>' )
				 	// });
				 	// setTimeout(function(){ 

					$.each(dropdownList[i], function(j, v) {
						select.append('<option value="'+v+'">'+v+'</option>')
					});
				// }, 1000);
				});
	        }
		});
	//});


	//re draw

	$(document).on("click",'#empFilter', function(e){
		e.preventDefault();
		dTable.draw();
	});
});
</script>
@endpush
@endsection
