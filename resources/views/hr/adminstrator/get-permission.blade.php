@php dd($user) @endphp
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
	    			<div class="card-header">{{-- 
	    				<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                          	<input type="checkbox" class="custom-control-input bg-success" id="Sl-{{$key}}-{{$count}}" >
                          	<label class="custom-control-label" for="Sl-{{$key}}-{{$count}}"></label>
                       	</div> --}}
                       	<input type="checkbox" id="Sl-{{$key}}-{{$count}}" style="transform: scale(1.5);"> &nbsp;
                            <a class="permission-item @if($count != 1) collapsed @endif" data-toggle="collapse" href="#{{$key}}-{{$count}}">
	        				{{$key1}} <i class="fa fa-angle-double-right f-16"></i>
	      				</a>
	    			</div>
	    			<div id="{{$key}}-{{$count}}" class="collapse @if($count == 1)show @endif" data-parent="#accordion-{{$key}}">
	      				<div class="card-body">
	      					<div class="row permissions_{{$key1}}">
                                @foreach($group as $key2 => $permission)
                                <div class="col-sm-4">
                                	<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline @if($user->hasDirectPermission($permission->name)) direct  @endif">
		                              	<input type="checkbox" class="custom-control-input bg-success" id="perm-{{$permission->id}}" value="{{$permission->name}}" name="permissions[]" @if($user->hasPermissionTo($permission->name)) checked @if(!$user->hasDirectPermission($permission->name)) disabled="disabled" rel='tooltip' data-tooltip-location='top' data-tooltip='This permissons can not revoked here. Please change in Roles page'  @endif @endif>
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