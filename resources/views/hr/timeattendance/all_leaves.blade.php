@extends('hr.layout')
@section('title', 'Leave List')
@section('main-content')
@push('css')
<style type="text/css">
    #dataTables th:nth-child(7) input{
      width: 40px !important;
    }
    #dataTables th:nth-child(5) select, #dataTables th:nth-child(2) input{
      width: 50px !important;
    }
    #dataTables th:nth-child(1) input, #dataTables th:nth-child(3) input{
      width: 100px !important;
    }
    #dataTables th:nth-child(8) select{
      width: 80px !important;
    }
    .nav-year {
        font-size: 13px;
        font-weight: bold;
        color: #9c9c9c;
        padding: 1px 10px;
        border-radius: 10px;
        margin: 0 2px;
        background: #eff7f8;
        display: inline-block;
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
                    <a href="#"> Time & Attendance </a>
                </li>
                <li class="active"> Leaves : {{\Carbon\Carbon::parse($month)->format('F, Y')}}</li>
                <li class="top-nav-btn">
                    <a href="{{ url('hr/timeattendance/leave-entry')}}" class="pull-right btn btn-sm  btn-primary">Leave Entry</a>
                </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        @include('inc/message')
        <div class="page-content"> 
            <div class="panel">
            <div class="panel-body text-center p-2">
                @foreach(array_reverse($months) as $k => $i)
                    <a href="{{url('hr/timeattendance/all_leaves?month='.$k)}}" class="nav-year @if($k== $month) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Leave list of {{$i}}" >
                        {{$i}}
                    </a>
                @endforeach
            </div>
        </div>
            <div class="panel panel-info"> 
                <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100%; overflow-x: auto; display: block; ">
                        <thead>
                            <tr>
                                <th width="10%">Associate ID</th>
                                <th width="10%">Oracle ID</th>
                                <th width="20%">Name</th>
                                <th width="20%">Unit</th>
                                <th width="10%">Leave Type</th>
                                <th width="20%">Duration</th>
                                <th width="10%" style="text-align: center;"> Day(s)</th>
                                <th width="10%"> Status</th>
                                <th width="20%">Action</th>
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
    var searchable = [0,1,2,5];
    var selectable = [3,4,7]; //use 2,4....and * for all
    // dropdownList = {column_number: {'key':value}};
    var dropdownList = {
        '3':[@foreach($unit as $u) <?php echo "'$u'," ?> @endforeach],
        '4':['Casual','Earned','Sick', 'Special','Maternity'],
        '7':['Applied','Approved','Declined'] 
    };
    var exportColName = ['Associate ID','Oracle ID','Name','Unit', 'Leave Type', 'Leave Duration','Day(s)', 'Status'];
      var exportCol = [0,1,2,3,4,5,6,7];
    var dt = $('#dataTables').DataTable({
        order: [],  
        processing: true,
        responsive: false,
        serverSide: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        pagingType: "full_numbers", 
        dom:'lBfrtip', 
        ajax: {
            url: '{!! url("hr/timeattendance/all_leaves_data") !!}',
            type: "POST",
            data:{
                month: '{{request()->get('month')??date('Y-m')}}'
            },
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        }, 
        buttons: [  
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'All Leaves',
                header: false,
                footer: true,
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
                title: 'All Leaves',
                header: false,
                footer: true,
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
                className: 'btn-sm btn-primary', 
                title: 'All Leaves',
                header: false,
                footer: true,
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
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'All Leaves',
                header: true,
                footer: false,
                "action": allExport,
                exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      },
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'leave_ass_id', name: 'leave_ass_id'}, 
            { data: 'as_oracle_code', name: 'as_oracle_code' }, 
            { data: 'as_name',  name: 'as_name' , orderable: false}, 
            { data: 'hr_unit_name',  name: 'hr_unit_name' , orderable: false}, 
            { data: 'leave_type', name: 'leave_type' , orderable: false}, 
            { data: 'leave_duration', name: 'leave_duration' , orderable: false}, 
            { data: 'days', name: 'days' , orderable: false}, 
            { data: 'leave_status', name: 'leave_status'}, 
            { data: 'action', name: 'action', orderable: false, searchable: false}
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

                var select = $('<select  style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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