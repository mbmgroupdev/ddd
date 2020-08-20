@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">Add Role</h4>
               </div>
            </div>
            <div class="iq-card-body"> 
                <form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/role/store')}}">
                    <div class="row justify-content-md-center mb-3">
                    	<div class="col-4">
    	                    <div class="form-group has-float-label">
                                <input id="role-name" type="text" name="name" class="form-control" required placeholder="Enter role name"> 
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
    					                           	<div class="custom-control custom-checkbox checkbox-icon custom-control-inline">
    					                              <input type="checkbox" class="custom-control-input" id="group_{{strtolower($key)}}_{{$count}}" >
    					                              <label class="custom-control-label" for="group_{{strtolower($key)}}_{{$count}}"><i class="fa fa-shield"></i></label>
    					                           </div>
    							      				<a class="card-link @if($count != 1) collapsed @endif" data-toggle="collapse" href="#{{strtolower($key)}}_{{$count}}">
    							        				{{$key1}} &nbsp;<i class="fa fa-angle-double-right f-16"></i>
    							      				</a>
    							    			</div>
    							    			<div id="{{strtolower($key)}}_{{$count}}" class="collapse @if($count == 1)show @endif" data-parent="#accordion-{{$key}}">
    							      				<div class="card-body">
    							      					<div class="row permissions_{{strtolower($key)}}_{{$count}}">
    		                                                @foreach($group as $key2 => $permission)
    		                                                <div class="col-sm-4">
    		                                                	<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
    								                              	<input type="checkbox" class="custom-control-input bg-success" id="perm-{{$permission->id}}" value="{{$permission->name}}" name="permissions[]">
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
                            <button type="submit" class="btn btn-primary btn-100"> Save</button>
                        </div>

                </form>
                
            </div>
         </div>
      </div>
   </div>
@endsection