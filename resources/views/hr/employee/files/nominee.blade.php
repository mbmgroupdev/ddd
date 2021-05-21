<div class="row justify-content-center">
	<div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');
        $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
        ?>
	<div id="print-area" class="col-sm-9">
		<style type="text/css">
				.mb-2 span {
				    width: 160px;
				    display: inline-block;
				}
				.d-flex{
                    display: flex;
                }
                .bordered{
                    border-bottom: 1px dotted #777;
                    flex-grow: 1;
                }
                p{
                    margin: 5px;padding:0;
                }
                
                .page-break p{
                    
                    line-height: 14px;
                }
                td{
                    
                    line-height: 17px;
                }

                .page-break{
                    page-break-after: always;
                }

                
                
			
		</style>
		<style type="text/css" media="print">
			.bn-form-output{padding:18pt 12pt }
		</style>
		@foreach($employees as $key => $emp)
		<div id="jc-{{$emp->associate_id}}" class="bn-form-output page-break" >
			@php
            	$des['bn'] = '';
            	$des['en'] = '';
            	$des['grade'] = '';
            	$un['name'] = '';
            	$un['address'] = '';
            	if(isset($designation[$emp->as_designation_id])){
            		$des['bn'] = $designation[$emp->as_designation_id]['hr_designation_name_bn'];
            		$des['en'] = $designation[$emp->as_designation_id]['hr_designation_name'];
            		$des['grade'] = $designation[$emp->as_designation_id]['hr_designation_grade'];
            	}
            	if(isset($unit[$emp->as_unit_id])){
            		$un['name'] = $unit[$emp->as_unit_id]['hr_unit_name_bn'];
            		$un['address'] = $unit[$emp->as_unit_id]['hr_unit_address_bn'];
            	}

            @endphp
            
            <center><b style="font-size:14px">নমিনী মনোনয়ন</b></center>
            <center><p style="margin-bottom: 0">ফরম-৪১</p></center>
            <center><p style="margin-bottom: 0">[ধারা ১৯, ১৩১(১)(ক), ১৫৫(২), ২৩৪, ২৪৬, ২৬৫ ও ২৭৩ এবং বিধি ১১৮(১), ১৩৬, ২৩২(২), ২৬২(১), ২৮৯(১) ও ৩২১(১) দ্রষ্টব্য] <br/>জমা ও বিভিন্নখাতে প্রাপ্য অর্থ পরিশোধের ঘোষণা ও মনোনয়ন ফরম </p></center> 
            <br/>


            <p>১ । প্রতিষ্ঠানের নামঃ&nbsp;{{$un['name']}}</p>
            <p>২ । প্রতিষ্ঠানের ঠিকানাঃ&nbsp;{{ $un['address'] }}</p>
            <p class="d-flex">
                <font style="width:200px">৩ । কর্মকর্তা/কর্মচারী/শ্রমিকের নামঃ </font>
                <font class="bordered">
                    {{ (!empty($emp->hr_bn_associate_name)?$emp->hr_bn_associate_name:null) }} 
                    
                </font>
            </font>আইডিঃ<font style="width:20%;border-bottom:1px dotted #999;flex-grow: 1">&nbsp;
                <b>{{$emp->associate_id}}</b>
            </font>
            </p>
            
            <p class="d-flex">
                &nbsp;&nbsp;&nbsp;&nbsp;<font >ঠিকানাঃ </font>
                <font style="width:75%;border-bottom:1px dotted #999; display:inline-block;margin-left:20px;"> 
                    ({{ (!empty($emp->hr_bn_present_road)?"রোড নং-".$emp->hr_bn_present_road:null) }},
                    {{ (!empty($emp->hr_bn_present_house)?"বাড়ি নং-".$emp->hr_bn_present_house:null) }},
                    {{ (!empty($emp->hr_bn_present_po)?"ডাকঘর-".$emp->hr_bn_present_po:null) }},
                    {{ (!empty($emp->present_upazilla_bn)?"উপজেলা-".$emp->present_upazilla_bn:null) }},
                    {{ (!empty($emp->present_district_bn)?"জেলা-".$emp->present_district_bn:null) }} )
                </font>লিঙ্গঃ<font style="width:20%;border-bottom:1px dotted #999;flex-grow: 1">&nbsp;{{ ((!empty($emp->as_gender) && $emp->as_gender=="Male")?"পুরুষ":"মহিলা") }}
                </font>
            </p>
            
            <p class="d-flex">
                <font style="width:150px">
                ৪ । পিতা/মাতা/{{ ((!empty($emp->as_gender) && $emp->as_gender=="Male")?"স্ত্রীর":"স্বামীর") }}  নামঃ 
                </font>
                &nbsp;
                <font class="bordered">{{ (!empty($emp->hr_bn_father_name)?$emp->hr_bn_father_name:null) }}/{{ (!empty($emp->hr_bn_mother_name)?$emp->hr_bn_mother_name:null) }}/{{ (!empty($emp->hr_bn_spouse_name)?$emp->hr_bn_spouse_name:null) }}
                </font>
            </p>
            
            <p style="display: flex;">
                <font style="width:150px;">
                ৫ । জন্ম তারিখঃ
               </font>&nbsp;
                তারিখঃ&nbsp;
                <font style="border-bottom:1px dotted #999; display:inline-block; text-align: center;">
                    {{ (!empty($emp->as_dob)?str_replace($en,$bn, date("d", strtotime($emp->as_dob))):null) }}&nbsp;&nbsp;&nbsp;
                </font>
                মাসঃ&nbsp;
                <font style="border-bottom:1px dotted #999;text-align: center; display:inline-block">
                    {{ (!empty($emp->as_dob)?str_replace($en,$bn, date("F", strtotime($emp->as_dob))):null) }}&nbsp;&nbsp;&nbsp;
                </font>
                বছরঃ&nbsp;
                <font style="border-bottom:1px dotted #999;text-align: center; display:inline-block">
                    {{ (!empty($emp->as_dob)?str_replace($en,$bn, date("Y", strtotime($emp->as_dob))):null) }}&nbsp;
                </font>
            </p>
            <p style="display: flex;">
                <font style="width:24%;display:inline-block">৬ । সনাক্তকরণ চিন্হ (যদি থাকে)</font>
                <font style="width:76%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                </font>
            </p>
            
            <p class="d-flex">
                <font style="width:14%;display:inline-block">৭ । স্থায়ী ঠিকানাঃ</font>
                গ্রামঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    {{ (!empty($emp->hr_bn_permanent_village)?$emp->hr_bn_permanent_village:null) }}
                </font>
                ডাকঘরঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    {{ (!empty($emp->hr_bn_permanent_po)?$emp->hr_bn_permanent_po:null) }}
                </font>
            </p>
            
            <p class="d-flex">
                <font style="width:14%;display:inline-block">&nbsp;</font>
                থানাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    {{ (!empty($emp->permanent_upazilla_bn)?$emp->permanent_upazilla_bn:null) }}
                </font>
                জেলাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    {{ (!empty($emp->permanent_district_bn)?$emp->permanent_district_bn:null) }}
                </font>
            </p> 
            
            <p class="d-flex">
                <font style="display:inline-block">৮ । চাকরিতে নিযুক্তির তারিখঃ</font>
                <font class="bordered">
                    &nbsp;&nbsp;{{ (!empty($emp->as_doj)?str_replace($en,$bn, date("d F, Y", strtotime($emp->as_doj))):null) }}
                </font>
            </p>
        
            <p class="d-flex">
                <font style="display:inline-block">৯ । পদের নামঃ</font>
                <font class="bordered" style="padding-left: 10px;">
                    {{ $des['bn'] }}
                </font>
            </p> 

            <p style="text-align:justify;"><br>আমি এতদ্বারা ঘোষণা করিতেছি যে, আমার মৃত্যু হইলে বা আমার অবর্তমানে, আমার অনুকূলে জমা ও বিভিন্নখাতে প্রাপ্য টাকা গ্রহণের জন্য আমি নিন্মবর্ণিত ব্যক্তিকে/ব্যক্তিগণকে মনোনয়ন দান করিতেছি এবং নির্দেশ দিচ্ছি যে, উক্ত টাকা নিম্নবর্ণিত পদ্ধতিতে মনোনীত ব্যাক্তিদের মধ্যে বন্টন করিতে হইবেঃ</p>

            <table style="font-size:11px; border-collapse: collapse;" align="center" width="100%" border="1" cellspacing="0" cellpadding="4">
                <thead>
                    <tr>
                        <td width="45%" align="center" style="line-height: 15px;">
                            মনোনীত ব্যক্তি বা ব্যক্তিদের নাম, ঠিকানা ও ছবি <br/>
                            (নমিনীর ছবি ও স্বাক্ষর কর্মকর্তা/কর্মচারী/শ্রমিক কর্তৃক সত্যায়িত) <br/>
                            এন আই ডি নং  
                        </td>
                        <td width="15%" align="center">সদস্যদের সহিত মনোনীত ব্যক্তিদের সম্পর্ক</td>
                        <td width="10%" align="center">বয়স</td>
                        <td width="30%" align="center" colspan="2">প্রত্যেক মনোনীত ব্যক্তিকে দেয় অংশ</td>
                    </tr>
                    <tr>
                        <td align="center">(১)</td>
                        <td align="center">(২)</td>
                        <td align="center">(৩)</td>
                        <td align="center" colspan="2">(৪)</td>
                    </tr>
                    <tr>
                        <td align="center"  rowspan="2" style="height: 70px"></td>
                        <td align="center"  rowspan="2"></td>
                        <td align="center"  rowspan="2"></td>
                        <td align="center" width="15%">জমাখাত</td>
                        <td align="center" width="15%">অংশ</td> 
                    </tr> 
                    <tr>
                        <td align="center">বকেয়া মজুরি </td>
                        <td align="center"></td> 
                    </tr>
                    <tr>
                        <td align="center" rowspan="2" style="height: 70px"></td>
                        <td align="center" rowspan="2"></td>
                        <td align="center" rowspan="2"></td>
                        <td align="center">প্রভিডেন্ট ফান্ড </td>
                        <td align="center"></td> 
                    </tr>
                    <tr>
                        <td align="center">বীমা</td>
                        <td align="center"></td> 
                    </tr>
                    <tr>
                        <td align="center" rowspan="2" style="height: 70px"></td>
                        <td align="center" rowspan="2"></td>
                        <td align="center" rowspan="2"></td>
                        <td align="center">দুর্ঘটনার ক্ষতিপূরণ</td>
                        <td align="center"></td> 
                    </tr>
                    <tr>
                        <td align="center">লভ্যাংশ</td>
                        <td align="center"></td> 
                    </tr>
                    <tr>
                        <td align="center" style="height: 70px;"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center">অন্যান্য</td>
                        <td align="center"></td> 
                    </tr> 
                </thead>
                <tbody>
                    <tr></tr>
                </tbody>
            </table>

            <p style="margin-top:15px;text-align:justify;">প্রত্যয়ন করিতেছি যে, আমার উপস্থিতিতে জনাব/জনাবা <font style="width:160px;border-bottom:1px dotted #999; display:inline-block"></font> লিপিবদ্ধ বিবরণসমূহ পাঠ করিবার পর উক্ত ঘোষণা সাক্ষর করিয়াছেন। </p>

            <div style="display: flex;justify-content: space-between;">
                <div style="width: 50%">
                    <p style="text-align:left;">
                        <br><br>
                        <font style="width:100%;border-top:1px dotted #999; display:inline-block;padding-top:10px">তারিখ সহ মনোনীত ব্যক্তিগণের স্বাক্ষর অথবা টিপসই <br/>
                        (কর্মকর্তা/কর্মচারী//শ্রমিক কর্তৃক সত্যায়িত ছবি)</font> <br>
                        <div style="width:120px;height: 120px;border:1px solid;"></div> 
                    </p> 
                    
                </div>
                <div style="width: 50%">
                    <p style="text-align:right;">
                        <br>
                        <font style="width:70%;border-bottom:1px dotted #999; display:inline-block">&nbsp;</font><br/><br/>
                        <font style="width:70%;display:inline-block;text-align:left">মনোনয়ন প্রদানকারী কর্মকর্তা/কর্মচারী/শ্রমিকের স্বাক্ষর, টিপসই ও তারিখ</font>
                    </p>
                    <p style="text-align:right;">
                        <br><br>
                        <font style="width:70%;border-bottom:1px dotted #999; display:inline-block">&nbsp;</font><br/><br/>
                        <font style="width:70%;display:inline-block;text-align:left">মালিকের বা প্রধিকারপ্রাপ্ত কর্মকর্তার স্বাক্ষর ও তারিখ</font><br/>
                    </p>
                    
                </div>
            </div>
      
        
		</div>
		
		@endforeach
	</div>
</div>   