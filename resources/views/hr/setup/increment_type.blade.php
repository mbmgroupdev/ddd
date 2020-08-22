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
				<li class="active"> Increment Type </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            
                @include('inc/message')
        <div class="panel panel-info">
          <div class="panel-heading"><h6>Increment Type</h6></div> 
            <div class="panel-body">
            <div class="row">
                  <!-- Display Erro/Success Message -->
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/increment_type')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="increment_type" > Increment Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="increment_type" name="increment_type" placeholder="Increment Type" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128"/>
                            </div>
                        </div>
 
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
                <div class="col-sm-12 col-xs-12">
                  <div class="clearfix form-actions">
                    <div class="col-md-offset-4 col-md-4 text-center"> 
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
                    </form>
            </div> 
            </div>
            </div>
        <div class="panel panel-info">
              <div class="panel-heading"><h6>Increment Type List</h6></div> 
                <div class="panel-body">  
                <div class="col-sm-offset-2 col-sm-8">
                    <table id="dataTables" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Increment Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $data AS $type)
                            <tr>
                                <td>{{ $type->increment_type }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a type="button" href="{{ url('hr/setup/increment_type_edit/'.$type->id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                        <a href="{{ url('hr/setup/increment_type_delete/'.$type->id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        pagingType: "full_numbers" ,
        // searching: false,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'

    }); 
});
</script>
@endsection