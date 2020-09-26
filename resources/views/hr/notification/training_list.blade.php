@extends('hr.layout')
@section('title', 'Training List')
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
                    <a href="#">Notifcation</a>
                </li>
                <li>
                    <a href="#">Training List</a>
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Notification <small><i class="ace-icon fa fa-angle-double-right"></i> Training List </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS --> 
                        <table id="dataTables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <!-- <th>Sl. No</th> -->
                                    <th>Associate ID</th>
                                    <th>Associate Name</th>
                                    <th>Training Name</th>
                                    <th>Trainer Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
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
            ajax: '{!! url("hr/notification/training/training_data") !!}',
            dom: "lBftrip", 
            buttons: [   
                {
                    extend: 'csv', 
                    className: 'btn-sm btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                }, 
                {
                    extend: 'excel', 
                    className: 'btn-sm btn-warning',
                    exportOptions: {
                        columns: ':visible'
                    }
                }, 
                {
                    extend: 'pdf', 
                    className: 'btn-sm btn-primary', 
                    exportOptions: {
                        columns: ':visible'
                    }
                }, 
                {
                    extend: 'print', 
                    className: 'btn-sm btn-default',
                    exportOptions: {
                        columns: ':visible'
                    } 
                } 
            ], 
            columns: [ 
                // { data: 'serial_no', name: 'serial_no' }, 
                { data: 'tr_as_ass_id', name: 'tr_as_ass_id' }, 
                { data: 'as_name',  name: 'as_name' }, 
                { data: 'hr_tr_name', name: 'hr_tr_name' }, 
                { data: 'tr_trainer_name', name: 'tr_trainer_name' }, 
                { data: 'date', name: 'date' }, 
                { data: 'time', name: 'time' }, 
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],  
        }); 
    });
</script>
@endpush
@endsection