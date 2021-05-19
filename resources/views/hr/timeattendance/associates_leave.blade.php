<style>.text-bold{font-weight:bold;font-size:14px;}</style>
@php $unit = unit_by_id() @endphp
{{--  --}}
<div class="col-sm-5">
    <div class="user-details-block benefit-employee">
        <div class="user-profile text-center mt-0">
            <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($info) }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
        </div>
        <div class="text-center mt-3">
            <h4><b id="user-name">{{$info->as_name}} </b></h4>
            <p class="mb-0" id="designation">
                Associate ID: {{$info->associate_id}} </p>
            <p class="mb-0" >
                Oracle ID: {{$info->as_oracle_code}} </p>
            <p class="mb-0" >
                  Unit: {{$unit[$info->as_unit_id]['hr_unit_name']}} </p>
            <p class="mb-0" >
                  Date of Join: {{$info->as_doj->format('d-m-Y')}} </p>
          </div>
    </div>
<center>
    <div style="margin-top: 20px">
        <button class="btn btn-sm btn-success"onClick="printDiv1()">Print</button>
    </div>
</center>
</div>

@php
        $member_join_year = $info->as_doj->format('Y');
        $member_join_month = $info->as_doj->format('m');
        $this_year = \Carbon\Carbon::now()->year;
@endphp

<div class="col-sm-7">
    <ul class="speciality-list m-0 p-0">
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-calendar-day"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Casual Leave</h6>
              <p class="mb-0">
                <span class="text-danger">Total:</span>  <span class="text-bold" id="total_earn_leave">{{$member_join_year == $this_year ? ceil((10/12)*(12-$member_join_month)) : '10'}}</span>
                <span class="text-danger">Enjoyed:</span> <span class="text-bold" id="enjoyed_earn_leave">{{ (!empty($leaves->casual)?$leaves->casual:0) }}</span >
                <span class="text-danger">Remained:</span> <span class="text-bold" id="remained_earn_leave">{{ $member_join_year == $this_year ? ceil((10/12)*(12-$member_join_month))-$leaves->casual : (10-$leaves->casual) }}</span></p>
           </div>
        </li>
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-stethoscope"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Sick Leave</h6>
              <p class="mb-0">
                <span class="text-danger">Total: </span>  <span class="text-bold" id="total_earn_leave">{{$member_join_year == $this_year ? ceil((14/12)*(12-$member_join_month)) : '14'}}</span class="text-danger">
                <span class="text-danger">Enjoyed: </span> <span class="text-bold" id="enjoyed_earn_leave">{{ (!empty($leaves->sick)?$leaves->sick:0) }}</span >
                <span class="text-danger">Remained: </span> <span class="text-bold" id="remained_earn_leave">{{ $member_join_year == $this_year ? ceil((14/12)*(12-$member_join_month))-$leaves->sick : (14-$leaves->sick) }}</span></p>
           </div>
        </li>

        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-dollar-sign"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Earned Leave</h6>
              <p class="mb-0">
                <span class="text-danger">Total: </span>  <span class="text-bold" id="total_earn_leave">{{($earnedLeaves[date('Y')]['remain']+ $earnedLeaves[date('Y')]['enjoyed'])}}</span class="text-danger">
                <span class="text-danger">Enjoyed: </span> <span class="text-bold" id="enjoyed_earn_leave">{{$earnedLeaves[date('Y')]['enjoyed']??0}}</span >
                <span class="text-danger">Remained: </span> <span class="text-bold" id="remained_earn_leave">{{$earnedLeaves[date('Y')]['remain']}}</span></p>
           </div>
        </li>
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-gift"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Special Leave</h6>
              <p class="mb-0">{{ (!empty($leaves->special)?$leaves->special:0) }}</p>
           </div>
        </li>
     </ul>

</div>







<div id="DivIdToPrint" style="padding: 0px 96px; display: none; ">
    <div style="text-align: center; margin-top: -5px">
        <span style="font-size: 1em; font-weight: bolder">{{$unit[$info->as_unit_id]['hr_unit_name']}}</span>
        <hr>
        <span style="font-size: 1em; font-weight: bolder">Leave Application Form</span>
        <hr>
    </div>
    <div style="margin-top: -10px;">
        <p>Application Submission Date: </p>
        <div style="margin-top: -20px">
            <div style="width: 50%; float: left;">
                <h4>Personal Details</h4>
                <table style="text-align: left; margin-top: -20px;">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <td>: {{$info->as_name}}</td>
                    </tr>
                    <tr>
                        <th>Designation</th>
                        <td>: {{$info->designation->hr_designation_name}}</td>
                    </tr>
                    <tr>
                        <th>Card No</th>
                        <td>:  {{$info->associate_id}}</td>
                    </tr>
                    <tr>
                        <th>Dept/ Section</th>
                        <td>: {{$info->department->hr_department_name}}</td>
                    </tr>
                    <tr>
                        <th>Unit</th>
                        <td style="font-size: 13px">: {{$unit[$info->as_unit_id]['hr_unit_name']}}</td>
                    </tr>
                    <tr>
                        <th>Date of Joining</th>
                        <td>: {{$info->as_doj->format('d-m-Y')}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div style="float: right; width: 50%;">
                <h4>Application Leave Details</h4>
                <table style="text-align: left; margin-top: -20px;">
                    <tbody>
                    <tr>
                        <th>Leave From</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>Leave To</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>No Of Days</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>Leave Type</th>
                        <td style="font-size: 13px">: Casual/Sick/Earn/Maternity/Without Pay</td>
                    </tr>
                    <tr>
                        <th>Reason of Leave</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>Resume Duty On</th>
                        <td>: </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div >
        <div style="width: 75%; float: left;">
            <div class="boxbody">
                <div style="width: 95%; height: 80px; background: rgba(255, 255, 255, 0.9); padding: auto; border: 1px solid black; border-radius: 10px;">
                    <div class="featured"><u>Address While On leave (Include Mobile Number)</u></div>
                </div>
            </div>
        </div>

        <div style="width: 25%; float: right; margin-top: 3%;">
            <p>------------------------<br> Applicant's Signature</p>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div>
        <hr>
        <p>Application can be given a leave of total ________ From __________________ To __________________</p>
    </div>
    <div style="margin-top: 15px;">
        <div style="float: left;">
            <p style="margin-top: 5%;">------------------------------- <br> Leave Recommended</p>
        </div>
        <div style="float: right;">
            <p style="margin-top: 5%;">------------------------------- <br>Department Head</p>
        </div>
    </div>
    <div style="clear: both;">
        <hr>
        <p>HRD Department Use only</p>
        <div style="float: left; width: 15%; margin-top: 12%;">
            <p >--------------<br>HR Officer</p>
        </div>
        <div style="width: 70%; float: left;">
            <table style="border: 1px solid black; width: 95%;">
                <thead>
                <tr style="border: 1px solid black;">
                    <th>Leave Type</th>
                    <th>Entitled Leave</th>
                    <th>Availed</th>
                    <th>Not Availed</th>
                    <th>Approved Leave</th>
                </tr>
                </thead>
                <tbody>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;">Casual</td>
                    <td style="border: 1px solid black;">{{$member_join_year == $this_year ? ceil((10/12)*(12-$member_join_month)) : '10'}}</td>
                    <td style="border: 1px solid black;">{{ (!empty($leaves->casual)?$leaves->casual:0) }}</td>
                    <td style="border: 1px solid black;">{{ $member_join_year == $this_year ? ceil((10/12)*(12-$member_join_month))-$leaves->casual : (10-$leaves->casual) }}</td>
                    <td style="border: 1px solid black;"></td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td>Sick</td>
                    <td style="border: 1px solid black;">{{$member_join_year == $this_year ? ceil((14/12)*(12-$member_join_month)) : '14'}}</td>
                    <td style="border: 1px solid black;">{{ (!empty($leaves->sick)?$leaves->sick:0) }}</td>
                    <td style="border: 1px solid black;">{{ $member_join_year == $this_year ? ceil((14/12)*(12-$member_join_month))-$leaves->sick : (14-$leaves->sick) }}</td>
                    <td style="border: 1px solid black;"></td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;">Earn</td>
                    <td style="border: 1px solid black;">{{($earnedLeaves[date('Y')]['remain']+ $earnedLeaves[date('Y')]['enjoyed'])}}</td>
                    <td style="border: 1px solid black;">{{$earnedLeaves[date('Y')]['enjoyed']??0}}</td>
                    <td style="border: 1px solid black;">{{$earnedLeaves[date('Y')]['remain']}}</td>
                    <td style="border: 1px solid black;"></td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;">Without Pay</td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;">Special</td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div style="float: right; width: 15%; margin-top: 12%;">
            <p>--------------<br>Approved By</p>
        </div>
    </div>
    <div style="clear: both;"></div>
    <hr>
    <div >
        <h4 style="text-align: center">{{$unit[$info->as_unit_id]['hr_unit_name']}}</h4>
        <hr>
        <div>
            <span style="text-transform:capitalize; float: left; font-size: 1.17em; font-weight: bolder">Leave Pass</span>
            <span style="float: right; border: 1px solid black; padding: 5px;">Date: @for($nb = 1; $nb < 18; $nb++) &nbsp; @endfor </span>
        </div>
        <p style="clear: both; line-height: 150%">This is certify that <b>{{$info->as_name}}</b> Designation <b>{{$info->designation->hr_designation_name}}</b> Card No <b>{{$info->associate_id}}</b> Section <b>{{$info->section->hr_section_name}}</b> Department
            <b>{{$info->department->hr_department_name}}</b> Unit <b>{{$unit[$info->as_unit_id]['hr_unit_name']}}</b> is given a casual/sick/earn/maternity/without
            pay <br> Leave of total ________ From _________________ To _________________</p>
    </div>
    <div style="margin-top: 15px;">
        <div style="float: left;">
            <p style="margin-top: 6%;">------------------------------- <br> Applicant's Signature</p>
        </div>
        <div style="float: right;">
            <p style="margin-top: 6%;">------------------------------- <br>Office Signature</p>
        </div>
    </div>

</div>



