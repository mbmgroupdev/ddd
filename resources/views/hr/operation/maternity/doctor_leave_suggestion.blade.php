<button type="button" onclick="printMe('leave-suggestion')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div class="col-xs-12 no-padding-left" id="leave-suggestion" style="font-size: 18px !important;">
    <div class="tinyMceLetter" style="font-size: 18px !important;min-height: 600px;border: 1px solid #d1d1d1;padding: 20px;position: relative;">
        <style type="text/css">
            span.input-class{
                border-bottom: 1px dotted #d1d1d1; 
                width: 120px;
                font-weight: bold;
                display: inline-block;
            }
            .footer-block {
                color: #000;
                position: absolute;
                bottom: 100px;
                width: 90%;
            }
        </style>
    	<p style="font-size: 18px !important;">Name: <span class="input-class" style="width:200px;font-size: 18px;"> {{$employee->as_name}} </span> Age: <span class="input-class" style="font-size: 18px;"> {{$employee->as_dob->age}} </span>  Date: <span class="input-class" style="font-size: 18px;"> {{date('Y-m-d')}} </span> </p>
    	<hr>
    	<br>
    	<p style="text-align: justify;font-size: 18px !important;">
    		&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;This is to certify that <b style="font-size: 18px;">{{$employee->as_name}}</b> Associate ID <b style="font-size: 18px;">{{$employee->associate_id}}</b> is carrying. Her EDD is <b style="font-size: 18px;"> {{$leave->edd}}</b>. According to her EDD, she can get maternity leave from <b style="font-size: 18px;"> {{$leave->leave_from_suggestion}} </b>. Please arrange as rule.
    	</p>
        <br><br><br><br>
        <div class="footer-block">
            <p style="text-align:right; padding-right:60px;font-size: 18px;"><strong style="font-size: 18px;">Medical Officer</strong></p>
            <hr>
            <p ><strong style="font-size: 18px;">{{$employee->hr_unit_short_name}} Clinic</i></strong></p>
            <p>{{$employee->hr_unit_address}}</p>
            
        </div>
        
    </div>
</div>