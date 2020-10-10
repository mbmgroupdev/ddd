@extends('hr.layout')
@section('title', 'Employee Attendance Record')
@section('main-content')
@push('css')
<style>
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
   }
   .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .iq-card {
        border: 1px solid #ccc;
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
          <a href="#">Reports</a>
        </li>
        <li class="active"> Employee daily attendance excel</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col">
                <form role="form" method="get" action="{{ url('hr/reports/employee-daily-attendance') }}" class="attendanceReport" id="attendanceReport">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="date" class="form-control" id="date" name="date" placeholder=" date"required="required" value="{{ (request()->date?request()->date:date('Y-m-d') )}}"autocomplete="off" />
                                        <label  for="date"> Date </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm "><i class="fa fa-save"></i> Generate</button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@endsection