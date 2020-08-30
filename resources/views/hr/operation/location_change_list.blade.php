@extends('hr.layout')
@section('title', 'Outside List')
@section('main-content')
@section('content')
<div class="main-content">
    <div class="main-content-inner col-sm-12">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active">Outside List 11</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div class="panel"> 
            <div class="panel-heading">
                <h6>
                    Outside List 
                    <a class="btn btn-primary pull-right" href="{{url('hr/operation/location_change/entry')}}">Outside Entry</a>
                </h6>
            </div>

            <div class="panel-body pb-3">
                <table id="unit_change_table" class="table table-striped table-bordered"> 
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Associate</th>
                            <th>Requested Location</th>
                            <th>Type</th>
                            <th>Requested Place</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Applied on</th>
                            <th>Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <?php $i= 1; ?>
                    <tbody id="unit_change_table_body">
                        @foreach($requestList AS $request)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $request->as_id }}</td>
                                <td>{{ $request->location_name  }}</td>
                                <td>
                                    <?php 
                                        if($request->type == 1){ echo "Full Day";}
                                        if($request->type == 2){ echo "1st Half";}
                                        if($request->type == 3){ echo "2nd Half";}
                                    ?>
                                </td>
                                <td>{{ $request->requested_place  }}</td>
                                <td>{{ $request->start_date  }}</td>
                                <td>{{ $request->end_date  }}</td>
                                <td>{{ $request->applied_on  }}</td>
                                <td><?php if($request->status==0) printf("Applied");
                                       else if($request->status==1) printf("Approved");
                                       else printf("Rejected"); ?></td>
                                <td>
                                    @if($request->status!=0)
                                        <a type="button" class='btn btn-xs btn-success' data-toggle="tooltip" title="Approve" disabled><i class="ace-icon fa fa-check bigger-120"></i></a>
                                        <a type="button" class='btn btn-xs btn-warning' data-toggle="tooltip" title="Reject" disabled><i class="ace-icon fa fa-ban bigger-120"></i></a>
                                    @else
                                    <a href="{{ url('hr/operation/location_change/approve?id='.$request->id.'&as_id='.$request->as_id.'&type='.$request->type.'&start_date='.$request->start_date.'&end_date='.$request->end_date) }}" type="button" class='btn btn-xs btn-success' data-toggle="tooltip" title="Approve"><i class="ace-icon fa fa-check bigger-120"></i></a>
                                    <a href="{{ url('hr/operation/location_change/reject/'.$request->id) }}" type="button" class='btn btn-xs btn-warning' data-toggle="tooltip" title="Reject"><i class="ace-icon fa fa-ban bigger-120"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> {{-- row-end --}}


        </div> {{-- page-content-end --}}
    </div> {{-- main-content-inner-end --}}
</div> {{-- main-content-end --}}
@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        $("#unit_change_table").DataTable({
            paging: true,

            dom: "<'row'<'col-sm-4'i><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
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
    });
    });

</script>
@endpush

@endsection
