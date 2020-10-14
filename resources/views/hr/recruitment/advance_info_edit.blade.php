@extends('hr.layout')
@section('title', $advance->emp_adv_info_as_id.' advance info')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">{{$advance->emp_adv_info_as_id}}</a>
                </li>
                <li class="active"> Advance Information</li>
            </ul><!-- /.breadcrumb -->
 
        </div>
        @include('inc/message')
        <div class="panel">
            <div class="panel-heading">
                <h6>{{$employee->as_name}}
                    <div class="btn-group pull-right"> 
                        <a href='{{ url("hr/recruitment/employee/show/$advance->emp_adv_info_as_id") }}' target="_blank" class="btn  btn-success" title="Profile"><i class="las la-user-tie"></i></a>

                        

                        <a href='{{ url("hr/recruitment/operation/advance_info_edit/$advance->emp_adv_info_as_id") }}' class="btn  btn-info" title="Advance Info"><i class="las la-id-card"></i></a>
                        <a href='{{ url("hr/employee/benefits?associate_id=$advance->emp_adv_info_as_id") }}' class="btn  btn-primary" title="Benefits"><i class="las la-dollar-sign"></i></a>
                        <a  href="{{url("hr/recruitment/operation/medical_info_edit/$advance->emp_adv_info_as_id")}}" target="_blank" data-tooltip="Edit Medical Info" data-tooltip-location="left" class="btn  btn-warning" style="border-radius: 2px !important; padding: 4px;"><i class="las la-stethoscope bigger-100" ></i></a>
                        <a href='{{ url("hr/employee/medical_incident?associate_id=$advance->emp_adv_info_as_id") }}' class="btn  btn-warning" title="Medical Incident"><i class="las la-procedures"></i></a>
                        <a href='{{ url("hr/employee/servicebook?associate_id=$advance->emp_adv_info_as_id") }}' class="btn  btn-danger" title="Service Book"><i class="las la-address-book"></i></a>
                    </div>
                </h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="user-details-block" >
                            <div class="user-profile text-center mt-0" >
                                <img id="avatar" class="avatar-130 img-fluid" src="{{$employee->profile_picture}}">
                              </div>
                              <div class="text-center mt-3">
                                 <h4><b id="user-name">{{$employee->as_name}}</b></h4>
                                 <p>Oracle ID: {{$employee->as_oracle_code}}</p>
                                 
                              </div>
                        </div>

                        <div class="nav flex-column nav-pills text-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#advance-info" role="tab" aria-controls="v-pills-home" aria-selected="true">Advance Information</a>
                            <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#education" role="tab" aria-controls="v-pills-profile" aria-selected="false">Education</a>
                            <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#bangla" role="tab" aria-controls="v-pills-messages" aria-selected="false">বাংলা</a>
                        </div>
                      </div>
                      <div class="col-sm-9" style="border-left: 1px solid #d1d1d1;">
                         <div class="tab-content mt-0" id="v-pills-tabContent">
                            <div class="tab-pane fade active show" id="advance-info" role="tabpanel" aria-labelledby="v-pills-home-tab">
                               <form class="form-horizontal" role="form" method="post" action="{{ url('hr/recruitment/operation/advance_info_update') }}" enctype="multipart/form-data"> 
                                    <div class="row">
                                        <div class="col-sm-4">
                                            {{ csrf_field() }} 

                                            {{ Form::hidden('emp_adv_info_id', $advance->emp_adv_info_id) }}
                                            <div class="form-group has-float-label">
                                                <input class="form-control" id="emp_adv_info_as_id" name="emp_adv_info_as_id" value="{{$advance->emp_adv_info_as_id}}" readonly />
                                                <label  for="emp_adv_info_as_id"> Associate's ID </label>
                                                  
                                            </div>
                     
                                           
                                            <div class="form-group ">
                                                <label  for="emp_adv_info_stat"> Status </label>
                                                @if($advance->emp_adv_info_stat == 1)
                                                <div class="radio">
                                                    <label>
                                                        <input id="emp_adv_info_stat" name="emp_adv_info_stat" type="radio" class="ace" value="1" checked />
                                                        <span class="lbl"> Permanent</span>
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input name="emp_adv_info_stat" id="emp_adv_info_stat" type="radio" class="ace" value="0"/>
                                                        <span class="lbl">Probationary</span>
                                                    </label>
                                                </div>
                                                @else
                                                <div class="radio">
                                                    <label>
                                                        <input id="emp_adv_info_stat" name="emp_adv_info_stat" type="radio" class="ace" value="1"/>
                                                        <span class="lbl"> Permanent</span>
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input name="emp_adv_info_stat" id="emp_adv_info_stat" type="radio" class="ace" value="0" checked/>
                                                        <span class="lbl">Probationary</span>
                                                    </label>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            

                                            <div class="form-group has-float-label">
                                                <input type="text" name="emp_adv_info_passport" placeholder="NID/Passport No" class="form-control" value="{{ $advance->emp_adv_info_passport }}" />
                                                <label  for="emp_adv_info_passport"> NID/Passport No </label>
                                            </div>
                                             <div class="form-group has-float-label hide">
                                                <input name="emp_adv_info_bank_name" type="text" id="bank_acc_name" placeholder="Mobile Banking/Bank Name " class="form-control" value="{{$advance->emp_adv_info_bank_name}}"  />
                                                <label  for="emp_adv_info_bank_name">Mobile Banking/Bank Name </label>
                                            </div>

                                            <div class="form-group has-float-label hide">
                                                <input name="emp_adv_info_bank_num" type="text" id="bank_acc_number" placeholder="Account Number" value="{{$advance->emp_adv_info_bank_num}}" class="form-control"  />
                                                <label  for="emp_adv_info_bank_num">Account Number</label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_tin" type="text" id="tin_etin" placeholder="TIN/ETIN" class="form-control" value="{{$advance->emp_adv_info_tin}}" />
                                                <label  for="emp_adv_info_tin">TIN/ETIN</label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input type="text" name="emp_adv_info_refer_name" placeholder="Reference Name" class="form-control" value="{{ $advance->emp_adv_info_refer_name }}"   />
                                                <label  for="emp_adv_info_refer_name"> Reference Name </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input type="text" name="emp_adv_info_refer_contact" placeholder="Reference Contact" class="form-control" value="{{ $advance->emp_adv_info_refer_contact }}"  />
                                                <label  for="emp_adv_info_refer_contact"> Reference Contact </label>
                                            </div>
                                            <!-- added from  basic info -->
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_fathers_name" type="text" id="father_name" placeholder="Father's Name" class="form-control" value="{{ $advance->emp_adv_info_fathers_name }}" />
                                                <label  for="emp_adv_info_fathers_name"> Father's Name </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_mothers_name" type="text" id="mother_name" placeholder="Mother's Name" class="form-control" value="{{ $advance->emp_adv_info_mothers_name }}"     />
                                                <label  for="emp_adv_info_mothers_name"> Mother's Name </label>
                                            </div>

                                            <div class="form-group has-float-label select-search-group">
                                                
                                                <select name="emp_adv_info_marital_stat" id="married_unmarried" class="form-control no-select" >
                                                    <option <?php if($advance->emp_adv_info_marital_stat == "Married") echo "Selected" ?>  value="Married">Married</option>
                                                    <option <?php if($advance->emp_adv_info_marital_stat == "Unmarried") echo "Selected" ?>  value="Unmarried">Unmarried</option>
                                                    <option <?php if($advance->emp_adv_info_marital_stat == "Divorced") echo "Selected" ?> value="Divorced">Divorced</option>
                                                    <option <?php if($advance->emp_adv_info_marital_stat == "Widowed") echo "Selected" ?> value="Widowed">Widowed</option>
                                                </select>
                                                <label  for="emp_adv_info_marital_stat"> Marital Status </label>
                                            </div>
                                            <div id="marritalInfo" @if($advance->emp_adv_info_marital_stat == 'Unmarried')
                                             class="hide" @endif >
                                                <div class="form-group has-float-label">
                                                    <input name="emp_adv_info_spouse" type="text" id="Spouse" placeholder="Spouse (Husband/Wife)" class="form-control" value="{{$advance->emp_adv_info_spouse}}" />
                                                    <label  for="emp_adv_info_spouse">Spouse (Husband/Wife) </label>
                                                </div>

                                                <div class="form-group has-float-label">
                                                    <input name="emp_adv_info_children" type="text" id="Children" placeholder="No. of Children (if applicable )" class="form-control" value="{{$advance->emp_adv_info_children}}"/>
                                                    <label  for="emp_adv_info_children">No. of Children </label>
                                                </div>
                                             </div>

                                            <div class="form-group has-float-label select-search-group">
                                                
                                                <select name="emp_adv_info_religion" class="form-control no-select" id="religion" required-error-msg="The Religion field is required">
                                                    @if(!empty($advance->emp_adv_info_religion))
                                                    <option value="{{$advance->emp_adv_info_religion}}">{{$advance->emp_adv_info_religion}}</option>
                                                    @endif
                                                    <option value="">Select Religion</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Hinduism">Hinduism</option>
                                                    <option value="Buddhists">Buddhists</option>
                                                    <option value="Christians">Christians</option>
                                                </select>
                                                <label  for="emp_adv_info_religion"> Religion </label>
                                            </div>
                                            
                     
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_pre_org" type="text" id="previous_org_name" placeholder="Name of Previous Organization" class="form-control" value="{{ $advance->emp_adv_info_pre_org }}"   required-="3-255" />
                                                <label  for="emp_adv_info_pre_org"> Last Organization </label>
                                            </div>
                     
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_work_exp" type="text" id="experience" placeholder="Work Experience in Year" class="form-control" value="{{$advance->emp_adv_info_work_exp}}" required="number"  required-allowing="range[0;50,float]" required-error-msg="The Work Experience my be 0 to 50 years" />
                                                <label  for="emp_adv_info_work_exp"> Work Experience </label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-4">
                                            <legend>Nominee</legend>
                                            @if(!$nomineeList->isEmpty())
                                                @foreach($nomineeList as $nominee)
                                                    <div class="addRemove">
                                                        <div class="form-group">
                                                            <label  for="emp_adv_info_nom_name"> Nominee Name</label>
                                                            <div class="nominee-div row pt-0 pb-0 p-3 ">
                                                                <input name="emp_adv_info_nom_name[]" type="text" id="Nominee" placeholder="Nominee Name" class="col-sm-6 form-control" value="{{ $nominee->nom_name }}"   />

                                                                <input name="emp_adv_info_nom_per[]" type="text" id="Percent" placeholder="(%)" class="col-sm-3 form-control" value="{{ $nominee->nom_ben }}"  />

                                                                <div class="col-sm-3 p-0">
                                                                    <button type="button" class="btn btn-xs btn-success AddBtn">+</button>
                                                                    <button type="button" class="btn btn-xs btn-danger RemoveBtn">-</button>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                @endforeach
                                            @else
                                            <div class="addRemove">
                                                <div class="form-group">
                                                    <div class="nominee-div row pb-0 pt-0 p-3  ">
                                                        <input name="emp_adv_info_nom_name[]" type="text" id="Nominee" placeholder="Nominee Name" class="col-sm-6 form-control" value=""   />

                                                        <input name="emp_adv_info_nom_per[]" type="text" id="Percent" placeholder="(%)" class="col-sm-3 form-control"  />

                                                        <div class="col-sm-3 p-0">
                                                            <button type="button" class="btn btn-xs btn-success AddBtn">+</button>
                                                            <button type="button" class="btn btn-xs btn-danger RemoveBtn">-</button>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>
                                            @endif 
                                            <hr> 
                                            <legend>Permanent Address</legend>
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_per_vill" type="text" id="as_per_vill" placeholder="Village" class="form-control" value="{{$advance->emp_adv_info_per_vill}}"/>
                                                <label  for="emp_adv_info_per_vill"> Village </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_per_po" type="text" id="as_per_po" placeholder="PO" class="form-control" value="{{$advance->emp_adv_info_per_po}}"    />
                                                <label  for="emp_adv_info_per_po"> PO </label>
                                            </div>

                                            <div class="form-group has-float-label select-search-group">
                                                {{ Form::select('emp_adv_info_per_dist', $districtList, $advance->emp_adv_info_per_dist, ['placeholder'=>'Select District', 'id'=>'as_per_dis', 'class'=> 'form-control']) }} 
                                                <label  for="emp_adv_info_per_dist"> District  </label>
                                            </div>

                                            <div class="form-group has-float-label select-search-group">
                                                {{ Form::select('emp_adv_info_per_upz', $upazillaList, $advance->emp_adv_info_per_upz, ['placeholder'=>'Select Upazilla', 'id'=>'as_per_upz', 'class'=> 'no-select form-control']) }} 
                                                <label  for="emp_adv_info_per_upz"> Upazilla </label> 
                                            </div>

                                            <legend>Present Address</legend>
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_pres_house_no" type="text" id="house_no" placeholder="House No" class="form-control" value="{{$advance->emp_adv_info_pres_house_no}}"/>
                                                <label  for="emp_adv_info_pres_house_no"> House No </label>
                                            </div>
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_pres_road" type="text" id="Road" placeholder="Road" class="form-control" value="{{$advance->emp_adv_info_pres_road}}" />
                                                <label  for="emp_adv_info_pres_road"> Road </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_pres_po" type="text" id="PO" placeholder="PO" class="form-control" value="{{$advance->emp_adv_info_pres_po}}"/>
                                                <label  for="emp_adv_info_pres_po"> PO </label>
                                            </div>

                                            <div class="form-group has-float-label select-search-group">
                                                {{ Form::select('emp_adv_info_pres_dist', $districtList, $advance->emp_adv_info_pres_dist, ['placeholder'=>'Select District', 'id'=>'as_pre_dis', 'class'=> 'form-control']) }}  
                                                <label  for="emp_adv_info_pres_dist"> District </label>
                                            </div>

                                            <div class="form-group has-float-label select-search-group">
                                                {{ Form::select('emp_adv_info_pres_upz', $upazillaList, $advance->emp_adv_info_pres_upz, [ 'placeholder' =>'Select Upazilla', 'id'=>'as_pre_upz', 'class'=> 'no-select form-control']) }}
                                                <label  for="emp_adv_info_pres_upz"> Upazilla </label>
                                            </div>
                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_emg_con_name" type="text" id="emergency_contact_name" placeholder="Emergency Contact Name"  value="{{$advance->emp_adv_info_emg_con_name}}" class="form-control"  />
                                                <label  for="emp_adv_info_emg_con_name"> Emergency Contact Name </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="emp_adv_info_emg_con_num" type="text" id="emergency_contact_number" placeholder="Emergency Contact Number" class="form-control" value="{{$advance->emp_adv_info_emg_con_num}}"     />
                                                <label  for="emp_adv_info_emg_con_num"> Emergency Contact Number </label>
                                            </div>

                                           
                                            
                                        </div>
                                        <div class="col-sm-4 form-group-file">
                                            
                                            <div class="form-group ">
                                                <label  for="emp_adv_info_birth_cer">Birth Certificate<br><span class="text-danger">(pdf|doc|docx|jpg|jpeg|png)</span></label>
                                                    @if(!empty($advance->emp_adv_info_birth_cer))
                                                    <a href="{{ url($advance->emp_adv_info_birth_cer) }}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> View</a>
                                                    <a href="{{ url($advance->emp_adv_info_birth_cer) }}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> Download</a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input type="file" class="file-type-validation" name="emp_adv_info_birth_cer" id="emp_adv_info_birth_cer"  required-allowing="docx,doc,pdf,jpeg,png,jpg">
                                                        <span id="file_upload_error1" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png </strong>type file supported(<1MB).</span>
                                                    </div>
                                            </div>

                                            <div class="form-group ">
                                                <label  for="emp_adv_info_city_corp_cer">City Corp. Certificate <br><span class="text-danger">(pdf|doc|docx|jpg|jpeg|png)</span> </label>
                                                    @if(!empty($advance->emp_adv_info_city_corp_cer))
                                                    <a href="{{ url($advance->emp_adv_info_city_corp_cer) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_city_corp_cer) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-download"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input type="file" class="file-type-validation" name="emp_adv_info_city_corp_cer" id="emp_adv_info_city_corp_cer"  required-allowing="docx,doc,pdf,jpeg,png,jpg" data-validat required-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file">
                                                        <span id="file_upload_error2" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png </strong>type file supported(<1MB).</span>

                                                    </div>
                                            </div>

                                            <div class="form-group ">
                                                <label  for="emp_adv_info_police_veri">Police Verification<br><span class="text-danger"> (pdf|doc|docx|jpg|jpeg|png)</span> </label>
                                                    @if(!empty($advance->emp_adv_info_police_veri))
                                                    <a href="{{ url($advance->emp_adv_info_police_veri) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_police_veri) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-eye"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input type="file" class="file-type-validation" name="emp_adv_info_police_veri" id="emp_adv_info_police_veri"  required-allowing="docx,doc,pdf,jpeg,png,jpg" data-validat required-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file">
                                                        <span id="file_upload_error3" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png </strong>type file supported(<1MB).</span>
                                                    </div>
                                            </div>
                                            
                                            <div class="form-group ">
                                                <label  for="emp_adv_info_job_app">Job Application<br><span class="text-danger"> (pdf|doc|docx|jpg|jpeg|png)</span> </label>
                                                    @if(!empty($advance->emp_adv_info_job_app))
                                                    <a href="{{ url($advance->emp_adv_info_job_app) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_job_app) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-eye"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input name="emp_adv_info_job_app" type="file" class="file-type-validation" id="as_job_appl"
                                                        required-allowing="docx,doc,pdf,jpeg,png,jpg">
                                                        <span id="file_upload_error4" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png </strong>type file supported(<1MB).</span>
                                                    </div>
                                            </div>

                                            <div class="form-group ">
                                                <label  for="emp_adv_info_cv">Curriculum Vitae<br><span class="text-danger"> (pdf|doc|docx|jpg|jpeg|png)</span> </label>
                                                    @if(!empty($advance->emp_adv_info_cv))
                                                    <a href="{{ url($advance->emp_adv_info_cv) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_cv) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-eye"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input name="emp_adv_info_cv" type="file" class="file-type-validation" id="as_cv"
                                                        required-allowing="docx,doc,pdf,jpeg,png,jpg">
                                                        <span id="file_upload_error5" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png </strong>type file supported(<1MB).</span>
                                                    </div>
                                            </div>

                                            
                            
                                            <div class="form-group ">
                                                <label  for="emp_adv_info_finger_print">Finger Print <br><span class="text-danger">(jpg|jpeg|png)</span></label>
                                                    @if(!empty($advance->emp_adv_info_finger_print))
                                                    <a href="{{ url($advance->emp_adv_info_finger_print) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_finger_print) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-eye"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp mb-0">  
                                                        <input name="emp_adv_info_finger_print" type="file" class="file-type-validation" id="finger_print"
                                                        
                                                        data-file-allow="jpeg,png,jpg" >
                                                        <span id="file_upload_error6" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg, jpg or png </strong>type file supported(<512kb).</span>
                                                    </div>
                                            </div>
                            
                                            <div class="form-group ">
                                                <label  for="emp_adv_info_signature">Signature<br><span class="text-danger"> (jpg|jpeg|png)</span> </label>
                                                    @if(!empty($advance->emp_adv_info_signature))
                                                    <a href="{{ url($advance->emp_adv_info_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-eye"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input name="emp_adv_info_signature" type="file" class="file-type-validation" id="Signature"
                                                        required-allowing="jpeg,png,jpg"
                                                        required-error-msg-mime="You can only upload jpeg, jpg or png images">
                                                        <span id="file_upload_error7" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg, jpg or png </strong>type file supported(<512kb).</span>
                                                    </div>
                                            </div>

                                            <div class="form-group ">
                                                <label  for="emp_adv_info_auth_sig">Authority Signature<br><span class="text-danger"> (jpg|jpeg|png)</span></label>
                                                    @if(!empty($advance->emp_adv_info_auth_sig))
                                                    <a href="{{ url($advance->emp_adv_info_auth_sig) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                                        <i class="fa fa-eye"></i>
                                                         View
                                                    </a>
                                                    <a href="{{ url($advance->emp_adv_info_auth_sig) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                                        <i class="fa fa-eye"></i>
                                                         Download
                                                    </a>
                                                    @else
                                                        <strong class="text-danger">No file found!</strong>
                                                    @endif
                                                    <div class="file-zone-emp">
                                                        <input name="emp_adv_info_auth_sig" type="file" class="file-type-validation" id="authority_signature" 
                                                        required-allowing="jpeg,png,jpg">
                                                        <span id="file_upload_error8" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg, jpg or png </strong>type file supported(<512kb).</span>
                                                    </div>
                                            </div>
                                            <div class="form-group ">
                                                <button name="approve" class="btn btn-success" type="submit">
                                                    <i class="fa fa-chec"></i> Update
                                                </button>
                                            </div>
                                            
                                        </div>
                                    </div>
                                            
                                </form>
                            </div>
                            <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                               <form class="form-horizontal" role="form" method="POST" action="{{ url('hr/recruitment/operation/education_info')}}" enctype="multipart/form-data">
                                    <div class="row">
                                        {{ csrf_field() }} 
                                        <div class="col-sm-4" > 

                                            <div class="form-group has-float-label">
                                                <input type="text" name="education_as_id" placeholder="Associate's ID" class="form-control" value="{{ (!empty($advance->emp_adv_info_as_id)?$advance->emp_adv_info_as_id:(request()->route('emp_adv_info_as_id'))) }}" readonly /> 
                                                <label  for="emp_adv_info_as_id"> Associate's ID </label>
                                            </div>  

                                            <div class="form-group has-float-label select-search-group">
                                                {{ Form::select('education_level_id', $levelList, null, ['placeholder'=>'Select Education Level', 'id'=>'education_level_id', 'required'=> 'required']) }}
                                                <label for="education_level_id"> Education Level </label>
                                            </div>

                                            <div class="form-group hide has-float-label select-search-group" id="degrreforPhd">
                                                {{ Form::select('education_degree_id_1', [], null, ['id'=>'education_degree_id_1']) }}
                                                <label  for="education_degree_id_1"> Exam/Degree Title </label>
                                            </div>

                                            <div class="form-group hide has-float-label" id="PhdTitle">
                                                <input name="education_degree_id_2" type="text" id="education_degree_id_2" placeholder="Exam/Degree Title" class="form-control"/>
                                                <label  for="education_degree_id_2">Exam/Degree Title</label>
                                            </div>

                                            <div class="form-group hide has-float-label" id="major">
                                                <label  for="education_major_group_concentation">Concentration/ Major/Group </label>
                                                <input name="education_major_group_concentation" type="text" id="education_major_group_concentation" placeholder="Concentration/ Major/Group" class="form-control"   />
                                            
                                            </div>

                                            <div class="form-group has-float-label has-required">
                                                <label  for="education_institute_name">Institute Name </label>
                                                <input name="education_institute_name" type="text" id="education_institute_name" placeholder="Institute Name" class="form-control"  required="required" />
                                            </div>

                                            <div class="form-group has-float-label select-search-group has-required">
                                                {{ Form::select('education_result_id', $resultList, null, ['placeholder'=>'Result Type', 'id'=>'education_result_id','required']) }}
                                                <label  for="education_result_id"> Result </label>
                                            </div>

                                            <div class="hide" id="cgpa_scale">
                                                <div class="form-group has-float-label">
                                                    <input type="text" name="education_result_cgpa" id="education_result_cgpa" placeholder="CGPA" class="form-control" />
                                                    <label  for="education_result_cgpa"> CGPA </label>
                                                </div>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input type="text" name="education_result_scale" id="education_result_scale" placeholder="Scale" class="form-control" />
                                                <label  for="education_result_scale"> Scale </label>
                                            </div>


                                            <div class="form-group hide has-float-label" id="division_mark">
                                                <input type="text" name="education_result_marks" id="education_result_marks" placeholder="Marks" class="form-control"  />
                                                <label  for="education_result_marks"> Marks(%) </label>
                                            </div>
                                            <div class="form-group has-float-label select-search-group has-required">
                                                
                                                <select  name="education_passing_year" id="education_passing_year" required="required">
                                                    <option value="">Selecet Passing Year</option>
                                                    @for($year=1950; $year<=date('Y') ; $year++)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                    @endfor
                                                </select>
                                                <label  for="education_level_title"> Passing Year </label>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-sm btn-success" type="submit">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                                </button>

                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-sm" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-sm-8" id="educationHistory">
                                            {!!$education!!}
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="bangla" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                {{ Form::open(['url'=>'hr/recruitment/employee/add_employee_bn',  'class'=>'form-horizontal']) }}
                                    <div class="row">
                                    
                                        <div class="col-sm-4">

                                            <input type="hidden" name="hr_bn_id" id="hr_bn_id"/> 

                                            <div class="form-group has-float-label">
                                                <input type="text" name="hr_bn_associate_id" placeholder="Associate's ID" class="form-control" value="{{ (!empty($advance->emp_adv_info_as_id)?$advance->emp_adv_info_as_id:(request()->route('emp_adv_info_as_id'))) }}" readonly /> 
                                                <label  for="emp_adv_info_as_id"> Associate's ID </label>
                                            </div>  

                                            <div class="form-group has-float-label has-required">
                                                <input name="hr_bn_associate_name" type="text" id="hr_bn_associate_name" placeholder="নাম" class="form-control" required="required" />
                                                <label  for="hr_bn_associate_name"> নাম </label>
                                            </div>

                                            <div class="form-group has-float-label has-required">
                                                <input type="text" id="hr_bn_unit" placeholder="ইউনিটের নাম" value="{{ (!empty($bangla->hr_unit_name_bn)?$bangla->hr_unit_name_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_unit"> ইউনিট </label>
                                            </div>
                                            
                                            <div class="form-group has-float-label has-required">
                                                <input type="text" id="hr_bn_department" placeholder="ডিপার্টমেন্টের নাম" value="{{ (!empty($bangla->hr_department_name_bn)?$bangla->hr_department_name_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_department"> ডিপার্টমেন্ট </label>
                                            </div>

                                            <div class="form-group has-float-label has-required">
                                                <input type="text" id="hr_bn_designation" placeholder="পদবি" value="{{ (!empty($bangla->hr_designation_name_bn)?$bangla->hr_designation_name_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_designation"> পদবি </label>
                                            </div>

                                           

                                            <div class="form-group has-float-label ">
                                                <input name="hr_bn_father_name" type="text" id="hr_bn_father_name" placeholder="পিতার নাম" class="form-control" required="required" />
                                                <label  for="hr_bn_father_name">পিতার নাম </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="hr_bn_mother_name" type="text" id="hr_bn_mother_name" placeholder="মাতার নাম" class="form-control" required="required " />
                                                <label  for="hr_bn_mother_name">মাতার নাম </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="hr_bn_spouse_name" type="text" id="hr_bn_spouse_name" placeholder="স্বামী/স্ত্রীর নাম (ঐচ্ছিক)" class="form-control"  />
                                                <label  for="hr_bn_spouse_name">স্বামী/স্ত্রীর নাম </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <legend><small>স্থায়ী ঠিকানা </small></legend>
                                            <div class="form-group has-float-label">
                                                <input name="hr_bn_permanent_village" type="text" id="hr_bn_permanent_village" placeholder="গ্রামের নাম"  class="form-control" required="required "  />
                                                <label  for="hr_bn_permanent_village"> গ্রাম  </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input name="hr_bn_permanent_po" type="text" id="hr_bn_permanent_po" placeholder="ডাকঘরের নাম"  class="form-control" required="required "  />
                                                <label  for="hr_bn_permanent_po"> ডাকঘর </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input type="text" id="hr_bn_permanent_upazilla" placeholder="উপজেলার নাম" value="{{ (!empty($bangla->permanent_upazilla_bn)?$bangla->permanent_upazilla_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_permanent_upazilla"> উপজেলা </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input type="text" id="hr_bn_permanent_district" placeholder="জেলার নাম" value="{{ (!empty($bangla->permanent_district_bn)?$bangla->permanent_district_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_permanent_district"> জেলা </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <legend><small>বর্তমান ঠিকানা</small></legend>
                                            <div class="form-group has-float-label">
                                                    <input name="hr_bn_present_road" type="text" id="hr_bn_present_road" placeholder="রোড নং "  class="form-control" required="required "  />
                                                <label  for="hr_bn_present_road"> রোড নং </label>
                                            </div> 

                                            <div class="form-group has-float-label">
                                                    <input name="hr_bn_present_house" type="text" id="hr_bn_present_house" placeholder="বাড়ি নং"  class="form-control" required="required "  />
                                                <label  for="hr_bn_present_house"> বাড়ি নং </label>
                                            </div> 

                                            <div class="form-group has-float-label">
                                                    <input name="hr_bn_present_po" type="text" id="hr_bn_present_po" placeholder="ডাকঘরের নাম"  class="form-control" required="required "  />
                                                <label  for="hr_bn_present_po"> ডাকঘর </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                    <input type="text" id="hr_bn_present_upazilla" placeholder="উপজেলার নাম" value="{{ (!empty($bangla->present_upazilla_bn)?$bangla->present_upazilla_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_present_upazilla"> উপজেলা </label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                    <input type="text" id="hr_bn_present_district" placeholder="জেলার নাম" value="{{ (!empty($bangla->present_district_bn)?$bangla->present_district_bn:null) }}" class="form-control" required="required" readonly />
                                                <label  for="hr_bn_present_district"> জেলা </label>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-sm btn-success" type="button">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                                </button>

                                                <button class="btn btn-sm" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                            </div>

                                        
                                            
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                         </div>
                      </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
     <div class="modal-content">
        <div class="modal-header">
           <h5 class="modal-title" id="exampleModalLabel">Edit education</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">×</span>
           </button>
        </div>
        <div class="modal-body" style="width:250px;margin: 0 auto;">
            <form class="needs-validation" novalidate action="{{url('hr/recruitment/operation/education_info/update')}}" method="post"> 
                @csrf
                <input type="hidden" name="education_id" id="education_id">
                <div class="form-group has-float-label select-search-group">
                    {{ Form::select('education_level_id', $levelList, null, ['placeholder'=>'Select Education Level', 'id'=>'edit_education_level_id', 'required'=> 'required']) }}
                    <label for="edit_education_level_id"> Education Level </label>
                </div>

                <div class="form-group hide has-float-label select-search-group" id="edit_degrreforPhd">
                    {{ Form::select('education_degree_id_1', [], null, ['id'=>'edit_education_degree_id_1']) }}
                    <label  for="edit_education_degree_id_1"> Exam/Degree Title </label>
                </div>

                <div class="form-group hide has-float-label" id="edit_PhdTitle">
                    <input name="education_degree_id_2" type="text" id="edit_education_degree_id_2" placeholder="Exam/Degree Title" class="form-control"/>
                    <label  for="edit_education_degree_id_2">Exam/Degree Title</label>
                </div>

                <div class="form-group hide has-float-label" id="edit_major">
                    <input name="education_major_group_concentation" type="text" id="edit_education_major_group_concentation" placeholder="Concentration/ Major/Group" class="form-control"  required="required" />
                    <label  for="edit_education_major_group_concentation">Concentration/ Major/Group </label>
                
                </div>

                <div class="form-group has-float-label">
                    <input name="education_institute_name" type="text" id="edit_education_institute_name" placeholder="Institute Name" class="form-control"  required="required" />
                    <label  for="edit_education_institute_name">Institute Name </label>
                </div>

                <div class="form-group has-float-label select-search-group">
                    {{ Form::select('education_result_id', $resultList, null, ['placeholder'=>'Select Result type Level', 'id'=>'edit_education_result_id']) }}
                    <label  for="edit_education_result_id"> Result </label>
                </div>

                <div class="hide" id="edit_cgpa_scale">
                    <div class="form-group has-float-label">
                        <input type="text" name="education_result_cgpa" id="edit_education_result_cgpa" placeholder="CGPA" class="form-control" />
                        <label  for="edit_education_result_cgpa"> CGPA </label>
                    </div>
                </div>

                <div class="form-group has-float-label">
                    <input type="text" name="education_result_scale" id="edit_education_result_scale" placeholder="Scale" class="form-control" required="required"/>
                    <label  for="edit_education_result_scale"> Scale </label>
                </div>


                <div class="form-group hide has-float-label" id="edit_division_mark">
                    <input type="text" name="education_result_marks" id="edit_education_result_marks" placeholder="Marks" class="form-control"  />
                    <label  for="edit_education_result_marks"> Marks(%) </label>
                </div>
                <div class="form-group has-float-label select-search-group">
                    
                    <select  name="education_passing_year" id="edit_education_passing_year" required="required">
                        <option value="">Selecet Passing Year</option>
                        @for($year=1950; $year<=date('Y') ; $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                    <label  for="edit_education_passing_year"> Passing Year </label>
                </div>
                <div class="form-group">
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                   <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
     </div>
  </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function()
{  
    $('.app-loader').show();
    var associate_id = '{{ (request()->route("emp_adv_info_as_id")) }}';
    showInfo(associate_id);
    //educationHistory(associate_id);
    if( window.location.hash == '#education' || window.location.hash == '#bangla'){
        $(".nav-link").removeClass('active')
        $("a[href='"+window.location.hash+"']").addClass('active');
        $(".tab-pane").addClass('active').removeClass('active show')
        $(window.location.hash).addClass('active show');

    }
    $('.app-loader').hide();

    

    $('#emp_adv_info_as_id').on('change', function(){
          window.location = '{{url('hr/recruitment/operation/advance_info_edit')}}'+'/'+$(this).val();  
    });

    /*$('#edit-education').on('click', function(){
        var data = $.parseJSON($('.edit-item-trigger-clicked').data('education'));
        console.log(data);
        $('#education_id').val(data.id);
        $('#edit_education_institute_name').val(data.education_institute_name);
    });*/

    $(document).on('click', ".edit-education", function() {
        var data = $(this).data('edu');
        $('#education_id').val(data.id);
        $('#edit_education_level_id').val(data.education_level_id).trigger('change.select2');
        
        $.ajax({
            url: '{{ url("level_wise_degree") }}',
            type: 'json',
            method: 'get',
            data: {id:data.education_level_id },
            success: function(data)
            {
                $("#edit_education_degree_id_1").html(data);
                
            },
            error: function()
            {
                $.notify('please try again','error');
            }

        });
        var status= ['1','2'];

        if (data.education_level_id == 1 || data.education_level_id == 2 )
        {
            $("#edit_major").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#edit_major").addClass('hide', 500, "linear");
        }

        if (data.education_level_id == 8)
        {
            $("#edit_PhdTitle").removeClass('hide', 500, "linear");
            $("#edit_degrreforPhd").addClass('hide', 500, "linear");
        }
        else
        {
            $("#edit_PhdTitle").addClass('hide', 500, "linear");
            $("#edit_degrreforPhd").removeClass('hide', 500, "linear");
        }
        $('#edit_education_institute_name').val(data.education_institute_name);
        $('#edit_education_passing_year').val(data.education_passing_year).trigger('change.select2');
        $('#edit_education_result_marks').val(data.education_result_marks);
        $('#edit_education_result_scale').val(data.education_result_scale);
        $('#edit_education_result_id').val(data.education_result_id).trigger('change.select2');
        $('#edit_education_result_cgpa').val(data.education_result_cgpa);
        $('#edit_education_major_group_concentation').val(data.education_major_group_concentation);
        $('#edit_education_degree_id_1').val(data.education_degree_id_1).trigger('change.select2');
        $('#edit_education_degree_id_2').val(data.education_degree_id_2).trigger('change.select2');
        
        if (data.education_result_id == 4)
        {

            $("#edit_cgpa_scale").removeClass('hide');
        }
        else
        {
            $("#edit_cgpa_scale").addClass('hide');
        }

        if (data.education_result_id == 1 || data.education_result_id == 2 || data.education_result_id == 3)
        {
            $("#edit_division_mark").removeClass('hide');
        }
        else
        {
            $("#edit_division_mark").addClass('hide');
        }
        
        var options = {
          'backdrop': 'static'
        };
        $('#editModal').modal(options);
    });


      // on modal hide
      $('#editModal').on('hide.bs.modal', function() {
        $("#edit-form").trigger("reset");
      })


    

    $('select.associates').select2({
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    }) 
                };
          },
          cache: true
        }
    }); 



    /*
    *----------------------------------------
    *   Marital Information
    *-----------------------------------------
    */


    $("#married_unmarried").on('change', function(){
        var status = ["Married", "Divorced", "Widowed"];

        if (status.includes($(this).val()))
        {
            $("#marritalInfo").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#marritalInfo").addClass('hide', 500, "linear");
        }
    });
 
    /*
    *----------------------------------------
    *   Add or Remove Nominee
    *-----------------------------------------
    */

    var data = $('.AddBtn').parent().parent().parent().parent().html();
    $('body').on('click', '.AddBtn', function(){
        $('.addRemove').append(data);
    });

    $('body').on('click', '.RemoveBtn', function(){
        $(this).parent().parent().parent().remove();
    });


    /*
    *----------------------------------------
    *   Permanent Address - District & Upazilla
    *-----------------------------------------
    */

    $("#as_per_dis").on('change', function()
    { 
        var id = $(this).val();
        if (id != '')
        {
            $.ajax({
                url: '{{ url("district_wise_upazilla") }}',
                type: 'json',
                method: 'get',
                data: {district_id: $(this).val() },
                success: function(data)
                {
                    $("#as_per_upz").html(data);
                },
                error: function()
                {
                    $.notify('please try again','error');
                }

            });
        } 
    });

    $("#as_pre_dis").on('change', function()
    { 
        var id = $(this).val();
        if (id != '')
        {
            $.ajax({
                url: '{{ url("district_wise_upazilla") }}',
                type: 'json',
                method: 'get',
                data: {district_id: $(this).val() },
                success: function(data)
                {
                    $("#as_pre_upz").html(data);
                },
                error: function()
                {
                    $.notify('please try again','error');
                }

            });
        } 
    });

   /*
    *----------------------------------------
    *   Exam/Degree Title- On Education Level
    *-----------------------------------------
    */

    $("#education_level_id").on('change', function()
    { 
        var id = $(this).val();
        if (id != '')
        {
            $.ajax({
                url: '{{ url("level_wise_degree") }}',
                type: 'json',
                method: 'get',
                data: {id: $(this).val() },
                success: function(data)
                {
                    $("#education_degree_id_1").html(data);
                },
                error: function()
                {
                    $.notify('please try again','error');
                }

            });
        }
        var status= ['1','2'];

        if (!status.includes($(this).val()))
        {
            $("#major").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#major").addClass('hide', 500, "linear");
        }


        var phd= ['8'];

        if (phd.includes($(this).val()))
        {
            $("#PhdTitle").removeClass('hide', 500, "linear");
            $("#degrreforPhd").addClass('hide', 500, "linear");
        }
        else
        {
            $("#PhdTitle").addClass('hide', 500, "linear");
            $("#degrreforPhd").removeClass('hide', 500, "linear");
        }
    });


    $("#edit_education_level_id").on('change', function()
    { 
        var id = $(this).val();
        if (id != '')
        {
            $.ajax({
                url: '{{ url("level_wise_degree") }}',
                type: 'json',
                method: 'get',
                data: {id: $(this).val() },
                success: function(data)
                {
                    $("#edit_education_degree_id_1").html(data);
                },
                error: function()
                {
                    $.notify('please try again','error');
                }

            });
        }
        var status= ['1','2'];

        if (!status.includes($(this).val()))
        {
            $("#edit_major").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#edit_major").addClass('hide', 500, "linear");
        }


        var phd= ['8'];

        if (phd.includes($(this).val()))
        {
            $("#edit_PhdTitle").removeClass('hide', 500, "linear");
            $("#edit_degrreforPhd").addClass('hide', 500, "linear");
        }
        else
        {
            $("#edit_PhdTitle").addClass('hide', 500, "linear");
            $("#edit_degrreforPhd").removeClass('hide', 500, "linear");
        }
    });

    /*
    *----------------------------------------
    *   CGPA and Scale On Grade
    *-----------------------------------------
    */
    $("#education_result_id").on('change', function(){
        var status = ['4'];
        var selected= ['1','2','3'];
        if (status.includes($(this).val()))
        {
            $("#cgpa_scale").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#cgpa_scale").addClass('hide', 500, "linear");
        }
        if (selected.includes($(this).val()))
        {
            $("#division_mark").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#division_mark").addClass('hide', 500, "linear");
        }
    });

    $("#edit_education_result_id").on('change', function(){
        var status = ['4'];
        var selected= ['1','2','3'];
        if (status.includes($(this).val()))
        {
            $("#edit_cgpa_scale").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#edit_cgpa_scale").addClass('hide', 500, "linear");
        }
        if (selected.includes($(this).val()))
        {
            $("#edit_division_mark").removeClass('hide', 500, "linear");
        }
        else
        {
            $("#edit_division_mark").addClass('hide', 500, "linear");
        }
    });


    // retrive all information by associate selction 
    $('body').on('change', '.associates', function(){
        showInfo($(this).val());
    });



});


function convertE2B(string)
{
    var bn = string.replace(/0/g, "০");
    bn = bn.replace(/1/g, "১");
    bn = bn.replace(/2/g, "২");
    bn = bn.replace(/3/g, "৩");
    bn = bn.replace(/4/g, "৪");
    bn = bn.replace(/5/g, "৫");
    bn = bn.replace(/6/g, "৬");
    bn = bn.replace(/7/g, "৭");
    bn = bn.replace(/8/g, "৮");
    bn = bn.replace(/9/g, "৯"); 
    return bn;
}

//bangla information
function showInfo(associate_id)
{
    $.ajax({
        url: '{{ url("hr/associate") }}',
        type: 'get',
        dataType: 'json',
        data: {associate_id: associate_id},
        success: function(data)
        { 
            // update previous information 
            $("#hr_bn_id").empty().val(data.hr_bn_id);
            $("#hr_bn_associate_name").empty().val(data.hr_bn_associate_name);
            $("#hr_bn_unit").empty().val(data.hr_unit_name_bn);
            $("#hr_bn_department").empty().val(data.hr_department_name_bn);
            $("#hr_bn_designation").empty().val(data.hr_designation_name_bn);
            $("#hr_bn_doj").empty().val((data.as_doj));
            $("#hr_bn_father_name").empty().val(data.hr_bn_father_name);
            $("#hr_bn_mother_name").empty().val(data.hr_bn_mother_name);
            $("#hr_bn_spouse_name").empty().val(data.hr_bn_spouse_name);

            $("#hr_bn_permanent_village").empty().val(data.hr_bn_permanent_village);
            $("#hr_bn_permanent_po").empty().val(data.hr_bn_permanent_po);
            $("#hr_bn_permanent_upazilla").empty().val(data.permanent_upazilla_bn);
            $("#hr_bn_permanent_district").empty().val(data.permanent_district_bn);

            $("#hr_bn_present_road").empty().val(data.hr_bn_present_road);
            $("#hr_bn_present_house").empty().val(data.hr_bn_present_house);
            $("#hr_bn_present_po").empty().val(data.hr_bn_present_po);
            $("#hr_bn_present_upazilla").empty().val(data.present_upazilla_bn);
            $("#hr_bn_present_district").empty().val(data.present_district_bn);


            //display employee informaiton in english 
            $("#associateInformation").html(
                "<dl class=\"dl-horizontal\">"+
                    "<dt>Associate's ID</dt><dd>"+data.associate_id+"</dd>"+
                    "<dt>Associate's Name</dt><dd>"+data.as_name+"</dd>"+
                    "<dt>Unit</dt><dd>"+data.hr_unit_name+"</dd>"+
                    "<dt>Department</dt><dd>"+data.hr_department_name+"</dd>"+
                    "<dt>Designation</dt><dd>"+data.hr_designation_name+"</dd>"+
                    "<dt>Date of Joining</dt><dd>"+data.as_doj+"</dd>"+
                    "<dt>Father's Name</dt><dd>"+data.emp_adv_info_fathers_name+"</dd>"+
                    "<dt>Mother's Name</dt><dd>"+data.emp_adv_info_mothers_name+"</dd>"+
                    "<dt>Spouse's Name</dt><dd>"+data.emp_adv_info_spouse+"</dd>"+
                    "<legend><small>Permanent Address</small></legend>"+
                    "<dt>Village</dt><dd>"+data.emp_adv_info_per_vill+"</dd>"+
                    "<dt>Post Office</dt><dd>"+data.emp_adv_info_per_po+"</dd>"+
                    "<dt>Upazilla</dt><dd>"+data.permanent_upazilla+"</dd>"+
                    "<dt>District</dt><dd>"+data.permanent_district+"</dd>"+
                    "<legend><small>Present Address</small></legend>"+
                    "<dt>House No</dt><dd>"+data.emp_adv_info_pres_house_no+"</dd>"+
                    "<dt>Road No</dt><dd>"+data.emp_adv_info_pres_road+"</dd>"+
                    "<dt>Post Office</dt><dd>"+data.emp_adv_info_pres_po+"</dd>"+
                    "<dt>Upazilla</dt><dd>"+data.present_upazilla+"</dd>"+
                    "<dt>District</dt><dd>"+data.present_district+"</dd>"+
                "</dl>"
            );


        },
        error: function(xhr)
        {
            $.notify('please try again...','error');
        }
    });
}

// Education History
function educationHistory(associate_id)
{

    $.ajax({
        url: '{{ url("hr/recruitment/education_history") }}',
        dataType: 'json',
        data: {associate_id: associate_id},
        success: function(data)
        { 
            $("#educationHistory").html(data); 
        },
        error: function(xhr)
        {
            $.notify('please try again...','error');
        }
    });
}

   
    //file upload validation....
    $("#emp_adv_info_birth_cer, #emp_adv_info_city_corp_cer, #emp_adv_info_police_veri, #as_job_appl, #as_cv").change(function(){
        
        var fileExtension1 = ['docx','doc','pdf','jpeg','png','jpg'];
        var ext = $(this).val().split('.').pop().toLowerCase();

        var size= $(this)[0].files[0].size/1024/1024;
        //console.log(size);

        if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension1) == -1) {
            $(this).next().show();
            $(this).val('');
        }
        else{
            $(this).next().hide();
        }

        if(ext=='pdf'|| ext== 'doc' || ext =='docx'){
            if(size>1){
                $(this).val('');
                $.notify('too big! maximum is 1MB','error');
            }

        }



    });

    $("#finger_print, #Signature, #authority_signature").change(function(){
        var fileExtension2 = ['jpeg','png','jpg'];
        if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension2) == -1) {
            $(this).next().show();
            $(this).val('');
        }
        else{
            $(this).next().hide();
        }
    });

</script>


@endpush
@endsection