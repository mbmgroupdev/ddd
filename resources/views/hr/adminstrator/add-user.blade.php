@extends('hr.layout')
@section('title', 'Add User')
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
              <a href="#">User</a>
          </li>
          <li class="active">Create</li>
      </ul><!-- /.breadcrumb --> 
  </div>
  @include('inc/message')
   <div class="panel">
      <div class="panel-heading">
            <h6 class="card-title">
               Add User
               <a class="pull-right btn btn-primary" href="{{url('hr/adminstrator/users')}}">User List</a>
            </h6>
      </div>
      <div class="panel-body">   
         <form class="needs-validation" novalidate method="post" action="{{url('hr/adminstrator/user/store')}}">
            @csrf
            <div class="row">
               <div class="col-sm-4">
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
                  <div class="form-group has-float-label select-search-group">
                     {!! Form::select('role', $roles, old('role'), ['class' => 'form-control', 'required' => 'required','placeholder' => 'Select a role']) !!}
                     <label  for="role"> Role<span class="text-danger">*</span> </label>
                     <div class="invalid-feedback pt-40">
                        Please select a role!
                     </div>
                  </div>
                  <div class="form-group">
                     <span class="text-muted">Default password for user is </span><strong class="text-success">123456</strong >
                     
                  </div>
                  <div class="form-group">
                     <button class="btn btn-primary btn-100" type="submit">Save</button>
                  </div>
                  
                  
               </div>
               <div class="col-sm-2">
                  
                  
                  <div class="form-group ">
                     <label  for="roles" ><b>Unit Permission </b></label>
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
                  
                  
                  
                  
               </div>
               <div class="col-sm-2">
                  
                  
                  <div class="form-group ">
                     <label  for="roles" ><b>Location </b></label>
                     <br>
                     @foreach($locations as $key => $location)
                     <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                        <input class="custom-control-input bg-success" type="checkbox" value="{{ $location->hr_location_id }}" id="location{{ $location->hr_location_id }}" name="location_permission[]" >
                        <label class="custom-control-label" for="location{{ $location->hr_location_id }}">
                        {{ $location->hr_location_name }}
                        </label>
                     </div>
                     @endforeach
                  </div>
                  
               </div>
              <div class="col-sm-4">
                  <div class="user-details-block" >
                      <div class="user-profile text-center mt-0">
                          <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                      </div>
                      <div class="text-center mt-3">
                       <h4><b id="emp-name">-------------</b></h4>
                       <p class="mb-0" id="designation">
                          --------------------------</p>
                       <p class="mb-0" >
                          Oracle ID: <span id="oracle_id" class="text-success">-------------</span>
                       </p>
                       <p class="mb-0" >
                          Associate ID: <span id="associate_id_emp" class="text-success">-------------</span>
                       </p>
                       <p  class="mb-0">Department: <span id="department" class="text-success">------------------------</span> </p>
                       
                      </div>
                  </div>
              </div>  
            </div>
         </form>

      </div>
   </div>
@push('js')
<script type="text/javascript">
    $(document).on('change', '#associate_id', function(){ 
        var url = '{{url("/")}}'; 
        if( $(this).val() != ''){
            $.ajax({
                url : "{{ url('hr/timeattendance/station_as_info') }}",
                type: 'json',
                method: 'get',
                data: {associate_id: $(this).val()},
                success: function(data)
                {
                    $('#associate_id_emp').text(data['associate_id']);
                    $('#oracle_id').text(data['as_oracle_code']);
                    $('#name').val(data['as_name']);
                    $('#emp-name').text(data['as_name']);
                    $('#department').text(data['hr_department_name']);
                    $('#designation').text(data['hr_designation_name']);
                    
                    $('#avatar').attr('src', url+data['as_pic']); 
                },
                error: function()
                {
                }
            });
        }

    });

</script>
@endpush
@endsection