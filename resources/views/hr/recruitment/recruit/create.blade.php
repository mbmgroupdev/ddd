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
               <div class="row">
                  <div class="col-md-3">
                     <ul id="top-tabbar-vertical" class="p-0">
                        <li class="active" id="personal">
                           <a href="javascript:void();">
                           <i class="fa fa-address-book"></i><span>Basic</span>
                           </a>
                        </li>
                        <li id="contact">
                           <a href="javascript:void();">
                           <i class="fa fa-user-md"></i><span>Medical</span>
                           </a>
                        </li>
                        <li id="official">
                           <a href="javascript:void();">
                           <i class="fa fa-eye text-success"></i><span>IE</span>
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
                                    <h3 class="mb-4">Basic Info:</h3>
                                 </div>
                              </div>
                              <div class="row form-card-details">
                                 <div class="col-md-12">
                                    <div class="card mb-3">
                                       <div class="card-body row">
                                          <div class="col-md-6">
                                             <div class="form-group has-float-label has-required">
                                                <input type="text" class="form-control" id="associate-name" name="associate_name" placeholder="Type Associate Name" required="required" />
                                                <label for="associate-name">Associate Name</label>
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group has-float-label has-required">
                                                <input type="text" class="form-control" id="contactNo" name="contact" placeholder="Type Contact Number" />
                                                <label for="contactNo">Contact No.</label>
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group has-float-label has-required">
                                                <label class="gender">Gender</label>
                                                <select class="form-control" id="gender" required="">
                                                   <option selected="" disabled="" value="">Choose...</option>
                                                   <option>...</option>
                                                </select>
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group required">
                                                <label for="dob">Date Of Birth:</label>
                                                <input type="date" class="form-control" id="dob" name="dob" />
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group required">
                                             <label for="dob">Date of Joining:</label>
                                             <input type="date" class="form-control" id="dob" name="dob" />
                                          </div>   
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group ">
                                                <label for="lname">NID/Birth Certificate:</label>
                                                <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" />
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="card">
                                       <div class="card-body row">
                                          <div class="col-md-6">
                                             <div class="form-group required">
                                                <label for="employeeType">Employee Type:</label>
                                                <select class="form-control" id="employeeType" required="">
                                                   <option selected="" disabled="" value="">Choose...</option>
                                                   <option>...</option>
                                                </select>
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group required">
                                                <label for="designation">Designation:</label>
                                                <select class="form-control" id="designation" required="">
                                                   <option selected="" disabled="" value="">Choose...</option>
                                                   <option>...</option>
                                                </select>
                                             </div>
                                          </div>
                                       </div>
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
                              <div class="row form-card-details">
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
                              <div class="row form-card-details">
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
     <!-- Select2 JavaScript -->
     <script src="{{ asset('assets/js/select2.min.js') }}"></script>

   @endpush
@endsection