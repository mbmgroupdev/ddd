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
        table th:nth-child(2) input{
            width: 80px!important;
        }
        .nav-year{
            font-size: 14px;
            font-weight: bold;
            color: #706f6f;
            padding: 0 10px;
            border-right: 1px solid #706f6f;
        }
        .nav-year:last-child{
            border: 0;
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
				<li class="active"> Increment </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/payroll/increment')}}" class="btn btn-sm btn-primary">Add Increment</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>
        @php
            $year = request()->get('year')??date('Y');

        @endphp
        <div class="panel">
            <div class="panel-body text-center p-2">
                @foreach(range(date('Y')-12, date('Y')) as $i)
                    <a href="{{url('hr/payroll/increment-list?year='.$i)}}" class="nav-year @if($i == $year) text-primary @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Yearly Report" >
                        {{$i}}
                    </a>
                @endforeach
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
                                <th>Oracle ID</th>
                                <th>Designation</th>
                                <th>Inc. Type</th>
                                <th>Inc. Amount</th>
                                <th>Applied Date</th>
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
<div id="increment_letter_bn" style="display: none;">
    @include('hr.common.increment_letter_bn')
</div>
<div id="increment_letter_en" style="display: none;">
    @include('hr.common.increment_letter_en')
</div>
@push('js')
<script type="text/javascript"> 
function printLetter(letter)
{ 
    $('#bn_letter_name').text(letter.name);
    $('#bn_letter_designation').text(letter.designation);
    $('#bn_letter_id').text(letter.associate_id);
    $('#bn_letter_section').text(letter.section);
    $('#bn_prev_salary').text(letter.salary);
    $('#bn_incr').text(letter.inc);
    $('#bn_present_salary').text(letter.new_salary);
    $('#bn_effective_date').text(letter.effective_date);
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<html><head></head><body style="font-size:10px;">');
    myWindow.document.write(document.getElementById('increment_letter_bn').innerHTML);
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
    myWindow.document.write(document.getElementById('increment_letter_en').innerHTML);
    myWindow.document.write('</body></html>');
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}
$(document).ready(function(){
    var totalempcount = 0;
    var totalemp = 0;
    var searchable = [1,2];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {};
    var exportColName = ['Sl.','Associate ID','Name','Oracle Code','Designation','Increment Type','Increment Amount','Applied Date','Effective Date'];
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
                url: '{!! url("hr/payroll/increment-list-data?year=".$year) !!}',
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
                { data: 'as_oracle_code', name: 'as_oracle_code'},
                { data: 'designation', name: 'designation'},
                { data: 'increment_type', name: 'increment_type'},
                { data: 'increment_amount',  name: 'increment_amount' },
                { data: 'applied_date',  name: 'applied_date' },
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