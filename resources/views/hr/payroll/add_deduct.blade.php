@extends('hr.layout')
@section('title', 'Salary Adjustment')
@section('main-content')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Payroll</a>
                </li>
                <li class="active">Salary Adjustment (Add/Deduct Bulk Upload)</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">

            <div class="row">
                <div class="col-xs-12">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                </div>
                <div class="col-sm-8 col-sm-offset-2" >
                     @if (Session::has('status') && Session::has('value'))

                        <div class="process_section">
                            <div class="progress">
                              <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" id="progress-bar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    @else
                        <div class="bulk_upload_section" >
                            <div class="panel panel-success">
                                <div class="panel-heading"><h6>Bulk Upload</h6></div>

                                <div class="panel-body">
                                    {{ Form::open(['url'=>'hr/payroll/add_deduct', 'files' => true,  'class'=>'form-horizontal']) }}


                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="file">Salary Add/Deduct File <span style="font-size: 9px">(only<strong>.xls/xlsx</strong> file supported.)</span> <a href="{{ url('hr/payroll/sample_file') }}" >Download Sample File </a></label>
                                            <div class="col-sm-8">
                                                <input type="file" name="file" id="file_upload" class="col-xs-12" data-validation="required" data-validation-allowing="xls,xlsx" style="margin-top: 3%;" />
                                                 <span id="file_upload_error" class="red" style="display: none; font-size: 14px;">Only <strong>xls or xlsx</strong> file supported.</span>
                                            </div>
                                        </div>

                                    <div class="clearfix form-actions bulk_form_button">
                                        <div class="col-sm-offset-4 col-sm-8 no-padding">
                                            <button class="btn btn-xs" type="reset">
                                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                            </button>
                                            &nbsp; &nbsp; &nbsp;
                                            <button type="submit" class="btn btn-info btn-xs" id="upload" type="button">
                                                <i class="ace-icon fa fa-check bigger-110"></i> Upload
                                            </button>

                                        </div>
                                    </div>


                                    {{ Form::close() }}
                                </div>

                            </div>
                        </div>
                    @endif
                </div>

                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function (){
        $('#file_upload').on('change', function(){
            var fileExtension = ['xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#file_upload_error').show();
                $(this).val('');
            }
            else{
                $('#file_upload_error').hide();
            }
        });
    });
</script>
@endsection
