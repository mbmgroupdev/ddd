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
				    display: inline-block;
				}
				label {
                    display: initial;
                    vertical-align: top;
                }
                td{
                    font-size: 10px;
                }
                input[type=radio], input[type=checkbox] {
				    vertical-align: middle;
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
            	$des['grade'] = '';
            	$un['name'] = '';
            	$un['address'] = '';
            	$sec = isset($section[$emp->as_section_id])?$section[$emp->as_section_id]['hr_section_name_bn']:'';
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
                
                <center><b style="font-size: 16px;">{{ $un['name'] }}</b></center>
                <center>{{ $un['address'] }}</center>
                <br>
                <center><u style="font-weight: bold;" >ব্যাক্তিগত তথ্য ও চাকুরি যাচাই ফরম</u></center>
                <center><u style="font-size: 10px;" >(Background Information and Job Verification Form)</u></center>
                <p style="font-weight: bold;">ক.  ব্যক্তিগত তথ্য (Personal Information):</p>
                <p style="display:flex;justify-content: space-between;"> 
                	<font style="width:10%;">১. আই.ডিঃ</font>
                	<font style="width:30%;border-bottom:1px dotted #999;font-weight: bold;font-size:12px;text-align: center;"> {{ (!empty($emp->associate_id)?$emp->associate_id:null) }}
                    </font>
                    <font style="width:10%;">পদবীঃ</font>
                    <font style="width:50%;border-bottom:1px dotted #999; text-align: center;">&nbsp; {{ $des['bn'] }}
                    </font>
                </p>
                <p style="display:flex;justify-content: space-between;">
                	<font style="width:10%;"></font>
                    <font style="width:10%;">সেকশনঃ</font>
                    <font style="width:40%;border-bottom:1px dotted #999;text-align: center;">&nbsp; {{ $sec}}
                    </font>
                    <font style="width:15%;">যোগদানের তারিখঃ</font>
                    <font style="width:25%;border-bottom:1px dotted #999;text-align: center;">&nbsp;{{ (!empty($emp->as_doj)?str_replace($en,$bn, date("d-m-Y", strtotime($emp->as_doj))):null) }}
                    </font>
                </p>
                <p style="display: flex;justify-content: space-between;"> 
                	<font >২. পূর্ণ নাম (বাংলায়)</font>
                	<font style="width:75%;border-bottom:1px dotted #999;">:&nbsp; {{ (!empty($emp->hr_bn_associate_name)?$emp->hr_bn_associate_name:null) }}
                    </font>
                </p>
                <p style="display: flex;justify-content: space-between;"> 
                	<font >৩. পূর্ণ নাম (ইংরেজীতে)</font>
                	<font style="width:75%;border-bottom:1px dotted #999;">:&nbsp;{{ (!empty($emp->as_name)?$emp->as_name:null) }}
                    </font>
                </p>
                <p style="display:flex;justify-content: space-between;">
                    <font >৪. স্বামী/পিতার নাম
                    </font>
                    <font style="width:36%;border-bottom:1px dotted #999; display:inline-block">:&nbsp;{{ (!empty($emp->hr_bn_father_name)?$emp->hr_bn_father_name:null) }}/{{ (!empty($emp->hr_bn_spouse_name)?$emp->hr_bn_spouse_name:null) }}
                    </font>৫. মাতার নাম
                    <font style="width:36%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; {{ (!empty($emp->hr_bn_mother_name)?$emp->hr_bn_mother_name:null) }}
                    </font>
                </p>

                <div style="display: flex;min-height: 15px;"> 
                    <font style="width:14%;display:inline-block">৬ । স্থায়ী ঠিকানাঃ
                    </font>
                    <div style="width:86%;display: flex;justify-content: space-between;">
                    বাড়ীর নামঃ 
                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp;
                    </font>গ্রামঃ 
                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->hr_bn_permanent_village)?$emp->hr_bn_permanent_village:null) }}
                    </font>ডাকঘরঃ 
                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->hr_bn_permanent_po)?$emp->hr_bn_permanent_po:null) }}
                    </font>
                    </div>
                </div>
                <div style="display: flex;min-height: 15px;"> 
                    <font style="width:14%;display:inline-block">&nbsp;</font>
                    <div style="width:86%;display: flex;justify-content: space-between;">
                    থানাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->permanent_upazilla_bn)?$emp->permanent_upazilla_bn:null) }}
                    </font>
                    জেলাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->permanent_district_bn)?$emp->permanent_district_bn:null) }}
                    </font>
                    </div>
                </div>

                <div style="display: flex;min-height: 15px;"> 
                    <font style="width:14%;display:inline-block">৭ । বর্তমান ঠিকানাঃ
                    </font>
                    <div style="width:86%;display: flex;justify-content: space-between;">
	                    নামঃ
	                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp;
	                    </font>বাসা নং
	                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->hr_bn_present_house)?$emp->hr_bn_present_house:null) }}
	                    </font>রাস্তা নং
	                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->hr_bn_present_road)?$emp->hr_bn_present_road:null) }}
	                    </font> 
	                </div>
                </div>
                <div style="display: flex;min-height: 15px;"> 
                    <font style="width:14%;display:inline-block">&nbsp;</font>
                    <div style="width:86%;display: flex;justify-content: space-between;">
                        ওয়ার্ড<font style="width:18%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp;
                        </font>
                        ডাকঘরঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->hr_bn_present_po)?$emp->hr_bn_present_po:null) }}
                        </font>
                        থানাঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->present_upazilla_bn)?$emp->present_upazilla_bn:null) }}
                        </font>
                        জেলাঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block;text-align: center;">&nbsp; {{ (!empty($emp->present_district_bn)?$emp->present_district_bn:null) }}
                        </font>
                    </div>
                </div> 
                <br>
                <p> <font style="width:50%;display:inline-block">জাতীয় পরিচয়পত্র নাম্বার <font style="font-size: 8px;">(কোনো ঠিকানা পরিবর্তিত হলে কর্তৃপক্ষকে জানাতে হবে)</font></font><font style="width:50%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; {{ eng_to_bn(!empty($emp->emp_adv_info_nid)?$emp->emp_adv_info_nid:'') }}
                    </font>
                </p>
                <p> <font style="width:50%;display:inline-block">বাড়ীর মালিকের নাম ও মোবাইল নাম্বার<font style="font-size: 8px;"></font></font><font style="width:50%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; </font>
                </p>
                <p> <font style="width:50%;display:inline-block">ইউনিয়ন/পৌরসভা/সিটি কর্পোরেশন চেয়ারম্যান/মেম্বার-এর নাম ও মোবাইল নাম্বার<font style="font-size: 8px;"></font></font><font style="width:50%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; </font>
                </p>
                 <p style="font-weight: bold;">খ. সঠিকতা নিরূপণ (Determining Accuracy):</p>
                </p>
                <div style="width:100%;display: flex;">
                    <strong style="width:100px;padding-left: 15px;">পদ্ধতি - </strong>
                    <div>
                        
                        <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike">
                        <label for="vehicle1">&nbsp;&nbsp;স্থানীয় বাড়ির মালিকের সাথে যোগাযোগ করে</label> &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="vehicle2" name="vehicle2" value="Bike">
                        <label for="vehicle2">&nbsp;&nbsp;কারখানা কর্তৃক সরেজমিনে পরিদর্শন</label> <br> 
                    
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="Bike">
                        <label for="vehicle3">&nbsp;&nbsp;ইউনিয়ন/পৌরসভা/সিটি কর্পোরেশন চেয়ারম্যান/মেম্বার-এর সাথে যোগাযোগ করে ।</label>
                    </div>

                </div>
                <table style="margin-left: 15px;">
                    <tr>
                        <td style="width:300px;">আপনি তাকে ব্যাক্তিগতভাবে চিনেন কিনা? </td>
                        <td style="width:50px;">
                            <input type="checkbox" id="t1" name="t1" value="Bike">
                            <label for="t1">&nbsp;&nbsp;হ্যাঁ</label>
                        </td>
                        <td style="width:50px;">
                            <input type="checkbox" id="t3" name="t3" value="Bike">
                            <label for="t3">&nbsp;&nbsp;না</label>
                        </td>
                    </tr>
                    <tr>
                        <td>সে আপনার ইউনিয়ন/ওয়ার্ডের স্থায়ী বাসিন্দা কিনা? </td>
                        <td>
                            <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;হ্যাঁ</label>
                        </td>
                        <td>
                            <input type="checkbox" id="t4" name="t4" value="Bike">
                            <label for="t4">&nbsp;&nbsp;না</label>
                        </td>
                    </tr>
                    <tr>
                        <td>তার চরিত্রগত কোন সমস্যা আছে কিনা? </td>
                        <td>
                            <input type="checkbox" id="t5" name="t5" value="Bike">
                            <label for="t5">&nbsp;&nbsp;হ্যাঁ</label>
                        </td>
                        <td>
                            <input type="checkbox" id="t6" name="t6" value="Bike">
                            <label for="t6">&nbsp;&nbsp;না</label>
                        </td>
                    </tr>
                    <tr>
                        <td>কোন ফৌজদারি মামলার আসামী কিনা? </td>
                        <td>
                            <input type="checkbox" id="t7" name="t7" value="Bike">
                            <label for="t7">&nbsp;&nbsp;হ্যাঁ</label>
                        </td>
                        <td>
                            <input type="checkbox" id="t8" name="t8" value="Bike">
                            <label for="t8">&nbsp;&nbsp;না</label>
                        </td>
                    </tr>

                </table>

                <p style="font-weight: bold;">গ. পূর্বের চাকুরী সম্পর্কিত তথ্য(যদি থাকে) (Information of Previous Employement):
                </p>
                

                <p style="display: flex;justify-content: space-between;">&nbsp;&nbsp;&nbsp;&nbsp;প্রতিষ্ঠানের নামঃ
                    <font style="width:85%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>
                </p>
                <p style="display: flex;justify-content: space-between;">&nbsp;&nbsp;&nbsp;&nbsp;পদবীঃ
                    <font style="width:85%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>
                </p>

                

                <p style="display: flex;justify-content: space-between;">&nbsp;&nbsp;&nbsp;&nbsp;ঠিকানাঃ
                    <font style="width:55%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>ফোন/মোবাইলঃ
                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>
                </p>
                <div style="display: flex;margin-top: 5px;">
                    <div >
                    &nbsp;&nbsp;&nbsp;&nbsp;প্রতিষ্ঠান ত্যাগের কারনঃ
                     </div>
                    <div style="padding-left: 10px;">
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;পারিবারিক সমস্যা</label>&nbsp;&nbsp;
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;ব্যাক্তিগত সমস্যা</label>&nbsp;&nbsp;
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;অসুস্থতাজনিত সমস্যা</label>&nbsp;&nbsp;
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;অন্যান্য</label>
                    </div>
                </div>
                <p style="display: flex;justify-content: space-between;">&nbsp;&nbsp;&nbsp;&nbsp;সংশ্লিষ্ট প্রতিষ্ঠানের একজন পরিচিত ব্যক্তির নাম
                    <font style="width:60%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>
                </p>
                <br>
                <p style="font-weight: bold;">গ. পূর্বে এই প্রতিষ্ঠানে কর্মরত ছিলেন কিনা? হ্যাঁ/ না (যদি হ্যাঁ হয় বিবরণ লিখুন)(Information of Previous Employement in this Company):
                </p>

                

                <p style="display: flex;justify-content: space-between;">&nbsp;&nbsp;&nbsp;&nbsp;পদবীঃ
                    <font style="width:45%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>আইডি নং
                    <font style="width:35%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>
                </p>
                <div style="display: flex;margin-top: 5px;">
                    <div >
                    &nbsp;&nbsp;&nbsp;&nbsp;প্রতিষ্ঠান ত্যাগের কারনঃ
                     </div>
                    <div style="padding-left: 10px;">
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;পারিবারিক সমস্যা</label>&nbsp;&nbsp;
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;ব্যাক্তিগত সমস্যা</label>&nbsp;&nbsp;
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;অসুস্থতাজনিত সমস্যা</label>&nbsp;&nbsp;
                        <input type="checkbox" id="t2" name="t2" value="Bike">
                            <label for="t2">&nbsp;&nbsp;অন্যান্য</label>
                    </div>
                </div>

                <p style="display: flex;justify-content: space-between;">&nbsp;&nbsp;&nbsp;&nbsp;সুপারিশকারীর নাম, পদবী, সেকশন ও মোবাইল নং (যদি থাকে)
                    <font style="width:60%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                    </font>
                </p>
                <br>
                <p><font style="font-weight: bold;">যাচাইকারী অফিসারের মন্তব্যঃ </font>উপরোক্ত কর্মকর্তা/কর্মচারীর আবেদন পত্রে প্রদত্ত তথ্য সমূহ, রেফারেন্স হিসাবে উল্লেখিত ব্যক্তিদ্বয়ের মাধ্যমে যথাযথ ভাবে যাচাই করিয়া তথ্যের সত্যতা পাওয়া গিয়েছে/ পাওয়া যায় নাই।</p>
                <br>
                <br>

                <table border="0" style="width: 100%;">
                    <tr>
                        <td style="text-align: left;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <b>কর্মীর স্বাক্ষর</b>
                        </td>
                        <td style="text-align: center;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <b>তদন্তকারীর স্বাক্ষর</b>
                        </td>
                        <td style="text-align: right;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <b>মানবসম্পদ বিভাগ</b>
                        </td>
                    </tr>
                </table>
		</div>
		<div class="page-break"></div>
		@endforeach
	</div>
</div>   