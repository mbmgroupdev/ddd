@extends('hr.layout')
@section('title', 'Floor Edit')
@section('main-content')
    @push('css')
    @endpush
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Human Resource </a>
            </li> 
            <li>
                <a href="#"> Library </a>
            </li>
            <li class="active"> Floor Edit </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       <div class="col-lg-2 pr-0">
           <!-- include library menu here  -->
           @include('hr.settings.library_menu')
       </div>
       <div class="col-lg-10 mail-box-detail">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>
                        Floor
                        <a class="btn btn-primary pull-right" href="#list">Floor List</a>
                    </h6>
                </div> 
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/floor_update')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <input type="hidden" name="hr_floor_id" value="{{ $floor->hr_floor_id }}">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('hr_floor_unit_id', $unitList, $floor->hr_floor_unit_id, ['placeholder'=>'Select Unit Name', 'id'=>'hr_floor_unit_id', 'class'=> 'form-control', 'required'=>'required']) }} 
                                    <label  for="hr_floor_unit_id"> Unit Name  </label>
                                </div>
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label">
                                    <input type="text" id="hr_floor_name" name="hr_floor_name" placeholder="Floor name" class="form-control" required value="{{ $floor->hr_floor_name }}"/>
                                    <label  for="hr_floor_name" > Floor Name  </label>
                                </div>

                                
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_floor_name_bn" name="hr_floor_name_bn" placeholder="ফ্লোরের নাম" class="form-control" value="{{ $floor->hr_floor_name_bn }}" required/>
                                    <label  for="hr_floor_name_bn" > ফ্লোর (বাংলা) </label>
                                </div>
                                <div class="form-group"> 
                                    <button class="btn pull-right btn-primary" type="submit">Submit</button>
                                </div>
                                
                            </div>
                                 

                            

                                
                        </div>    
                            
                    </form> 
                </div>
            </div>
            <div id="list" class="panel panel-info">
                <div class="panel-body">
                   
                         
                    <div class="table-responsive">
                        <table id="global-datatable" class="table table-striped table-bordered" style="display: block;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Unit Name</th>
                                    <th style="width: 20%;">Floor Name</th>
                                    <th style="width: 20%;">ফ্লোর (বাংলা)</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($floors as $floor)
                                <tr>
                                    <td>{{ $floor->hr_unit_name }}</td>
                                    <td>{{ $floor->hr_floor_name }}</td>
                                    <td>{{ $floor->hr_floor_name_bn }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a type="button" href="{{ url('hr/setup/floor_update/'.$floor->hr_floor_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('hr/setup/floor/'.$floor->hr_floor_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
    </div>
@endsection