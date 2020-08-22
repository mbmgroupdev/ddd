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
				<li class="active"> Increment Type Update </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Increment Type Update </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/increment_type_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="increment_type" > Increment Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-9">
                                <input type="text" id="increment_type" name="increment_type" placeholder="Increment Type" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128" value="{{ $data->increment_type }}" />
                            </div>
                        </div>
                        
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
                <div class="col-sm-12 col-xs-12">
                    <div class="clearfix form-actions">
                        <div class="col-md-offset-4 col-md-4 text-center"> 
                            <button class="btn btn-sm btn-success" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i> Update
                            </button>

                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-sm" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                    </form> 
            </div>
		</div><!-- /.page-content -->
	</div>
</div>

@endsection