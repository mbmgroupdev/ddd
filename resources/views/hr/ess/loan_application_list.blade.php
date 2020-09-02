@extends('hr.layout')
@section('title', 'Loan Application List')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="#">Loan</a>
                </li>
                <li class="active">Loan Application List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div class="panel panel-success mb-3">
            <div class="panel-heading"><h6>Loan Application List</h6></div> 
            <div class="panel-body"> 
                    <table id="dataTables" class="table table-striped table-bordered" style="display:table;overflow-x: auto;width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Amount</th>
                                <th>Updated at</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead> 
                        <tfoot>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Amount</th>
                                <th>Updated at</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot> 
                        
                    </table>
                
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 
       ///Filter
    var searchable = [1,2];
    var selectable = [3,6]; //use 4,5,6,7,8,9,10,11,....and * for all
      var dropdownList = {
      
      '3' :[@foreach($unit as $e) <?php echo "\"$e\"," ?> @endforeach],
      '6' :['Applied','Approved','Declined']
      
    };

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/ess/loan_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "<'row'<'col-sm-2'l><'col-sm-3'i><'col-sm-4 text-center'B><'col-sm-3'f>>tp", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Loan Application List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5,6]
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Loan Application List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5,6]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Loan Application List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5,6]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Loan Application List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5,6]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Loan Application List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: [0,1,2,3,4,5,6],
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'hr_la_as_id', name: 'hr_la_as_id' }, 
            { data: 'hr_la_name',  name: 'hr_la_name' }, 
            { data: 'hr_unit_name',  name: 'hr_unit_name' }, 
            { data: 'hr_la_applied_amount', name: 'hr_la_applied_amount' }, 
            { data: 'updated_at', name: 'updated_at' }, 
            { data: 'status', name: 'status' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
         initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; height:25px; border:1px solid whitesmoke;');


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

                var select = $('<select style="width: 110px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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