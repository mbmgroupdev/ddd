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
				<li class="active"> Add Loan Type </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Update Loan Type </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/loan_type_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                <div class="col-sm-6 col-md-offset-3">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                        <input type="hidden" name="loan_type_id" id="loan_type_id" value="{{$loantype->id}}">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_unit_name" >Loan Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_loan_type_name" name="hr_loan_type_name" placeholder="Loan Type Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128" value="{{$loantype->hr_loan_type_name}}" />
                            </div>
                        </div>
                </div>

                <div class="col-sm-12 col-xs-12">
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 30px;"> 
                                <button class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-sm" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div>

                        <!-- /.row --> 
                    <!-- PAGE CONTENT ENDS -->
                </div>
                    </form> 
                <!-- /.col -->
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
@endsection