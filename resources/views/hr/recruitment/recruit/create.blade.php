@extends('hr.layout')
@section('title', 'Recruitment Process')
@push('css')
   {{-- <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css')}}"> --}}
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
                  <div class="col-sm-3">
                     <div class="stepwizard">
                        <div class="stepwizard-row setup-panel require-section" id="top-tabbar-vertical-section">
                           <div id="user" class="wizard-step active">
                              <a href="#basic-info" class="active btn">
                              <i class="fa fa-address-book"></i><span>Basic</span>
                              </a>
                           </div>
                           <div id="document" class="wizard-step">
                              <a href="#medical-info" class="btn btn-default disabled">
                              <i class="fa fa-user-md"></i><span>Medical</span>
                              </a>
                           </div>
                           
                           <div id="confirm" class="wizard-step">
                              <a href="#ie-info" class="btn btn-default disabled">
                              <i class="fa fa-eye text-success"></i><span>IE</span>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-9">
                     <form action="{{ route('recruit.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation form" novalidate>
                        @csrf
                        <div class="row setup-content" id="basic-info">
                           <div class="col-sm-12">
                              <div class="col-md-12 p-0">
                                 <div class="form-card text-left">
                                    <div class="row">
                                       <div class="col-12">
                                          <h3 class="mb-1">Basic Info:</h3>
                                       </div>
                                    </div>
                                    <div class="row form-card-details">
                                       <div class="col-md-12">
                                          <div class="card mb-2">
                                             <div class="card-body row">
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_emp_type_id" class="form-control capitalize select-search" id="employeeType" required="" onchange="employeeTypeWiseDesignation(this.value)">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         @foreach($getEmpType as $emptype)
                                                         <option value="{{ $emptype->emp_type_id }}">{{ $emptype->hr_emp_type_name }}</option>
                                                         @endforeach
                                                      </select>
                                                      <label for="employeeType">Employee Type</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_designation_id" class="form-control capitalize select-search" id="designation" required="" disabled>
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                      </select>
                                                      <label for="designation">Designation</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_unit_id" class="form-control capitalize select-search" id="unit" required="">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         @foreach($getUnit as $unit)
                                                         <option value="{{ $unit->hr_unit_id }}">{{ $unit->hr_unit_name }}</option>
                                                         @endforeach
                                                      </select>
                                                      <label for="unit">Unit</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_area_id" class="form-control capitalize select-search" id="area" required="" onchange="areaWiseDepartment(this.value)">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         @foreach($getArea as $area)
                                                         <option value="{{ $area->hr_area_id }}">{{ $area->hr_area_name }}</option>
                                                         @endforeach
                                                      </select>
                                                      <label for="area">Area</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_department_id" class="form-control capitalize select-search" id="department" required="" disabled onchange="departmentWiseSection(this.value)">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         
                                                      </select>
                                                      <label for="department">Department</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_section_id" class="form-control capitalize select-search" id="section" required="" disabled onchange="sectionWiseSubSection(this.value)">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         
                                                      </select>
                                                      <label for="section">Section</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      <select name="worker_subsection_id" class="form-control capitalize select-search" id="subSection" required="" disabled>
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         
                                                      </select>
                                                      <label for="subSection">Sub Section</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_ot" type="checkbox" class="custom-control-input" id="otHolder">
                                                      <label class="custom-control-label" for="otHolder">OT Holder</label>
                                                   </div>
                                                </div>
                                                
                                             </div>
                                          </div>
                                          <div class="card mb-3">
                                             <div class="card-body row">
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="associate-name" name="worker_name" placeholder="Type Associate Name" required="required" />
                                                      <label for="associate-name">Associate Name</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="contactNo" name="worker_contact" placeholder="Type Contact Number" required="required" autocomplete="off" />
                                                      <label for="contactNo">Contact No.</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <select name="worker_gender" class="form-control" id="gender" required="">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         <option value="male">Male</option>
                                                         <option value="female">Female</option>
                                                         <option value="other">Other</option>
                                                      </select>
                                                      <label class="gender" for="gender">Gender</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="date" class="form-control" id="dob" name="worker_dob" required="required" autocomplete="off" />
                                                      <label for="dob">Date Of Birth</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                   <input type="date" class="form-control" id="doj" name="worker_doj" required="required" autocomplete="off" />
                                                   <label for="doj">Date of Joining</label>
                                                </div>   
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label ">
                                                      <input type="text" class="form-control" id="nid" name="worker_nid" placeholder="Type NID/Birth Certificate Number" autocomplete="off" />
                                                      <label for="nid">NID/Birth Certificate</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label">
                                                      <input type="text" class="form-control" id="oracleId" name="as_oracle_code" placeholder="Type Oracle ID" autocomplete="off" />
                                                      <label for="oracleId">Oracle ID</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label">
                                                      <input type="text" class="form-control" id="rfId" name="as_rfid" placeholder="Type RFID" autocomplete="off" />
                                                      <label for="rfId">RFID</label>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       
                                    </div>
                                 </div>
                                 <button class="btn btn-success btn-lg text-center" type="button" id="saveSubmit"><i class="fa fa-save"></i> Save and New</button>
                                 <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Continue <i class="fa fa-forward"></i></button>
                              </div>
                           </div>
                        </div>
                        <div class="row setup-content" id="medical-info">
                           <div class="col-sm-12">
                              <div class="col-md-12 p-0">
                                 <div class="form-card text-left">
                                    <div class="row">
                                       <div class="col-12">
                                          <h3 class="mb-1">Medical Info:</h3>
                                       </div>
                                    </div>
                                    <div class="row form-card-details">
                                       <div class="col-md-12">
                                          <div class="card mb-3">
                                             <div class="card-body row">
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="height" name="worker_height" placeholder="Type Employee Height (Height in inch)" required="required" autocomplete="off" />
                                                      <label for="height">Height</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="weight" name="worker_weight" placeholder="Type Employee Weight (Weight in kg)" required="required" autocomplete="off" />
                                                      <label for="weight">Weight</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="toothStructure" name="worker_tooth_structure" placeholder="Type Tooth Structure" required="required" value="N/A" autocomplete="off" />
                                                      <label for="toothStructure">Tooth Structure</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <select name="worker_blood_group" class="form-control" id="bloodGroup" required="">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         <option value="A+">A+</option>
                                                         <option value="A-">A-</option>
                                                         <option value="B+">B+</option>
                                                         <option value="B-">B-</option>
                                                         <option value="O+">O+</option>
                                                         <option value="O-">O-</option>
                                                         <option value="AB+">AB+</option>
                                                         <option value="AB-">AB-</option>
                                                      </select>

                                                      <label for="bloodGroup">Blood Group</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="identificationMark" name="worker_identification_mark" placeholder="Type Identification Mark" required="required" autocomplete="off" />
                                                      <label for="identificationMark">Identification Mark</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <select name=" worker_doctor_age_confirm" class="form-control" id="age-confirmation" required="">
                                                         <option selected="" disabled="" value="">Choose...</option>
                                                         <option value="18-20">18-20</option>
                                                         <option value="21-25">21-25</option>
                                                         <option value="26-30">26-30</option>
                                                         <option value="31-35">31-35</option>
                                                         <option value="36-40">36-40</option>
                                                         <option value="41-45">41-45</option>
                                                         <option value="46-50">46-50</option>
                                                         <option value="51-55">51-55</option>
                                                         <option value="56-60">56-60</option>
                                                         <option value="61-65">61-65</option>
                                                         <option value="66-70">66-70</option>
                                                      </select>
                                                      <label for="age-confirmation">Doctor's Age Confirmation</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control" id="doctorComments" name="worker_doctor_comments" placeholder="Type Doctor's Comments" required="required" autocomplete="off" />
                                                      <label for="doctorComments">Doctor's Comments</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_doctor_acceptance" type="checkbox" class="custom-control-input" id="acceptance" value="1">
                                                      <label class="custom-control-label" for="acceptance">Acceptance</label>
                                                   </div>
                                                </div>

                                             </div>
                                          </div>
                                       </div>
                                       
                                    </div>
                                 </div>
                                 <button class="btn btn-success btn-lg text-center" type="button" id="saveMedicalSubmit"><i class="fa fa-save"></i> Save and New</button>
                                 <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Continue <i class="fa fa-forward"></i></button>

                              </div>
                           </div>
                        </div>
                        
                        <div class="row setup-content" id="ie-info">
                           <div class="col-sm-12">
                              <div class="col-md-12 p-0">
                                 <div class="form-card text-left">
                                    <div class="row">
                                       <div class="col-12">
                                          <h3 class="mb-1">IE (Industrial Engineering):</h3>
                                       </div>
                                    </div>
                                    <div class="row form-card-details">
                                       <div class="col-md-12">
                                          <div class="card mb-3">
                                             <div class="card-body row pb-15">
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_pigboard_test" type="checkbox" class="custom-control-input" id="pegboard">
                                                      <label class="custom-control-label" for="pegboard">Pegboard Test</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_finger_test" type="checkbox" class="custom-control-input" id="finger">
                                                      <label class="custom-control-label" for="finger">Finger Test</label>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="card mb-2">
                                             
                                             <div class="card-body row pb-15">
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_color_join" type="checkbox" class="custom-control-input" id="colorJoin">
                                                      <label class="custom-control-label" for="colorJoin">Color Join</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_color_band_join" type="checkbox" class="custom-control-input" id="colorBandJoin">
                                                      <label class="custom-control-label" for="colorBandJoin">Color Band Join</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_box_pleat_join" type="checkbox" class="custom-control-input" id="colorPleatJoin">
                                                      <label class="custom-control-label" for="colorPleatJoin">Box Pleat Join</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_color_top_stice" type="checkbox" class="custom-control-input" id="colorTopStice">
                                                      <label class="custom-control-label" for="colorTopStice">Color Top Stice</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_urmol_join" type="checkbox" class="custom-control-input" id="urmolJoin">
                                                      <label class="custom-control-label" for="urmolJoin">Urmol Join</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_clip_join" type="checkbox" class="custom-control-input" id="clipJoin">
                                                      <label class="custom-control-label" for="clipJoin">Clip Join</label>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       
                                    </div>
                                 </div>
                                 
                                 <button class="btn btn-primary btn-lg pull-right" type="submit" ><i class="fa fa-right"></i> Confirm </button>

                              </div>
                           </div>
                        </div>
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
      <script>
         $(".select-search").select2({});

         function employeeTypeWiseDesignation(id) {
            if(id !== null || id !== ''){
               $('#designation').attr('disabled','true');
               $('#designation').after('<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>');
               $.ajax({
                  url: '{{ url("/hr/employee-type-wise-designation")}}'+'/'+id,
                  type: 'GET',
               })
               .done(function(response) {
                  $('#designation').empty();
                  $('#designation').append('<option value="">Choose...</option>');
                  $('.loading-select').remove();
                  $('#designation').removeAttr('disabled');
                  if(response.status === 'success'){
                     if(response.value.length > 0){
                        $.each(response.value,function(index,designation){
                           $('#designation').append('<option value="'+designation.hr_designation_id+'">'+designation.hr_designation_name+'</option>');
                        })
                     }else{
                        $('#designation').append('<option disabled>No Designation Found!</option>');
                     }
                     
                  }

               })
               .fail(function(response) {
                  console.log(response);
               });
               
            }
         }

         function areaWiseDepartment(id) {
            if(id !== null || id !== ''){
               $('#department').attr('disabled','true');
               $('#department').after('<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>');
               $.ajax({
                  url: '{{ url("/hr/area-wise-department")}}'+'/'+id,
                  type: 'GET',
               })
               .done(function(response) {
                  $('#department').empty();
                  $('#department').append('<option value="">Choose...</option>');
                  $('.loading-select').remove();
                  $('#department').removeAttr('disabled');
                  if(response.status === 'success'){
                     if(response.value.length > 0){
                        $.each(response.value,function(index,department){
                           $('#department').append('<option value="'+department.hr_department_id+'">'+department.hr_department_name+'</option>');
                        })
                     }else{
                        $('#department').append('<option disabled>No Department Found!</option>');
                     }
                     
                  }

               })
               .fail(function(response) {
                  console.log(response);
               });
               
            }
         }

         function departmentWiseSection(id) {
            if(id !== null || id !== ''){
               $('#section').attr('disabled','true');
               $('#section').after('<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>');
               $.ajax({
                  url: '{{ url("/hr/department-wise-section")}}'+'/'+id,
                  type: 'GET',
               })
               .done(function(response) {
                  $('#section').empty();
                  $('#section').append('<option value="">Choose...</option>');
                  $('.loading-select').remove();
                  $('#section').removeAttr('disabled');
                  if(response.status === 'success'){
                     if(response.value.length > 0){
                        $.each(response.value,function(index,section){
                           $('#section').append('<option value="'+section.hr_section_id+'">'+section.hr_section_name+'</option>');
                        })
                     }else{
                        $('#section').append('<option disabled>No Section Found!</option>');
                     }
                     
                  }

               })
               .fail(function(response) {
                  console.log(response);
               });
               
            }
         }
         function sectionWiseSubSection(id) {
            if(id !== null || id !== ''){
               $('#subSection').attr('disabled','true');
               $('#subSection').after('<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>');
               $.ajax({
                  url: '{{ url("/hr/section-wise-subsection")}}'+'/'+id,
                  type: 'GET',
               })
               .done(function(response) {
                  $('#subSection').empty();
                  $('#subSection').append('<option value="">Choose...</option>');
                  $('.loading-select').remove();
                  $('#subSection').removeAttr('disabled');
                  if(response.status === 'success'){
                     if(response.value.length > 0){
                        $.each(response.value,function(index,subSection){
                           $('#subSection').append('<option value="'+subSection.hr_subsec_id+'">'+subSection.hr_subsec_name+'</option>');
                        })
                     }else{
                        $('#subSection').append('<option disabled>No Sub Section Found!</option>');
                     }
                     
                  }

               })
               .fail(function(response) {
                  console.log(response);
               });
               
            }
         }
         
         jQuery('#saveSubmit').click(function(event) {
            var curStep = jQuery(this).closest(".setup-content"),
              curInputs = curStep.find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
              isValid = true;
            jQuery(".form-group").removeClass("has-error");
            for (var i = 0; i < curInputs.length; i++) {
               if (!curInputs[i].validity.valid) {
                  isValid = false;
                  jQuery(curInputs[i]).closest(".form-group").addClass("has-error");
               }
            }
            if (isValid){
               $.ajax({
                  type: "POST",
                  url: '{{ url("/hr/recruitment/first-step-recruitment") }}',
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  },
                  data: curInputs.serialize(), // serializes the form's elements.
                  success: function(response)
                  {
                     console.log(response);
                     $.notify(response.message, {
                        type: response.type,
                        allow_dismiss: true,
                        delay: 100,
                        z_index: 1031,
                        timer: 300
                     });
                     if(response.type === 'success'){
                        window.location.href=response.url;
                     }
                  },
                  error: function (reject) {
                      if( reject.status === 400 ) {
                          var data = $.parseJSON(reject.responseText);
                           $.notify(data.message, {
                              type: data.type,
                              allow_dismiss: true,
                              delay: 100,
                              timer: 300
                          });
                      }
                  }
               });
            }else{
               $.notify("Some field are required", {
                  type: 'error',
                  allow_dismiss: true,
                  delay: 100,
                  z_index: 1031,
                  timer: 300
               });
            }
            
         });

         jQuery('#saveMedicalSubmit').click(function(event) {
            var basicId = jQuery("#basic-info"),
               basicInputs = basicId.find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select");

            var curStep = jQuery(this).closest(".setup-content"),
              curMeInputs = curStep.find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
              isValid = true;
            jQuery(".form-group").removeClass("has-error");
            for (var i = 0; i < curMeInputs.length; i++) {
               if (!curMeInputs[i].validity.valid) {
                  isValid = false;
                  jQuery(curMeInputs[i]).closest(".form-group").addClass("has-error");
               }
            }
            if (isValid){
               var data = curMeInputs.serialize() + '&' + basicInputs.serialize();
               $.ajax({
                  type: "POST",
                  url: '{{ url("/hr/recruitment/second-step-recruitment") }}',
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  },
                  data: data, // serializes the form's elements.
                  success: function(response)
                  {
                     // console.log(response);
                     $.notify(response.message, {
                        type: response.type,
                        allow_dismiss: true,
                        delay: 100,
                        z_index: 1031,
                        timer: 300
                     });
                     if(response.type === 'success'){
                        window.location.href=response.url;
                     }
                  },
                  error: function (reject) {
                      if( reject.status === 400 ) {
                          var data = $.parseJSON(reject.responseText);
                           $.notify(data.message, {
                              type: data.type,
                              allow_dismiss: true,
                              delay: 100,
                              timer: 300
                          });
                      }
                  }
               });
            }else{
               $.notify("Some field are required", {
                  type: 'error',
                  allow_dismiss: true,
                  delay: 100,
                  z_index: 1031,
                  timer: 300
               });
            }
            
         });
      </script>
   @endpush
@endsection