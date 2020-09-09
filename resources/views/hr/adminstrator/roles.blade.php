@extends('hr.layout')
@section('title', 'Roles')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
           <div class="panel">
                <div class="panel-heading">
                    <h6>Roles
                        <div class="pull-right">
                            
                            <a class="btn btn-primary" href="{{url('hr/adminstrator/user/create')}}"><i class="las la-user-tie f-18"></i> Add User</a> 
                            <a class="btn btn-primary" href="{{url('hr/adminstrator/role/create')}}"><i class="las la-shield-alt f-18"></i> Add Role</a>
                        </div>
                    </h6>
                </div>
                <div class="panel-body">
                    <table id="global-datatable" class="datatable table table-bordered table-striped" >
                        <thead>
                            <tr>
                                <th>Serial No.</th>
                                <th>Role</th> 
                                <th>Hierarchy</th> 
                                <th>Permissions</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($roles) > 0)
                                @foreach ($roles as $role)
                                    <tr data-entry-id="{{ $role->id }}">
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->hierarchy }}</td>
                                        <td>
                                            @foreach ($role->permissions()->pluck('name') as $permission)
                                                <span class="label label-info label-many">{{ $permission }}</span>
                                            @endforeach
                                        </td>
                                        <td style="width:80px;"> 
                                            <a href="{{ url('hr/adminstrator/role/edit/'.$role->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
                                            <a href="{{ url('hr/adminstrator/role/delete',[$role->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a> 
                                        </td>
                                    </tr>
                                @endforeach 
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection