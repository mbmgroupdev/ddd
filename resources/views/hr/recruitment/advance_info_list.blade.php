@extends('hr.layout')
@section('title', '')
@section('main-content')
<style type="text/css">
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
        /*border-color: #ffffff !important;*/
        border: 0px !important;
        box-shadow: none !important;
    }

     {{-- removing the links in print and adding each page header --}}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}
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
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Advance Information List</li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content"> 
            <div class="panel panel-success">
              {{-- <div class="panel-heading"><h6>Advance Information List</h6></div>  --}}
                <div class="panel-body">

                    <div class="row">
                         <!-- Display Erro/Success Message -->
                            @include('inc/message')
                        <div class="col-xs-12 advance_info">
                            <!-- PAGE CONTENT BEGINS -->
                            <table id="dataTables" class="table table-striped table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <!-- <th>Sl. No</th> -->
                                        <th>Associate ID</th>
                                        <th>Name</th>
                                        <th>Father's Name</th>
                                        <th>Mother's Name</th>
                                        <th>Job Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                            

                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<script type="text/javascript"> 
$(document).ready(function(){ 

    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        ajax: {
            url: '{!! url("hr/recruitment/operation/advance_info_list_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        //dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        dom:'lBfrtip',
        buttons: [   
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Advance Info Employee List',
                "action": allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Advance Info Employee List',
                "action": allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Advance Info Employee List',
                "action": allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,1,2,3,4]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Advance Info Employee List',
                "action": allExport,
                exportOptions: {
                    columns: [0,1,2,3,4]
                } 
            } 
        ], 
        columns: [ 
            { data: 'emp_adv_info_as_id', name: 'emp_adv_info_as_id' }, 
            { data: 'as_name',  name: 'as_name' }, 
            { data: 'emp_adv_info_fathers_name', name: 'emp_adv_info_fathers_name' }, 
            { data: 'emp_adv_info_mothers_name', name: 'emp_adv_info_mothers_name' }, 
            { data: 'emp_adv_info_stat', name: 'emp_adv_info_stat' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endsection