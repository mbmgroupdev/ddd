@extends('hr.layout')
@section('title', 'Recruitment Process')
@push('css')
   <link rel="stylesheet" href="{{ asset('assets/css/recruitment.css')}}">
@endpush
@section('main-content')
	<div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">Recruitment Process</h4>
               </div>
            </div>
            <div class="iq-card-body">
               <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                     <div id="user" class="wizard-step active">
                        <a href="#user-detail" class="active btn">
                        <i class="ri-lock-unlock-line text-primary"></i><span>User Detail</span>
                        </a>
                     </div>
                     <div id="document" class="wizard-step">
                        <a href="#document-detail" class="btn btn-default disabled">
                        <i class="ri-user-fill text-danger"></i><span>Document Detail</span>
                        </a>
                     </div>
                     
                     <div id="confirm" class="wizard-step">
                        <a href="#cpnfirm-data" class="btn btn-default disabled">
                        <i class="ri-check-fill text-warning"></i><span>Confirm</span>
                        </a>
                     </div>
                  </div>
               </div>
               <form class="form">
                  <div class="row setup-content" id="user-detail">
                     <div class="col-sm-12">
                        <div class="col-md-12 p-0">
                           <h3 class="mb-4">User Information:</h3>
                           <div class="row">
                              <div class="form-group col-md-6">
                                 <label class="control-label">First Name</label>
                                 <input  maxlength="100" type="text" required="required" class="form-control" placeholder="Enter First Name"  />
                              </div>
                              <div class="form-group col-md-6">
                                 <label class="control-label">Last Name</label>
                                 <input maxlength="100" type="text" required="required" class="form-control" placeholder="Enter Last Name" />
                              </div>
                              <div class="col-md-6 form-group">
                                 <label for="uname" class="control-label">User Name: *</label>
                                 <input type="text" class="form-control" id="uname" required="required" name="uname" placeholder="Enter User Name">
                              </div>
                              <div class="col-md-6 form-group">
                                 <label for="emailid" class="control-label">Email Id: *</label>
                                 <input type="email" id="emailid" class="form-control" required="required" name="emailid" placeholder="Email ID">
                              </div>
                              <div class="col-md-6 form-group">
                                 <label for="pwd" class="control-label">Password: *</label>
                                 <input type="password" class="form-control" required="required" id="pwd" name="pwd" placeholder="Password">
                              </div>
                              <div class="col-md-6 form-group">
                                 <label for="cpwd" class="control-label">Confirm Password: *</label>
                                 <input type="password" class="form-control" id="cpwd" required="required" name="cpwd" placeholder="Confirm Password">
                              </div>
                              <div class="col-md-6 form-group">
                                 <label for="cno" class="control-label">Contact Number: *</label>
                                 <input type="text" class="form-control" required="required" id="cno" name="cno" placeholder="Contact Number">
                              </div>
                              <div class="col-md-6 form-group">
                                 <label for="acno" class="control-label">Alternate Contact Number: *</label>
                                 <input type="text" class="form-control" required="required" id="acno" name="acno" placeholder="Alternate Contact Number">
                              </div>
                              <div class="col-md-12 mb-3 form-group">
                                 <label for="address" class="control-label">Address: *</label>
                                 <textarea name="address" class="form-control" id="address" rows="5" required="required"></textarea>
                              </div>
                           </div>
                           <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
                        </div>
                     </div>
                  </div>
                  <div class="row setup-content" id="document-detail">
                     <div class="col-sm-12">
                        <div class="col-md-12 p-0">
                           <h3 class="mb-4">Document Details:</h3>
                           <div class="row">
                              <div class="col-md-6 form-group">
                                 <label for="fname" class="control-label">Company Name: *</label>
                                 <input type="text" class="form-control" required="required" id="fname" name="fname" placeholder="Company Name">
                              </div>
                              <div class="col-md-6 form-group">
                                 <div class="form-group">
                                    <label for="ccno" class="control-label">Contact Number: *</label>
                                    <input type="text" class="form-control" required="required" id="ccno" name="ccno" placeholder="Contact Number">
                                 </div>
                              </div>
                              <div class="col-md-6 form-group">
                                 <div class="form-group">
                                    <label for="url" class="control-label">Company Url: *</label>
                                    <input type="text" class="form-control" required="required" id="url" name="url" placeholder="Company Url.">
                                 </div>
                              </div>
                              <div class="col-md-6 form-group">
                                 <div class="form-group">
                                    <label for="cemail" class="control-label">Company Mail Id: *</label>
                                    <input type="email" class="form-control" required="required" id="cemail" name="cemail" placeholder="Company Mail Id.">
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="cadd" class="control-label">Company Address: *</label>
                                    <textarea name="cadd" required="required" id="cadd" class="form-control" rows="5"></textarea>
                                 </div>
                              </div>
                           </div>
                           <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row setup-content" id="cpnfirm-data">
                     <div class="col-sm-12">
                        <div class="col-md-12 p-0">
                           <h3 class="mb-4 text-left">Finish:</h3>
                           <div class="row justify-content-center">
                              <div class="col-3"> <img src="{{ asset('assets/images/page-img/img-success.png') }}" class="fit-image" alt="img-success"> </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
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