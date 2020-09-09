@extends('hr.layout')
@section('title', 'Edit Role')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
           <div class="panel">
                <div class="panel-heading">
                    <h6>Edit Role
                        <div class="pull-right">
                            
                            <a class="btn btn-primary" href="{{url('hr/adminstrator/role/create')}}"><i class="las la-user-tie f-18"></i> Edit Role</a> 
                            <a class="btn btn-primary" href="{{url('hr/adminstrator/roles')}}"><i class="las la-shield-alt f-18"></i> Role List</a>
                        </div>
                    </h6>
                </div>
                <div class="panel-body">
                    <form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/role/store')}}">
                        @csrf
                        <input type="hidden" name="id" id="role_id" value="{{$role->id}}">
                        <div class="row justify-content-md-center mb-3">
                        	<div class="col-4">
        	                    <div class="form-group has-float-label">
                                    <input id="role-name" type="text" name="name" class="form-control" required placeholder="Enter role name" value="{{$role->name}}" > 
                                    <label for="role-name">Role Name</label> 
                                    <div class="invalid-feedback">
                                      Please enter role name!
                                   </div> 
                                </div>
                        	</div>
                        </div>
                        <div class="row">
                        	<div id="permission-gallery" class="col-12">
                        		<ul class="nav nav-tabs"  role="tablist">
                                    @foreach($permissions as $key => $module)
                                    <li class="nav-item">
                                        <a class="nav-link @if($key == 'HR') active @endif" id="{{$key}}-tab" data-toggle="tab" href="#{{$key}}" role="tab" aria-controls="{{$key}}" aria-selected="true">{{$key}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" >
                                    @foreach($permissions as $key => $module)
                                    <div class="tab-pane fade @if($key == 'HR')active show @endif" id="{{$key}}" role="tabpanel" aria-labelledby="{{$key}}-tab">
                                        <div id="accordion-{{$key}}">
                                            @php $count = 0; @endphp
                                            @foreach($module as $key1 => $group)
                                                @php $count++; @endphp
                                                <div class="card">
                                                    <div class="card-header">
                                                        <input type="checkbox" id="Sl-{{$key}}-{{$count}}" style="transform: scale(1.5);" disabled="disabled"> &nbsp;
                                                            <a class="permission-item @if($count != 1) collapsed @endif" data-toggle="collapse" href="#{{$key}}-{{$count}}">
                                                            {{$key1}} <i class="fa fa-angle-double-right f-16"></i>
                                                        </a>
                                                    </div>
                                                    <div id="{{$key}}-{{$count}}" class="collapse @if($count == 1)show @endif" data-parent="#accordion-{{$key}}">
                                                        <div class="card-body">
                                                            <div class="row permissions_{{$key1}}">
                                                                @foreach($group as $key2 => $permission)
                                                                <div class="col-sm-4">
                                                                    <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline ">
                                                                        <input type="checkbox" class="permissions custom-control-input bg-success" id="perm-{{$permission->id}}" value="{{$permission->name}}" name="permissions[]" @if($role->hasPermissionTo($permission->name)) checked  @endif>
                                                                        <label class="custom-control-label" for="perm-{{$permission->id}}">{{$permission->name}}</label>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                  @endforeach
                                </div>
                        	</div>
                        </div>
                        <div class="row">
                            <div class="col-12 mt-3 text-right ">
                                <button type="submit" class="btn btn-primary btn-100"> Update</button>
                            </div>

                    </form>
                </div>
                
            </div>
         </div>
      </div>
@push('js')
<script type="text/javascript">
    $(document).on('click','.perm-group', function(){
        
        $(this).parent()
               .next()
               .find('input:checkbox')
               .prop('checked', this.checked);

    });
    
    $(document).on('change', '.permissions', function(){
        $('.perm-group').each(function() {
            if($(this).parent().next().find('input:checkbox').not(':checked').length == 0){
                $(this).prop('checked', true);
            }else{
                $(this).prop('checked', false);
            }
        });

        var type = 'revoke';
        if($(this).is(':checked')){
            type = 'assign';
        }
        var data = {
            type : type,
            permission : $(this).val(),
            id : $('#role_id').val()
        }

        $.ajax({
            url : "{{ url('hr/adminstrator/role/sync-permission') }}",
            type: 'get',
            data: data,
            success: function(res)
            {
                toastr.options.progressBar = true ;
                toastr.options.positionClass = 'toast-top-right';
                toastr.success('Permission '+res+' role!');
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });
</script>
@endpush
@endsection