
<style media='print'>
	@import url(https://fonts.googleapis.com/css?family=Poppins:200,200i,300,400,500,600,700,800,900&amp;display=swap);
	.page-break{page-break-after: always;}
	body {
	    font-family: Poppins,sans-serif;
	}
</style>
@if (count($employees)>0)

    @php $page = array_chunk($employees->toArray(), 3); @endphp
    @foreach ($page as $key => $emps) 
        @foreach ($emps as $associate)
            <div style="border-style: solid;width:900px;margin: 30px auto;line-height: 1.2;">
	        	<div style="display: flex;justify-content: space-between;">
	        		
	            	<div style="width: 650px;padding: 30px 10px;">
		        		<p style="text-align:center; font-size:54px; font-weight:700; margin:0px; padding:0px;"> {{ strtoupper($associate->as_name) }} </p>
	            		
		                <p style="text-align:center; font-weight:600; font-size:48px; margin:0px; padding:0px;">

		                            {!! (!empty($associate->associate_id)?
		                            (substr_replace($associate->associate_id, "<big style='font-size:72px; font-weight:700'>$associate->temp_id</big>", 3, 6)):
		                            null) !!}
		                            <span style='font-size:30px'>({{ $associate->as_oracle_code }}) </span><br></p>
		                <p style="text-align:center; font-size:32px; font-weight:700; margin:0px; padding:0px;">{{ strtoupper($designation[$associate->as_designation_id]['hr_designation_name']) }} </p>
		                <p style="text-align:center; font-size:32px; font-weight:700; margin:0px; padding:0px;">Section: {{ strtoupper($section[$associate->as_section_id]['hr_section_name']) }}</p>
		                <p style="text-align:center; font-size:40px; font-weight:700; margin:0px; padding:0px;"> {{ date('d-M-Y',strtotime($associate->as_doj)) }} </p>
		            </div>
		            <div style="width: 250px;padding-right: 10px">
		            	{{-- id card view for worker --}}
		            	@if($associate->as_emp_type_id == 3)
		            	<table border="0" style="float:right;margin: 20px 10px;width: 220px;height: 310px;background:white;border:1px solid #333;margin-right: 0;font-family: sans-serif;">
				            <tr>
				                <td style="padding-left: 5px;">
				                    <span style="width:135px;display:block;line-height:16px;font-size:12px;font-weight:700">{{$unit[$associate->as_unit_id]['hr_unit_name_bn']}}</span>
				                    
				                </td>
				                <td style="text-align: right;padding-right:5px;">
				                	@if($unit[$associate->as_unit_id]['hr_unit_logo'])
				                	<img style="width:55px;height:28px;margin-left: auto;" src="{{url($unit[$associate->as_unit_id]['hr_unit_logo'])}}" alt="Logo">
				                	@endif
				                </td>
				            </tr>
				            <tr>
				                <td colspan="2" style="text-align: center;">
				                    <img style="margin:0px auto;width:75px;height:75px;display:block" src="{{url(emp_profile_picture($associate))}}" >
				                </td>

				            </tr>
				            <tr>
				                <td colspan="2" style="text-align: center;">
				                    <strong style="display:block;font-size:10px;font-weight:700"> {{($associate->hr_bn_associate_name?$associate->hr_bn_associate_name:null)}}</strong>
				                    <strong style="display:block;font-size:9px">পদবীঃ {{$designation[$associate->as_designation_id]?$designation[$associate->as_designation_id]['hr_designation_name_bn']:null}}</strong>
				                    <strong style="display:block;font-size:9px;">সেকশনঃ {{($section[$associate->as_section_id]?$section[$associate->as_section_id]['hr_section_name_bn']:null)}}</strong>
				                    <strong style="display:block;font-size:9px;">বিভাগ {{($department[$associate->as_department_id]?$department[$associate->as_department_id]['hr_department_name_bn']:null)}}</strong>
				                    <strong style="display:block;font-size:9px">যোগদানের তারিখ: 
				                        {{eng_to_bn((date("d M, Y", strtotime($associate->as_doj))))}} ইং

				                    </strong>
				                    <strong style="display:block;font-size:9px;">পূর্বের আইডিঃ {{($associate->as_oracle_code?$associate->as_oracle_code:null)}}</strong>
				                </td>
				            </tr>
				            <tr>
				                <td colspan="2" style="padding-left: 5px;text-align: center;">
				                    <strong style="display:block;font-size:12px">
				                        @php
				                            $strId = (!empty($associate->associate_id)?
				                        (substr_replace($associate->associate_id, "<big style='font-size:18px'>".$associate->temp_id."</big>", 3, 6)):
				                        '');
				                        @endphp
				                        আইডিঃ {!!$strId!!}
				                    </strong>
				                </td>
				            </tr>
				            <tr>
				                <td style="text-align: left;padding-left: 5px;padding-top: 25px;">
				                    <strong style="font-size:9px;;">শ্রমিকের স্বাক্ষর</strong>
				                </td>
				                <td style="text-align: center;padding-right:5px;position: relative;">
				                    @if($unit[$associate->as_unit_id]['hr_unit_authorized_signature'])
				                    <img style="height: 30px;margin-top: -14px;position: absolute;right: 5px;" src="{{asset($unit[$associate->as_unit_id]['hr_unit_authorized_signature'])}}">
				                    @else
				                    <img style="height: 30px;margin-top: -8px;margin-left: auto;" src=""></img>
				                    @endif
				                    <br>
				                    <strong style="font-size:9px;position: absolute;right: 0;width: 100px;">
				                    মালিক/ব্যবস্থাপক</strong>
				                </td>
				            </tr>
				        </table>

		            	@else
		            	{{-- id card view for management/staff --}}
		            	<table border="0" style="float:right;margin: 20px 10px;width: 220px;height: 310px;background:white;border:1px solid #333;margin-right: 0;">
				            <tr>
				                <td style="padding-left: 5px;">
				                    <span style="width:100px;display:block;line-height:16px;font-size:12px;font-weight:700">{{$unit[$associate->as_unit_id]['hr_unit_name']}}</span>
				                    
				                </td>
				                <td style="text-align: right;padding-right:5px;">
				                	@if($unit[$associate->as_unit_id]['hr_unit_logo'])
				                	<img style="width:55px;height:28px;margin-left: auto;" src="{{url($unit[$associate->as_unit_id]['hr_unit_logo'])}}" alt="Logo">
				                	@endif
				                </td>
				            </tr>
				            <tr>
				                <td colspan="2" style="text-align: center;">
				                    <img style="margin:0px auto;width:75px;height:75px;display:block" src="{{url(emp_profile_picture($associate))}}" >
				                </td>

				            </tr>
				            <tr>
				                <td colspan="2" style="text-align: center;">
				                    <strong style="display:block;font-size:11px;font-weight:700">{{$associate->as_name}}</strong>
				                    <span style="display:block;font-size:9px">{{$designation[$associate->as_designation_id]['hr_designation_name']}}</span>
				                    <strong style="display:block;font-size:9px;">Sec: {{$section[$associate->as_section_id]['hr_section_name']}}</strong>
				                    <strong style="display:block;font-size:9px;">Dept: {{$department[$associate->as_department_id]['hr_department_name']}}</strong>
				                    <span style="display:block;font-size:9px">DOJ: {{date("d-M-Y", strtotime($associate->as_doj))}}</span>
				                    <span style="display:block;font-size:9px">Previous ID: {{$associate->as_oracle_code}}</span>
				                </td>
				            </tr>
				            <tr>
				                <td colspan="2" style="padding-left: 5px;">
				                    <strong style="display:block;font-size:12px">
				                        @php
				                            $strId = (!empty($associate->associate_id)?
				                        (substr_replace($associate->associate_id, "<big style='font-size:18px'>".$associate->temp_id."</big>", 3, 6)):
				                        '');
				                        @endphp
				                        {!!$strId!!}
				                    </strong>
				                    <strong style="display:block;font-size: 10px;">Blood Group: {{$associate->med_blood_group??''}}</strong>
				                </td>
				            </tr>
				            <tr>
				                <td style="text-align: left;padding-left: 5px;padding-top: 20px;">
				                    <strong style="font-size:9px;;">Signature</strong>
				                </td>
				                <td style="text-align: center;padding-right:5px;padding-top: 20px;">
				                    
				                    <strong style="font-size:9px">
				                        Authority</strong>
				                </td>
				            </tr>
				        </table>
				        @endif
	            	</div>
	        	</div>
                
            </div>
        @endforeach
        <div class='page-break'></div>
    @endforeach
@else
	<div class="alert alert-danger">No File Tag Found!</div>';
@endif

{{-- $data['printbutton'] = "";
if (strlen($data['filetag'])>1)
{
    $data['printbutton'] .= "<button onclick="printContent('idCardPrint')\" type=\"button\" class=\"btn btn-success btn-xs\"><i class=\"fa fa-print\" title=\"Print\"></i></button>";
} --}}