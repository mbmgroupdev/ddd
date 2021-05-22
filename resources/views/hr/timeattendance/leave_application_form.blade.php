<div id="DivIdToPrint" style="padding: 0px 96px; display: none; ">
    <div style="text-align: center; margin-top: -5px">
        <span style="font-size: 1.5em; font-weight: bolder">{{$unit[$info->as_unit_id]['hr_unit_name']}}</span>
        <hr>
        <span style="font-size: 1.17em; font-weight: bolder">Leave Application Form</span>
        <hr>
    </div>
    <div>
        <p>Application Submission Date: </p>
        <div>
            <div style="width: 50%; float: left;">
                <h4>Personal Details</h4>
                <table style="text-align: left;">
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
                        <td>: {{$unit[$info->as_unit_id]['hr_unit_name']}}</td>
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
                <table style="text-align: left;">
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
                        <td>: Casual/Sick/Earn/Maternity/Without Pay</td>
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
    <div style="margin-top: 20px">
        <div style="width: 75%; float: left;">
            <div class="boxbody">
                <div style="width: 95%; height: 80px; background: rgba(255, 255, 255, 0.9); padding: auto; border: 1px solid black; border-radius: 10px;">
                    <div style="padding-left: 5px"><u>Address While On leave (Include Mobile Number)</u></div>
                </div>
            </div>
        </div>

        <div style="width: 25%; float: right; margin-top: 8%;">
            <p>-------------------------------<br> Applicant's Signature</p>
        </div>
    </div>
    <div style="clear: both;"></div>
<div style="margin-top: 20px">
    <div style="margin-top: 30px">
        <hr>
        <p style="margin-top: 10px">Application can be given a leave of total _____________ From _______________________________ To _______________________________</p>
    </div>
    <div style="margin-top: 40px;">
        <div style="float: left;">
            <p style="margin-top: 5%; text-align: center">-------------------------------------- <br> Leave Recommended</p>
        </div>
        <div style="float: right;">
            <p style="margin-top: 5%; text-align: center">-------------------------------------- <br>Department Head</p>
        </div>
    </div>
</div>
    <div style="clear: both;"></div>
    <div style="margin-top: 20px">
        <hr style="color: black; font-weight: bolder">
        <h4>HRD Department Use only</h4>
        <div style="margin-top: 30px">
            <div style="float: left; width: 15%; margin-top: 20%;">
                <p style="text-align: center">----------------------------<br>HR Officer</p>
            </div>
            <div style="width: 70%; float: left;">
                <table style="border: 1px solid black; width: 95%; border-collapse: collapse; text-align: center">
                    <thead>
                    <tr style="border: 1px solid black; padding: 20px">
                        <th style="border: 1px solid black; padding: 7px 0px">Leave Type</th>
                        <th style="border: 1px solid black; padding: 7px 0px">Entitled Leave</th>
                        <th style="border: 1px solid black; padding: 7px 0px">Availed</th>
                        <th style="border: 1px solid black; padding: 7px 0px">Not Availed</th>
                        <th style="border: 1px solid black; padding: 7px 0px">Approved Leave</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="border: 1px solid black; padding: 20px">
                        <td style="border: 1px solid black; padding: 7px 0px">Casual</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{$member_join_year == $this_year ? ceil((10/12)*(12-$member_join_month)) : '10'}}</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{ (!empty($leaves->casual)?$leaves->casual:0) }}</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{ $member_join_year == $this_year ? ceil((10/12)*(12-$member_join_month))-$leaves->casual : (10-$leaves->casual) }}</td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                    </tr>
                    <tr style="border: 1px solid black; padding: 20px">
                        <td style="border: 1px solid black; padding: 7px 0px">Sick</td>
                        <td style="border: 1px solid black ; padding: 7px 0px">{{$member_join_year == $this_year ? ceil((14/12)*(12-$member_join_month)) : '14'}}</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{ (!empty($leaves->sick)?$leaves->sick:0) }}</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{ $member_join_year == $this_year ? ceil((14/12)*(12-$member_join_month))-$leaves->sick : (14-$leaves->sick) }}</td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                    </tr>
                    <tr style="border: 1px solid black; padding: 20px">
                        <td style="border: 1px solid black; padding: 7px 0px">Earn</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{($earnedLeaves[date('Y')]['remain']+ $earnedLeaves[date('Y')]['enjoyed'])}}</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{$earnedLeaves[date('Y')]['enjoyed']??0}}</td>
                        <td style="border: 1px solid black; padding: 7px 0px">{{$earnedLeaves[date('Y')]['remain']}}</td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                    </tr>
                    <tr style="border: 1px solid black; padding: 20px">
                        <td style="border: 1px solid black; padding: 7px 0px">Without Pay</td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                    </tr>
                    <tr style="border: 1px solid black; padding: 20px">
                        <td style="border: 1px solid black; padding: 7px 0px">Special</td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                        <td style="border: 1px solid black; padding: 7px 0px"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div style="float: right; width: 15%; margin-top: 20%;">
                <p style="text-align: center">----------------------------<br>Approved By</p>
            </div>
        </div>

    </div>
    <div style="clear: both;"></div>


    <div style="margin-top: 30px">
        <hr>
        <div >
            <h4 style="text-align: center">{{$unit[$info->as_unit_id]['hr_unit_name']}}</h4>
            <hr style="font-weight: bolder; color: black">
            <div style="margin-top: 30px">
                <div>
                    <span style="text-transform:capitalize; float: left; font-size: 1.17em; font-weight: bolder">Leave Pass</span>
                    <span style="float: right; border: 1px solid black; padding: 5px;">Date: @for($nb = 1; $nb < 18; $nb++) &nbsp; @endfor </span>
                </div>
                <div style="clear: both"></div>
                <p style="clear: both; line-height: 150%; margin-top: 30px">This is certify that <b>{{$info->as_name}}</b> Designation <b>{{$info->designation->hr_designation_name}}</b> Card No <b>{{$info->associate_id}}</b> Section <b>{{$info->section->hr_section_name}}</b> Department
                    <b>{{$info->department->hr_department_name}}</b> Unit <b>{{$unit[$info->as_unit_id]['hr_unit_name']}}</b> is given a casual/sick/earn/maternity/without
                    pay <br> Leave of total _____________ From _______________________ To _______________________</p>
            </div>
        </div>
        <div style="margin-top: 40px;">
            <div style="float: left;">
                <p style="margin-top: 12%; text-align: center;">------------------------------- <br> Applicant's Signature</p>
            </div>
            <div style="float: right;">
                <p style="margin-top: 12%; text-align: center">------------------------------- <br>Office Signature</p>
            </div>
        </div>
    </div>

</div>
