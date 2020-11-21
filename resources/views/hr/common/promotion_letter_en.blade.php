<div class="col-xs-12 no-padding-left"  style="font-size: 16px;">
    <div class="tinyMceLetter" name="job_application"  style="font-size: 16px;">
        <p>
        <br>
        <br>
        <br>
        <style type="text/css" media="print">
            table, p{
                font-size: 16px;
            }
            table td{
                padding: 5px 0;
                font-size: 16px;
            }
            strong{
                font-size: 16px;
            }
        </style>
        <table border="0" style="width: 100%;">
           
            <tr>
                <td colspan="2">Issue Date: {{ date('Y-m-d')}}</td>
            </tr>
            <tr>
                <td style="width: 20%;">Name:</td>
                <td id="letter_name"></td>
            </tr>
            <tr>
                <td style="width: 20%;">Designation:</td>
                <td id="letter_designation"></td>
            </tr>
            <tr>
                <td style="width: 20%;">Employee ID:</td>
                <td id="letter_id"></td>
            </tr>
            <tr>
                <td style="width: 20%;">Department:</td>
                <td id="letter_department"></td>
            </tr>
            <tr>
                <td style="width: 20%;">Date of Join:</td>
                <td id="letter_doj"></td>
            </tr>
        </table>
        

        <h2><strong>Subject: Promotion</strong></h2>
        <p>Dear <span id="letter_title"></span></p>

        <p style="text-align: justify;">We have been keeping a close eye on your performance & we are pleased with the result. Your accomplishments have been integrated to our success & we deeply appreciated that. We would like to express our gratitude by awarding you promotion from  <strong id="en_prev_desg"></strong> to <strong id="en_curr_desg"></strong>. This will be effective from <strong id="en_effective_date"></strong></p>
        
        <p>You are entitling to have all the benefits as admissible under service rules & regulation of the company.</p>
        <p>  
            We wish you a prosperous career with the company & trust that you will continue to work hard to improve your efficiency in your assignment. Congratulation & keep the spirit up.
        </p>
        <br>

        <br>
        <h2><strong>Sincerely Yours,</strong></h2>

        <br>
        <br>
        <br>
        <p><u>HR, Admin & Compliance</u></p>
        <br>
        
        <strong>Copy</strong>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;Personal file.</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;Finance & Accounts.</p>
        <p><strong style="font-style: italic;font-size: 14px;">“This Letter is highly confidential & sharing the information with others will be considered as Misconduct.”</strong></p>
    </div>
</div>