 <div class="panel panel-success" id="voucher" hidden="hidden">
            <div class="panel-heading">
                <h6>Voucher
                    <button class="btn  btn-danger pull-right printVoucher" style="border-radius: 2px;" data-tooltip="Print" Data-tooltip-location="left"><i class="fa fa-print"></i> Print</button>
                </h6>
            </div>
            <div class="panel-body">
                <div class="col-sm-12 print_div" style="border:1px solid grey;"  id="print_div">
                    <h1 style="text-align: center; color: forestgreen;" id="unit_print"></h1>
                    <h5 style="text-align: center; " id="unit_addr_print"></h5>
                    <h5 class="pull-right" style="margin-left: 80%;">তারিখঃ<?php
                        echo eng_to_bn(date('d-m-Y'));

                    ?></h5>


                    <div style="margin-left: 40px; margin-top: 60px;">
                        <div style="margin-left: 40px; padding: 0px;" id="already_saved_data" hidden="hidden">
                            <span style="margin-left: 90%; font-weight: 800; color:darkgrey; font-size: 16px;">COPY</span>
                        </div>
                        
                        <h5 style="margin-left: 10%;">অব্যাহতীকালীন সুযোগ-সুবিধার হিসাব-</h5>

                        <table style="border: none; margin-left: 10%; width: 60%; font-size: 11px;">
                            <tbody>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">নামঃ</th>
                                    <th style="padding:2px; text-align: left; width: 60%;" id="emp_name_print">  <br></th>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">পদবীঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_deg_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">ডিপার্টমেন্টঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_dep_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">আইডি নংঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_ass_id_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">মূল বেতনঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_basic_sal_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">মোট বেতনঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_current_sal_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">চাকুরীর মোট সময়কালঃ</th>
                                    <td style="padding:2px; width: 60%;" id="total_service_days_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">অব্যাহতীর কারনঃ</th>
                                    <td style="padding:2px; width: 60%;" id="reason_print">  <br></td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin:0px; padding:0px;" >
                            <h5 style="margin-top: 10px; margin-left: 10%; text-decoration: underline;">প্রদেয় সুযোগ-সুবিধা সমুহ ও পাওনাদিঃ</h5>
                            <table style="border: 1px solid darkgrey; margin-left: 10%; width: 60%; border-collapse: collapse; font-size: 11px; ">
                                <thead>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;">
                                        <th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 40%;  padding-left: 30px;">সুযোগ-সুবিধা সমুহ </th>
                                        <th style="border: 1px solid darkgrey; padding: 5px;  text-align: center;  padding-left: 30px;">টাকার পরিমান</th>
                                    </tr>
                                </thead>
                                <tbody id="the_payble_body_print">
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="earn_leave_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            আহরিত ছুটির হিসাব বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px; text-align: right;" id="earn_leave_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="service_benefit_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            সেবা বাবদ     
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right" id="service_benefit_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="subsistence_allowance_row_print" {{-- hidden="hidden" --}}>
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            জীবিকা ভাতা বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right" id="subsistence_allowance_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="notice_pay_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            নোটিশ পে বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right" id="notice_pay_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="termination_benefit_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            অবসান সুবিধা বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right;" id="termination_benefit_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="natural_death_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            স্বাভাবিক মৃত্যু
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right;" id="natural_death_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" 
                                                    id="on_duty_and_accidental_death_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            কর্তব্যরত অবস্থায় এবং দুর্ঘটনায় মৃত্যু
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right;" id="on_duty_and_acci_death_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;">
                                        <th style="border: 1px solid darkgrey; padding: 5px; text-align: right; color: maroon;">মোট</th>
                                        <th style="border: 1px solid darkgrey; padding: 5px; text-align: left; color: maroon;  padding-left: 30px;text-align: right;" id="grand_toal_print_value"> ৳</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        

                        <table style="  width: 100%; margin-top: 20%; margin-bottom: 40px; font-size: 10px;">
                                <tr style=" padding: 5px;">
                                    <td style=" padding: 4px;">
                                        প্রস্তুতকারী
                                    </td>
                                    <td style=" padding: 4px;">
                                        হিসাব বিভাগ
                                    </td>
                                    <td style=" padding: 4px;">
                                        সহঃ ব্যবস্থাপক <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
                                    </td>
                                    <td style=" padding: 4px;">
                                        সহঃ মহাব্যবস্থাপক <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
                                    </td>
                                    <td style=" padding: 4px;">
                                        এভিপি <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
                                    </td>
                                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>