@extends('hr.layout')
@section('title', 'Recruitment Process')
@push('css')
   <link rel="stylesheet" href="{{ asset('assets/css/recruitment.css')}}">
@endpush
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12"> 
         <div class="panel h-min-400">
            <div class="panel-heading">
                  <h6>Recruitment Process
                      <div class="pull-right">
                        <a class="btn btn-primary" href="{{ url('hr/recruitment/recruit-bulk-upload') }}"><i class="las la-users"></i> Recruit Bulk</a>
                        <a class="btn btn-primary" href="{{ url('hr/recruitment/recruit') }}"><i class="las la-list"></i> Recruit List</a>
                        
                    </div>
                  </h6>
            </div>
            <div class="panel-body">
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
                     <form action="{{ route('recruit.store') }}" method="POST" enctype="multipart/form-data" >
                        {{-- class="needs-validation form" novalidate --}}
                        @csrf
                        <div class="row setup-content" id="basic-info">
                           <div class="col-sm-12">
                              <div class="col-md-12 p-0">
                                 <div class="form-card text-left">
                                    <div class="row">
                                       <div class="col-12">
                                          <h3 class="mb-1 border-left-heading">Basic Information:</h3>
                                       </div>
                                    </div>
                                    <div class="row form-card-details pt-3">
                                        <div class="col-sm-4">
                                            <div class="form-group has-float-label has-required">
                                              <input type="text" class="form-control @error('worker_name') is-invalid @enderror" id="associate-name" name="worker_name" placeholder="Type Associate Name" required="required" value="{{ old('worker_name') }}" autocomplete="off" />
                                              <label for="associate-name">Associate Name</label>
                                              <div class="invalid-feedback">
                                                Please choose an Associate name.
                                             </div>
                                           </div>
                                           @error('worker_name')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="form-group has-float-label has-required">
                                              <input type="text" class="form-control @error('worker_contact') is-invalid @enderror" id="contactNo" name="worker_contact" placeholder="format: 01700000000" value="{{ old('worker_contact') }}" required="required" autocomplete="off"  pattern="[0-9]{10,15}"/>
                                              <label for="contactNo">Contact No.</label>
                                           </div>
                                           @error('worker_contact')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="form-group has-float-label has-required select-search-group">
                                              {{ Form::select('worker_gender', ['Male'=>'Male', 'Female'=>'Female', 'others'=>'Others'], old('worker_gender'), ['placeholder'=>'Select Gender', 'id'=>'gender', 'class'=> 'form-control' . ($errors->has('worker_gender') ? ' is-invalid' : ''), 'required']) }} 
                                              <label class="gender" for="gender">Gender</label>
                                           </div>
                                           @error('worker_gender')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="form-group has-float-label has-required">
                                              <input type="date" max="{{\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')}}" class="age-validate form-control @error('worker_dob') is-invalid @enderror" value="{{ old('worker_dob') }}" id="dob" name="worker_dob" required="required" autocomplete="off" />
                                              <label for="dob">Date Of Birth</label>
                                           </div>
                                           @error('worker_dob')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                              

                                           <div class="form-group has-float-label has-required">
                                              <input type="text" class="form-control @error('worker_nid') is-invalid @enderror" id="nid" value="{{ old('worker_nid') }}" name="worker_nid" placeholder="Type NID/Birth Certificate Number" autocomplete="off" required />
                                              <label for="nid">NID/Birth Certificate</label>
                                           </div>
                                           @error('worker_nid')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                        </div>
                                        <!-- basic location-->
                                        <div class="col-sm-4">
                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_unit_id" class="form-control capitalize select-search @error('worker_unit_id') is-invalid @enderror" id="unit" required="">
                                                 <option selected="" disabled="" value="">Choose...</option>
                                                 @foreach($getUnit as $key => $unit)
                                                 <option value="{{ $key }}">{{ $unit }}</option>
                                                 @endforeach
                                              </select>
                                              <label for="unit">Unit</label>
                                           </div>
                                           @error('worker_unit_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_area_id" class="form-control capitalize select-search @error('worker_area_id') is-invalid @enderror" id="area" required="" onchange="areaWiseDepartment(this.value)">
                                                 <option selected="" disabled="" value="">Choose...</option>
                                                 @foreach($getArea as $area)
                                                 <option value="{{ $area->hr_area_id }}">{{ $area->hr_area_name }}</option>
                                                 @endforeach
                                              </select>
                                              <label for="area">Area</label>
                                           </div>
                                           @error('worker_area_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_department_id" class="form-control capitalize select-search @error('worker_department_id') is-invalid @enderror" id="department" required="" disabled onchange="departmentWiseSection(this.value)">
                                                 <option selected="" disabled="" value="">Choose...</option>
                                                 
                                              </select>
                                              <label for="department">Department</label>
                                           </div>
                                           @error('worker_department_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_section_id" class="form-control capitalize select-search @error('worker_section_id') is-invalid @enderror" id="section" required="" disabled onchange="sectionWiseSubSection(this.value)">
                                                 <option selected="" disabled="" value="">Choose...</option>
                                                 
                                              </select>
                                              <label for="section">Section</label>
                                           </div>
                                           @error('worker_section_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_subsection_id" class="form-control capitalize select-search @error('worker_subsection_id') is-invalid @enderror" id="subSection" required="" disabled>
                                                 <option selected="" disabled="" value="">Choose...</option>
                                                 
                                              </select>
                                              <label for="subSection">Sub Section</label>
                                           </div>
                                           @error('worker_subsection_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                        </div>
                                        <div class="col-sm-4">
                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_emp_type_id" class="form-control capitalize select-search @error('worker_emp_type_id') is-invalid @enderror" id="employeeType" required="" onchange="employeeTypeWiseDesignation(this.value)">
                                                 <option selected="" disabled="" value="">Choose...</option>
                                                 @foreach($getEmpType as $emptype)
                                                 <option value="{{ $emptype->emp_type_id }}">{{ $emptype->hr_emp_type_name }}</option>
                                                 @endforeach
                                              </select>
                                              <label for="employeeType">Employee Type</label>
                                           </div>
                                           @error('worker_emp_type_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="form-group has-float-label has-required select-search-group">
                                              <select name="worker_designation_id" class="form-control capitalize select-search @error('worker_designation_id') is-invalid @enderror" id="designation" required="" disabled>
                                                 <option selected="" disabled="" value="">Choose...</option>
                                              </select>
                                              <label for="designation">Designation</label>
                                           </div>
                                           @error('worker_designation_id')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="form-group has-float-label has-required">
                                              <input type="date" class="form-control @error('worker_doj') is-invalid @enderror" value="{{ old('worker_doj') }}" id="doj" name="worker_doj" required="required" autocomplete="off" />
                                              <label for="doj">Date of Joining</label>
                                           </div>
                                           @error('worker_doj')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           

                                           <div class="form-group has-float-label">
                                              <input type="text" class="form-control @error('as_oracle_code') is-invalid @enderror" id="oracleId" name="as_oracle_code" placeholder="Type Oracle ID" value="{{ old('as_oracle_code') }}" autocomplete="off" />
                                              <label for="oracleId">Oracle ID</label>
                                           </div>
                                           @error('as_oracle_code')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="form-group has-float-label">
                                              <input type="text" class="form-control @error('as_rfid') is-invalid @enderror" id="rfId" name="as_rfid" value="{{ old('as_rfid') }}" placeholder="Type RFID" autocomplete="off" />
                                              <label for="rfId">RFID</label>
                                           </div>
                                           @error('as_rfid')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror

                                           <div class="custom-control custom-switch">
                                              <input name="worker_ot" type="checkbox" class="custom-control-input @error('worker_ot') is-invalid @enderror" id="otHolder" value="{{ old('worker_ot')?old('worker_ot'):'1' }}">
                                              <label class="custom-control-label" for="otHolder">OT Holder</label>
                                           </div>
                                           @error('worker_ot')
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                             </span>
                                           @enderror
                                        </div>
                                        
                                    </div>
                                 </div>
                                 <div class="pull-right">
                                   <button class="btn btn-success" type="button" id="saveSubmit"> Save Now <i class="fa fa-save"></i></button> &nbsp; &nbsp;
                                   <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Continue <i class="las la-angle-double-right f-18"></i></button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row setup-content" id="medical-info">
                           <div class="col-sm-12">
                              <div class="col-md-12 p-0">
                                 <div class="form-card text-left">
                                    <div class="row">
                                       <div class="col-12">
                                          <h3 class="mb-1 border-left-heading">Medical Info:</h3>
                                       </div>
                                    </div>
                                    <div class="row form-card-details">
                                       <div class="col-md-12">
                                          <div class="card mb-3">
                                             <div class="card-body row">
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control @error('worker_height') is-invalid @enderror" value="{{ old('worker_height') }}" id="height" name="worker_height" placeholder="Type Employee Height (Height in inch)" required="required" autocomplete="off" />
                                                      <label for="height">Height</label>
                                                   </div>
                                                   @error('worker_height')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control @error('worker_weight') is-invalid @enderror" value="{{ old('worker_weight') }}" id="weight" name="worker_weight" placeholder="Type Employee Weight (Weight in kg)" required="required" autocomplete="off" />
                                                      <label for="weight">Weight</label>
                                                   </div>
                                                   @error('worker_weight')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control @error('worker_tooth_structure') is-invalid @enderror" id="toothStructure" name="worker_tooth_structure" placeholder="Type Tooth Structure" required="required" value="{{ old('worker_tooth_structure')?old('worker_tooth_structure'):'N/A' }}" autocomplete="off" />
                                                      <label for="toothStructure">Tooth Structure</label>
                                                   </div>
                                                   @error('worker_tooth_structure')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      {{ Form::select('worker_blood_group', ['A+'=>'A+', 'A-'=>'A-', 'B+'=>'B+','B-'=>'B-', 'O+'=>'O+', 'O-'=>'O-', 'AB+'=>'AB+', 'AB-'=>'AB-'], old('worker_blood_group'), ['placeholder'=>'Select Blood Group', 'id'=>'bloodGroup', 'class'=> 'form-control' . ($errors->has('worker_blood_group') ? ' is-invalid' : ''), 'required']) }} 

                                                      <label for="bloodGroup">Blood Group</label>
                                                   </div>
                                                   @error('worker_blood_group')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control @error('worker_identification_mark') is-invalid @enderror" id="identificationMark" name="worker_identification_mark" placeholder="Type Identification Mark" value="{{ old('worker_identification_mark')??'N/A' }}" required="required" autocomplete="off" />
                                                      <label for="identificationMark">Identification Mark</label>
                                                   </div>
                                                   @error('worker_identification_mark')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required select-search-group">
                                                      {{ Form::select('worker_doctor_age_confirm', ['18-20'=>'18-20', '21-25'=>'21-25', '26-30'=>'26-30','31-35'=>'31-35', '36-40'=>'36-40', '41-45'=>'41-45', '46-50'=>'46-50', '51-55'=>'51-55', '56-60'=>'56-60', '61-65'=>'61-65', '66-70'=>'66-70'], old('worker_doctor_age_confirm'), ['placeholder'=>'Select Age Group', 'id'=>'age-confirmation', 'class'=> 'form-control' . ($errors->has('worker_doctor_age_confirm') ? ' is-invalid' : ''), 'required']) }}
                                                      <label for="age-confirmation">Doctor's Age Confirmation</label>
                                                   </div>
                                                   @error('worker_doctor_age_confirm')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="form-group has-float-label has-required">
                                                      <input type="text" class="form-control @error('worker_doctor_comments') is-invalid @enderror" id="doctorComments" name="worker_doctor_comments" value="{{ old('worker_doctor_comments') }}??'Good'" placeholder="Type Doctor's Comments" required="required" autocomplete="off" />
                                                      <label for="doctorComments">Doctor's Comments</label>
                                                   </div>
                                                   @error('worker_doctor_comments')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_doctor_acceptance" type="checkbox" class="custom-control-input @error('worker_doctor_acceptance') is-invalid @enderror" id="acceptance" value="{{ old('worker_doctor_acceptance')?old('worker_doctor_acceptance'):'1' }}">
                                                      <label class="custom-control-label" for="acceptance">Acceptance</label>
                                                   </div>
                                                   @error('worker_doctor_acceptance')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>

                                             </div>
                                          </div>
                                       </div>
                                       
                                    </div>
                                 </div>
                                 <button class="btn btn-success btn-lg text-center" type="button" id="saveMedicalSubmit"><i class="fa fa-save"></i> Save and New</button>
                                 <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Continue <i class="las la-angle-double-right f-18"></i></button>

                              </div>
                           </div>
                        </div>
                        
                        <div class="row setup-content" id="ie-info">
                           <div class="col-sm-12">
                              <div class="col-md-12 p-0">
                                 <div class="form-card text-left">
                                    <div class="row">
                                       <div class="col-12">
                                          <h3 class="mb-1 border-left-heading">IE (Industrial Engineering):</h3>
                                       </div>
                                    </div>
                                    <div class="row form-card-details">
                                       <div class="col-md-12">
                                          <div class="card mb-3">
                                             <div class="card-body row pb-15">
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_pigboard_test" value="{{ old('worker_pigboard_test')?old('worker_pigboard_test'):'1' }}" type="checkbox" class="custom-control-input @error('worker_pigboard_test') is-invalid @enderror" id="pegboard">
                                                      <label class="custom-control-label" for="pegboard">Pegboard Test</label>
                                                   </div>
                                                   @error('worker_pigboard_test')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_finger_test" value="{{ old('worker_finger_test')?old('worker_finger_test'):'1' }}" type="checkbox" class="custom-control-input @error('worker_finger_test') is-invalid @enderror" id="finger">
                                                      <label class="custom-control-label" for="finger">Finger Test</label>
                                                   </div>
                                                   @error('worker_finger_test')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                             </div>
                                          </div>
                                          <div class="card mb-2">
                                             
                                             <div class="card-body row pb-15">
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_color_join" value="{{ old('worker_color_join')?old('worker_color_join'):'1' }}" type="checkbox" class="custom-control-input @error('worker_color_join') is-invalid @enderror" id="colorJoin">
                                                      <label class="custom-control-label" for="colorJoin">Color Join</label>
                                                   </div>
                                                   @error('worker_color_join')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_color_band_join" value="{{ old('worker_color_band_join')?old('worker_color_band_join'):'1' }}" type="checkbox" class="custom-control-input @error('worker_color_band_join') is-invalid @enderror" id="colorBandJoin">
                                                      <label class="custom-control-label" for="colorBandJoin">Color Band Join</label>
                                                   </div>
                                                   @error('worker_color_band_join')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_box_pleat_join" value="{{ old('worker_box_pleat_join')?old('worker_box_pleat_join'):'1' }}" type="checkbox" class="custom-control-input @error('worker_box_pleat_join') is-invalid @enderror" id="colorPleatJoin">
                                                      <label class="custom-control-label" for="colorPleatJoin">Box Pleat Join</label>
                                                   </div>
                                                   @error('worker_box_pleat_join')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_color_top_stice" value="{{ old('worker_color_top_stice')?old('worker_color_top_stice'):'1' }}" type="checkbox" class="custom-control-input @error('worker_color_top_stice') is-invalid @enderror" id="colorTopStice">
                                                      <label class="custom-control-label" for="colorTopStice">Color Top Stice</label>
                                                   </div>
                                                   @error('worker_color_top_stice')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_urmol_join" value="{{ old('worker_urmol_join')?old('worker_urmol_join'):'1' }}" type="checkbox" class="custom-control-input @error('worker_urmol_join') is-invalid @enderror" id="urmolJoin">
                                                      <label class="custom-control-label" for="urmolJoin">Urmol Join</label>
                                                   </div>
                                                   @error('worker_urmol_join')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                                <div class="col-md-6">
                                                   <div class="custom-control custom-switch">
                                                      <input name="worker_clip_join" value="{{ old('worker_clip_join')?old('worker_clip_join'):'1' }}" type="checkbox" class="custom-control-input @error('worker_clip_join') is-invalid @enderror" id="clipJoin">
                                                      <label class="custom-control-label" for="clipJoin">Clip Join</label>
                                                   </div>
                                                   @error('worker_clip_join')
                                                     <span class="invalid-feedback" role="alert">
                                                         <strong>{{ $message }}</strong>
                                                     </span>
                                                   @enderror
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       
                                    </div>
                                 </div>
                                 <button class="btn btn-success btn-lg text-center" type="submit" ><i class="fa fa-save"></i> Save and Confirm</button>
                                 {{-- <button class="btn btn-primary btn-lg pull-right" type="submit" ><i class="fa fa-right"></i> Confirm </button> --}}

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
      <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
      <script>
         $(".select-search").select2({});

         $(document).on('change', '.age-validate', function(){
            var birthDate = new Date($(this).val());
            var difdt = new Date(new Date() - birthDate);
            var age = difdt.toISOString().slice(0, 4) - 1970;
            if (age >= 18) {
                return true;
            }else{
                $.notify('Age is under 18! Please select a valid date', 'error');
                $(this).val('');
            }
        });

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
                     // console.log(response);
                     $.notify(response.message, {
                        type: response.type,
                        allow_dismiss: true,
                        delay: 100,
                        z_index: 1031,
                        timer: 300
                     });
                     if(response.type === 'success'){
                        setTimeout(function() {
                           window.location.href=response.url;
                        }, 500);
                     }
                  },
                  error: function (reject) {
                    console.log(reject);
                    if( reject.status === 400) {
                        var data = $.parseJSON(reject.responseText);
                         $.notify(data.message, {
                            type: data.type,
                            allow_dismiss: true,
                            delay: 100,
                            timer: 300
                        });
                    }else if(reject.status === 422){
                      var data = $.parseJSON(reject.responseText);
                      var errors = data.errors;
                      // console.log(errors);
                      for (var key in errors) {
                        var value = errors[key];
                        $.notify(value[0], 'error');
                      }
                       
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
                     $.notify(response.message, {
                        type: response.type,
                        allow_dismiss: true,
                        delay: 100,
                        z_index: 1031,
                        timer: 300
                     });

                     if(response.type === 'success'){
                        setTimeout(function() {
                           window.location.href=response.url;
                        }, 500);
                     }
                  },
                  error: function (reject) {
                    console.log(reject);
                    if( reject.status === 400) {
                        var data = $.parseJSON(reject.responseText);
                         $.notify(data.message, {
                            type: data.type,
                            allow_dismiss: true,
                            delay: 100,
                            timer: 300
                        });
                    }else if(reject.status === 422){
                      var data = $.parseJSON(reject.responseText);
                      var errors = data.errors;
                      // console.log(errors);
                      for (var key in errors) {
                        var value = errors[key];
                        $.notify(value[0], 'error');
                      }
                       
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