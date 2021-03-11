@extends('hr.layout')
@section('title', 'Edit Users '. $user->name)
@push('css')
	<style type="text/css">
		.btn-special{
			border: 1px solid #089eaf;
			border-radius: 20px;
			padding: 2px 15px;
			color: #089eaf;
			text-transform: uppercase;
		}
		.btn-special:hover{
			color: #fff;
			background: #089eaf;
		}
	</style>
@endpush
@section('main-content')
   <div class="breadcrumbs ace-save-state" id="breadcrumbs">
	  <ul class="breadcrumb">
		  <li>
			 <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
		  </li>
		  <li>
			  <a href="#">Adminstrator</a>
		  </li>
		  <li>
			  <a href="#">{{$user->name}}</a>
		  </li>
		  <li class="active">Edit</li>
		  <li class="top-nav-btn">
			  <a href="{{url('hr/adminstrator/user/create')}}" class="btn btn-sm btn-primary pull-right"> Add User</a>
		  </li>
	  </ul><!-- /.breadcrumb --> 
  </div>
  @include('inc/message')  
	

	<div class="row justify-content-center">
	   <div class="col-sm-3 " style="margin-top: 85px;">

			<form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/update/'.$user->id)}}">
				<div class="panel">
					<div class="panel-body" style="height: 140px;"> 
						  <div class="user-details-block" style="top: -85px;position: relative;">
							  <div class=" text-center mt-0" >
								@if($user->employee)
								   <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($user->employee) }} " >
								@else
								  <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " >
								@endif
							  </div>
							  <div class="text-center mt-3">
							   <h4><b id="emp-name">{{ $user->name }}</b></h4>
							   <p class="mb-0" id="designation">
								  {{ $user->email }}</p>
							   
							  </div>
						  </div>
					</div>
				</div>
			</form>
			<form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/update/'.$user->id)}}">
				@csrf
				<div class="panel">
					<div class="panel-heading text-center">
						<h6>Password</h6>
					</div>
					<div class="panel-body"> 
						  <div class="form-group has-float-label">
								 <label  for="password">Reset Password<span class="text-danger">*</span></label>
								 <input type="text" id="password" name="password" placeholder="Change Password"  class="form-control" required  />
								 <div class="invalid-feedback">
									Please enter password!
								 </div>
							</div>
							<div class="form-group text-center">
							 <button class="btn btn btn-special btn-100" type="submit">Reset</button>
						  </div>
					</div>
				</div>
			</form>
		</div>
		<div class="col-sm-3 pl-0 pr-0">
			<form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/update/'.$user->id)}}">
				@csrf
				<div class="panel" style="min-height: 400px;">
					<div class="panel-heading text-center">
						<h6>Info</h6>
					</div>
					<div class="panel-body"> 
					 <div class="form-group has-float-label select-search-group">
						@if($user->associate_id)
						   <input type="text" class="form-control"  value="{{ $user->associate_id }}" disabled>
						@else
						{{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate ID', 'id'=>'associate_id', 'class'=> 'associates form-control']) }}
						@endif
						<label  for="associate_id"> Associate's ID </label>
						<div class="invalid-feedback">
						   Please select associate id!
						</div>
					 </div>
				  <div class="form-group has-float-label">
					 <label  for="name"> Name<span class="text-danger">*</span> </label>
					 <input type="text" id="name" name="name" placeholder="Enter name" class="form-control"  value="{{ $user->name }}" required>
					 <div class="invalid-feedback">
						Please enter name!
					 </div>
				  </div>
				  <div class="form-group has-float-label">
					 <label  for="associate_id"> Email<span class="text-danger">*</span></label>
					 <input type="text" id="email" name="email" placeholder="Email Address"  value="{{ $user->email }}" class="form-control" required  />
					 <div class="invalid-feedback">
						Please enter email address!
					 </div>
				  </div>
				  <div class="form-group has-float-label select-search-group">
					 {!! Form::select('role', $roles, $role, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Select a role']) !!}
					 <label  for="role"> Role<span class="text-danger">*</span> </label>
					 <div class="invalid-feedback pt-40">
						Please select a role!
					 </div>
				  </div>
				  <div class="form-group text-center">
					 <button class="btn btn btn-special btn-100" type="submit">Update</button>
				  </div>
				</div>
			</div>
			</form>
		  
		  
		</div>
		<div class="col-sm-6 " style="padding-right: 15px;">
			<form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/update/'.$user->id)}}">
				@csrf
				<div class="panel" style="min-height: 400px;">
					<div class="panel-heading text-center">
						<h6>Permission</h6>
					</div>
					<div class="panel-body">
						<div class="row"> 
							<div class="col-sm-4 pr-0">
							  <div class="form-group ">
								 <label  for="roles" ><b>Unit</b> </label>
								 <br>
								 @php 
									$unit_permission = [];
									if($user->unit_permissions){
									   $unit_permission =  explode(",",$user->unit_permissions);
									}
								 @endphp
								 @foreach($units as $key => $unit)
								 <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
									<input class="custom-control-input bg-success" type="checkbox" value="{{ $unit->hr_unit_id }}" id="unit{{ $unit->hr_unit_id }}" name="unit_permissions[]"  @if(in_array($unit->hr_unit_id, $unit_permission)) checked  @endif>
									<label class="custom-control-label" for="unit{{ $unit->hr_unit_id }}">
									{{ $unit->hr_unit_short_name }}
									</label>
								 </div>
								 @endforeach
							  </div>
		  
							</div>
							<div class="col-sm-4">
							  
							  
							  <div class="form-group ">
								 <label  for="roles" ><b>Location</b> </label>
								 <br>
								 @php 
									$location_permission = [];
									if($user->location_permission){
									   $location_permission =  explode(",",$user->location_permission);
									}
								 @endphp
								 @foreach($locations as $key => $location)
								 <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
									<input class="custom-control-input bg-success" type="checkbox" value="{{ $location->hr_location_id }}" id="location{{ $location->hr_location_id }}" name="location_permission[]"  @if(in_array($location->hr_location_id, $location_permission)) checked  @endif>
									<label class="custom-control-label" for="location{{ $location->hr_location_id }}">
									{{ $location->hr_location_name }}
									</label>
								 </div>
								 @endforeach
							  </div>

							</div>
							<div class="col-sm-4">
							  
							  
							  <div class="form-group ">
								 <label  for="roles" ><b>Buyer</b> </label>
								 <br>
								 @php 
									$buyer_permissions = [];
									if($user->buyer_permissions){
									   $buyer_permissions =  explode(",",$user->buyer_permissions);
									}
								 @endphp
								 @foreach($buyers as $key => $buyer)
								 <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
									<input class="custom-control-input bg-success" type="checkbox" value="{{ $buyer->b_id }}" id="buyer{{ $buyer->b_id }}" name="buyer_permissions[]"  @if(in_array($buyer->b_id, $buyer_permissions)) checked  @endif>
									<label class="custom-control-label" for="buyer{{ $buyer->b_id }}">
									{{ $buyer->b_name }}
									</label>
								 </div>
								 @endforeach
							  </div>
							  
							  
							  
							</div>
							<div class="col-sm-12">
								<div class="form-group text-center">
								 <button class="btn btn btn-special btn-100" type="submit">Update</button>
							  </div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	 
@endsection