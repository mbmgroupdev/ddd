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
			.bn-form-output{padding:10pt}
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
            <center><u style="font-size: 13px">JOB APPLICATION </u> </center>
            <div style="display:flex;justify-content: space-between;">
                <div style="width: 70%;">
                    <p style="font-size: 12px;">বরাবর,</p>
                    <p style="font-size: 12px;">ব্যবস্থাপনা পরিচালক</p>
                    <p style="font-size: 12px;">{{ $un['name'] }}</p>
                    <p style="font-size: 12px;">{{ $un['address'] }}</p>
                </div>
                <div style="width: 30%;">
                	{{-- photo block --}}
                    <div style="width: 100px;height:110px;border:1px solid;margin-left: auto; "></div>
                </div>
            </div>
            
            
            <p style="font-size: 12px;"><u> <b>বিষয়ঃ {{$des['bn']}} পদে চাকুরীর জন্য আবেদন</b></u></p>
            <p style="font-size: 12px;"><u> <b> Sub: Application for the post of {{$des['en']}}</b></u></p>
            <table style="border: none; font-size: 12px;" width="100%" cellpadding="3">
                <tr>
                    <td width="290px" style="border: none;">নামঃ (Name)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_associate_name )?$emp->hr_bn_associate_name:null) }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">পিতার নামঃ (Name of Father)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_father_name )?$emp->hr_bn_father_name:null) }} </td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">মাতার নামঃ (Name of Mother)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_mother_name )?$emp->hr_bn_mother_name:null) }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">স্বামী/স্ত্রীর নামঃ (Name of Husband/Wife)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->hr_bn_spouse_name )?$emp->hr_bn_spouse_name:null) }}</td>
                </tr>
                <tr>
                    <tr>
                        <td width="290px" style="border: none;" rowspan="2">স্থায়ী ঠিকানাঃ (Permanent Address)</td>
                        <td style="border: none;">গ্রাম(Village): {{ (!empty($emp->hr_bn_permanent_village )?$emp->hr_bn_permanent_village:null) }}
                        </td>
                        <td style="border: none;">পোস্ট(P.O): {{ (!empty($emp->hr_bn_permanent_po )?$emp->hr_bn_permanent_po:null) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none;">থানা(P.S): 
                        	@if(isset($upazila[$emp->emp_adv_info_per_upz]))
                        	{{ $upazila[$emp->emp_adv_info_per_upz]['upa_name_bn'] }}
                        	@endif

                        </td>
                        <td style="border: none;">জেলা(Dist.): 
                        	@if(isset($district[$emp->emp_adv_info_per_dist]))
                        	{{ $district[$emp->emp_adv_info_per_dist]['dis_name_bn'] }}
                        	@endif
                        </td>
                    </tr>
                </tr>
               
                <tr >
                    <tr>
                        <td width="290px" style="border: none;" rowspan="2">বর্তমান ঠিকানাঃ (Permanent Address)</td>
                        <td style="border: none;">গ্রাম(Village): {{ (!empty($emp->hr_bn_present_house )?$emp->hr_bn_present_house:null) }} {{ (!empty($emp->emp_adv_info_pres_road )?$emp->emp_adv_info_pres_road:null) }}
                        </td>
                        <td style="border: none;">পোস্ট(P.O): {{ (!empty($emp->hr_bn_present_po )?$emp->hr_bn_present_po:null) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none;">থানা(P.S): 
                        	@if(isset($upazila[$emp->emp_adv_info_pres_upz]))
                        	{{ $upazila[$emp->emp_adv_info_pres_upz]['upa_name_bn'] }}
                        	@endif
                        </td>
                        <td style="border: none;">জেলা(Dist.): 
                        	@if(isset($district[$emp->emp_adv_info_pres_dist]))
                        	{{ $district[$emp->emp_adv_info_pres_dist]['dis_name_bn'] }}
                        	@endif
                        </td>
                    </tr>
                </tr>
                 <tr>
                    <td width="290px" style="border: none;">মোবাইল নংঃ (Mobile No.)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->as_contact )?eng_to_bn($emp->as_contact):null) }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">শিক্ষাগত যোগ্যতাঃ (Edu. Qualification)</td>
                    <td style="border: none; border-bottom: 1px dotted">: </td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">জন্ম তারিখ/বয়সঃ (Date of Birth/ Age)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->as_dob )?eng_to_bn($emp->as_dob):null) }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">ধর্মঃ (Religion)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ religion_bangla($emp->emp_adv_info_religion) }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">জাতীয়তাঃ (Nationality)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->emp_adv_info_nationality )?$emp->emp_adv_info_nationality:'বাংলাদেশী') }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">বৈবাহিক অবস্থাঃ (Maritial Status)</td>
                    <td style="border: none; border-bottom: 1px dotted">:


                     {{ maritial_bangla($emp->emp_adv_info_marital_stat) }}</td>
                            
                </tr>
                <tr>
                    <td width="290px" style="border: none;">সন্তানঃ (Children)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->emp_adv_info_children )?eng_to_bn($emp->emp_adv_info_children):null) }}</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">অভিজ্ঞতাঃ (Experience)</td>
                    <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($emp->emp_adv_info_work_exp )?eng_to_bn($emp->emp_adv_info_work_exp):"০") }} বছর</td>
                </tr>
                <tr>
                    <td width="290px" style="border: none;">সুপারিশকারীর নাম ও পরিচিতি/ঠিকানাঃ (Name and Address of recommender)</td>
                    <td style="border: none; border-bottom: 1px dotted">: </td>
                </tr>
                <tr>
                    <td style="border: none;" colspan="2">
                        
                        <p>
                            অতএব, অনুগ্রহ করে আমাকে উক্ত পদে নিয়োগ দান করিয়া বাধিত করিবেন।
                        </p>
                        <p>
                            May I, therefore pray and hope that you would be kind enough to appoint me for the above post.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;" width="290px"><br><br><br>
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
            <table style="border: 1px solid; font-size: 12px;border-collapse: collapse;" width="100%" cellpadding="3" width="100%">
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
                        ৪. নিয়োগের তারিখ (Date of App) : {{eng_to_bn($emp->as_doj)}}
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
                        ৩. কার্ড নং (Card Number) : {{$emp->associate_id}}
                    </td>
                </tr>
                <tr style="width: 100%">
                    <td style="border: none;"><br></td>
                    <td style="border: none;"><br></td>
                </tr>
                
            </table>
            <table style="border: 1px solid; font-size: 12px;border-collapse: collapse;" width="100%" cellpadding="3" width="100%">
                
                <tr style="width: 100%">
                    <td style="width: 33%">
                    </td>
                    <td style="border:0;width: 33%">
                        
                    </td>
                    <td style="border: 0; text-align: center;border-collapse: none;">
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