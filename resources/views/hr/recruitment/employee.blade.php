@extends('hr.layout')
@section('title', $info->as_name.' Information')
@section('main-content')
@push('css')
<style type="text/css">
table{
  border-collapse: collapse;
  width: 100%;
}

table td, table th {
  border: 1px solid #ffebbb;
  padding: 6px;
  text-align: center;
}

table tr:nth-child(even){background-color: #f2f2f2;}
.nav-tabs li {
    width: auto;
}


table th {
  padding-top: 8px;
  padding-bottom: 8px;
  background-color: #ffebbb;
  color: #393939;
}

.fc-prev-button, .fc-next-button, .fc-today-button{display: none;}
.progress[data-percent]:after {
    display: none;
}

.btn {
	border-radius: 2px;
}
</style>
<link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}">
@endpush

<div  class="main-content">
	<div class="main-content-inner">

        <div class="row">
      		<div class="col-4">
		        <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-user-profile-block" style="height: calc(100% - 80px);">
		            <div class="iq-card-body">
		               <div class="user-details-block">
		                  <div class="user-profile text-center">
		                     @if($info->employee)
		                     <img src='{{ $info->employee['as_pic'] != null?asset($info->employee['as_pic'] ):($info->employee['as_gender'] == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}' class="avatar-130 img-fluid" alt="{{ $info->name }}" onError='this.onerror=null;this.src="{{ ($info->employee['as_gender'] == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}";'>
		                     @else
		                        <img class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} ">
		                      @endif
		                  </div>
		                  <div class="text-center mt-3">
		                     <h2><strong>{{ $info->as_name }}</strong></h2>
		                     <p class="mb-0">{{ $info->hr_designation_name }}, {{ $info->hr_department_name }}</p>
		                     <p class="">{{ $info->hr_unit_name }}</p>
		                  	 <p class="mb-0">Associate ID: {{ $info->associate_id }} </p>
		                     <p class="">Today's Status: 
								 @if($status['status']==2)
								    <span class="label label-warning "> Leave </span>
								    <span class="label label-success "> {{ $status['type'] }} </span>
								 @elseif($status['status']==1)
								    <span class="label label-success"> Present </span>
								 @else
								    <span class="label label-danger "> Absent </span>
								 @endif
							 </p>
							 <div class="buttons">
			                	@if(auth()->user()->canany(['Manage Employee']) || auth()->user()->hasRole('Super Admin'))
				                    <div class="btn-group"> 
				                        <a  href='{{url("hr/recruitment/employee/pdf/$info->associate_id")}}' target="_blank" data-tooltip="Download Employee Profile" data-tooltip-location="top" class="btn btn-sm btn-danger"  style="border-radius: 2px !important; padding: 4px; "><i class="fa fa-file-pdf-o bigger-120"></i></a> 
				                        <a  href='{{url("hr/recruitment/employee/edit/$info->associate_id")}}' target="_blank" data-tooltip="Edit Profile" data-tooltip-location="top" class="btn btn-sm btn-info"  style="border-radius: 2px !important; padding: 4px;"><i class="las la-user-tie bigger-120">&nbsp</i> </a> 
				                      
				                        <a  href='{{url("hr/recruitment/operation/medical_info_edit/$info->associate_id")}}' target="_blank" data-tooltip="Edit Medical Info" data-tooltip-location="left" class="btn btn-sm btn-warning" style="border-radius: 2px !important; padding: 4px;"><i class="fa fa-user-md bigger-120">&nbsp</i></a>
				                    </div>
			                    @endif
			                </div>
		                  </div>
		               </div>
		            </div>
		        </div>
      		</div>
  			<div class="col-lg-8">
  				<div class="iq-card iq-card-block iq-card-stretch iq-card-height">
		            <div class="iq-card-body">
		            	<div class="row">
		            		<div class="col-12">
		            			<div class="progress pos-rel" data-percent="{{$per_complete}}%">
									<div class="progress-bar" style="width:{{$per_complete}}%;">This profile is {{$per_complete}}% complete.</div>
								</div>
		            		</div>
		            		<div class="col-6">
								<div class="profile-user-info adv-info">
											
									<div class="profile-info-row">
										<div class="profile-info-name"> Gender </div>
										<div class="profile-info-value">
											<span> {{ $info->as_gender }} </span>
										</div>
									</div>

									<div class="profile-info-row">
										<div class="profile-info-name"> Date of Birth </div>
										<div class="profile-info-value">
											<span>
												{{ (!empty($info->as_dob)?(date("d-M-Y",strtotime($info->as_dob))):null) }}
											</span>
										</div>
									</div>
									@if(auth()->user()->canany(['Advance Info List','Manage Employee']) || auth()->user()->hasRole('Super Admin'))
									<div class="profile-info-row">
										<div class="profile-info-name"> Contact  </div>
										<div class="profile-info-value">
											<span> {{ $info->as_contact }} </span>
										</div>
									</div>
									@endif
									<div class="profile-info-row">
										<div class="profile-info-name"> OT Status </div>
										<div class="profile-info-value">
											<span> @if($info->as_ot == 0) Non OT @else OT @endif </span>
										</div>
									</div>


									<div class="profile-info-row">
										<div class="profile-info-name"> Joining Date </div>
										<div class="profile-info-value">
											<span>
												{{ (!empty($info->as_doj)?(date("d-M-Y",strtotime($info->as_doj))):null) }}
											</span>
										</div>
									</div>


									<div class="profile-info-row">
										<div class="profile-info-name"> Status</div>
										<div class="profile-info-value">
											<span>@if($info->as_status == 1) Active @else Inactive @endif </span>
										</div>
									</div>

								</div>
							</div>

							<div class="col-6">
								<div class="profile-user-info adv-info">

									<div class="profile-info-row">
										<div class="profile-info-name"> Type </div>
										<div class="profile-info-value">
											<span> {{ $info->hr_emp_type_name }} </span>
										</div>
									</div>

									<div class="profile-info-row">
										<div class="profile-info-name"> Area </div>
										<div class="profile-info-value">
											<span> {{ $info->hr_area_name }} </span>
										</div>
									</div>

									<div class="profile-info-row">
										<div class="profile-info-name"> Section </div>
										<div class="profile-info-value">
											<span> {{ $info->hr_section_name }} </span>
										</div>
									</div>

									<div class="profile-info-row">
										<div class="profile-info-name"> Sub Section </div>
										<div class="profile-info-value">
											<span> {{ $info->hr_subsec_name }} </span>
										</div>
									</div>

									<div class="profile-info-row">
										<div class="profile-info-name"> Floor </div>
										<div class="profile-info-value">
											<span> {{ $info->hr_floor_name }} </span>
										</div>
									</div>


									<div class="profile-info-row">
										<div class="profile-info-name"> Line </div>
										<div class="profile-info-value">
											<span> {{ $info->hr_line_name }} </span>
										</div>
									</div>


									<div class="profile-info-row">
										<div class="profile-info-name"> Shift </div>
										<div class="profile-info-value">
											<span> {{ $roasterShift!=null?$roasterShift.' (Changed)':$info->as_shift_id.' (Default)' }} </span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<div class="profile-info-name">Current Station</div>
								<table class="table">
									<body>
										<tr>
									        <td>Floor</td>
									        <td>Line</td>
									        <td>Start</td>
									        <td>End</td>
									        <td>Changed by</td>
									    </tr>
									    @if($station)
										<tr>
											<td>{{ $station->hr_floor_name }}</td>
											<td>{{ $station->hr_line_name }}</td>
											<td>{{ $station->start_date }}</td>
											<td>{{ $station->end_date }}</td>
											<td>{{ $station->as_name }}-{{ $station->updated_by }}</td>
										</tr>
										@else
										<tr><td colspan="5"> No data found!</td></tr>
										@endif
									</body>
								</table>
							</div>
		            	</div>
		            </div>
		        </div>
  			</div>
  			<div class="col-12">
  				<div class="iq-card iq-card-block iq-card-stretch iq-card-height">
  					<div class="p-3">
  						
	  					<ul class="nav nav-pills mb-2 mt-2" id="pills-tab" role="tablist">
							<li class="nav-item" class="active">
								<a data-toggle="tab" class="nav-link active" role="tab" aria-controls="home" aria-selected="true" href="#info">
									<i class="green ace-icon fa fa-user bigger-120"></i>
									Advance Info
								</a>
							</li>
							@if(auth()->user()->canany(['Salary Sheet','Salary Report','Assign Benefit']) || auth()->user()->hasRole('Super Admin'))
							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" href="#benefit">
									<i class="orange ace-icon fa fa-gift bigger-120"></i>
									Benefits
								</a>
							</li>
							@endif

							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" href="#employment">
									<i class="blue ace-icon fa fa-users bigger-120"></i>
									Employment History
								</a>
							</li>
							@if(auth()->user()->canany(['Salary Sheet','Salary Report']) || auth()->user()->hasRole('Super Admin'))
							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" href="#salary">
									<i class="pink ace-icon fa fa-money bigger-120"></i>
									Salary Record
								</a>
							</li>
							@endif

							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" href="#loan">
									<i class="pink ace-icon fa fa-money bigger-120"></i>
									Loan History
								</a>
							</li>
							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" href="#leave">
									<i class="orange ace-icon fa fa-list bigger-120"></i>
									Leave History
								</a>
							</li>
							@if(auth()->user()->canany(['Query Attendance','Attendance Report','Manual Attendance Report']) || auth()->user()->hasRole('Super Admin'))
							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" id="attload" href="#attendance">
									<i class="orange ace-icon fa fa-list bigger-120"></i>
									Attendance History
								</a>
							</li>
							@endif

							<li class="nav-item">
								<a data-toggle="tab" class="nav-link" role="tab" aria-controls="home" aria-selected="true" href="#discipline">
									<i class="blue ace-icon fa fa-file bigger-120"></i>
									Disciplinary Record
								</a>
							</li>

							
						</ul>
						<hr>
						<div class="tabbable">
							
							<div class="tab-content no-border" >
								<div id="info" class="tab-pane in active">
									<!-- /.row -->

									
									@if(auth()->user()->canany(['Advance Info List','Manage Employee']) || auth()->user()->hasRole('Super Admin'))
									<div class="row">
										<div class="col-xs-12 col-sm-5">
										
											<div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														About Me
													</h4>
												</div>

												<div class="">
												<div class="profile-user-info adv-info">

												<div class="profile-info-row">
													<div class="profile-info-name"> Job Status</div>
													<div class="profile-info-value">
														<span> 
														@if($info->emp_adv_info_stat == 1) 
															Permanent 
														@else 
															Probationary

														@endif
														<?php if($info->emp_adv_info_stat == 0) echo "(for ". $info->emp_adv_info_prob_period . " Months)";

														 ?>
														</span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> Nationality </div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_nationality }} </span>
													</div>
												</div>
												
												
												<div class="profile-info-row">
													<div class="profile-info-name"> Passport Number</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_passport }} </span>
													</div>
												</div>
												<div class="profile-info-row">
													<div class="profile-info-name">National Id</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_nid }} </span>
													</div>
												</div>
												<div class="profile-info-row">
													<div class="profile-info-name">Bank Info</div>
													<div class="profile-info-value">
														<span> Bank Name: {{ $info->emp_adv_info_bank_name }}<br> Account N0:{{ $info->emp_adv_info_bank_num }} </span>
													</div>
												</div>

												

												<div class="profile-info-row">
													<div class="profile-info-name">Father's Name</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_fathers_name }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Mother's Name</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_mothers_name }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Marital Status</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_marital_stat }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Spouse Name</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_spouse }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Children</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_children }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Religion</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_religion }} </span>
													</div>
												</div>

												

												<div class="profile-info-row">
													<div class="profile-info-name">Permanent Address</div>
													<div class="profile-info-value">
													    <i class="fa fa-map-marker light-orange bigger-100"> </i> &nbsp;
														<span> {{ $info->emp_adv_info_per_vill }} {{ $info->emp_adv_info_per_po }} {{ $info->emp_adv_info_per_upz }} {{ $info->permanent_district }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Present Address</div>
													<div class="profile-info-value">
													    <i class="fa fa-map-marker light-orange bigger-100"> </i>&nbsp;
														<span> {{ $info->emp_adv_info_pres_house_no }} {{ $info->emp_adv_info_pres_road }} {{ $info->emp_adv_info_pres_po }} {{ $info->emp_adv_info_pres_upz }} {{ $info->present_district }} </span>
													</div>
												</div>

												
												
												


												<div class="profile-info-row">
													<div class="profile-info-name">Emergency Contact Name</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_emg_con_name }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Emergency Contact Number</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_emg_con_num }} </span>
													</div>
												</div>

												
											</div>
													
												</div>
											</div>
										</div>

										<div class="col-xs-12 col-sm-4">
											<div class=" transparent">
												<div class=" header-color-blue2">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Files
													</h4>
												</div>

												<div class="">
													<div class="widget-main padding-16">
													<div class="profile-info-row">
													<div class="profile-info-name">Job Application</div>
													<div class="profile-info-value  btn-group">
														@if(!empty($info->emp_adv_info_job_app))
														<a href="{{ url($info->emp_adv_info_job_app) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_job_app) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-eye"></i>
															 
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div> 
												</div>


												<div class="profile-info-row">
													<div class="profile-info-name">CV</div>
													<div class="profile-info-value  btn-group">
														@if(!empty($info->emp_adv_info_cv))
														<a href="{{ url($info->emp_adv_info_cv) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_cv) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-eye"></i>
															 
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div> 
												</div>
												<div class="profile-info-row">
													<div class="profile-info-name"> Reference Name</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_refer_name }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> Reference Contact</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_refer_contact }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> Reference Biodata</div>
													<div class="profile-info-value">
														<span> Bio Data </span>
													</div>
												</div>
													<div class="profile-info-row">
													
													<div class="profile-info-name"> Birth Certificate</div>
													<div class="profile-info-value btn-group">
														@if(!empty($info->emp_adv_info_birth_cer))
														<a href="{{ url($info->emp_adv_info_birth_cer) }}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
														<a href="{{ url($info->emp_adv_info_birth_cer) }}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> City Corp. Certificate</div>  
													<div class="profile-info-value  btn-group">
														@if(!empty($info->emp_adv_info_city_corp_cer))
														<a href="{{ url($info->emp_adv_info_city_corp_cer) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_city_corp_cer) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-download"></i>
															 Download
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name"> Police Verification</div>

													<div class="profile-info-value btn-group">
														@if(!empty($info->emp_adv_info_police_veri))
														<a href="{{ url($info->emp_adv_info_police_veri) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_police_veri) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-eye"></i>
															 
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div> 
												</div>

														

												

												<div class="profile-info-row">
													<div class="profile-info-name">TIN/ETIN</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_tin }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Finger Print</div>
													<div class="profile-info-value  btn-group">
														@if(!empty($info->emp_adv_info_finger_print))
														<a href="{{ url($info->emp_adv_info_finger_print) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_finger_print) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-eye"></i>
															 
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div> 
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Signature</div>
													<div class="profile-info-value btn-group">
														@if(!empty($info->emp_adv_info_signature))
														<a href="{{ url($info->emp_adv_info_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-eye"></i>
															 
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div> 
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Authorized Signature</div>
													<div class="profile-info-value btn-group">
														@if(!empty($info->emp_adv_info_auth_sig))
														<a href="{{ url($info->emp_adv_info_auth_sig) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
															<i class="fa fa-eye"></i>
															 
														</a>
														<a href="{{ url($info->emp_adv_info_auth_sig) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
															<i class="fa fa-eye"></i>
															 
														</a>
														@else
															<strong class="text-danger">No file found!</strong>
														@endif
													</div> 
												</div>
												<div class="profile-info-row">
													<div class="profile-info-name">Previous Organization</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_pre_org }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Work Experience</div>
													<div class="profile-info-value">
														<span> {{ $info->emp_adv_info_work_exp }} Year(s)</span>
													</div>
												</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xs-12 col-sm-3">
											<div class=" transparent">
												<div class=" header-color-blue2">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Medical Info
													</h4>
												</div>

												<div class="">
													<div class="widget-main padding-16">
													<div class="profile-user-info">
														<div class="profile-info-row">
															<div class="profile-info-name">Height</div>
															<div class="profile-info-value">
																<span> {{ $info->med_height }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Weight</div>
															<div class="profile-info-value">
																<span> {{ $info->med_weight }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Tooth Structure</div>
															<div class="profile-info-value">
																<span> {{ $info->med_tooth_str }} </span>
															</div>
														</div> 

														<div class="profile-info-row">
															<div class="profile-info-name">Blood Group</div>
															<div class="profile-info-value">
																<span> {{ $info->med_blood_group }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Identification Mark</div>
															<div class="profile-info-value">
																<span> {{ $info->med_ident_mark }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Others</div>
															<div class="profile-info-value">
																<span> {{ $info->med_others }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Doctors Comment</div>
															<div class="profile-info-value">
																<span> {{ $info->med_doct_comment }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Doctors Age Confirmation</div>
															<div class="profile-info-value">
																<span> {{ $info->med_doct_conf_age }} </span>
															</div>
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name btn-group">Signature</div>
															<div class="profile-info-value btn-group">
																@if(!empty($info->med_signature))
																<a href="{{ url($info->med_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
																	<i class="fa fa-eye"></i>
																	 
																</a>
																<a href="{{ url($info->med_signature) }}" class="btn btn-xs btn-success" download target="_blank" title="Download">
																	<i class="fa fa-eye"></i>
																	 
																</a>
																@else
																	<strong class="text-danger">No file found!</strong>
																@endif
															</div> 
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Authorized Signature</div>
															<div class="profile-info-value btn-group">
																@if(!empty($info->med_auth_signature))
																<a href="{{ url($info->med_auth_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
																	<i class="fa fa-eye"></i>
																	 
																</a>
																<a href="{{ url($info->med_auth_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
																	<i class="fa fa-eye"></i>
																	 
																</a>
																@else
																	<strong class="text-danger">No file found!</strong>
																@endif
															</div> 
														</div>

														<div class="profile-info-row">
															<div class="profile-info-name">Doctors Signature</div>
															<div class="profile-info-value btn-group">
																@if(!empty($info->med_doct_signature))
																<a href="{{ url($info->med_doct_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
																	<i class="fa fa-eye"></i>
																	 
																</a>
																<a href="{{ url($info->med_doct_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
																	<i class="fa fa-eye"></i>
																	 
																</a>
																@else
																	<strong class="text-danger">No file found!</strong>
																@endif
															</div> 
														</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12">
											<div class=" transparent">
												<div class=" header-color-blue2">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Education History
													</h4>
												</div>

												<div class="">
													<div class="widget-main padding-16">
													@if(!empty($educations) && count($educations) >0)
													<table class="table table-compact">  
														<tbody>
														@foreach($educations as $education)
								                            <tr> 
									                            <td>
									                            	<strong>Lavel of Education:</strong> {{ $education->education_level_title }}
									                            	<br>

									                            	<strong>Institute:</strong> {{ $education->education_institute_name }}
									                            </td>
									                        	<td>
									                            	<strong>Exam/Degree Title:</strong> 
								                            		{{ $education->education_degree_title }} 
									                            	<br>
									                            	@if(!in_array($education->education_level_id, [1,2,8]))
										                            	<strong>Concentration/Major/Group:</strong> 
										                            	{{ $education->education_major_group_concentation }} 
										                            @endif

									                            	@if(in_array($education->education_level_id, [8]))
										                            	<strong>Concentration/Major/Group:</strong> 
										                            	{{ $education->education_degree_id_2 }} 
									                            	@endif
									                            </td> 
									                        	<td>
									                            	<strong>Year:</strong> {{ $education->education_passing_year }} 

									                            	<br/>

									                            	<strong>Result:</strong> {{ $education->education_result_title }} <br/>

									                            	@if(in_array($education->education_result_id, [1,2,3]))
										                            	<strong>Marks:</strong> {{ $education->education_result_marks }}  <br/>
									                            	@elseif(in_array($education->education_result_id,[4]))
										                            	<strong>CGPA:</strong> {{ $education->education_result_cgpa }}  <br/>
										                            	<strong>Scale:</strong> {{ $education->education_result_scale }}
										                            @endif 
									                            </td>
									                        </tr> 
														@endforeach  
														</tbody>
													</table>
													@else 
													No records found!
													@endif 
													</div>
												</div>
											</div>
										</div>
										
										
									</div>
									@else
										You are not permitted to see!
									@endif
								</div><!-- /#home -->
								@if(auth()->user()->canany(['Salary Sheet','Salary Report','Assign Benefit']) || auth()->user()->hasRole('Super Admin'))
								<div id="benefit" class="tab-pane">
								    <div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Benefits
													</h4>
												</div>

												<div class="">
								                <div class="profile-user-info">

												<div class="profile-info-row">
													<div class="profile-info-name">Joining Salary</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_joining_salary }} </span>
													</div>
												</div>
												
												<div class="profile-info-row">
													<div class="profile-info-name">Current Salary</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_current_salary }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Basic Salary</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_basic }} </span>
													</div>
												</div>
												<div class="profile-info-row">
													<div class="profile-info-name">House Rent</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_house_rent }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Medical</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_medical }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Transportation</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_transport }} </span>
													</div>
												</div>

												<div class="profile-info-row">
													<div class="profile-info-name">Food</div>
													<div class="profile-info-value">
														<span> {{ $info->ben_food }} </span>
													</div>
												</div>
											</div>
										
									</div>
									</div>
									
								</div><!-- /#feed -->
								@endif
								@if(auth()->user()->canany(['Query Attendance','Attendance Report','Manual Attendance Report']) || auth()->user()->hasRole('Super Admin'))
								<div id="attendance" class="tab-pane">
                    				<div class="row"> 
                    				   <div class="col-sm-offset-2 col-sm-8">
										<div class=" transparent">
											<div class="">
												<h4 class="widget-title smaller border-left-heading mb-3">

													Attendance History
												</h4>
											</div>

					                        <div class="">
					                            <div id="attcal" class="widget-main no-padding">
					                            <br>
					                            <br>
					                            
					                            



					                            
					                            </div>
					                        </div>
					                    </div>
					                  </div>
					                </div>
					            </div>
					            @endif

								<div id="employment" class="tab-pane">
                    						<div class="row"> 
							                    <div class="col-sm-6">
													<div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Promotion History
													</h4>
												</div>

								                        <div class="">
								                            <div class="widget-main no-padding">
								                            
								                                <table class="table" >
								                                    <thead>
									                                    <tr>
									                                        <th >Current Designation</th>
									                                        <th >Previous Designation</th>
									                                        <th >Eligible Date</th>
									                                        <th >Effective Date</th>
									                                    </tr>   
								                                    </thead>	 
								                                    <tbody>
								                                    @if(!empty($promotions) && count($promotions) >0) 
								                                    	@foreach($promotions as $promotion)
									                                    <tr>
									                                        <td >{{ $promotion->current_designation }}</td>
									                                        <td >{{ $promotion->previous_designation }}</td>
									                                        <td >{{ $promotion->eligible_date }}</td>
									                                        <td >{{ $promotion->effective_date }}</td>
									                                    </tr> 
									                                    @endforeach
									                                    @else
											                                <tr><td colspan="4">No records found! </td></tr>      
											                            @endif

								                                    </tbody> 
								                                </table>
								                            
 
								                            </div>
								                        </div>
								                    </div> 
							                    </div>
							                     
							                    <div class="col-sm-6">
													<div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Increment History
													</h4>
												</div>

								                        <div class="">
								                            <div class="widget-main no-padding">
								                            
								                                <table class="table" >
								                                    <thead>
									                                    <tr>
									                                        <th >Current Salary</th>
									                                        <th >Previous Salary</th>
									                                        <th >Increment Amount</th>
									                                        <th >Eligible Date</th>
									                                        <th >Effective Date</th>
									                                    </tr>   
								                                    </thead>	 
								                                    <tbody> 
								                                    @if(!empty($increments) && count($increments) >0)
								                                    	@foreach($increments as $increment)
									                                    <tr>
									                                        <td >
									                                        <?php
																				$amount = $increment->current_salary;
									                                         	if ($increment->amount_type==2)
									                                         	{
									                                         		$incrementAmount = ($increment->current_salary/100)*$increment->increment_amount;
									                                         	} 
									                                         	else
									                                         	{
									                                         		$incrementAmount = $increment->increment_amount;
									                                         	}
									                                         	echo $amount+$incrementAmount;
								                                         	?>
									                                         </td>
									                                        <td >
								                                        	{{ $increment->current_salary }}
									                                        </td>
									                                        <td ><?php if($increment->amount_type==1) echo $increment->increment_amount; else echo $increment->increment_amount. " %"; ?></td>
									                                        <td >{{ date("d-M-Y",strtotime($increment->eligible_date)) }}</td>
									                                        <td >{{ date("d-M-Y",strtotime($increment->effective_date)) }}</td>
									                                    </tr> 
									                                    @endforeach
									                                    @else
											                                <tr><td colspan="5">No records found! </td></tr>      
											                            @endif
								                                    </tbody> 
								                                </table>
								                                
 
								                            </div>
								                        </div>
								                    </div> 
							                    </div>
						                    </div>
									
								</div><!-- /#friends -->

								<div id="loan" class="tab-pane">
									<div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Loan History
													</h4>
												</div>
											
											<table class="table table-compact">
												<thead>
												    <tr>
												        <th>Types of Loan</th>
												        <th>Approved Amount</th>
												        <th>Due</th>
												        <th>Date</th>
												        <th>Status</th>
												    </tr>
												</thead>
												<tbody>
												@if(!empty($loans) && count($loans) >0)
												@foreach($loans as $loan)
						                            <tr>
							                            <td>{{ $loan->hr_la_type_of_loan }}</td>
							                            <td>{{ $loan->hr_la_approved_amount }}</td>
							                            <td>0.00</td>
							                            <td>
							                            	@if($loan->hr_la_updated_at !=null) 	{{ date("d-M-Y",strtotime($loan->hr_la_updated_at)) }}
							                            	@endif
							                        	</td>
							                            <td>{{ $loan->hr_la_status }}</td>
							                        </tr>
												@endforeach
												@else
					                                <tr><td colspan="5">No records found! </td></tr>      
					                            @endif 
												</tbody>
											</table> 
										</div>
								</div><!-- /#pictures -->

								<div id="leave" class="tab-pane">
								     <div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Leave History
													</h4>
												</div>
								            @if(!empty($leaves) && count($leaves) >0) 
												@foreach($leaves as $leave)
												<div class="row">
													<div class="col-xs-2">
														<strong>{{ $leave->year }}</strong>
													</div>

													<div class="col-xs-10">
														<table class="table" style="border:1px solid #6EAED1">
															<thead>
															<tr>
																<th >Leave Type</th>
																<th >Total</th>
																<th >Taken</th>
																<th >Due</th>
															</tr>	
															</thead>
															<tbody>
															<tr>
																<th >Casual</th>
																<td >10</td>
																<td >{{ (!empty($leave->casual)?$leave->casual:0) }}</td>
																<td >{{ (10-$leave->casual) }}</td>
															</tr>
															<tr>
																<th >Earned</th>
																<td > {{$earnedLeaves[$leave->year]['earned']}} </td>
																<td > {{$earnedLeaves[$leave->year]['enjoyed']??0}} </td>
																<td > {{$earnedLeaves[$leave->year]['remain']}} </td>
															</tr>
															<tr>
																<th >Sick</th>
																<td >14</td>
																<td >{{ (!empty($leave->sick)?$leave->sick:0) }}</td>
																<td >{{ (14-$leave->sick) }}</td>
															</tr>
															<tr>
						                                        <th >Special</th>
						                                        <td > - </td>
						                                        <td >{{ (!empty($leave->special)?$leave->special:0) }}</td>
						                                        <td > - </td>
						                                    </tr>
															<?php 
																$display='';
																if($info->as_gender =='Male')
																{$display='display:none;';} 
															?>
															<tr style="{{$display}}">
																<th >Maternity</th>
																<td >112</td>
																@if(!empty($leave->maternity))
																	<td> 112 </td>
																	<td> 0 </td>
																@else	
																	<td> 0 </td>
																	<td> 112 </td>
																@endif
																
															</tr>
															</tbody>
															@if($info->as_gender=='Male')
															<tfoot>
																<tr>
																	<th >Subtotal</th>
																	<td >{{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned'])-112 }}</td>
																	<td> {{(!empty($leave->maternity)?112:0)+(!empty($leave->sick)?$leave->sick:0)+($earnedLeaves[$leave->year]['enjoyed']) + (!empty($leave->casual)?$leave->casual:0)}}</td>
																	
																	<td >{{ (10-$leave->casual)+($earnedLeaves[$leave->year]['remain'])+(14-$leave->sick)+(empty($display)?112:0) }}</td>
																	
																</tr>
																<!-- <tr>
																	<th >Total Leave</th>
																	<td colspan="3"  ><center><b> {{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned'])-112 }} </b></center></td>
																</tr> -->
															</tfoot>
															@else
															<tfoot>
																<tr>
																	<th >Subtotal</th>
																	<td >{{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned']) }}</td>
																	<td> {{(!empty($leave->maternity)?112:0)+(!empty($leave->sick)?$leave->sick:0)+($earnedLeaves[$leave->year]['enjoyed']) + (!empty($leave->casual)?$leave->casual:0)}}</td>
																	
																	<td >{{ (10-$leave->casual)+($earnedLeaves[$leave->year]['remain'])+(14-$leave->sick)+(empty($display)?112:0) }}</td>
																	
																</tr>
																<!-- <tr>
																	<th >Total Leave</th>
																	<td colspan="3"  ><center><b> {{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned']) }} </b></center></td>
																</tr> -->
															</tfoot>
															@endif
														</table>
													</div>
												</div>
												<hr>
												@endforeach 
											@else 
											    <div class="row">
													<div class="col-2">
														<strong>{{ date('Y') }}</strong>
													</div>

													<div class="col-10">
														<table class="table" style="border:1px solid #6EAED1">
															<thead>
															<tr>
																<th >Leave Type</th>
																<th >Total</th>
																<th >Taken</th>
																<th >Due</th>
															</tr>	
															</thead>
															<tbody>
															<tr>
																<th >Casual</th>
																<td >10</td>
																<td >0</td>
																<td >10</td>
															</tr>
															<tr>
																<th >Earned</th>
																<td >{{$earnedLeaves[date('Y')]['remain']}}</td>
																<td >0</td>
																<td >{{$earnedLeaves[date('Y')]['remain']}}</td>
															</tr>
															<tr>
																<th >Sick</th>
																<td >14</td>
																<td >0</td>
																<td >14</td>
															</tr>
															<tr>
						                                        <th >Special</th>
						                                        <td > - </td>
						                                        <td >0</td>
						                                        <td > - </td>
						                                    </tr>
															<?php 
																$display='';
																if($info->as_gender =='Male')
																{$display='display:none;';} 
															?>
															<tr style="{{$display}}">
																<th >Maternity</th>
																<td >112</td>
																<td >0</td>
																<td >112</td>
															</tr>
															</tbody>
															@if($info->as_gender=='Male')
															<tfoot>
																<tr>
																	<th >Subtotal</th>
																	<td >{{(136+$earnedLeaves[date('Y')]['remain']-112)}}</td>
																	<td >0</td>
																	<td >{{(136+$earnedLeaves[date('Y')]['remain']-112)}}</td>
																</tr>
																<!-- <tr>
																	<th >Total Leave</th>
																	<td colspan="3" ><center><b>{{(136+$earnedLeaves[date('Y')]['remain']-112)}} </b></center></td>
																</tr> -->
															</tfoot>
															@else
															<tfoot>
																<tr>
																	<th >Subtotal</th>
																	<td >{{(136+$earnedLeaves[date('Y')]['remain'])}}</td>
																	<td >0</td>
																	<td >{{(136+$earnedLeaves[date('Y')]['remain'])}}</td>
																</tr>
																<!-- <tr>
																	<th >Total Leave</th>
																	<td colspan="3" ><center><b>{{(136+$earnedLeaves[date('Y')]['remain'])}} </b></center></td>
																</tr> -->
															</tfoot>
															@endif
															
														</table>
													</div>
												</div>
												<hr>
											@endif 
								     </div>
								</div>
								<div id="discipline" class="tab-pane">
								      <div class=" transparent">
												<div class="">
													<h4 class="widget-title smaller border-left-heading mb-3">
														Disciplinary Record
													</h4>
												</div>
								            
											<table class="table ">
												<thead>
						                            <tr> 
						                                <th>Griever ID</th>
						                                <th>Reason</th>
						                                <th>Action</th>
						                                <th>Requested Remedy</th>
						                                <th>Discussed Date</th>
						                                <th>Date of Execution</th> 
						                            </tr>
												</thead>
												<tbody>
												@if(!empty($records) && count($records) >0)
												@foreach($records as $record)
						                            <tr> 
							                            <td><a href="{{ url('hr/recruitment/employee/show/'. $record->dis_re_griever_id) }}" target="_blank">{{ $record->dis_re_griever_id }}</a></td>
							                            <td>{{ $record->hr_griv_issue_name }}</td>
							                            <td>{{ $record->hr_griv_steps_name }}</td>
							                            <td>{{ $record->dis_re_req_remedy }}</td>
							                            <td> 
							                            	{{ (!empty($record->dis_re_discussed_date)?(date("d-M-Y",strtotime($record->dis_re_discussed_date))):null) }}
							                            </td>
							                            <td> 
							                            	{{ (!empty($record->date_of_execution)?(date("d-M-Y",strtotime($record->date_of_execution))):null) }}
							                            </td> 
							                        </tr>
												@endforeach
												@else
					                                <tr><td colspan="6">No records found! </td></tr>      
					                            @endif  
												</tbody>
											</table>
										
									  </div>
								</div>
								@if(auth()->user()->canany(['Salary Sheet','Salary Report']) || auth()->user()->hasRole('Super Admin'))
								<div id="salary" class="tab-pane">
								    <div class=" transparent">
										
		                                <div class="">
			                                <h4 class="widget-title text-center">
												Generate Salary Report
											</h4>
										</div>

		                                 <div class="row mt-3" id="choice_1_div" name="choice_1_div">
						                    <div class="col-sm-2 no-padding">
						                       <input type="hidden" value="{{ $info->associate_id }}" id="as_id" name="as_id">
						                        
						                    </div>
						                    <div class="col-sm-3 no-padding">
						                    	<div class="form-group has-required has-float-label">
						                    		
							                        <input type="month" name="form-date" id="form-date" class="form-control" value="" />
							                        <label  for="month_number">Form</label>
						                    	</div>
						           
						                    </div>
						                    <div class="col-sm-3 no-padding">
						                    	<div class="form-group has-required has-float-label">
							                        <label  for="month_number">To</label>
							                        <input type="month" name="to-date" id="to-date" class="form-control" value="" />
						                        </div>   
						                    </div>
						                    <div class="col-sm-2">
						                        <button onclick="individual()" class="btn btn-primary choice_1_generate_btn" id="choice_1_generate_btn" name="choice_1_generate_btn" ></span> Generate</button>
						                    </div>

						                </div>
						                <div class="space-10"></div>
						                 <div class="progress progress-striped active pos-rel" id="progress_bar_main" data-percent="10%" style="display: none">
							                <div class="progress-bar" id="progress_bar" style="width:10%;"></div>
							            </div>
							            <div class="row text-center" id="loader"></div>
							            {{-- result of list --}}
							            <div class="panel no-border" id="salary-sheet-result" >
							                <div class="panel-heading salary_report_panel" id="salary-sheet-result-inner"><button title='Salary sheet result print' type="button" onClick="printMe('result-show')" class="btn btn-primary btn-xs text-right"><i class="fa fa-print"></i> Print</button></div>
							                <div class="panel-body" id="result-show">
							                	
                                                @include('hr.common.view_salary_sheet_list')

							                </div>
							            </div>
										                            
										
									</div>
								</div>
								@endif
							</div>
						</div>
  					</div>

  				</div>
  			</div>
      	</div>
	</div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
<script type="text/javascript">
var _token = $('input[name="_token"]').val();
function errorMsgRepeter(id, check, text){

        var flug1 = false;
        if(check == ''){
            $('#'+id).html('<label class="control-label status-label" for="inputError">* '+text+'<label>');
            flug1 = false;
        }else{
            $('#'+id).html('');
            flug1 = true;
        }
        return flug1;
    }

    $('#attload').on('click', function(){
	    $('#attcal').html('<center><i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:80px;margin-top:100px;"></i></center>');
		setTimeout(function(){ 
			$('#attcal').load('{{URL::to("hr/user/attendance_calendar/$info->associate_id")}}'); 
		}, 1000);
    });

	function individual() {
        var as_id       = $('input[name="as_id"]').val();
        var form_date   = $('input[name="form-date"]').val();
        var to_date     = $('input[name="to-date"]').val();
        var flug        = new Array();
        flug.push(errorMsgRepeter('error_ac_id_f', as_id, 'Employee not empty'));
        flug.push(errorMsgRepeter('error_form_date_f', form_date, 'From date not empty'));
        flug.push(errorMsgRepeter('error_to_date_f', to_date, 'To date not empty'));
        //console.log(as_id);
        if(jQuery.inArray(false, flug) === -1){
            $('.prepend').remove();
            $("#salary-sheet-result").show();
            $("#salary-sheet-result-inner").hide();
            $("#result-show").html('');

            $("#choice_2_generate_btn").attr('disabled','disabled');
            $('html, body').animate({
                scrollTop: $("#result-show").offset().top
            }, 2000);
            $("#result-show").html('<div class="loader-cycle"><img src="'+loaderPath+'" /></div>');

            setTimeout(() => {
                $.ajax({
                    url: url+'/hr/reports/salary-sheet-custom-individual-search',
                    type: "GET",
                    data: {
                      _token : _token,
                      as_id : as_id,
                      form_date : form_date,
                      to_date : to_date
                    },
                    success: function(response){
                        // console.log(response);
                        // remove all append message
                        $('.prepend').remove();
                        $("#salary-sheet-result-inner").show();
                        $("#result-show").html(response);
                        // remove grnerate button disabled attribute
                        $("#choice_2_generate_btn").removeAttr('disabled');
                    }, else: function() {
                        console.log('individual error occurred.');
                    }
                });
            }, 1000);
        }
    }


    function myPrintf() {
    	//$('#print-div').load('print');
        /*var myPrintContent = document.getElementById('print-div');
        var windowUrl = 'about:blank';
        var uniqueName = new Date();
        var windowName = 'Print' + uniqueName.getTime();
        var myPrintWindow = window.open(windowUrl, windowName, 'width=800,height=800');
        myPrintWindow.document.write(myPrintContent.innerHTML);
        myPrintWindow.document.getElementById('print-div').style.display='block';
        myPrintWindow.document.close();
        myPrintWindow.focus();
        myPrintWindow.print();
        myPrintWindow.close();    
        return false;*/


        /*$.ajax({
            type:"POST",
            cache:false,
            url:"print",
	        success: function(response) {
	        	alert(response);
	  
	        }
        });*/
    }

    //date-validation
    $(document).ready(function(){
    	//Dates entry alerts....
            $('#form-date').on('dp.change',function(){
                $('#to-date').val($('#form-date').val());    
            });

            $('#to-date').on('dp.change', function(){
                var to_date = new Date($(this).val());
                var from_date  = new Date($('#form-date').val());
                if(from_date == '' || from_date == null){
                    alert("Please enter From-Month-Year first");
                    $('#to-date').val('');
                }
                else{
                    if(to_date < from_date){
                        alert("Invalid!!\n From-Month-Year is latest than To-Month-Year");
                        $('#to-date').val('');
                    }
                }
            });
    });
</script>  
@endpush
     
@endsection