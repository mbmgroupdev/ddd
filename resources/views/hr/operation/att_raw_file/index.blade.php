@extends('hr.layout')
@section('title', 'Attendance Raw File')
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
          <a href="#">Operation</a>
        </li>
        <li class="active"> Attendance Raw File</li>
      </ul><!-- /.breadcrumb -->
    </div>
    <div class="iq-accordion career-style mat-style  ">
        <div class="iq-card iq-accordion-block mb-3 accordion-active">
            <div class="active-mat clearfix">
              <div class="container-fluid">
                 <div class="row">
                    <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;border-radius: 50%;"> Filter </span> </a></div>
                 </div>
              </div>
            </div>
            <div class="accordion-details">
                <div class="row">
                    <div class="col">
                      <form role="form" method="post" action="{{ url('hr/operation/attendance-raw-file') }}" class="attendanceReport" id="attendanceReportEmp">
                        @csrf
                        <div class="panel" style="margin-bottom: 0;">
                            
                            <div class="panel-body" style="padding-bottom: 5px;">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            {{ Form::select('unit', $unitList, auth()->user()->employee->as_unit_id, ['placeholder'=>'Select Unit Name', 'id'=>'unit', 'class'=> 'form-control', 'required'=>'required']) }} 
                                            <label  for="unit"> Unit </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <input type="date" class="form-control" id="fromdate" name="from_date" placeholder=" Month-Year"required="required" value="{{ date('Y-m-d', strtotime('-1 day')) }}"autocomplete="off" />
                                            <label  for="fromdate">From Date </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <input type="date" class="form-control" id="to_date" name="to_date" placeholder=" Month-Year"required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
                                            <label  for="to_date">To Date </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <input type="text" class="form-control" id="file_name" name="file_name" placeholder=" Month-Year"required="required" value="Data-{{ date('Ymd') }}"autocomplete="off" />
                                            <label  for="file_name">File Name </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                  </div>
              </div>
           </div>
        </div>
        
    </div>
    <div class="page-content">
        
        
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script>
  $("#to_date").on("change", function() {
    var selected = $(this).val();
    if(selected !== null){
      var date = selected.split("-");
      $('#file_name').val('Data-'+date[0]+date[1]+date[2]);
    }

  });
</script>
@endpush
@endsection