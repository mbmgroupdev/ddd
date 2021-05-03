@extends('hr.layout')
@section('title', 'Employee List')
@section('main-content')
@push('css')
<style type="text/css">
	#dataTables tr td:nth-child(2){
		display: block;
	    width: 70px !important;
	}
	#dataTables tr th:nth-child(3) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(6) input{
		width: 70px !important;
	}
	#dataTables tr th:nth-child(7) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(8) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(9) select{
		width: 70px !important;
	}
	#dataTables tr th:nth-child(10) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(11) select{
		width: 70px !important;
	}
	#dataTables tr th:nth-child(12) select{
		width: 60px !important;
	}
	#dataTables tr th:nth-child(13) select{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(14) select{
		width: 120px !important;
	}
	#dataTables tr th:nth-child(15) select{
		width: 180px !important;
	}
	#dataTables tr th:nth-child(16) select{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(17) select{
		width: 60px !important;
	}
	#dataTables tr th:nth-child(19) select{
		width: 60px !important;
	}
	#dataTables tr th:nth-child(18) input{
		width: 100px !important;
	}
	#dataTables tr th:nth-child(20) input{
		width: 50px !important;
	}
	#dataTables tr th:nth-child(21) input{
		width: 150px !important;
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
				<li class="top-nav-btn">
					{{-- <button class="btn btn-sm btn-primary" id="employee_summary" data-type="employee">
						<i class="fa fa-grid"></i>
					</button> --}}
				</li>
			</ul><!-- /.breadcrumb -->
		</div>

        @include('inc/message')
		<div class="page-content">
 			<div class="panel ">
                
                <div class="panel-body pb-0">
			 <!-- Display Erro/Success Message -->
					<form class="mb-2" role="form" id="empFilter" method="get" action="#">
						<div class="row">

	                        <div class="col-2">
	                            <div class="form-group has-float-label has-required select-search-group">
	                                {{ Form::select('unit', $allUnit, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'form-control']) }}
	                                <label  for="unit"> Unit </label>
	                            </div>
	                        </div>

	                        <div class="col-2">
	                            <div class="form-group has-float-label select-search-group">
	                                <select name="otnonot" id="otnonot" class="form-control filter">
	                                    <option value="">Select OT/Non-OT</option>
	                                    <option value="0">Non-OT</option>
	                                    <option value="1">OT</option>
	                                </select>
	                                <label  for="otnonot">OT/Non-OT </label>
	                            </div>
	                        </div>

	                        <div class="col-2">
	                            <div class="form-group has-float-label select-search-group">
	                                {{ Form::select('emp_type', $empTypes, null, ['placeholder'=>'Select Employee Type', 'id'=>'emp_type',  'class'=>'form-control']) }}
	                                <label  for="emp_type"> Employee Type </label>
	                            </div>
	                        </div>
	                        <div class="col-2">
	                            <div class="form-group has-float-label">
	                                <input type="date" name="doj_from" class="form-control" id="doj_from">
	                                <label  for="doj_from"> DOJ From</label>
	                            </div>
	                        </div>
	                        <div class="col-2">
	                            <div class="form-group has-float-label">
	                                <input type="date" name="doj_to" class="form-control" id="doj_to">
	                                <label  for="doj_to"> DOJ To</label>
	                            </div>
	                        </div>
	                        <div class="col-2">
	                        	@php
	                        		$status = [
	                        			1 => 'Active',
	                        			6 => 'Maternity',
	                        		];
	                        	@endphp
	                            <div class="form-group has-float-label select-search-group">
	                                {{ Form::select('as_status', $status, 1, ['placeholder'=>'Select Employee Type', 'id'=>'as_status',  'class'=>'form-control']) }}
	                                <label  for="as_status"> Status </label>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row ">
	                        <div class="col-2 pull-right">
	                            <button type="button" id="" class="btn btn-primary  empFilter">
	                            <i class="fa fa-search"></i>
	                            Search
	                            </button>
	                        </div>
	                    </div>
		            </form>
		        </div>
		    </div>
		    

			<div class="panel ">	
				<div class="panel-heading"><h6>Employee List</h6></div> 	
				
				
				<div class="col-12 worker-list pb-3 pt-3">
					<table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;" border="1">
						<thead>
							<tr>
								<th>Sl.</th>
								<th>Action</th>
								<th>Associate ID</th>
								<th>Name</th>
								<th>Designation</th>
								<th>Oracle ID</th>
								<th>RFID</th>
								<th>Date of Join</th>
								<th>Unit</th>
								<th>Location</th>
								<th id="floor">Floor</th>
								<th id="line">Line</th>
								<th>Department</th>
								<th>Section</th>
								<th>Sub Section</th>
								<th>Employee Type</th>
								<th>Gender</th>
								<th>Date Of Birth</th>
								<th>OT Status</th>
								<th>Grade</th>
								<th>Default Shift</th>
								<th>Last Education</th>
								<th>Present Address</th>
								<th>Permanent Address</th>
								<th>Permanent District</th>
								<th>Salary</th>
							</tr>
						</thead>
		
					</table>
				</div>
			</div>

		</div><!-- /.page-content -->
	</div>
</div>

{{-- 	include summary --}}

@include('hr.employee.summary.summary-modal')
@push('js')
<script type="text/javascript">
$(document).ready(function()
{
	
		var searchable = [2,3,4,5,6,7,9,15,17,19,20];
		var selectable = [8,10,11,12,13,14,15,16,18]; 

		var dropdownList = {
			'15' :[@foreach($employeeTypes as $emp) <?php echo "'$emp'," ?> @endforeach],
			'10' :[@foreach($floorList as $floor) <?php echo "'$floor'," ?> @endforeach],
			'11' :[@foreach($lineList as $e) <?php echo "'$e'," ?> @endforeach],
			'12' :[@foreach($departmentList as $e) <?php echo "'$e'," ?> @endforeach],
			'13' :[@foreach($sectionList as $e) <?php echo "'$e'," ?> @endforeach],
			'14' :[@foreach($subSectionList as $e) <?php echo "'$e'," ?> @endforeach],
			'8' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
			'16':['Female','Male'],
			'18':['OT','Non OT']
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
		

		
		var exportColName = ['Sl.','','Associate ID','Name','Designation','Oracle ID','RFID','Date of Join','Unit','Location',  'Floor','Line','Department','Section','Subsection','Employee Type','Gender','Date Of Birth','OT Status','Grade','Shift', 'Education', 'Present Address', 'Permanent Address', 'Permanent District', 'Salary'];
      	var exportCol = [2,3,4,5,10,11,12,13,14,15, 16, 17, 18,19,20,21,22,23,24];

	    var dt = $('#dataTables').DataTable({

	    	order: [],
	    	lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		    processing: true,
		    responsive: false,
		    serverSide: true,
	        processing: true,
            language: {
              processing: '<i class="fa fa-spinner fa-spin f-60" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
	        pagingType: "full_numbers",
	        dom: "lBftrip",
	        //dom: 'lBfrtip',
	        ajax: {
	            url: '{!! url("hr/recruitment/employee/employee_data") !!}',
	            type: "get",
	            data: function (d) {
	                d.unit  = $('#unit').val(),
	                d.emp_type = $('#emp_type').val(),
	                d.as_status = $('#as_status').val(),
	                d.otnonot = $('#otnonot').val(),
	                d.doj_from = $('#doj_from').val(),
	                d.doj_to = $('#doj_to').val()
	            },
	            headers: {
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	            }
	        },
		    columns: [
		        {data:'DT_RowIndex', name: 'DT_RowIndex'},
		        {data:'action', name: 'action', orderable: false, searchable: false},
		        {data:'associate_id', name: 'associate_id'},
		        {data:'as_name',  name: 'as_name'},
		        {data:'hr_designation_name', name: 'hr_designation_name', orderable: false},
		        {data:'as_oracle_code', name: 'as_oracle_code'},
		        {data:'as_rfid_code', name: 'as_rfid_code'},
		        {data:'as_doj', name: 'as_doj'},
		        {data:'hr_unit_short_name', name: 'hr_unit_short_name', orderable: false},
		        {data:'hr_location_name', name: 'hr_location_name', orderable: false},
		        {data:'hr_floor_name', name: 'hr_floor_name', orderable: false},
		        {data:'hr_line_name',  name: 'hr_line_name', orderable: false},
		        {data:'hr_department_name',  name: 'hr_department_name', orderable: false},
		        {data:'hr_section_name',  name: 'hr_section_name', orderable: false},
		        {data:'hr_subsec_name',  name: 'hr_subsec_name', orderable: false},
		        {data:'hr_emp_type_name', name: 'hr_emp_type_name', orderable: false},
		        {data:'as_gender', name: 'as_gender', orderable: false,},
		        {data:'age', name: 'age', orderable: false,},
		        {data:'as_ot', name: 'as_ot', orderable: false},
		        {data:'hr_designation_grade', name: 'hr_designation_grade'},
		        {data:'as_shift_id', name: 'as_shift_id', orderable: false},
		        {data:'education', name: 'education'},
		        {data:'present_address', name: 'present_address'},
		        {data:'permanent_address', name: 'permanent_address'},
		        {data:'permanent_dist', name: 'permanent_dist'},
		        {data:'salary', name: 'salary'}

		    ],
	        buttons: [   
              {
                  extend: 'csv', 
                  className: 'btn btn-sm btn-success',
                  title: 'Employee list',
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
                  title: 'Employee list',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19, 20, 21,22,23,24,25],
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
                  title: 'Employee list',
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
                  messageTop: function () {
                  	  var unit = '';
                  	  if($('#unit').val() != null){
                  	  	 unit = $('#unit').select2('data')[0].text; 
                  	  }
	                  return customReportHeader('Employee list', { 'unit':unit, 'emp_type' : $('#emp_type').val(), ot: $('#otnonot').val() });
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

				api.columns(selectable).every( function (i, x) {
				    var column = this;

				    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
				        .appendTo($(column.header()).empty())
				        .on('change', function(e){
				            var val = $.fn.dataTable.util.escapeRegex(
				                $(this).val()
				            );
				            column.search(val ? ('^'+val.replace("'S","").replace( /&/g, '&amp;' )+'$'): '', true, true ).draw();

				            e.stopPropagation();
				        });

					$.each(dropdownList[i], function(j, v) {
						select.append('<option value="'+v+'">'+v+'</option>')
					});
				// }, 1000);
				});
	        }
		});
	//});


	$(document).on("change", '#doj_from', function(e) {
		var val = $(this).val();
		$('#doj_to').val('');
		if(val){
			$('#doj_to').attr('min',val);
		}
	});

	$(document).on("change",'#unit,#emp_type,#otnonot,#doj_from,#doj_to,#as_status', function(e){
		e.preventDefault();
		dt.draw();
	});
});
</script>
@endpush
@endsection
