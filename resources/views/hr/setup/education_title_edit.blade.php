@extends('hr.layout')
@section('title', 'Education Edit')
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
                <li class="active"> Education Edit</li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4"> 
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Education Title</h6></div> 
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/education_title_update')  }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$degree->id}}">
                            <div class="form-group">
                                <label for="id"> Education Level  </label>
                                {{ Form::select('id', $levelList, $degree->lvl_id, ['placeholder'=>'Select Education Level', 'id'=>'id', 'class'=> 'form-control', 'data-validation' => 'required']) }}  
                            </div> 

                            <div class="form-group">
                                <label for="education_degree_title" >Exam/Degree Title<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <input type="text" id="education_degree_title" name="education_degree_title" placeholder="Exam/Degree Title Name" class="form-control" required  value="{{$degree->education_degree_title}}"/>
                            </div>
                            <div class="form-group" > 
                                <button class="btn btn-primary" type="submit">
                                    <i class=" fa fa-check "></i> Submit
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
        </div><!-- /.page-content -->
    </div>
</div>
@endsection