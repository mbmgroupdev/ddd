
<button type="button" onclick="printMe('doctor_report')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
 <div class="col-xs-12 no-padding-left" id="doctor_report" style="font-size: 12px;">
    <div class="tinyMceLetter" style="font-size: 14px;min-height: 600px;border: 1px solid #d1d1d1;padding: 20px;">
        <style type="text/css">

        </style>
        <center><h3>{{$employee->hr_unit_name_bn??''}}</h3></center>
        <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center><br><br>
        <style type="text/css">
            table{
                font-size: 12px;
            }
            .table-bordered {
                border-collapse: collapse;
            }
            .table-bordered th,
            .table-bordered td {
              border: 1px solid #000 !important;
              padding: 0 5px;
              white-space: nowrap;

            }
        </style>
    	<div style="display: flex; justify-content:space-between;font-size: 14px;">
	        <div style="text-align:justify;padding-right: 10px;justify-content:space-between; "> 
	        	আইডি নং<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:80px;font-weight:bold;text-align:center;">{{$employee->associate_id}}</span> &nbsp;
	        	নাম<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:150px;font-weight:bold;text-align:center;">{{$employee->as_name}}</span>
	        	পদবী<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:100px;font-weight:bold;text-align:center;">{{$employee->hr_designation_name}}</span> &nbsp;
	        	বয়স<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{$employee->as_dob->age}}</span> <br>
	        	
	        	স্বামীর নামঃ<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:100px;font-weight:bold;text-align:center;">{{$leave->husband_name}}</span>
	        	স্বামীর বয়স<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{$leave->husband_age}}</span> &nbsp;
	        	পেশা<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:100px;font-weight:bold;text-align:center;">{{$leave->husband_occupasion}}</span> &nbsp;
	        	সন্তান সংখ্যা  <span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:40px;font-weight:bold;text-align:center;">{{($leave->no_of_son + $leave->no_of_daughter)}}     </span> &nbsp;
	        	ছোট সন্তানের বয়স<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:40px;font-weight:bold;text-align:center;">{{($leave->last_child_age)}}</span> বছর &nbsp;
	        	<br>
	        	এনিমিয়া<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{($leave->medical->anemia)}}</span> &nbsp;
	        	হার্ট<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{($leave->medical->heary)}}</span> &nbsp;
	        	ফুস্ফুস<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{($leave->medical->lungs)}}</span> &nbsp;
	        	র‍্যাশ<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{($leave->medical->rash)}}</span> &nbsp;
	        	মুখ গহ্বরে আলসার/থ্রাস/অন্যান্য<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:60px;font-weight:bold;text-align:center;">{{($leave->medical->others??' ')}}</span>
	        	<br>
	        </div>
	        <div style="border: 1px solid #000;width: 200px; padding: 10px;">
	        	Blood Group<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:50px;font-weight:bold;text-align:center;">{{$leave->medical->blood_group}}</span><br>
	        	LMP<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:80px;font-weight:bold;text-align:center;">{{$leave->medical->lmp??''}}</span><br>
	        	EDD<span style="border-bottom: 1px dotted #d1d1d1;display:inline-block;min-width:80px;font-weight:bold;text-align:center;">{{$leave->medical->edd??''}}</span><br>
	        </div>
	        
        </div>
        <p style="text-align: justify;">
        	অতীতে গর্ভজনিত জটিলতাঃ @if($leave->medical->pregnant_complexity == 1) হ্যাঁ @else না @endif বর্ণনাঃ {{$leave->medical->pregnant_complexity_details??'--------------------------------'}}
        </p>
        <p style="text-align: justify;">
        	অতীতের প্রধান অসুস্থতাঃ অপারেশনঃ @if($leave->medical->operation == 1) হ্যাঁ @else না @endif বর্ণনাঃ {{$leave->medical->operation_details??'--------------------------------'}}
        </p>
        <p style="text-align: justify;">
        	STI/RTI অতীত ইতিহাসঃ @if($leave->medical->stl_rtl == 1) হ্যাঁ @else না @endif বর্ণনাঃ {{$leave->medical->stl_rtl_details??'--------------------------------'}}
            ওষুধে এলার্জীঃ @if($leave->medical->alergy == 1) হ্যাঁ @else না @endif 
        </p>
        <p style="text-align: justify;">
            মাদকাসক্তির ইতিহাসঃ @if($leave->medical->drug_addiction == 1) হ্যাঁ @else না @endif বর্ণনাঃ {{$leave->medical->drug_addiction_details??'--------------------------------'}}
        </p>
        <br>
        <h4>গর্ভকালীন সেবাঃ</h4>
        <table style="text-align: center;width: 100%;display: block;overflow: auto;" class="table-bordered">
        	<thead>
        		<tr>
        			<th rowspan="2">
        				তারিখ
        			</th>
        			<th rowspan="2">
        				ওজন
        			</th>
        			<th rowspan="2">
        				বিপি
        			</th>
        			<th rowspan="2">
        				ইডিমা
        			</th>
        			<th rowspan="2">
        				হিমোগ্লোবিন <br>
        				% রক্তস্বল্পতা
        			</th>
        			<th rowspan="2">
        				জন্ডিস
        			</th>
        			<th rowspan="2">
        				জরায়ুর উচ্চতা
        			</th>
        			<th colspan="3">
        				গর্ভস্থ শিশুর
        			</th>
        			<th colspan="2">
        				প্রস্রাব পরীক্ষা
        			</th>
        			<th rowspan="2">
        				অন্যান্য
        			</th>
        			<th rowspan="2">
        				মন্ত্যব্য
        			</th>
        		</tr>
        		<tr>
        			<th>অবস্থান</th>
        			<th>নড়াচরা</th>
        			<th>হৃদ-স্পন্দন</th>
        			<th>আল্বুমিন</th>
        			<th>সুগার</th>
        		</tr>
        	</thead>
        	<tbody>
        		@foreach($leave->medical->record as $key => $record)
        			<tr>
        				<td>{{$record->checkup_date}}</td>
        				<td>{{$record->weight}}</td>
        				<td>{{$record->bp}}</td>
        				<td>{{$record->edema}}</td>
        				<td></td>
        				<td>{{$record->jaundice}}</td>
        				<td>{{$record->uterus_height}}</td>
        				<td>{{$record->baby_position}}</td>
        				<td>{{$record->baby_movement}}</td>
        				<td>{{$record->albumine}}</td>
        				<td>{{$record->sugar}}</td>
        				<td>{{$record->others}}</td>
        				<td>{{$record->comment}}</td>
        				<td></td>
        			</tr>
        		@endforeach
        	</tbody>
        </table>
    </div>
</div>

