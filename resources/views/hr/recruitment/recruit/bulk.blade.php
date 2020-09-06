@extends('hr.layout')
@section('title', 'Recruitment Bulk Upload')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="panel h-min-400">
            <div class="panel-heading">
                  <h6>Recruitment Bulk Upload
                    <div class="pull-right">
                        <a class="btn btn-primary" href="{{ url('hr/recruitment/recruit/create') }}">Recruit</a>
                        <a class="btn btn-primary" href="{{ url('hr/recruitment/recruit') }}">Recruit List</a>
                        
                    </div>
                  </h6>
            </div>
            <div class="panel-body">
               <div class="row">
                    <div class="col-sm-12 widget-box widget-color-green2 responsive-hundred">
                        <h4 class="page-header widget-header" style="text-align:center;height:10px !important;padding-top: 8px;padding-bottom: 1px;"> Bulk Upload <span style="color: red; vertical-align: text-top;">*</span></h4>
                        <form method="POST" action="http://hrm.aql-bd.com/hr/recruitment/worker/recruit/excel/import" accept-charset="UTF-8" class="form-horizontal has-validation-callback" enctype="multipart/form-data"><input name="_token" type="hidden" value="Znv1CDwfJkSxykeYIrpjYqer78egowcNAfZaeBW5">
                            <div class="form-group" style="padding-left: 35%;">
                                <label class="col-sm-3 control-label no-padding-right no-padding-top" for="excel_file"> File <br><span>(only .xls and .xlsx files)<br><a href="http://hrm.aql-bd.com/samplefiles/example_worker_recruitment.xlsx" title="Sample File">Sample File</a></span></label>
                                <div class="col-sm-4" style="padding-top: 10px;">
                                    <input type="file" name="excel_file" id="excel_file" class="col-xs-12" style="padding:0;" data-validation-allowing="xls, xlsx" required="required">
                                    <span id="recruit_file_upload_error" class="red" style="display: none; font-size: 14px;">only <strong>.xls</strong> or <strong>.xlsx</strong> file supported.</span>
                                </div>
                            </div> 
                            
                          <div class="col-sm-12" style="padding-left: 30px; padding-right: 30px;">
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center">
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <button type="submit" id="file_save" class="btn btn-sm btn-primary">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Upload
                                    </button>
                                </div>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection