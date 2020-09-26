@extends('hr.layout')
@section('title', 'Edit Users'. $user->name)
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
      </ul><!-- /.breadcrumb --> 
  </div>
  @include('inc/message')
   <div class="panel">
      <div class="panel-heading">
         <h6 >{{$user->name}} - Edit User
            <a href="{{url('hr/adminstrator/user/create')}}" class="btn btn-sm btn-primary pull-right"> Add User</a>
         </h6>
      </div>
      <div class="panel-body">   
         <form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/update/'.$user->id)}}">
            @csrf
            <div class="row justify-content-center">
               <div class="col-sm-4">
                  <div class="user-details-block" >
                      <div class="user-profile text-center mt-0">
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
               <div class="col-sm-4">
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
                  {{-- <div class="form-group">
                     <span class="text-muted">Default password for user is </span><strong class="text-success">123456</strong >
                     
                  </div> --}}
                  
                  
               </div>
               <div class="col-sm-3">
                  
                  
                  <div class="form-group ">
                     <label  for="roles" >Unit Permission </label>
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
                  
                  {{-- <div class="form-group has-float-label">
                     <label  for="associate_id"> Password<span class="text-danger">*</span></label>
                     <input type="password" id="password" name="password" placeholder="Password"  value="{{ old('password') }}" class="form-control" required />
                     <div class="invalid-feedback">
                        Please enter password!
                     </div>
                  </div>
                  <div class="form-group has-float-label">
                     <label  for="associate_id"> Confirm Password<span class="text-danger">*</span></label>
                     <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"  value="{{ old('password_confirmation') }}" class="form-control" required />
                     <div class="invalid-feedback">
                        Please enter password!
                     </div>
                  </div> --}}
                  
                  
                  <div class="form-group">
                     <button class="btn btn-primary btn-100" type="submit">Update</button>
                  </div>
               </div>
            </div>
         </form>

      </div>
   </div>
@endsection