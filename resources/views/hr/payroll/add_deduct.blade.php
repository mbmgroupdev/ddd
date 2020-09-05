@extends('hr.layout')
@section('title', 'Salary Adjustment')
@section('main-content')
<div class="main-content">
    <div class="col-12">
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

        <div class="panel">

            <div class="row">
                <div class="col-12">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                </div>
                <div class="col-12" >
                     @if (Session::has('status') && Session::has('value'))

                        <div class="process_section">
                            <div class="progress">
                              <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" id="progress-bar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        @else
                        <div class="bulk_upload_section" >
                            <div class="panel panel-success">
                                <div class="panel-heading"><h6>Salary Adjustment</h6></div>

                                <div class="panel-body">
                                    <div class="row justify-content-center">
                                        <div class="col-5">
                                            
                                            {{ Form::open(['url'=>'hr/payroll/add_deduct', 'files' => true,  'class'=>'form-horizontal']) }}
                                                <p class="mb-5">(only<strong class="text-danger">.xls/xlsx</strong> file supported.)</span> <a href="{{ url('hr/payroll/sample_file') }}" >Download Sample File </a></p>
                                                <div class="form-group  file-zone">
                                                    <label  for="file"> Salary Add/Deduct File</label>
                                                    <input type="file" id="file_upload" name="file" class="file-type-validation" data-file-allow='["xls","xlsx"]' autocomplete="off" />
                                                    <div class="invalid-feedback" role="alert">
                                                        <strong>Select a file</strong>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn " type="reset">
                                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                    </button>
                                                    &nbsp; &nbsp; &nbsp;
                                                    <button type="submit" class="btn btn-primary" id="upload" type="button">
                                                        <i class="ace-icon fa fa-check bigger-110"></i> Upload
                                                    </button>
                                                </div>

                                            {{ Form::close() }}
                                        </div>
                                    </div>
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
@push('js')
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
@endpush
@endsection
