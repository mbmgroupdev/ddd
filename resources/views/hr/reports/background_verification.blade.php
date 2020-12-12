@extends('hr.layout')
@section('title', 'Background Verification')
@section('main-content')
@push('css')
<style type="text/css">

    @media only screen and (max-width: 771px) {
        .background_field{width: 100% !important;}
}

@media only screen and (max-width: 771px) {
        .background_div{width: 100% !important;}
}
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li class="active"> Background Verification</li>
            </ul>
        </div>

        <div class="page-content"> 
            <?php $type='bg_verification'; ?>

            <div class="row">
                 @include('inc/message')
                <div class="col-12">
                    <form class="" role="form" method="post" action="{{ url('hr/recruitment/background-verification') }}" enctype="multipart/form-data"> 

                        @csrf
                        
                        <div class="panel">
                            <div class="panel-heading">
                                <h6>Background Verification</h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    
                                    <div class="col-md-offset-2 col-4">
                                        
                                        <div class="form-group has-float-label has-required select-search-group">
                                            
                                            {{ Form::select('associate_id', [Request::get('associate_id') => Request::get('associate_id')], Request::get('associate_id'),['placeholder'=>'Select Associate\'s ID', 'data-validation'=> 'required','id'=>'associate_id',  'class'=> 'associates no-select col-xs-12', 'required' => 'required']) }} 
                                            <label  for="associate_id"> Associate's ID </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                         <a id="generate" class="btn btn-primary" href="{{ url('hr/recruitment/background-verification?associate_id=%ASSOCIATE_ID%') }}">Generate</a>
                                         @if(!empty(Request::get('associate_id')))
                                         <button type="button" onclick="printMe('printArea')" title="Print" class="btn btn-warning">
                                                <i class="fa fa-print"></i> 
                                        </button> 
                                        
                                        {{-- <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" title="PDF" class="btn btn-danger">
                                            <i class="fa fa-file-pdf-o"> </i> 
                                        </a> --}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty(Request::get('associate_id')) && $info != null)
                        <div class="panel p-30">
                            {{-- <div class="col-xs-2"></div> --}}
                            <div class="col-xs-12 background_div" id="printable" style="font-size: 11px;">
                                <div class="tinyMceLetter" name="printArea" id="printArea">
                                    <style type="text/css" media="all">
                                        label {
                                            display: initial;
                                            vertical-align: top;
                                        }
                                        td{
                                            font-size: 10px;
                                        }
                                    </style>
                                    <?php
                                    date_default_timezone_set('Asia/Dhaka');
                                    $en = array('0','1','2','3','4','5','6','7','8','9');
                                    $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
                                    $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
                                    ?>
                                    <center><b style="font-size: 24px;">{{ $info->hr_unit_name_bn }}</b></center>
                                    <center>{{ $info->hr_unit_address_bn }}</center>
                                    <br>
                                    <center><u style="font-size: 14px; font-weight: bold;" >ব্যাক্তিগত তথ্য ও চাকুরি যাচাই ফরম</u></center>
                                    <center><u style="font-size: 10px;" >(Background Information and Job Verification Form)</u></center>
                                    <p style="font-size: 12px; font-weight: bold;">ক.  ব্যক্তিগত তথ্য (Personal Information):</p>
                                    <p style="display:flex;justify-content: space-between;"> <font style="display:inline-block">১. আই.ডি</font><font style="width:12%;border-bottom:1px dotted #999; display:inline-block;font-weight: bold;font-size:13px;">:&nbsp; {{ (!empty($info->associate_id)?$info->associate_id:null) }}
                                        </font>পদবীঃ<font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_designation_name_bn)?$info->hr_designation_name_bn:null) }}
                                        </font>সেকশনঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_section_name_bn)?$info->hr_section_name_bn:null) }}
                                        </font>যোগদানের তারিখঃ<font style="width:11%;border-bottom:1px dotted #999; display:inline-block">&nbsp;{{ (!empty($info->as_doj)?str_replace($en,$bn, date("d-m-Y", strtotime($info->as_doj))):null) }}
                                        </font>
                                    </p>
                                    <p> <font style="width:14%;display:inline-block">২. পূর্ণ নাম (বাংলায়)</font><font style="width:86%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; {{ (!empty($info->hr_bn_associate_name)?$info->hr_bn_associate_name:null) }}
                                        </font>
                                    </p>
                                    <p> <font style="width:14%;display:inline-block">৩. পূর্ণ নাম (ইংরেজীতে)</font><font style="width:86%;border-bottom:1px dotted #999; display:inline-block">:&nbsp;{{ (!empty($info->as_name)?$info->as_name:null) }}
                                        </font>
                                    </p>
                                    <p style="display:flex;justify-content: space-between;">
                                        <font >৪. স্বামী/পিতার নাম
                                        </font>
                                        <font style="width:36%;border-bottom:1px dotted #999; display:inline-block">:&nbsp;{{ (!empty($info->hr_bn_father_name)?$info->hr_bn_father_name:null) }}/{{ (!empty($info->hr_bn_spouse_name)?$info->hr_bn_spouse_name:null) }}
                                        </font>৫. মাতার নাম
                                        <font style="width:36%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; {{ (!empty($info->hr_bn_mother_name)?$info->hr_bn_mother_name:null) }}
                                        </font>
                                    </p>

                                    <div style="display: flex;min-height: 15px;"> 
                                        <font style="width:14%;display:inline-block">৬ । স্থায়ী ঠিকানাঃ
                                        </font>
                                        <div style="width:86%;display: flex;justify-content: space-between;">
                                        বাড়ীর নামঃ 
                                        <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                        </font>গ্রামঃ 
                                        <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_bn_permanent_village)?$info->hr_bn_permanent_village:null) }}
                                        </font>ডাকঘরঃ 
                                        <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_bn_permanent_po)?$info->hr_bn_permanent_po:null) }}
                                        </font>
                                        </div>
                                    </div>
                                    <div style="display: flex;min-height: 15px;"> 
                                        <font style="width:14%;display:inline-block">&nbsp;</font>
                                        <div style="width:86%;display: flex;justify-content: space-between;">
                                        থানাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->permanent_upazilla_bn)?$info->permanent_upazilla_bn:null) }}
                                        </font>
                                        জেলাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->permanent_district_bn)?$info->permanent_district_bn:null) }}
                                        </font>
                                        </div>
                                    </div>

                                    <div style="display: flex;min-height: 15px;"> 
                                        <font style="width:14%;display:inline-block">৭ । বর্তমান ঠিকানাঃ
                                        </font>
                                        <div style="width:86%;display: flex;justify-content: space-between;">
                                        নামঃ
                                        <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                        </font>বাসা নং
                                        <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_bn_present_house)?$info->hr_bn_present_house:null) }}
                                        </font>রাস্তা নং
                                        <font style="width:25%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_bn_present_road)?$info->hr_bn_present_road:null) }}
                                        </font> 
                                    </div>
                                    </div>
                                    <div style="display: flex;min-height: 15px;"> 
                                        <font style="width:14%;display:inline-block">&nbsp;</font>
                                        <div style="width:86%;display: flex;justify-content: space-between;">
                                            ওয়ার্ড<font style="width:18%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                            </font>
                                            ডাকঘরঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->hr_bn_present_po)?$info->hr_bn_present_po:null) }}
                                            </font>
                                            থানাঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->present_upazilla_bn)?$info->present_upazilla_bn:null) }}
                                            </font>
                                            জেলাঃ<font style="width:18%;border-bottom:1px dotted #999; display:inline-block">&nbsp; {{ (!empty($info->present_district_bn)?$info->present_district_bn:null) }}
                                            </font>
                                        </div>
                                    </div> 
                                    <br>
                                    <p> <font style="width:50%;display:inline-block">জাতীয় পরিচয়পত্র নাম্বার <font style="font-size: 8px;">(কোনো ঠিকানা পরিবর্তিত হলে কর্তৃপক্ষকে জানাতে হবে)</font></font><font style="width:50%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; {{ eng_to_bn(!empty($info->emp_adv_info_nid)?$info->emp_adv_info_nid:'') }}
                                        </font>
                                    </p>
                                    <p> <font style="width:50%;display:inline-block">বাড়ীর মালিকের নাম ও মোবাইল নাম্বার<font style="font-size: 8px;"></font></font><font style="width:50%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; </font>
                                    </p>
                                    <p> <font style="width:50%;display:inline-block">ইউনিয়ন/পৌরসভা/সিটি কর্পোরেশন চেয়ারম্যান/মেম্বার-এর নাম ও মোবাইল নাম্বার<font style="font-size: 8px;"></font></font><font style="width:50%;border-bottom:1px dotted #999; display:inline-block">:&nbsp; </font>
                                    </p>
                                     <p style="font-size: 12px; font-weight: bold;">খ. সঠিকতা নিরূপণ (Determining Accuracy):</p>
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

                                    <p style="font-size: 12px; font-weight: bold;">গ. পূর্বের চাকুরী সম্পর্কিত তথ্য(যদি থাকে) (Information of Previous Employement):
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
                                    <p style="font-size: 12px; font-weight: bold;">গ. পূর্বে এই প্রতিষ্ঠানে কর্মরত ছিলেন কিনা? হ্যাঁ/ না (যদি হ্যাঁ হয় বিবরণ লিখুন)(Information of Previous Employement in this Company):
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
                                                <b>করমীর স্বাক্ষর</b>
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
                            </div>
                        </div>
                        @endif
                    </form>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
    $(document).ready(function(){   
        // retrive all information  
        function formatState (state) {
            //console.log(state.element);
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "/user/pages/images/flags";
            var $state = $(
            '<span><img /> <span></span></span>'
            );
            // Use .text() instead of HTML string concatenation to avoid script injection issues
            var targetName = state.name;
            $state.find("span").text(targetName);
            // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
            return $state;
        };
    });
    $('body').on('change', '.associates', function(){
        var id = $(this).val();
        var str = $("#generate").attr("href");
        var x = str.replace("%ASSOCIATE_ID%", id);
        console.log(x);
        $("#generate").attr('href', x);
    });

        function printMe(el){ 
            var myWindow=window.open('','','width=800,height=800');
            myWindow.document.write('<html><head></head><body style="font-size:9px;">');
            myWindow.document.write(document.getElementById(el).innerHTML);
            myWindow.document.write('</body></html>');
            myWindow.focus();
            myWindow.print();
            myWindow.close();
        }
</script>
@endpush
@endsection