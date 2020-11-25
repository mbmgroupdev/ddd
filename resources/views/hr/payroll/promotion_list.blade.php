@extends('hr.layout')
@section('title', 'Promotion List')
@section('main-content')
@push('css')
    <style type="text/css">
        {{-- removing the links in print and adding each page header --}}
        a[href]:after { content: none !important; }
        table th:nth-child(2) input{
            width: 80px!important;
        }
        table th:nth-child(7) input{
            width: 60px!important;
        }
        table th:nth-child(8) input{
            width: 80px!important;
        }
        table th:nth-child(4) input{
            width: 40px!important;
        }
         table th:nth-child(3) input{
            width: 120px!important;
        }

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
				<li class="active"> Promotion </li>
                <li class="top-nav-btn"><a href="{{url('hr/payroll/promotion')}}" class="btn btn-primary pull-right">Promotion</a></li>
			</ul><!-- /.breadcrumb --> 
		</div>
        @php
            $nextyear = null;
            $year = request()->get('year')??date('Y');
            $prevyear = $year-1;
            if($year < date('Y')){
                $nextyear = $year+1;
            }
        @endphp
        <div class="panel">
            <div class="panel-body text-center p-2">
                <a href="{{url('hr/payroll/promotion-list?year='.$prevyear)}}" class="btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Year Report" >
                  <i class="las la-chevron-left f-16"></i>
                </a>

                <b class="f-16" id="result-head">Promotion List: {{ $year }} </b>
                @if($nextyear != null)
                <a href="{{url('hr/payroll/promotion-list?year='.$nextyear)}}" class="btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Year Report" >
                  <i class="las la-chevron-right f-16"></i>
                </a>
                @endif
            </div>
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
                                <th>Unit</th>
                                <th>Prev. Designation</th>
                                <th>Curr. Designation</th>
                                <th>Month</th>
                                <th>Date</th>
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
    <div id="promotion_letter_bn" style="display: none;">
        @include('hr.common.promotion_letter_bn')
    </div>
    <div id="promotion_letter_en" style="display: none;">
        @include('hr.common.promotion_letter_en')
    </div>

</div>
@push('js')
<script type="text/javascript"> 
$(document).ready(function(){
    var totalempcount = 0;
    var totalemp = 0;
    var searchable = [1,2,3,4,5,6,7];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {};
    var exportColName = ['Sl.','Associate ID','Name','Unit','Prev. Designation','Curr. Designation','Month','Effective Date'];
        var exportCol = [0,1,2,3,4,5,6,7];

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
                url: '{!! url("hr/payroll/promotion-list-data") !!}',
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data:{
                    'year' : {{$year}}
                }
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    "action": allExport,
                    exportOptions: {
                        columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    "action": allExport,
                    exportOptions: {
                        columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                    }
                },
                {
                    extend: 'pdf',
                    "action": allExport,
                    className: 'btn-sm btn-primary',
                    exportOptions: {
                        columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    "action": allExport,
                    className: 'btn-sm btn-default print',
                    title: '',
                    exportOptions: {
                        columns: exportCol,
                        format: {
                            header: function ( data, columnIdx ) {
                                return exportColName[columnIdx];
                            }
                        },
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
                { data: 'as_unit_id', name: 'as_unit_id'},
                { data: 'previous_designation_id', name: 'previous_designation_id'},
                { data: 'current_designation_id',  name: 'current_designation_id' },
                { data: 'month',  name: 'month' },
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
function printLetter(letter)
{ 
    $('#bn_letter_name').text(letter.name);
    $('#bn_letter_designation').text(letter.prev_desg);
    $('#bn_letter_id').text(letter.associate_id);
    $('#bn_letter_section').text(letter.section);
    $('#prev_desg').text(letter.prev_desg);
    $('#curr_desg').text(letter.curr_desg);
    $('#effective_date').text(letter.effective_date);
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<html><head></head><body style="font-size:10px;">');
    myWindow.document.write(document.getElementById('promotion_letter_bn').innerHTML);
    myWindow.document.write('</body></html>');
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

function printEnLetter(letter)
{ 
    $('#letter_name').text(letter.name);
    $('#letter_title').text(letter.title);
    $('#letter_doj').text(letter.doj);
    $('#letter_designation').text(letter.prev_desg);
    $('#letter_id').text(letter.associate_id);
    $('#letter_department').text(letter.department);
    $('#en_prev_desg').text(letter.prev_desg);
    $('#en_curr_desg').text(letter.curr_desg);
    $('#en_effective_date').text(letter.effective_date);
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<html><head></head><body style="font-size:10px;">');
    myWindow.document.write(document.getElementById('promotion_letter_en').innerHTML);
    myWindow.document.write('</body></html>');
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}
</script>
@endpush
@endsection