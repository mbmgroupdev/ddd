@extends('hr.layout')
@section('title', 'Medical Incident List')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Employee</a>
                </li>
                <li class="active">Medical Incident List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-success">
              <div class="panel-heading"><h6>Medical Incident List</h6></div> 
                <div class="panel-body">

                    <div class="row">
                          <!-- Display Erro/Success Message -->
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <!-- <h1 align="center">Add New Employee</h1> -->
                            <div class="col-xs-12 worker-list">
                            <!-- PAGE CONTENT BEGINS --> 
                                <table id="dataTables" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl. No</th>
                                            <th>Associate ID</th>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Incident Details</th>
                                            <th>Doctors Name</th>
                                            <th>Doctors Recommendation</th>
                                            <th>Supporting File </th>
                                            <th>Company's Action</th>
                                            <th>Allowance</th>
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
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: '{!! url("hr/ess/medical_incident_data") !!}',
        ajax: {
            url: '{!! url("hr/ess/medical_incident_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        }, 
        dom: "lBftrip", 
        buttons: [  
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Employee Medical Incident List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Employee Medical Incident List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Employee Medical Incident List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Employee Medical Incident List',
                exportOptions: {
                    columns: ':visible'
                } 
            } 
        ], 
        columns: [ 
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'hr_med_incident_as_id', name: 'hr_med_incident_as_id' }, 
            { data: 'hr_med_incident_as_name',  name: 'hr_med_incident_as_name' }, 
            { data: 'hr_med_incident_date', name: 'hr_med_incident_date' }, 
            { data: 'hr_med_incident_details', name: 'hr_med_incident_details' }, 
            { data: 'hr_med_incident_doctors_name', name: 'hr_med_incident_doctors_name' }, 
            { data: 'hr_med_incident_doctors_recommendation', name: 'hr_med_incident_doctors_recommendation' }, 
            { data: 'file', name: 'file' }, 
            { data: 'hr_med_incident_action', name: 'hr_med_incident_action' }, 
            { data: 'hr_med_incident_allowance', name: 'hr_med_incident_allowance'},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endpush
@endsection