@extends('hr.layout')
@section('title', 'Interview Notes')
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
                    <a href="#">Job Portal</a>
                </li>
                <li class="active">Interview Notes List</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Interview Notes<a href="{{ url('hr/recruitment/job_portal/interview_notes')}}" class="pull-right btn btn-xx btn-info">Interview Notes</a></h6></div> 
                  <div class="panel-body">

                    <div class="row">
                        <!-- Display Erro/Success Message -->
                        <div class="col-xs-12 worker-list">
                        @include('inc/message')
                            <!-- PAGE CONTENT BEGINS -->
                            <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
                                <thead>
                                    <tr>
                                        <!-- <th>Sl. No</th> -->
                                        <th width="10%">ID</th>
                                        <th width="10%">Interview Date</th>
                                        <th width="10%">Interviewer Name</th>
                                        <th width="10%">Contact Number</th>
                                        <th width="10%">Expected Salary</th>
                                        <th width="10%">Board Member</th>
                                        <th width="10%">Note</th>
                                        <th width="10%">Action</th>
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
    var searchable = [1,2,3,4,5];

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/recruitment/job_portal/interview_notes_data") !!}',
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
                title: 'Interview Notes List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Interview Notes List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Interview Notes List',
                header: false,
                footer: true, 
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Interview Notes List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: [0,1,2,3,4,5],
                    stripHtml: false
                } 
            } 
        ],
        columns: [
            { data: 'serial_no', name: 'serial_no' },
            { data: 'hr_interview_date',  name: 'hr_interview_date' },
            { data: 'hr_interview_name', name: 'hr_interview_name' },
            { data: 'hr_interview_contact', name: 'hr_interview_contact' },
            { data: 'hr_interview_exp_salary', name: 'hr_interview_exp_salary' },
            { data: 'hr_interview_board_member', name: 'hr_interview_board_member' },
            { data: 'hr_interview_note', name: 'hr_interview_note' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
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
        }
    });
});
</script>
@endsection
