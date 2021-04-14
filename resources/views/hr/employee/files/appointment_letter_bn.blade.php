<div class="row justify-content-center">
	<div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        
    ?>
	<div id="print-area" class="col-sm-9">
		<style type="text/css">
				.mb-2 span {
				    width: 160px;
				    display: inline-block;
				}
			
		</style>
		<style type="text/css" media="print">
			.bn-form-output{padding:36pt 36pt }
		</style>

		@foreach($employees as $key => $emp)
        @php $date = str_replace($en, $bn, $emp->as_doj); @endphp
		<div id="jc-{{$emp->associate_id}}" class="bn-form-output" >

			@php
            	$des['bn'] = '';
            	$des['en'] = '';
            	$des['grade'] = '';
            	$un['name'] = '';
            	$un['address'] = '';
                $un['signature'] = '';
            	if(isset($designation[$emp->as_designation_id])){
            		$des['bn'] = $designation[$emp->as_designation_id]['hr_designation_name_bn'];
            		$des['en'] = $designation[$emp->as_designation_id]['hr_designation_name'];
            		$des['grade'] = $designation[$emp->as_designation_id]['hr_designation_grade'];
            	}
            	if(isset($unit[$emp->as_unit_id])){
            		$un['name'] = $unit[$emp->as_unit_id]['hr_unit_name_bn'];
                    $un['address'] = $unit[$emp->as_unit_id]['hr_unit_address_bn'];
            		$un['signature'] = $unit[$emp->as_unit_id]['hr_unit_authorized_signature'];

            	}

            @endphp

            <center><b style="font-size: 16px;">{{ $un['name'] }} </b></center>
            <center><u> {{$un['address']}} </u> </center>
            <br><br><br>
            <p>তারিখঃ&nbsp; {{ $date }} ইং</p>
            <p>
            	@if($emp->as_gender == 'Female')
            		জনাবাঃ
            	@else
            		জনাবঃ
            	@endif

            	{{ (!empty($emp->hr_bn_associate_name)?$emp->hr_bn_associate_name:null) }}</p>
            
            <p>পিতা/স্বামীর নামঃ   {{ (!empty($emp->hr_bn_father_name)?$emp->hr_bn_father_name:null) }}/{{ (!empty($emp->hr_bn_spouse_name)?$emp->hr_bn_spouse_name:null) }}</p>
            <p>মাতার নামঃ {{ (!empty($emp->hr_bn_mother_name)?$emp->hr_bn_mother_name:null) }}</p>
            <p style="margin-top:0 !important"><b>ঠিকানাঃ স্থায়ী ঠিকানাঃ</b></p>
            <table width="500" style="margin-left:50px;font-size: 12px;">
                <tr>
                    <td>গ্রামঃ {{ (!empty($emp->hr_bn_permanent_village)?$emp->hr_bn_permanent_village:null) }}</td>
                    <td>ডাকঘরঃ {{ (!empty($emp->hr_bn_permanent_po)?$emp->hr_bn_permanent_po:null) }}</td>
                </tr>
                <tr>
                    <td>থানাঃ   {{ (!empty($emp->permanent_upazilla_bn)?$emp->permanent_upazilla_bn:null) }}</td>
                    <td>জেলাঃ {{ (!empty($emp->permanent_district_bn)?$emp->permanent_district_bn:null) }}</td>
                    
                </tr>
            </table>
            <p style="margin-top:0 !important"><b>অস্থায়ী/বর্তমান ঠিকানাঃ</b></p>
            <table width="500" style="margin-left:50px;font-size: 12px;">
                <tr>
                    <td>গ্রামঃ {{ (!empty($emp->hr_bn_present_road)?$emp->hr_bn_present_road:null) }}</td>
                    <td>ডাকঘরঃ {{ (!empty($emp->hr_bn_present_po)?$emp->hr_bn_present_po:null) }}</td>
                </tr>
                <tr>
                    <td>থানাঃ   {{ (!empty($emp->present_upazilla_bn)?$emp->present_upazilla_bn:null) }}</td>
                    <td>জেলাঃ {{ (!empty($emp->present_district_bn)?$emp->present_district_bn:null) }}</td>
                    
                </tr>
            </table>
            <br>
            <p><span style="text-decoration: underline;"><strong>বিষয়ঃ- নিয়োগপত্র</strong></span></p>
            <p style="text-align: justify;">কর্তৃপক্ষ অত্যন্ত আনন্দের সহিত জানাচ্ছে যে, আপনাকে নিম্নলিখিত শর্তসাপেক্ষে অত্র কারখানার <b>{{ $des['bn'] }}</b> পদে প্রতি মাসে সর্বসাকুল্যে মোট {{ (!empty($emp->ben_current_salary)?str_replace($en, $bn, $emp->ben_current_salary):null) }} টাকা বেতনে  
            	@if($emp->as_emp_type_id == 3)
            	গ্রেডঃ <span style="display: inline-block;min-width: 10px; text-align: center;"> {{$des['grade']}} </span> 
            	@endif
            নিয়োগ দেওয়ার সিদ্ধান্ত গ্রহণ করিয়াছেন, আপনার পরিচয় পত্র নং(আই.ডি. নং)-<b>{{$emp->associate_id}}</b>  যাহা <b>{{ (!empty($emp->as_doj)?str_replace($en, $bn, $emp->as_doj):null) }}</b> তারিখ হইতে কার্যকরী।</p>
            <p>১। আপনি চাকুরীতে প্রথম ০৩ (তিন) মাস প্রবেশনারী অবস্থায় থাকিবেন এবং উক্ত সময়ের মধ্যে আপনার কর্মদক্ষতা সন্তোষজনক না হইলে আপনার প্রবেশনকাল আরও তিন মাস বর্ধিত করা যেতে পারে। @if($des['grade'] <= 6) প্রবেশনকাল অতিবাহিত হওয়ার পর আপনি সরাসরি স্থায়ী শ্রমিক হিসাবে গণ্য হবেন। @endif</p><br>
            <p>২। <b> বেতনঃ</b></p>
            <table width="600">
                <tr>
                    <td>ক) মূল বেতন( Monthly Basic Pay )</td>
                    <td>: টাকা {{ (!empty($emp->ben_basic)?str_replace($en, $bn, $emp->ben_basic):null) }}/= 
                        @if($emp->as_ot == 1)
                            অতিরিক্ত কর্ম ঘন্টার হার: 
                            @php $ot_pay= ($emp->ben_basic/208)*2; $ot_pay = sprintf('%0.2f', $ot_pay);
                            @endphp 
                        {{str_replace($en, $bn, $ot_pay)}} টাকা
                        @endif
                     </td>
                </tr>
                <tr>
                    <td>খ) বাড়ী ভাড়া(House Rent-50% of Basic Pay)</td>
                    <td>: টাকা {{ (!empty($emp->ben_house_rent)?str_replace($en, $bn, $emp->ben_house_rent):null) }}/=</td>
                </tr>
                <tr>
                    <td>গ) চিকিৎসা ভাতা(Medical Allowance)</td>
                    <td>: টাকা {{ (!empty($emp->ben_medical)?str_replace($en, $bn, $emp->ben_medical):null) }}/=</td>
                </tr>
                <tr>
                    <td>ঘ) খাদ্য ভাতা(Food Allowance)</td>
                    <td>: টাকা {{ (!empty($emp->ben_food)?str_replace($en, $bn, $emp->ben_food):null) }}/=</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid;">ঙ) যাতায়াত ভাতা(Conveyance Allowance)</td>
                    <td style="border-bottom: 1px solid;">: টাকা {{ (!empty($emp->ben_transport)?str_replace($en, $bn, $emp->ben_transport):null) }}/=</td>
                </tr>
                <tr>
                    <td>সর্বমোট বেতন( Monthly Gross Salary )</td>
                    <td>: টাকা {{ (!empty($emp->ben_current_salary)?str_replace($en, $bn, $emp->ben_current_salary):null) }}/=</td>
                </tr>
            </table>
            
            <p style="text-align: center;margin-top: 0 !important;"> বেতন প্রদানঃ প্রতি মাসের বেতন পরবর্তী মাসের সাত কর্ম দিবসের মধ্যে বেতন এবং ওভার টাইম এক সঙ্গে প্রদান করা হয়। </p><br>

            <p>৩। কর্ম ঘন্টাঃ ফ্যাক্টরি সকাল ৮.০০ থেকে শুরু এবং বিকাল ৫.০০ টায় সাধারণ কর্মদিবসের সমাপ্তি এবং এর মধ্যবর্তী সময়ে ০১(এক) ঘণ্টা বিরতি।</p>
            <p style="margin-top: 0">৪। অতিরিক্ত কর্মঘন্টা (ওভার টাইম)ঃ  ইহা মূল বেতনের দ্বিগুন হারে প্রদেয়। ( মূল বেতন/২০৮x২xমোট অতিরিক্ত ঘন্টা)।</p><br>
            <p>৫।<b> ছুটিঃ</b></p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;১। শুক্রবার সাপ্তাহিক ছুটি।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;২। অন্যান্য ছুটি, (যাহা পূর্ণ বেতনে ভোগ করিতে পারিবেন।&nbsp;</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ক) নৈমিত্তিক ছুটিঃ বছরে ১০(দশ) দিন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;খ) অসুস্থতা জনিত ছুটিঃ বছরে ১৪(চৌদ্দ) দিন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;গ) উৎসব ছুটিঃ বছরে সর্বনিম্ন ১১(এগার) দিন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ঘ) অর্জিত ছুটিঃ- ০১(এক) বৎসর অতিবাহিত হওয়ার পর প্রতি ১৮ কর্মদিবসের জন্য একদিন করে বার্ষিক ছুটি ভোগ করিতে পারিবেন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ঙ) মাতৃকল্যাণ ছুটিঃ- কোন মহিলা শ্রমিক যদি অত্র প্রতিষ্ঠানে একাধিক্রমে ০৬(ছয়) মাস চাকুরী করেন তাহলে তিনি উক্ত ছুটি ভোগ করিতে পারিবেন। ২০০৬ সালের মাতৃকল্যাণ আইনের ধারা অনুযায়ী মোট ১৬ সপ্তাহ বা (৫৬+৫৬)=১১২ দিন মাতৃত্বকালীন ছুটি (আইনানুগ ও নগদে) ভোগ করিতে পারিবেন।</p>

            <p>৬। <b> সুবিধা</b></p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ক) মূল মজুরীর ৫% হারে বাৎসরিক ভিত্তিতে মজুরী বৃদ্ধি পাইবে।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;খ) যাহারা নিরবিচ্ছিন্নভাবে ১(এক) বৎসর চাকরি পূর্ণ করিয়াছেন তাহাদেরকে বৎসরে দুইটি উৎসব ভাতা প্রদান করা হইবে(প্রতিটি উৎসব ভাতা মূল বেতনের সমান)।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;গ) মাসের প্রতিটি কর্মদিনের সঠিক সময়ে ফ্যাক্টরিতে উপস্থিত হলে
            {{-- custom correction for unit wise attendance bonus --}}
            @if($emp->as_unit_id == 2)
             প্রথম মাস ৪০০/= টাকা এবং একইভাবে পরবর্তী মাসে উপস্থিত থাকলে ৫০০/=
            @else  ৫০০/= @endif
         টাকা হাজিরা বোনাস প্রদান করা হয়। (ইহা আইনানুনাগ কোন পাওনা নয়। ইহা বেতনের বাহিরের একটি অংশ)</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ঘ) বিনা খরচে ডাক্তার এবং নার্সের মাধ্যমে চিকিৎসা সুবিধা প্রদান করা হয়। </p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ঙ) শ্রমিক/কর্মচারীর জন্য গ্রুপ ইন্স্যুরেন্স এর ব্যবস্থা আছে। </p><br>
            <p>৭। <b> চাকুরি ছাড়ার নিয়মঃ</b></p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ক) চাকুরী ছাড়তে হলে বাংলাদেশ শ্রম আইন, (২০০৬) অনুসারে ২৭(১) ধারা মোতাবেক চাকুরী ছাড়ার ২ মাস(৬০ দিন) আগে কর্তৃপক্ষকে লিখিত নোটিশ প্রদান করতে হবে, অন্যথায় প্রদেয় নোটিশের পরিবর্তে নোটিশ মেয়াদের জন্য মূল মজুরীর সমপরিমাণ অর্থ মালিককে প্রদান করিয়া ইহা করিতে পারিবেন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;খ) অসুস্থতার কারণে চাকুরী ছেড়ে দিতে হলে মেডিকেল সার্টিফিকেট দাখিল করতে হবে।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;গ) চাকুরী ছাড়ার পর কোম্পানীর প্রদত্ত মালামাল অর্থাৎ পরিচয় পত্র, লকারের চাবি, ড্রেস, কাটার, টেপ ইত্যাদি মানব সম্পদ বিভাগে জমা প্রদান করতে হবে।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ঘ) চাকুরী স্থায়ী হইবার পর কর্তৃপক্ষ আপনার চাকুরী অবসান করিতে চাহিলে ১২০(একশত বিশ) দিনের লিখিত নোটিশ অথবা ১২০(একশত বিশ) দিনের বেতন প্রদান করিবেন।</p>

            <br>
            <p>৮। প্রবেশনারী থাকাকালীন সময়ে কোম্পানী যে কোন সময় কোন প্রকার কারণ দর্শানো ব্যতিরেকে বিনা নোটিশে আপনার চাকুরী অবসান করিতে পারিবেন অথবা আপনিও চাকুরী থেকে স্বেচ্ছায় ইস্তফা দিতে পারিবেন।</p>
            <br>
            <p>৯। <b>  বাংলাদেশের শ্রম আইন, ২০০৬ অনুসারে ধারা ২৩(৪) মোতাবেক নিম্নলিখিত কাজসমূহ "অসদাচরণ" বলিয়া বিবেচিত হইবে-</b></p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(ক) উপরস্থের কোন আইন সংগত বা যুক্তি সংগত আদেশ মানার ক্ষেত্রে এককভাবে বা অন্যের সঙ্গে সংঘবদ্ধ হইয়া ইচ্ছাকৃত ভাবে অবাধ্যতা।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(খ) মালিকের ব্যবসা বা সম্পত্তি সম্পর্কে চুরি আত্মসাৎ, প্রতারণা বা অসাধুতা।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(গ) মালিকের অধীন তাহার বা অন্য কোন শ্রমিকের
             চাকুরী সংক্রান্ত ব্যাপারে ঘুষ গ্রহণ বা প্রদান।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(ঘ) বিনা ছুটিতে অভ্যাসগত অনুপস্থিতি অথবা ছুটি না নিয়া একসঙ্গে ১০ (দশ) দিনের অধিক সময় অনুপস্থিতি।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(ঙ) অভ্যাসগত বিলম্ব।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(চ) প্রতিষ্ঠানে প্রযোজ্য কোন আইন, বিধি বা প্রবিধানের অভ্যাসগত লঙ্ঘন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(ছ) প্রতিষ্ঠানে উচ্ছৃংখল বা দাংগা হাংগামা, অগ্নিসংযোগ বা ভাংচুর।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(জ) কাজে কর্মে অভ্যাসগত গাফিলতি।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(ঝ) প্রধান পরিদর্শক কর্তৃক অনুমোদিত চাকুরী সংক্রান্ত শৃংখল বা আচরণসহ, যে কোন বিধির অভ্যাসগত লংঘন।</p>
            <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(ঞ) মালিকের অফিসিয়াল রেকর্ডের রদবদল, জালকরণ, অন্যায় পরিবর্তন, উহার ক্ষতিকরন বা উহা হারাইয়া ফেলা।</p>

            <p>আপনি যদি কখনো কোনরুপ অসদাচরণের অপরাধে দোষী প্রমাণিত হন তবে কর্তৃপক্ষ আপনার বিরুদ্ধে আইনগত শাস্তিমূলক ব্যবস্থা গ্রহণ করিতে পারবে। </p> <br>
            <p>১০।  আপনার চাকুরী কোম্পানী কর্তৃক জারিকৃত বিধি-বিধান ও বাংলাদেশের প্রচলিত শ্রম আইন দ্বারা পরিচালিত হইবে।</p>
            <p>১১।  কর্তৃপক্ষ আপনাকে প্রয়োজনবোধে এই প্রতিষ্ঠানের যে কোন বিভাগে অথবা বাংলাদেশে অবস্থিত যে কোন কারখানায়/অফিসে বদলি করিতে পারিবেন।</p>
            <p>১২। গোপনীয়তা রক্ষার নীতি <b>(Non-Disclosure Policy)</b>ঃ প্রতিষ্ঠানের স্বার্থে সকল প্রকার তথ্য গোপন রাখিতে হইবে।</p>
            <p>১৩।  কোম্পানীর যাবতীয় নিয়ম-কানুন পরিবর্তনযোগ্য ( যাহা দেশের প্রচলিত আইনের পরিপন্থি নহে) এবং আপনি পরিবর্তীত নিয়ম কানুন সর্বদা মানিয়া চলিতে বাধ্য থাকিবেন। </p><br><br><br><br><br><br>
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 50%">
                    <p>ধন্যবাদান্তে</p>
                    <p>সংশ্লিষ্ট ব্যবস্থাপক</p>
                </div>
                <div style="width: 50%;text-align: right;position: relative;">
                    @if($emp->as_unit_id == 2)
                        <img style="height: 40px;position: absolute;top: -35px;" src="{{asset($un['signature'])}}">
                    @endif
                    ----------------<br>

                    কারখানা কর্তৃপক্ষ

                </div>
            </div>
            
            <br><br>
            <p>&nbsp; &nbsp;অনুলিপিঃ</p>
            <p>&nbsp; &nbsp;১। হিসাব বিভাগ।</p>
            <p>&nbsp; &nbsp;২। ব্যক্তিগত নথি।</p>
            <p>আমি অত্র নিয়োগপত্র পাঠ করিয়া এবং ইহাতে বর্ণিত শর্তাদি সম্পূর্ণরুপে অবগত হইয়া এই নিয়োগপত্রের ১ কপি গ্রহণ করিয়া স্বাক্ষর করিলাম।</p>
            <p>&nbsp;</p><br><br><br><br>
            <p style="text-align: right;">&nbsp;শ্রমিকের স্বক্ষর&nbsp; &nbsp; &nbsp; &nbsp;</p>
            </p>
        </div>

        
		<div class="page-break"></div>
		@endforeach
	</div>
</div>   