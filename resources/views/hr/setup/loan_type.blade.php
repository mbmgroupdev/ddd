@extends('hr.layout')
@section('title', 'Loan Type')
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

        @include('inc/message')
		<div class="row"> 
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Loan Type</h6></div> 
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/loan_type')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group">
                                <label  for="hr_unit_name" >Loan Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <input type="text" id="hr_loan_type_name" name="hr_loan_type_name" placeholder="Loan Type Name" class="form-control" required/>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <table id="global-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Loan Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loantype as $lt)
                                    <tr>
                                        <td>{{ $lt->hr_loan_type_name }}</td>
                                        <td>
                                        <div class="btn-group">
                                            <a type="button" href="{{ url('hr/setup/loan_type/edit/'.$lt->id)}}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('hr/setup/loan_type/delete/'.$lt->id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                        </div>
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
           
		</div><!-- /.page-content -->
	</div>
</div>
@endsection