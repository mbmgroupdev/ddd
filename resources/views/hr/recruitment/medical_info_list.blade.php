@extends('hr.layout')
@section('title', 'Medical Information List')
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
                <li class="active"> Medical Information List</li>
            </ul><!-- /.breadcrumb -->
 
        </div>
        <!-- Display Erro/Success Message -->
        @include('inc/message')

        <div class="page-content"> 
            <div class="panel panel-success">
              {{-- <div class="panel-heading"><h6>Medical Information List</h6></div>  --}}
                <div class="panel-body">
                         
                    <div class=" medical_info">
                        <!-- PAGE CONTENT BEGINS -->
                        <table id="dataTables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <!-- <th>Sl. No</th> -->
                                    <th>Associate ID</th>
                                    <th>Name</th>
                                    <th>Height</th>
                                    <th>Weight</th>
                                    <th>Blood Group</th>
                                    <th>Identification Mark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                        

                        <!-- PAGE CONTENT ENDS -->
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
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        ajax: {
            url: '{!! url("hr/recruitment/operation/medical_info_list_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        }, 
        //dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        dom:'lBfrtip',
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Employee Medical Information List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Employee Medical Information List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Employee Medical Information List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Employee Medical Information List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Employee Medical Information List',
                exportOptions: {
                    columns: ':visible'
                } 
            } 
        ], 
        columns: [ 
            { data: 'med_as_id', name: 'med_as_id' }, 
            { data: 'as_name',  name: 'as_name' }, 
            { data: 'med_height', name: 'med_height' }, 
            { data: 'med_weight', name: 'med_weight' }, 
            { data: 'med_blood_group', name: 'med_blood_group' }, 
            { data: 'med_ident_mark', name: 'med_ident_mark' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endpush
@endsection
