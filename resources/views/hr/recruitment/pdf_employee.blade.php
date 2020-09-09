<style type="text/css">
body {
    font-family: 'SolaimanLipi', sans-serif;
}
p.break-page { page-break-after: always; }
@page {
	header: page-header;
	footer: page-footer;
}
@media print {
	body {
    font-family: 'SolaimanLipi', sans-serif;
}
.profile-user-info {
    display: table;
    width: 100%;
    margin: 0;
}
.profile-info-row {
    display: block;
    width: 100%;
}
.profile-info-name {
    text-align: left;
    width: 35%;
    padding: 6px 10px 6px 4px;
    font-weight: 400;
    color: #667E99;
    background-color: transparent;
    vertical-align: middle;
    float: left;
}
.profile-info-value {
    padding: 6px 4px 6px 6px;
    width: 45%;
    float:right;

}
.profile-info-name, .profile-info-value {
    display: table-cell;
    border-top: 1px dotted #D5E4F1;
}

    p.break-page { page-break-after: always; }
	page[size="A4"] {  
	  width: 21cm;
	  height: 29.7cm; 
	}
	.col-sm-6{
		width:49%;
		float: left;
	}
	.odd{margin-right: 1%}
	.even{margin-left: 1%}
	.col-sm-12{
		width: 100%;
	}
	.row{
		display: block;
		width:100%;
	}


table{
  border-collapse: collapse;
  width: 100%;
  font-family: 'SolaimanLipi', sans-serif;
}

table td, table th {
  border: 1px solid #ffebbb;
  padding: 6px;
  text-align: center;
}

table tr:nth-child(even){background-color: #f2f2f2;}



table th {
  padding-top: 8px;
  padding-bottom: 8px;
  background-color: #ffebbb;
  color: #393939;
}
}
</style>
<!-- page 1 -->
<!-- <htmlpageheader name="page-header">
	Employee Profile, MBM Group 
</htmlpageheader>

<htmlpagefooter name="page-footer">
	Your Footer Content
</htmlpagefooter> -->
<div style="text-align: center;">
    <br><br> <br><br><br> <br>
	<h1 > {{ $info->hr_unit_name }}</h1>
	<br><br> <br><br><br> <br><br><br><br> <br><br><br> <br><br>
	<h3>{{ $info->as_name}}</h3>
	<span>Associate Id: {{ $info->associate_id }}</p>
	<span >{{ $info->hr_designation_name }}, {{ $info->hr_department_name }}</span>
	<p style="text-align: center;"></p>
	
</div>

<p class="break-page"></p>

<h3 style="margin:0;">Basic Information</h3>
<hr>
<div class="row">
    <div class="col-sm-6 odd">
		<div class="profile-user-info">
		        <div class="profile-info-row">
					<div style="" class="profile-info-name"> Name </div>
					<div class="profile-info-value">
						<span> {{ $info->as_name}} </span>
					</div>
				</div>
				<div class="profile-info-row">
					<div style="" class="profile-info-name"> Associate Id </div>
					<div class="profile-info-value">
						<span> {{ $info->associate_id }} </span>
					</div>
				</div>
				
				<div class="profile-info-row">
					<div style="" class="profile-info-name"> Designation </div>
					<div class="profile-info-value">
						<span> {{ $info->hr_designation_name }} </span>
					</div>
				</div>
				<div class="profile-info-row">
					<div style="" class="profile-info-name"> Department </div>
					<div class="profile-info-value">
						<span> {{ $info->hr_department_name }} </span>
					</div>
				</div>
				<div class="profile-info-row">
					<div style="" class="profile-info-name"> Unit </div>
					<div class="profile-info-value">
						<span> {{ $info->hr_unit_name }} </span>
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
				<div class="profile-info-row">
					<div class="profile-info-name"> Nationality </div>
					<div class="profile-info-value">
						<span> {{ $info->emp_adv_info_nationality }} </span>
					</div>
				</div>

				<div class="profile-info-row">
					<div class="profile-info-name"> Contact </div>
					<div class="profile-info-value">
						<span> {{ $info->as_contact }} </span>
					</div>
				</div>



				


				
	    </div>
    </div>
    <div class="col-sm-6 even">
		<div class="profile-user-info">

			<div class="profile-info-row">
				<div class="profile-info-name"> Employee Type </div>
				<div class="profile-info-value">
					<span> {{ $info->hr_emp_type_name }} </span>
				</div>
			</div>
			<div class="profile-info-row">
					<div class="profile-info-name"> Status</div>
					<div class="profile-info-value">
						<span>@if($info->as_status == 1) Active @else Inactive @endif </span>
					</div>
			</div>
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
					<div class="profile-info-name"> OT Status </div>
					<div class="profile-info-value">
						<span> @if($info->as_ot == 0) Non OT @else OT @endif </span>
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
					<span> {{ $info->as_shift_id }} </span>
				</div>
			</div>
			
		</div>		           
	</div>
</div>
<br><br>
<h3 style="margin:0;">Advance Information</h3>
<hr>
<div class="row">
		<div class="col-sm-6 odd">

			<div class="profile-user-info">

				

				
				

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
					    
						<span> {{ $info->emp_adv_info_per_vill }} {{ $info->emp_adv_info_per_po }} {{ $info->emp_adv_info_per_upz }} {{ $info->permanent_district }} </span>
					</div>
				</div>

				<div class="profile-info-row">
					<div class="profile-info-name">Present Address</div>
					<div class="profile-info-value">
				
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

		<div class="col-sm-6 even">
			<div class="profile-user-info">
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
					<div class="profile-info-name">TIN/ETIN</div>
					<div class="profile-info-value">
						<span> {{ $info->emp_adv_info_tin }} </span>
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
	<p class="break-page"></p>
	<div class="row">

		<div class="col-sm-6 odd">
		    <h4 style="margin:0;">
				Education History
			</h4>
			<hr>
			<div class="profile-user-info">
			@if(!empty($educations) && count($educations) >0)
			<table border="1">  
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
			No record found!
			@endif 
			</div>
		</div>

		<div class="col-sm-6 even">
			<h4 style="margin:0;">
				Medical Information
			</h4>
			<hr>
		
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

				

				

				
			</div>
					
		</div>
	</div>
	<br><br>
	<div class="row">
		<h4 style="margin:0;">
			Benefits
		</h4>
		<hr>
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




<p class="break-page"></p>

@if(!empty($promotions) && count($promotions) >0)
<div class="row">
<h4 style="margin:0;">
	Promotion History
</h4>
<hr>
<div class="profile-user-info">
    <table border="1">
        <thead>
            <tr>
                <th style="padding:4px">Current Designation</th>
                <th style="padding:4px">Previous Designation</th>
                <th style="padding:4px">Eligible Date</th>
                <th style="padding:4px">Effective Date</th>
            </tr>   
        </thead>	 
        <tbody> 
        	@foreach($promotions as $promotion)
            <tr>
                <td style="padding:4px">{{ $promotion->current_designation }}</td>
                <td style="padding:4px">{{ $promotion->previous_designation }}</td>
                <td style="padding:4px">{{ date("d M, Y",strtotime($promotion->eligible_date)) }}</td>
                <td style="padding:4px">{{ date("d M, Y",strtotime($promotion->effective_date)) }}</td>
            </tr> 
            @endforeach
        </tbody> 
    </table>
</div>
</div>
<br>
<br>
@endif

@if(!empty($increments) && count($increments) >0)
<div class="row">
<h4 style="margin:0;">
	Increment History
</h4>
<hr>
<div class="profile-user-info">
        <table border="1">
            <thead>
                <tr>
                    <th style="padding:4px">Current Salary</th>
                    <th style="padding:4px">Previous Salary</th>
                    <th style="padding:4px">Increment Amount</th>
                    <th style="padding:4px">Eligible Date</th>
                    <th style="padding:4px">Effective Date</th>
                </tr>   
            </thead>	 
            <tbody> 
            	@foreach($increments as $increment)
                <tr>
                    <td style="padding:4px">
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
                    <td style="padding:4px">
                	{{ $increment->current_salary }}
                    </td>
                    <td style="padding:4px"><?php if($increment->amount_type==1) echo $increment->increment_amount; else echo $increment->increment_amount. " %"; ?></td>
                    <td style="padding:4px">{{ date("d M, Y",strtotime($increment->eligible_date)) }}</td>
                    <td style="padding:4px">{{ date("d M, Y",strtotime($increment->effective_date)) }}</td>
                </tr> 
                @endforeach
            </tbody> 
        </table>
    </div>
</div> 

<br><br>
@endif
<div class="row">
	<h4 style="margin:0;">
		Salary History
	</h4>
	<hr>
	<div class="profile-user-info">
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
						<th style="padding:4px" >Leave Type</th>
						<th style="padding:4px" >Total</th>
						<th style="padding:4px" >Taken</th>
						<th style="padding:4px" >Due</th>
					</tr>	
					</thead>
					<tbody>
					<tr>
						<th style="padding:4px" >Casual</th>
						<td style="padding:4px" >10</td>
						<td style="padding:4px" >{{ (!empty($leave->casual)?$leave->casual:0) }}</td>
						<td style="padding:4px" >{{ (10-$leave->casual) }}</td>
					</tr>
					<tr>
						<th style="padding:4px" >Earned</th>
						<td style="padding:4px" > {{$earnedLeaves[$leave->year]['earned']}} </td>
						<td style="padding:4px" > {{$earnedLeaves[$leave->year]['enjoyed']}} </td>
						<td style="padding:4px" > {{$earnedLeaves[$leave->year]['remain']}} </td>
					</tr>
					<tr>
						<th style="padding:4px" >Sick</th>
						<td style="padding:4px" >14</td>
						<td style="padding:4px" >{{ (!empty($leave->sick)?$leave->sick:0) }}</td>
						<td style="padding:4px" >{{ (14-$leave->sick) }}</td>
					</tr>
					<?php 
						$display='';
						if($info->as_gender =='Male')
						{$display='display:none;';} 
					?>
					<tr style="{{$display}}">
						<th style="padding:4px" >Maternity v</th>
						<td style="padding:4px" >112</td>
						{{-- <td style="padding:4px" >{{ (!empty($leave->maternity)?$leave->maternity:0) }}</td> --}}
						<td style="padding:4px" >{{ (!empty($leave->maternity)?112:0) }}</td>
						{{-- <td style="padding:4px" >{{ (112-$leave->maternity) }}</td> --}}
						<td style="padding:4px" >{{ (!empty($leave->maternity)?0:112) }}</td>
					</tr>
					</tbody>
					@if($info->as_gender=='Male')
					<tfoot>
						<tr>
							<th style="padding:4px;font-weight:bold;" >Subtotal</th>
							<td style="padding:4px" >{{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned'])-112 }}</td>
							{{-- <td style="padding:4px;"> {{(!empty($leave->maternity)?$leave->maternity:0)+(!empty($leave->sick)?$leave->sick:0)+($earnedLeaves[$leave->year]['enjoyed']) + (!empty($leave->casual)?$leave->casual:0)}}</td> --}}
							<td style="padding:4px;"> {{(!empty($leave->maternity)?112:0)+(!empty($leave->sick)?$leave->sick:0)+($earnedLeaves[$leave->year]['enjoyed']) + (!empty($leave->casual)?$leave->casual:0)}}</td>
							{{-- <td style="padding:4px" >{{ (10-$leave->casual)+($earnedLeaves[$leave->year]['remain'])+(14-$leave->sick)+(112-$leave->maternity) }}</td> --}}
							<td style="padding:4px" >{{ (10-$leave->casual)+($earnedLeaves[$leave->year]['remain'])+(14-$leave->sick)+(empty($leave->maternity)?112:0) }}</td>
						</tr>
						<tr>
							<th style="padding:4px;font-weight:bold;" >Total Leave</th>
							<td colspan="3"  ><center><b> {{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned'])-112 }} </b></center></td>
						</tr>
					</tfoot>
					@else
					<tfoot>
						<tr>
							<th style="padding:4px;font-weight:bold;" >Subtotal</th>
							<td style="padding:4px" >{{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned']) }}</td>
							{{-- <td style="padding:4px;"> {{(!empty($leave->maternity)?$leave->maternity:0)+(!empty($leave->sick)?$leave->sick:0)+($earnedLeaves[$leave->year]['enjoyed']) + (!empty($leave->casual)?$leave->casual:0)}}</td> --}}
							<td style="padding:4px;"> {{(!empty($leave->maternity)?112:0)+(!empty($leave->sick)?$leave->sick:0)+($earnedLeaves[$leave->year]['enjoyed']) + (!empty($leave->casual)?$leave->casual:0)}}</td>
							{{-- <td style="padding:4px" >{{ (10-$leave->casual)+($earnedLeaves[$leave->year]['remain'])+(14-$leave->sick)+(112-$leave->maternity) }}</td> --}}
							<td style="padding:4px" >{{ (10-$leave->casual)+($earnedLeaves[$leave->year]['remain'])+(14-$leave->sick)+(empty($leave->maternity)?112:0) }}</td>
						</tr>
						<tr>
							<th style="padding:4px;font-weight:bold;" >Total Leave</th>
							<td colspan="3"  ><center><b> {{ (14)+(10)+(112)+($earnedLeaves[$leave->year]['earned']) }} </b></center></td>
						</tr>
					</tfoot>
					@endif
				</table>
			</div>
		</div>
		<hr>
		@endforeach 
	@else 
	    <div class="row">
			<div class="col-xs-2">
				<strong>{{ date('Y') }}</strong>
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
						<td >14</td>
						<td >0</td>
						<td >14</td>
					</tr>
					<tr>
						<th >Earned</th>
						<td >{{$earnedLeaves[date('Y')]['remain']}}</td>
						<td >0</td>
						<td >{{$earnedLeaves[date('Y')]['remain']}}</td>
					</tr>
					<tr>
						<th >Sick</th>
						<td >10</td>
						<td >0</td>
						<td >10</td>
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
						<tr>
							<th >Total Leave</th>
							<td colspan="3" ><center><b>{{(136+$earnedLeaves[date('Y')]['remain']-112)}} </b></center></td>
						</tr>
					</tfoot>
					@else
					<tfoot>
						<tr>
							<th >Subtotal</th>
							<td >{{(136+$earnedLeaves[date('Y')]['remain'])}}</td>
							<td >0</td>
							<td >{{(136+$earnedLeaves[date('Y')]['remain'])}}</td>
						</tr>
						<tr>
							<th >Total Leave</th>
							<td colspan="3" ><center><b>{{(136+$earnedLeaves[date('Y')]['remain'])}} </b></center></td>
						</tr>
					</tfoot>
					@endif
				</table>
			</div>
		</div>
		<hr>
	@endif 
	</div>
</div>

<br><br>




@if(count($getSalaryList) >0 )
<div class="row">
	<h4 style="margin:0;">
		Salary History
	</h4>
	<hr>
	<div class="profile-user-info">

        <table class="table" autosize="1" style="overflow: no-wrap" >
            <thead>
                <tr style="color:hotpink">
                    <th>SL</th>
                    <th>Monthly Salary</th>
                    <th>Attendence</th>
                    <th>Deduction</th>
                    <th>Total Payable</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
              

                @php
                    $i = 0;
                @endphp
                @foreach($getSalaryList as $list)
                @php
                // get total hour with minutes calculation
                if (strpos($list->ot_hour, ':') !== false) {
                    list($hour,$minutes) = array_pad(explode(':',$list->ot_hour),2,NULL);
                    $minuteHour = 0;
                    if($minutes!==NULL) {
                        $minuteHour = number_format((float)($minutes/60), 3, '.', '');;
                        $list->ot_hour = $hour + $minuteHour;
                    }
                }
                // get designation
                if(isset($list->employee->as_designation_id)){
                    $designation = App\Models\Hr\Designation::where('hr_designation_id', $list->employee->as_designation_id)->first();
                } else {
                    $designation = new stdClass();
                }
                @endphp
                <?php //dump($list->ot_overtime_minutes);?>
                <tr>
                    <td>{{ ++$i }} </td>
                    <td style="text-align:left;">
                        <p style="margin:0;padding:0;">{{ date("F", mktime(0, 0, 0, $list->month, 1)) }}, {{$list->year}}</p>
                        <p style="margin:0;padding:0;"></p>
                        <p style="margin:0;padding:0;color:hotpink">Basic+House Rent+Medical+Transport+Food </p>
                        <p style="margin:0;padding:0;">
                            {{ $list->basic.'+'.$list->house.'+'.$list->medical.'+'.$list->transport.'+'.$list->food }} 
                        </p>
                        <p>=<font style="color:hotpink">{{ $list->gross }} </font></p>
                    </td>

                    <td style="text-align:left;" nowrap="nowrap">
                        <p style="margin:0;padding:0">
                           Attendence=<font style="color:hotpink;" > {{ $list->present}}</font>
                             
                        </p>
                        <p> Late Arrival = <font style="color:hotpink"> {{ $list->late_count }} </font> </p>
                        <p style="margin:0;padding:0">                            
                          Holiday =
                                <font style="color:hotpink"> {{$list->holiday}}</font>
                          
                        </p>
                        <p style="margin:0;padding:0;">
                        Absent=<font style="color:hotpink"> {{ $list->absent}}</font>
                            
                        </p>
                        <p style="margin:0;padding:0">
                        Leave =<font style="color:hotpink"> {{ $list->leave }}</font>
                            
                       
                        </p>
                        <p style="margin:0;padding:0">

                            Total Payable=<font style="color:hotpink"> {{ ($list->present + $list->holiday + $list->leave)}}</font>
                        
                        </p>
                    </td>
                    <td style="text-align:left;" nowrap="nowrap">
                        <p style="margin:0;padding:0">
                           
                            Absent Deduction = <font style="color:hotpink">{{  $list->absent_deduct }}</font>
                            
                        </p>
                        <p style="margin:0;padding:0">

                          Half Day Deduction =
                                <font style="color:hotpink">{{$list->half_day_deduct }}</font>
                        </p>
                        <p style="margin:0;padding:0">

                            Advance Deduction=<font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->advp_deduct }} </font>
                            </span>



                          

                        </p>
                        <p style="margin:0;padding:0">

                           Stamp=
                                <font style="color:hotpink"> 10.00</font>
                          
                        </p>
                        <p style="margin:0;padding:0">

                          Consumer  =
                           <font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : (isset($list->add_deduct['cg_product'])?$list->add_deduct['cg_product']:'') }}</font>
                           
                        </p>
                        <p style="margin:0;padding:0">
                           Food Deduction=
                            <font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->food_deduct }} </font>
                            
                        </p>
                        <p style="margin:0;padding:0">
                            Others =<font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->others_deduct }} </font>
                            

                        </p>
                    </td>
                    <td style="text-align:left;" nowrap="nowrap">
                        <p style="margin:0;padding:0">
                           
                             Salary=
                                    <font style="color:hotpink">{{ $list->salary_payable}}</font>
                             
                        </p>
                        <p style="margin:0;padding:0">
                      
                            Over Time =
                                <font style="color:hotpink">{{ ($list->ot_rate * $list->ot_hour)}}</font>
                               
                        </p>
                        <p style="margin:0;padding:0">
                         

                               Over Time Rate =
                                    <font style="color:hotpink">{{ $list->ot_rate }} </font>
                                
                                    <font style="color:hotpink"> ({{ $list->employee->as_ot==1?$list->ot_hour:'00' }}  Hour)</font>
                                    

                         
                        </p>
                        <p style="margin:0;padding:0">
                           
                            Attendence Bonus =
                                    <font style="color:hotpink">{{$list->attendance_bonus }}</font>
                                


                        </p>
                        <p style="margin:0;padding:0">

                            Advance Salary=
                            
                                <font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add }}</font>
                         


                        </p>
                    </td>
                    <td >
                        @php
                            $ot = ($list->ot_rate * $list->ot_hour);
                            $salaryAdd = ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add;
                            $total = ($list->salary_payable + $ot + $list->attendance_bonus + $salaryAdd);
                        @endphp
                        {{ $total }}



                    </td>
                </tr>
              @endforeach
            </tbody>

        </table>




	</div>
</div>

<br>
<br>

@endif

@if(!empty($loans) && count($loans)>0)
<div class="row">
	<h4 style="margin:0;">
		Loan History
	</h4>
	<hr>
	<div class="profile-user-info">
        
		<table class="table table-borderd table-compact">
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
			@foreach($loans as $loan)
                <tr>
                    <td>{{ $loan->hr_la_type_of_loan }}</td>
                    <td>{{ $loan->hr_la_approved_amount }}</td>
                    <td>0.00</td>
                    <td>
                    	@if($loan->hr_la_updated_at !=null) 	{{ date("d M, Y",strtotime($loan->hr_la_updated_at)) }}
                    	@endif
                	</td>
                    <td>{{ $loan->hr_la_status }}</td>
                </tr>
			@endforeach 
			</tbody>
		</table>
		 
	</div>
</div>



<br><br>
@endif

@if(!empty($records) && count($records)>0)
<div class="row">
	<h4 style="margin:0;">
		Disciplinary Record 
	</h4>
	<hr>
	<div class="profile-user-info">
		<table border="1">
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
			@foreach($records as $record)
                <tr> 
                    <td>{{ $record->dis_re_griever_id }}</td>
                    <td>{{ $record->hr_griv_issue_name }}</td>
                    <td>{{ $record->hr_griv_steps_name }}</td>
                    <td>{{ $record->dis_re_req_remedy }}</td>
                    <td> 
                    	{{ (!empty($record->dis_re_discussed_date)?(date("d M, Y",strtotime($record->dis_re_discussed_date))):null) }}
                    </td>
                    <td> 
                    	{{ (!empty($record->date_of_execution)?(date("d M, Y",strtotime($record->date_of_execution))):null) }}
                    </td> 
                </tr>
			@endforeach 
			</tbody>
		</table>
		
	</div>
</div>

@endif

