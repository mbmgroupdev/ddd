@extends('hr.layout')
@section('title', 'Warning Notice')
@section('main-content')
@push('css')
    
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
                    <a href="#">Buyer Mode</a>
                </li>
                <li class="active">Create</li>
            </ul>
        </div>
    </div>

    <div class="page-content">
        <div class="row">   
            <div class="col">
                <form role="form" method="post" action="{{ url('hr/buyer-mode/warning-notice') }}" class="noticeReport" id="noticeReport">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                        <label  for="year"> Month </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    <a href="{{url('hr/reports/warning-notices')}}" class="btn btn-success pull-right" >Warning Notice List <i class="fa fa-list bigger-120"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.col -->
        </div>
        <div class="panel panel-success" style=""> 
            <div class="panel-body">
            </div>
        </div>
    </div>
  </div>
</div>
@push('js')
<script type="text/javascript">
    
</script>
@endpush
@endsection