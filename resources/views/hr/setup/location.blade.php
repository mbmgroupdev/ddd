@extends('hr.layout')
@section('title', 'Location')
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
            <li class="active"> Location </li>
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
                        Location
                        <a class="btn btn-primary pull-right" href="#list">Location List</a>
                    </h6>
                </div> 
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/location')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group has-float-label has-required ">
                                    <input type="text" id="hr_location_name" name="hr_location_name" placeholder="Location name" class="form-control" required="required"/>
                                    <label  for="hr_location_name" > Location Name </label>
                                </div>

                                <div class="form-group has-float-label has-required ">
                                    <input type="text" id="hr_location_short_name" name="hr_location_short_name" placeholder="Location short name" class="form-control"  required />
                                    <label  for="hr_location_short_name" > Location Short Name </label>
                                </div>

                                <div class="form-group has-float-label has-required select-search-group">
                                    {{ Form::select('hr_location_unit_id', $unitList, null, ['id' => 'hr_location_unit_id', 'placeholder' => 'Select Unit', 'class' => ' form-control', 'required' => 'required']) }}
                                    <label  for="hr_location_unit_id" > Unit </label>
                                    
                                </div>
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_location_name_bn" name="hr_location_name_bn" placeholder="লোকেশনের নাম" class="form-control" />
                                    <label  for="hr_location_name_bn" > লোকেশন (বাংলা) </label>
                                </div>

                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_location_address" name="hr_location_address" placeholder="Location name" class="form-control" />
                                    <label  for="hr_location_address" > Location Adrress </label>
                                </div>

                                
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_location_address_bn" name="hr_location_address_bn" placeholder="লোকেশনর ঠিকানা (বাংলা)" class="form-control"/>
                                    <label  for="hr_location_address_bn" > লোকেশনর ঠিকানা (বাংলা) </label>
                                </div>
                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_location_code" name="hr_location_code" placeholder="Location code" class="form-control" />
                                    <label  for="hr_location_code"> Location Code </label>
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
                    <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="false">Active</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash" role="tab" aria-controls="trash" aria-selected="false">Trash</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="active" role="tabpanel" aria-labelledby="active-tab">
                         
                            <div class="table-responsive">
                                <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                                <thead>
                                    <tr>
                                        
                                        <th width="30%">Location Name</th>
                                        <th width="30%">Short Name</th>
                                        <th width="30%">লোকেশন (বাংলা)</th>
                                        <th width="30%">Location Code</th>
                                        <th width="30%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $loc)
                                    <tr>
                                        
                                        <td>{{ $loc->hr_location_name }}</td>
                                        <td>{{ $loc->hr_location_short_name }}</td>
                                        <td>{{ $loc->hr_location_name_bn }}</td>
                                        <td>{{ $loc->hr_location_code }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a type="button" href="{{ url('hr/setup/location_update/'.$loc->hr_location_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a href="{{ url('hr/setup/location/'.$loc->hr_location_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive">
                                <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                                <thead>
                                    <tr>
                                        
                                        <th width="30%">Location Name</th>
                                        <th width="30%">Short Name</th>
                                        <th width="30%">লোকেশন (বাংলা)</th>
                                        <th width="30%">Location Code</th>
                                        <th width="30%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $loc)
                                    <tr>
                                        
                                        <td>{{ $loc->hr_location_name }}</td>
                                        <td>{{ $loc->hr_location_short_name }}</td>
                                        <td>{{ $loc->hr_location_name_bn }}</td>
                                        <td>{{ $loc->hr_location_code }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a type="button" href="{{ url('hr/setup/location_update/'.$loc->hr_location_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a href="{{ url('hr/setup/location/'.$loc->hr_location_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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

       </div>
    </div>
@endsection