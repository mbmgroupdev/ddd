@extends('user.layout')
@section('title', 'User Dashboard')
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
                    <a href="#">Ess</a>
                </li>
                <li>
                    <a href="#">Greivance</a>
                </li>
                <li class="active">Appeal List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
               <div class="panel-heading"><h6>Appeal List<a href="{{ url('hr/ess/grievance/appeal')}}" class="pull-right btn btn-xx btn-info">Appeal</a></h6></div> 
                 <div class="panel-body">

                    <div class="row">
                          <!-- Display Erro/Success Message -->
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="col-xs-12 worker-list">
                            <!-- PAGE CONTENT BEGINS --> 
                                <table id="dataTables" class="table table-striped table-bordered" style="display:table;overflow-x: auto;white-space: nowrap; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Sl. No</th>
                                            <th>Offender ID</th>
                                            <th>Griever ID</th>
                                            <th>Issue</th>
                                            <th>Step</th>
                                            <th>Discussed Date</th>
                                            <th>Request Remedy</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                </table>
                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->

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

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        dom: "<'row'<'col-sm-2'l><'col-sm-3'i><'col-sm-4 text-center'B><'col-sm-3'f>>tp", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Greivance Appeal List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Greivance Appeal List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Greivance Appeal List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Greivance Appeal List', 
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Greivance Appeal List',
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                } 
            } 
        ], 
        ajax: {
            url: '{!! url("hr/ess/grievance/appeal_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        }, 
        columns: [  
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'offender', name: 'offender' }, 
            { data: 'griever',  name: 'griever' }, 
            { data: 'issue', name: 'issue' }, 
            { data: 'hr_griv_appl_step', name: 'hr_griv_appl_step' }, 
            { data: 'hr_griv_appl_discussed_date', name: 'hr_griv_appl_discussed_date' }, 
            { data: 'hr_griv_appl_req_remedy', name: 'hr_griv_appl_req_remedy' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endsection