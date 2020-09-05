@extends('hr.layout')
@section('title', 'Job Posting List')
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
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Job Posting List</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Job Posting List<a href="{{ url('hr/recruitment/job_portal/job_posting')}}" class="pull-right btn btn-xx btn-info">Job Posting</a></h6></div> 
                  <div class="panel-body">

                    <div class="row">
                         <!-- Display Erro/Success Message -->
                            @include('inc/message')
                        <div class="col-xs-12 worker-list">
                            <!-- PAGE CONTENT BEGINS -->
                            <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
                                <thead>
                                    <tr>
                                        <!-- <th>Sl. No</th> -->
                                        <th width="10%">Sl</th>
                                        <th width="10%">Job Title</th>
                                        <th width="10%">Vacancy</th>
                                        <th width="10%">Job Nature</th>
                                        <th width="10%">Application Deadline</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </thead>
                                 <tfoot>
                                    <tr>
                                        <!-- <th>Sl. No</th> -->
                                        <th width="10%">Sl</th>
                                        <th width="10%">Job Title</th>
                                        <th width="10%">Vacancy</th>
                                        <th width="10%">Job Nature</th>
                                        <th width="10%">Application Deadline</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </tfoot>
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
    var searchable = [1,3,4];
    // var selectable = [1,3]; //use 4,5,6,7,8,9,10,11,....and * for all
    // // dropdownList = {column_number: {'key':value}};
    // var dropdownList = {
    //     '1' :[@foreach($titleList as $e) <?php echo "'$e'," ?> @endforeach],
    //     '3' :['Full Time', 'Part Time', 'Contractual']
    // };

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/recruitment/job_portal/job_posting_data") !!}',
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
                title: 'Job Posting List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Job Posting List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Job Posting List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Job Posting List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                } 
            } 
        ],
        columns: [
            { data: 'serial_no', name: 'serial_no' },
            { data: 'job_po_title',  name: 'job_po_title' },
            { data: 'job_po_vacancy', name: 'job_po_vacancy' },
            { data: 'job_po_nature', name: 'job_po_nature' },
            { data: 'job_po_application_deadline', name: 'job_po_application_deadline' },
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
