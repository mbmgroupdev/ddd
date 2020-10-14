@extends('hr.layout')
@section('title', 'Benefit Info-'.$info->associate_id)
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
                    <a href="#">Employee</a>
                </li>
                <li>
                    <a href="#">{{$info->associate_id}}</a>
                </li>
                <li class="active">Benefit Info</li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        @include('inc/message')
        <div class="panel"> 
            <div class="panel-body">
	        		{{-- <a  href={{url("hr/payroll/benefit_edit/$info->associate_id")}} target="_blank" class="btn btn-xs btn-warning pull-right" title="Edit Benefits" style="border-radius: 2px;"><i class="fa fa-edit bigger-150"></i></a> --}}
	        	 
                <div class="row">
                    <div class="col-sm-6">
                    	<div class="user-details-block mb-3 mt-custom-4">
	                        <div class="user-profile text-center mt-0">
	                            <img id="off_avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($info) }} " >
	                        </div>
	                        <div class="text-center mt-3">
	                         <h4><b >{{ !empty($info->associate_id)?$info->associate_id:null }}</b></h4>
	                         <p class="mb-0" >
	                            {{ !empty($info->as_name)?$info->as_name:null }} <b></b></p>
	                         <p class="mb-0" >
	                            {{ !empty($info->hr_designation_name)?$info->hr_designation_name:null }}, <b>{{ !empty($info->hr_department_name)?$info->hr_department_name:null }}</b></p>
	                         <p class="mb-0" >
	                         </p>
	                         <p  class="mb-0">Unit: <span id="off_department" class="text-success">{{ !empty($info->hr_unit_name)?$info->hr_unit_name:null }}</span> </p>
	                         
	                         </div>
	                    </div>
	                </div>

                    <div class="col-sm-6">
						<h3 class="mb-3 border-left-heading">Benefits</h3>

                        <div class="info-body">
                            <div class="widget-main no-padding">
                                <table class="table table-borderd">
                                    <thead>
                                    <tr>
                                        <th style="padding:4px">Type</th>
                                        <th style="padding:4px">Amount</th>
                                    </tr>   
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="padding:4px">Gross Salary</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_joining_salary)?$benefit->ben_joining_salary:0) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px">Current Salary</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_current_salary)?$benefit->ben_current_salary:0) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px">Basic Salary</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_basic)?$benefit->ben_basic:0) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px">House Rent</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_house_rent)?$benefit->ben_house_rent:0) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px">Medical</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_medical)?$benefit->ben_medical:0) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px">Transportation</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_transport)?$benefit->ben_transport:0) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px">Food</td>
                                        <td style="padding:4px">{{ (!empty($benefit->ben_food)?$benefit->ben_food:0) }}</td>
                                    </tr>
                                    </tbody> 
                                </table>
                            </div>
	                    </div> 
                    </div>
                </div>


                <div class="row"> 
                    <div class="col-sm-6">
						<h3 class="mb-3 border-left-heading">Promotion History</h3>

                        <div class="info-body">
                            <div class="widget-main no-padding">
                                <table class="table table-borderd">
                                    <thead>
	                                    <tr>
	                                        <th style="padding:4px">Current Designation</th>
	                                        <th style="padding:4px">Previous Designation</th>
	                                        <th style="padding:4px">Eligible Date</th>
	                                        <th style="padding:4px">Effective Date</th>
	                                    </tr>   
                                    </thead>	 
                                    <tbody>
                                    @if(count($promotions ) >0) 
                                    	@foreach($promotions as $promotion)
	                                    <tr>
	                                        <td style="padding:4px">{{ $promotion->current_designation }}</td>
	                                        <td style="padding:4px">{{ $promotion->previous_designation }}</td>
	                                        <td style="padding:4px">{{ $promotion->eligible_date }}</td>
	                                        <td style="padding:4px">{{ $promotion->effective_date }}</td>
	                                    </tr> 
	                                    @endforeach
	                                @else
	                                <tr><td colspan="4">No history</td></tr>
	                                @endif
                                    </tbody> 
                                </table>
                            </div>
                        </div>
                    </div>
                     
                    <div class="col-sm-6">
						<h3 class="mb-3 border-left-heading">Increment History</h3>

                        <div class="info-body">
                            <table class="table table-borderd" >
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
                                @if(count($increments ) >0)
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
                                        <td style="padding:4px">
                                        <?php 
                                        if($increment->amount_type==1)    
                                        	 echo $increment->increment_amount;
                                        else {
                                        	if(!empty($benefit->ben_basic)){
                                        	echo ($benefit->ben_basic/100)*$increment->increment_amount;}
                                        	echo " (".$increment->increment_amount. "%)";} ?>
                                        </td>
                                        <td style="padding:4px">{{ $increment->eligible_date }}</td>
                                        <td style="padding:4px">{{ $increment->effective_date }}</td>
                                    </tr> 
                                    @endforeach
                                @else
                                <tr><td colspan="4">No history</td></tr>
                                @endif
                                </tbody> 
                            </table>
	                    </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection




