@extends('hr.layout')
@section('title', 'All Users')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title text-danger">Add User</h4>
               </div>
            </div>
            <div class="iq-card-body">   
               <form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/store')}}">
                  <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label  for="associate_id"> Associate's ID </label>
                           {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates form-control', 'required'=>'required']) }}
                           <div class="invalid-feedback">
                              Please select associate id!
                           </div>
                        </div>
                        <div class="form-group">
                           <label  for="associate_id"> Name </label>
                           <input type="text" id="name" name="name" placeholder="Name" class="form-control"  value="{{ old('name') }}" required>
                           <div class="invalid-feedback">
                              Please enter name!
                           </div>
                        </div>
                        <div class="form-group">
                           <label  for="associate_id"> Email</label>
                           <input type="text" id="email" name="email" placeholder="Email Address"  value="{{ old('email') }}" class="form-control" required />
                           <div class="invalid-feedback">
                              Please enter email address!
                           </div>
                        </div>
                        <div class="form-group">
                           <label  for="associate_id"> Password</label>
                           <input type="password" id="password" name="password" placeholder="Password"  value="{{ old('password') }}" class="form-control" required />
                           <div class="invalid-feedback">
                              Please enter password!
                           </div>
                        </div>
                        <div class="form-group">
                           <label  for="associate_id"> Confirm Password</label>
                           <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"  value="{{ old('password_confirmation') }}" class="form-control" required />
                           <div class="invalid-feedback">
                              Please enter password!
                           </div>
                        </div>

                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label  for="associate_id"> Roles </label>
                           {!! Form::select('roles[]', $roles, old('roles'), ['class' => 'form-control', 'multiple' => 'multiple', 'required' => 'required']) !!}
                           <div class="invalid-feedback">
                              Please select a role!
                           </div>
                        </div>
                         <div class="form-group">
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
                        <!--<div class="form-group">
                           <label  for="roles" >Buyer Permission </label>
                           <br>
                           @foreach($buyers as $key => $buyer)
                           <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                              <input class="custom-control-input bg-success" type="checkbox" value="{{ $buyer->b_id }}" id="buyer{{ $buyer->b_id }}" name="buyer_permissions[]" >
                              <label class="custom-control-label" for="buyer{{ $buyer->b_id }}">
                              {{ $buyer->b_name }}
                              </label>
                           </div>
                           @endforeach
                        </div>
                        <div class="form-group">
                           <label  for="roles" >Buyer Template Permission </label>
                           @foreach($templates as $buyerTemplate)
                           <div class="custom-control custom-radio">
                              <input type="radio" class="custom-control-input" id="but{{ $buyerTemplate->id }}" name="radio-stacked" required value="{{ $buyerTemplate->id }}">
                              <label class="custom-control-label" for="but{{ $buyerTemplate->id }}"> {{ $buyerTemplate->template_name }}</label>
                           </div>
                           @endforeach
                        </div> -->
                        <div class="form-group">
                           <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                     </div>
                  </div>
                        
                  
                  
               </form>

            </div>
         </div>
      </div>
   </div>
@endsection