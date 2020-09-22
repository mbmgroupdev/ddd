<div class="col-xs-12 no-padding-left" id="printable" style="font-size: 9px;">
    <div class="tinyMceLetter" name="job_application" id="job_application" style="font-size: 9px;">
        <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
        ?>
        <p>
        <center><h3>{{$employee->hr_unit_name_bn}}</h3></center>
        <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
        <hr>
        <p style="font-size: 12px;">বরাবর,</p>
        <p style="font-size: 12px;">ব্যবস্থাপক - মানবসম্পদ</p>
        <p style="font-size: 12px;">{{ (!empty($employee->hr_unit_name_bn )?$employee->hr_unit_name_bn:null) }}</p>
        <p style="font-size: 12px;">{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</p>
        <p style="font-size: 12px;"><u> <b>বিষয়ঃ মাতৃত্বকালীন ছুটির জন্য আবেদন</b></u></p>
        
                <td style="border: none;" colspan="2">
                    <br>    
                    <p>
                        অতএব, অনুগ্রহ করে আমাকে উক্ত পদে নিয়োগ দান করিয়া বাধিত করিবেন।
                    </p>
                    <p>
                        May I, therefore pray and hope that you would be kind enough to appoint me for the above post.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="border: none;" width="290px"><br>
                    আপনার বিশ্বস্ত
                </td>
            </tr>
            <tr>
                <td style="border: none;" width="290px" >
                    Your Faithfully,
                </td>
            </tr>
            <tr>
                <td style="border: none;" width="290px">
                  তারিখ
                </td>
                <td style="border: none;">
                    :
                </td>
            </tr>
        </table>
        <table style="border: 1px solid; font-size: 12px;" width="100%" cellpadding="3" width="100%">
            <tr style="width: 100%">
                <td style="border: none; text-align: right;" colspan= "2">
                    (অফিস কর্তৃক পূরণীয় For office use only)
                </td>
            </tr>
            <tr style="width: 100%">
                <td style="border: none;">
                    ১. লাইন নং (Dept) :
                </td>
                <td style="border: none;">
                    ৪. নিয়োগের তারিখ (Date of App) : {{$employee->as_doj}}
                </td>
            </tr>
            <tr style="width: 100%">
                <td style="border: none;">
                    ২. পুর্ণ নাম (Full Name) :
                </td>
                <td style="border: none;">
                    ৫. নির্ধারিত বেতন (Negotiated Salary) :
                </td>
            </tr>
            <tr style="width: 100%">
                <td style="border: none;">
                    ৩. কার্ড নং (Card Number) : {{$employee->associate_id}}
                </td>
            </tr>
            <tr style="width: 100%">
                <td style="border: none;"><br></td>
                <td style="border: none;"><br></td>
            </tr>
            
        </table>
        <table style="border: 1px solid; font-size: 12px;" width="100%" cellpadding="3" width="100%">
            
            <tr style="width: 100%">
                <td style="border: 1px solid; text-align: center;">
                    <br><br><br>
                    উৎপাদন ব্যবস্থাপক<br>
                    Production Manager 
                </td>
                <td style="border: 1px solid; text-align: center;">
                    <br><br><br>
                    প্রশাসনিক কর্মকর্তা<br>
                    Manager HR/ Asst. Manager HR
                </td>
                <td style="border: 1px solid; text-align: center;">
                    <br><br><br>
                    জি.এম/ম্যানেজার/এ.জি.এম<br>
                    G.M/Manager/A.G.M 
                </td>
            </tr>
        </table>
    </div>
</div>