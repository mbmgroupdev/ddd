@extends('hr.layout')
@section('title', 'Recruitment Bulk Upload') 
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">Recruit</a>
                </li>
                <li class="active">Bulk Upload</li>
            </ul><!-- /.breadcrumb --> 
        </div>
 
        @include('inc/message')
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
               <div class="row justify-content-center">
                    <div class="col-sm-4">
                        <form method="POST" action="{{url('hr/recruitment/worker/recruit/excel/import')}}" accept-charset="UTF-8" class="form-horizontal has-validation-callback" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <h3 class="mb-1 border-left-heading">Recruitment Bulk File </h3>
                                <p> Download <a href="{{url('samplefiles/example_worker_recruitment.xlsx')}}" title="Sample File">Sample File</a></p>
                                    
                                <div class="file-zone" style="padding-top: 10px;">
                                    <input type="file" name="excel_file" required="required" data-allow="['xls','xlsx']">
                                    <br>
                                    only <strong>.xls</strong> or <strong>.xlsx</strong> file supported.</span>
                                </div>
                            </div> 
                            <div class="form-group">
                                <button type="submit" id="file_save" class="btn  btn-primary">
                                    <i class="fa fa-check"></i> Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection