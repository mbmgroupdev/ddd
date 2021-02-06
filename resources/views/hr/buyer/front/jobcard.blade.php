@extends('hr.layout')
@section('title', 'Job Card')
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
                <li class="active"> Job Card</li>
            </ul><!-- /.breadcrumb -->
        </div>
    
        <form role="form" method="get" action="{{ url('hrm/operation/job_card') }}" class="attendanceReport mb-3" id="attendanceReportEmp">
            <div class="panel" style="margin-bottom: 0;">
                
                <div class="panel-body" style="padding-bottom: 5px;">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'allassociates no-select col-xs-12','style', 'required'=>'required']) }}
                                <label  for="associate"> Associate's ID </label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group has-float-label has-required select-search-group">
                                <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                <label  for="year"> Month </label>
                            </div>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <div class="panel">
            <div class="panel-body">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        
                        {!! $jobcardview !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
    function printMe1(divName)
    {
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
</script>
@endpush
@endsection