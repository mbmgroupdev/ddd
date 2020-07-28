@extends('hr.layout')
@section('title', 'HR Dashboard')
@section('main-content')
	<div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">Vertical Wizard</h4>
               </div>
            </div>
            <div class="iq-card-body">
               <div class="row">
                  <div class="col-md-3">
                     <ul id="top-tabbar-vertical" class="p-0">
                        <li class="active" id="personal">
                           <a href="javascript:void();">
                           <i class="ri-lock-unlock-line text-primary"></i><span>Personal</span>
                           </a>
                        </li>
                        <li id="contact">
                           <a href="javascript:void();">
                           <i class="ri-user-fill text-danger"></i><span>Medical</span>
                           </a>
                        </li>
                        <li id="official">
                           <a href="javascript:void();">
                           <i class="ri-camera-fill text-success"></i><span>IE</span>
                           </a>
                        </li>
                        
                     </ul>
                  </div>
                  <div class="col-md-9">
                     <form id="form-wizard3" class="text-center">
                        <!-- fieldsets -->
                        <fieldset>
                           <div class="form-card text-left">
                              <div class="row">
                                 <div class="col-12">
                                    <h3 class="mb-4">Personal Information:</h3>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="fname">First Name: *</label>
                                       <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required="required" />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="lname">Last Name: *</label>
                                       <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label>Gender: *</label>
                                       <div class="form-check">
                                          <div class="custom-control custom-radio custom-control-inline">
                                             <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
                                             <label class="custom-control-label" for="customRadio1"> Male</label>
                                          </div>
                                          <div class="custom-control custom-radio custom-control-inline">
                                             <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                                             <label class="custom-control-label" for="customRadio2"> Female</label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="dob">Date Of Birth: *</label>
                                       <input type="date" class="form-control" id="dob" name="dob" />
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <button id="submit" type="button" name="next" class="btn btn-primary next action-button float-right" value="Next" >Next</button>
                        </fieldset>
                        <fieldset>
                           <div class="form-card text-left">
                              <div class="row">
                                 <div class="col-12">
                                    <h3 class="mb-4">Contact Information:</h3>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="email">Email Id: *</label>
                                       <input type="email" class="form-control" id="email" name="email" placeholder="Email Id" />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="ccno">Contact Number: *</label>
                                       <input type="text" class="form-control" id="ccno" name="ccno" placeholder="Contact Number" />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="city">City: *</label>
                                       <input type="text" class="form-control" id="city" name="city" placeholder="City." />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="state">State: *</label>
                                       <input type="text" class="form-control" id="state" name="state" placeholder="State." />
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <button type="button" name="next" class="btn btn-primary next action-button float-right" value="Next" >Next</button>
                           <button type="button" name="previous" class="btn btn-dark previous action-button-previous float-right mr-3" value="Previous" >Previous</button>
                        </fieldset>
                        
                        <fieldset>
                           <div class="form-card text-left">
                              <div class="row">
                                 <div class="col-12">
                                    <h3 class="mb-4 text-left">Payment:</h3>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="panno">Pan No: *</label>
                                       <input type="text" class="form-control" id="panno" name="panno" placeholder="Pan No." />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="accno">Account No: *</label>
                                       <input type="text" class="form-control" id="accno" name="accno" placeholder="Account No." />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="holname">Account Holder Name: *</label>
                                       <input type="text" class="form-control" id="holname" name="accname" placeholder="Account Holder Name." />
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="form-group">
                                       <label for="ifsc">IFSC Code: *</label>
                                       <input type="text" class="form-control" id="ifsc" name="ifsc" placeholder="IFSC Code." />
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <a class="btn btn-primary action-button float-right" href="form-wizard-vertical.html" >Submit</a>
                           <button type="button" name="previous" class="btn btn-dark previous action-button-previous float-right mr-3" value="Previous" >Previous</button>
                        </fieldset>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @push('js')
	  <!-- Countdown JavaScript -->
	  <script src="{{ asset('assets/js/countdown.min.js') }}"></script>
	  <!-- Counterup JavaScript -->
	  <script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
	  <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
	  <!-- Wow JavaScript -->
	  <script src="{{ asset('assets/js/wow.min.js') }}"></script>
	  <!-- Apexcharts JavaScript -->
	  <script src="{{ asset('assets/js/apexcharts.js') }}"></script>
	  <!-- Slick JavaScript -->
	  <script src="{{ asset('assets/js/slick.min.js') }}"></script>
	  <!-- Select2 JavaScript -->
	  <script src="{{ asset('assets/js/select2.min.js') }}"></script>
	  <!-- Owl Carousel JavaScript -->
	  <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
	  <!-- lottie JavaScript -->
	  <script src="{{ asset('assets/js/lottie.js') }}"></script>
	  <!-- Chart Custom JavaScript -->
	  <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
   @endpush
@endsection