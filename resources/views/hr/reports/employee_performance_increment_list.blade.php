@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
  <style>

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
                <li class="active"> Increment Approval List </li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="row"> 
                <div class="col-xs-12">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                </div>
                <div class="col-xs-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Increment Approval List</div>
                        <div class="panel-body">
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Associate Id</th>
                                        <th>Date</th>
                                        <th>Submit By</th>
                                        <th>Comment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($incrementApprovalList))
                                        @foreach($incrementApprovalList as $k=>$incrementApproval)
                                            <tr>
                                                <td>{{$k+1}}</td>
                                                <td>{{$incrementApproval->associate_id}}</td>
                                                <td>{{$incrementApproval->date}}</td>
                                                <td>{{$incrementApproval->submit_by}}</td>
                                                <td>{{$incrementApproval->comments}}</td>
                                                <td>
                                                    <a href="{{url('hr/reports/emp_performance/'.$incrementApproval->associate_id.'/'.$incrementApproval->date.'?incId='.$incrementApproval->hr_increment_id)}}" class="btn btn-info btn-xs"><i class="fa fa-user" title="Review Increment"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No Data Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">

</script>
@endsection
