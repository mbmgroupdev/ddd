@extends('hr.layout')
@section('title', 'End of Job Benefits List')
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
        padding-left: 450px;
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
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Payroll</a>
                </li>
                <li class="active">End of Job Benefit List</li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content"> 
            @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>
                        Benefit List
                        <a href="{{url('hr/payroll/benefits')}}" class="btn btn-primary  pull-right" >End of Job Benefit</a>
                    </h6>
                </div> 
                <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered" style="display:block;overflow-x: scroll;width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Earn Amount</th>
                                <th>Service Benefits</th>
                                <th>Subsistence Allowance</th>
                                <th>Notice Pay</th>                                            
                                <th>Termination Benefits</th>                                            
                                <th>Natural Death Benefits</th>                                            
                                <th>Accidental Death Benefits</th>
                                <th>Total Amount</th>
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
$(document).ready(function(){ 
    var searchable = [1,2];
    var selectable = [3]; //use 4,5,6,7,8,9,10,11,....and * for all
    // dropdownList = {column_number: {'key':value}}; 
    var dropdownList = {
         '3' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach]
    };

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        // responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        ajax: {
            url: '{!! url("hr/payroll/get_given_benefit_data_list") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "lBfrtip", 
        buttons: [   
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    // columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    // columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    // columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Employee Benefit List',
                header: true,
                footer: false,
                exportOptions: {
                    // columns: [],
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 

            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, 
            { data: 'associate_id', name: 'associate_id' }, 
            { data: 'as_name', name: 'as_name' }, 
            { data: 'unit_name',  name: 'unit_name' }, 
            { data: 'benefit_on',  name: 'benefit_on' }, 
            { data: 'earn_leave_amount', name: 'earn_leave_amount' }, 
            { data: 'service_benefits', name: 'service_benefits' }, 
            { data: 'subsistance_allowance', name: 'subsistance_allowance' },
            { data: 'notice_pay', name: 'notice_pay' },
            { data: 'termination_benefits', name: 'termination_benefits' },
            { data: 'natural_death_benefits', name: 'natural_death_benefits' },
            { data: 'on_duty_accidental_death_benefits', name: 'on_duty_accidental_death_benefits' },
            { data: 'total_amount', name: 'total_amount' }
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
@endpush
@endsection
