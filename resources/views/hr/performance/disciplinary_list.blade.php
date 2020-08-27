@extends('hr.layout')
@section('title', 'Disciplinary Record List')
@section('main-content')
@push('css')
<style type="text/css">
    a[href]:after { content: none !important; }
    thead {display: table-header-group;} 
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class=" fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Performance </a>
                </li>
                <li class="active"> Disciplinary Record List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Disciplinary List<a href="{{ url('hr/performance/operation/disciplinary_form')}}" class="pull-right btn btn-primary">Disciplinary Form</a></h6></div> 
                <div class="panel-body">

                    
                    <!-- Display Erro/Success Message -->
                    <table id="dataTables" class="table table-striped table-bordered" style="display:table;overflow-x: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>SL. No</th>
                                <th>Offender ID</th>
                                <th>Griever ID</th>
                                <th>Reason</th>
                                <th>Action</th>
                                <th>Requested Remedy</th>
                                <th>Discussed Date</th>
                                <th>Date of Execution</th> 
                                <th>Action</th>
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

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true, 
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        dom: "lBftrip", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Disciplinary Record List',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Disciplinary Record List',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Disciplinary Record List',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Disciplinary Record List',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Disciplinary Record List',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7],
                    stripHtml: false
                } 
            } 
        ], 
        ajax: '{!! url("hr/performance/operation/disciplinary_data") !!}',
        columns: [  
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'offender',  name: 'offender' }, 
            { data: 'griever',  name: 'griever' }, 
            { data: 'issue',  name: 'issue' }, 
            { data: 'step', name: 'step' }, 
            { data: 'dis_re_req_remedy', name: 'dis_re_req_remedy' }, 
            { data: 'discussed_date', name: 'discussed_date' }, 
            { data: 'date_of_execution', name: 'date_of_execution' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endpush
@endsection