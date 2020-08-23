@extends('hr.layout')
@section('title', 'Add Users')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">Add User</h4>
               </div>
            </div>
            <div class="iq-card-body">   
               <form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/store')}}">
                  @csrf
                  <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group has-float-label select-search-group">
                           {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate ID', 'id'=>'associate_id', 'class'=> 'associates form-control']) }}
                           <label  for="associate_id"> Associate's ID </label>
                           <div class="invalid-feedback">
                              Please select associate id!
                           </div>
                        </div>
                        <div class="form-group has-float-label">
                           <label  for="name"> Name<span class="text-danger">*</span> </label>
                           <input type="text" id="name" name="name" placeholder="Enter name" class="form-control"  value="{{ old('name') }}" required>
                           <div class="invalid-feedback">
                              Please enter name!
                           </div>
                        </div>
                        <div class="form-group has-float-label">
                           <label  for="associate_id"> Email<span class="text-danger">*</span></label>
                           <input type="text" id="email" name="email" placeholder="Email Address"  value="{{ old('email') }}" class="form-control" required />
                           <div class="invalid-feedback">
                              Please enter email address!
                           </div>
                        </div>
                        <div class="form-group">
                           <span class="text-muted">Default password for user is </span><strong class="text-success">123456</strong >
                           
                        </div>
                        
                        
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group has-float-label select-search-group">
                           {!! Form::select('role', $roles, old('role'), ['class' => 'form-control', 'required' => 'required','placeholder' => 'Select a role']) !!}
                           <label  for="role"> Role<span class="text-danger">*</span> </label>
                           <div class="invalid-feedback pt-40">
                              Please select a role!
                           </div>
                        </div>
                        
                        <div class="form-group ">
                           <label  for="roles" >Unit Permission </label>
                           <br>
                           @foreach($units as $key => $unit)
                           <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                              <input class="custom-control-input bg-success" type="checkbox" value="{{ $unit->hr_unit_id }}" id="unit{{ $unit->hr_unit_id }}" name="unit_permissions[]" >
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
                        
                        
                        <div class="form-group text-right">
                           <button class="btn btn-primary btn-100" type="submit">Save</button>
                        </div>
                     </div>
                  </div>
               </form>

            </div>
         </div>
      </div>
   </div>
@endsection