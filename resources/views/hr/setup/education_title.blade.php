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
				<li class="active"> Education Title </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/education_title')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Education Title</h6></div> 
                <div class="panel-body">
                <div class="col-sm-6 col-md-offset-3">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="id"> Education Level  </label>
                            <div class="col-sm-8"> 
                                {{ Form::select('id', $levelList, null, ['placeholder'=>'Select Education Level', 'id'=>'id', 'class'=> 'col-xs-12', 'data-validation' => 'required']) }}  
                            </div>
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="education_degree_title" >Exam/Degree Title<span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="education_degree_title" name="education_degree_title" placeholder="Exam/Degree Title Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128" />
                            </div>
                        </div>
                    <!-- PAGE CONTENT ENDS -->
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
                </div>
            </div>
        </div>
                    </form> 
                <!-- /.col -->

        <div class="panel panel-info">
              <div class="panel-heading"><h6>Education Title List</h6></div> 
                <div class="panel-body">
                <div class="col-sm-offset-3 col-sm-6">
                    <table id="dataTables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Degree</th>
                                    <th>Education Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($degrees as $degree)
                                <tr>
                                    <td>{{ $degree->education_degree_title }}</td>
                                    <td>{{ $degree->education_level_title }}</td>
                                    <td>
                                    <div class="btn-group">
                                        <a type="button" href="{{ url('hr/setup/education_title/edit/'.$degree->id)}}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                        <a href="{{ url('hr/setup/education_title/delete/'.$degree->id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                    </div>
                                </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        pagingType: "full_numbers" ,
        searching: true,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'

    }); 
});
</script>
@endsection