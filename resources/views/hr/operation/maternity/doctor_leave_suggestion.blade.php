<button type="button" onclick="printMe('leave-suggestion')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div class="col-xs-12 no-padding-left" id="leave-suggestion" style="font-size: 9px;">
    <div class="tinyMceLetter" style="font-size: 10px;">
    	<p>Name: {{$employee->as_name}} Age: ......... Date: .................</p>
    	<hr>
    	<br>
    	<br>
    	<p style="text-align: justify;">
    		This is to certify that <b>{{$employee->as_name}}</b> Associate ID <b>{{$employee->associate_id}}</b> is carrying. Her EDD is {{$leave->edd}}. According to her EDD, she can get maternity leave from {{$leave->leave_from_suggestion}}. please arrange as rule.
    	</p>
    </div>
</div>