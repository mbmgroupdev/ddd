<div class="row justify-content-center">
	<div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
    ?>
	<div id="print-area" class="col-sm-9">
		<style type="text/css">
				.mb-2 span {
				    width: 160px;
				    font-size: 12px !important;
				    display: inline-block;
				}
			
		</style>
		<style type="text/css" media="print">
			.bn-form-output{padding:54pt 36pt }
		</style>
		@foreach($employees as $key => $emp)
		<div id="jc-{{$emp->associate_id}}" class="bn-form-output" >
			@php
                $des['bn'] = '';
            	$des['en'] = '';
            	$un['name'] = '';
            	$un['address'] = '';
            	if(isset($designation[$emp->as_designation_id])){
            		$des['bn'] = $designation[$emp->as_designation_id]['hr_designation_name_bn'];
                    $des['en'] = $designation[$emp->as_designation_id]['hr_designation_name'];
            		
            	}
            	if(isset($unit[$emp->as_unit_id])){
            		$un['name'] = $unit[$emp->as_unit_id]['hr_unit_name_bn'];
            		$un['address'] = $unit[$emp->as_unit_id]['hr_unit_address_bn'];
            	}

            @endphp
                                        
                                      
            <center><b style="font-size: 14px;">চাকুরীর আবেদনপত্র </b></center>
            <center><u >JOB APPLICATION </u> </center>
            <br><br>
            <div style="display:flex;justify-content: space-between;">
                <div style="width: 70%;">
                    <p> বরাবর,</p>
                    <p> ব্যবস্থাপনা পরিচালক</p>
                    <p> {{ $un['name'] }}</p>
                    <p> {{ $un['address'] }}</p>
                </div>
                <div style="width: 30%;">
                	{{-- photo block --}}
                    <div style="width: 100px;height:110px;border:1px solid;margin-left: auto; "></div>
                </div>
            </div>
            
            
            <p> <u> <b>বিষয়ঃ {{$des['bn']}} পদে চাকুরীর জন্য আবেদন</b></u></p>
            <p> <u> <b> Sub: Application for the post of {{$des['en']}}</b></u></p>
            <table style="border: none; font-size: 12px;" width="100%" cellpadding="3">
                <tr>
                    <td  style="border: none;">নামঃ (Name)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_associate_name )?$emp->hr_bn_associate_name:null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">পিতার নামঃ (Name of Father)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_father_name )?$emp->hr_bn_father_name:null) }} </td>
                </tr>
                <tr>
                    <td  style="border: none;">মাতার নামঃ (Name of Mother)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_mother_name )?$emp->hr_bn_mother_name:null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">স্বামী/স্ত্রীর নামঃ (Name of Husband/Wife)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_spouse_name )?$emp->hr_bn_spouse_name:null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;" rowspan="2">স্থায়ী ঠিকানাঃ (Permanent Address)</td>
                    <td style="border: none;">গ্রাম(Village): {{ (!empty($emp->hr_bn_permanent_village )?$emp->hr_bn_permanent_village:null) }}
                    </td>
                    <td style="border: none;">পোস্ট(P.O): {{ (!empty($emp->hr_bn_permanent_po )?$emp->hr_bn_permanent_po:null) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">থানা(P.S): 
                    	{{ (!empty($emp->permanent_upazilla_bn)?$emp->permanent_upazilla_bn:null) }}

                    </td>
                    <td style="border: none;">জেলা(Dist.): 
                    	{{ (!empty($emp->permanent_upazilla_bn)?$emp->permanent_district_bn:null) }}
                    </td>
                </tr>
               
                <tr >
                    <td  style="border: none;" rowspan="2">বর্তমান ঠিকানাঃ (Permanent Address)</td>
                    <td style="border: none;">গ্রাম(Village): {{ (!empty($emp->hr_bn_present_road)?$emp->hr_bn_present_road:null) }}
                    </td>
                    <td style="border: none;">পোস্ট(P.O): {{ (!empty($emp->hr_bn_present_po )?$emp->hr_bn_present_po:null) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">থানা(P.S): 
                    	{{ (!empty($emp->present_upazilla_bn)?$emp->present_upazilla_bn:null) }}
                    </td>
                    <td style="border: none;">জেলা(Dist.): 
                    	{{ (!empty($emp->present_upazilla_bn)?$emp->present_district_bn:null) }}
                    </td>
                </tr>
                 <tr>
                    <td  style="border: none;">মোবাইল নংঃ (Mobile No.)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->as_contact )?eng_to_bn($emp->as_contact):null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">শিক্ষাগত যোগ্যতাঃ (Edu. Qualification)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: </td>
                </tr>
                <tr>
                    <td  style="border: none;">জন্ম তারিখ/বয়সঃ (Date of Birth/ Age)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->as_dob )?eng_to_bn($emp->as_dob):null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">ধর্মঃ (Religion)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ religion_bangla($emp->emp_adv_info_religion) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">জাতীয়তাঃ (Nationality)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->emp_adv_info_nationality )?$emp->emp_adv_info_nationality:'বাংলাদেশী') }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">বৈবাহিক অবস্থাঃ (Maritial Status)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">:


                     {{ maritial_bangla($emp->emp_adv_info_marital_stat) }}</td>
                            
                </tr>
                <tr>
                    <td  style="border: none;">সন্তানঃ (Children)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->emp_adv_info_children )?eng_to_bn($emp->emp_adv_info_children):null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">অভিজ্ঞতাঃ (Experience)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted"> {{ (!empty($emp->emp_adv_info_work_exp )?eng_to_bn($emp->emp_adv_info_work_exp):"০") }} বছর</td>
                </tr>
                <tr>
                    <td  style="border: none;">সুপারিশকারীর নাম ও পরিচিতি/ঠিকানাঃ <br>(Name and Address of recommender)</td>
                    <td colspan="2" style="border: none; border-bottom: 1px dotted"></td>
                </tr>
                <tr>
                    <td style="border: none;" colspan="3">
                        <br><br>
                        <p>
                            অতএব, অনুগ্রহ করে আমাকে উক্ত পদে নিয়োগ দান করিয়া বাধিত করিবেন।
                        </p>
                        <p>
                            May I, therefore pray and hope that you would be kind enough to appoint me for the above post.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;"  colspan="3"><br><br><br>
                        আপনার বিশ্বস্ত<br>
                        Your Faithfully,<br>
                        <br>
                        তারিখ :
                </tr>
            </table>
            <table width="100%" style="border: 1px solid; font-size: 12px;border-collapse: collapse;" cellpadding="3">
                <tr style="width: 100%">
                    <td style="border: none; text-align: right;" colspan= "2">
                        (অফিস কর্তৃক পূরণীয় For office use only)
                    </td>
                </tr>
                <tr style="width: 100%">
                    <td style="border: none;padding: 3px 10px;width: 50%;">
                        ১. লাইন নং (Dept) :
                    </td>
                    <td style="border: none;padding: 3px 10px;">
                        ৪. নিয়োগের তারিখ (Date of App) : {{eng_to_bn($emp->as_doj)}}
                    </td>
                </tr>
                <tr style="width: 100%">
                    <td style="border: none;padding: 3px 10px;">
                        ২. পুর্ণ নাম (Full Name) :
                    </td>
                    <td style="border: none;padding: 3px 10px;">
                        ৫. নির্ধারিত বেতন (Negotiated Salary) :
                    </td>
                </tr>
                <tr style="width: 100%">
                    <td style="border: none;padding: 3px 10px;">
                        ৩. কার্ড নং (Card Number) : {{$emp->associate_id}}
                    </td>
                </tr>
                <tr style="width: 100%">
                    <td style="border: none;"><br></td>
                    <td style="border: none;"><br></td>
                </tr>
                
                
                <tr style="width: 100%">
                    <td style="border:0;width: 33%">
                        
                    </td>
                    <td style="border: 0; text-align: center;border-collapse: none;padding: 3px 10px;">
                        <br><br><br>
                        প্রশাসনিক কর্মকর্তা<br>
                        Manager HR/ Asst. Manager HR
                    </td>
                </tr>
            </table>
		</div>
		<div class="page-break"></div>
		@endforeach
	</div>
</div>   