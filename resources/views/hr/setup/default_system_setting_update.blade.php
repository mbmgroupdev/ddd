@extends('hr.layout')
@section('title', '')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Default System Setting Update</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            
                  <!-- Display Erro/Success Message -->
                @include('inc/message')

             <div class="row" style="padding-left: 20%; padding-right: 20%;">
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/default_system_setting_update_data')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                    <div class="panel panel-info">
                      <div class="panel-heading"><h6>Default System Setting</h6></div> 
                        <div class="panel-body">
                            <div class="form-group">
                                <p class="col-sm-1"></p>
                                <label class="col-sm-3 control-label no-padding-right"> Salary Lock <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-7">
                                    <input type="text" id="salary_lock" name="salary_lock" class="col-xs-12 datepicker" data-validation="required length custom" data-validation-length="1-50" value="{{$system_setting->salary_lock}}" 
                                    />
                                </div>
                            </div>
                            <input type="hidden" name="salary_lock_id" id="salary_lock_id" value="{{$id}}">
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-3 col-md-6 text-center" style="padding-left: 30px;"> 
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>

                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            </div>
         
                        </div>
                      </div>
                    </div>
              </div>
            </form> 
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 
 
    
});
</script>
@endsection