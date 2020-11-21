@php 
    $position = ['0','1','2','3','4','5','6','7','8','9', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $bnValue  = ['০','১','২','৩','৪','৫','৬','৭','৮','৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর ', 'ডিসেম্বর']; 
@endphp
<style type="text/css" media="print">
    .pagebreak{page-break-after: always;}
</style>
@if($type == "en")
    @php $chunkedEm = array_chunk($employees->toArray(), 8); @endphp
    @foreach($chunkedEm as $key1 => $emps)
        @foreach($emps as $key =>$associate )
        <div style="float:left;margin: 20px 10px;width: 200px;height: 290px;background:white;border:1px solid #333;">
            <div style="width:100%;height:10px"></div>
            <div style="width:100%;height:30px;padding:5px">
                <div style="float:left;width:65%;line-height:16px;font-size:12px;font-weight:700">{{$associate->hr_unit_name}}</div>
                <div style="float:left;width:35%"><img style="width:55px;height:28px;display:block" src="{{url(!empty($associate->hr_unit_logo)?$associate->hr_unit_logo:'')}}" alt="Logo"></div>
            </div>
            <div style="width:100%;height:80px;margin:0 0 10px 0">
                <img style="margin:0px auto;width:75px;height:75px;display:block" src="{{url(emp_profile_picture($associate))}}" >
            </div>
            <div style="height:50px;text-align:center">
                <strong style="display:block;font-size:12px;font-weight:700">{{$associate->as_name}}</strong>
                <span style="display:block;font-size:9px">{{$associate->hr_designation_name}}</span>
                <strong style="display:block;font-size:9px;">{{$associate->hr_department_name}}</strong>
                <span style="display:block;font-size:9px">DOJ: {{date("d-M-Y", strtotime($associate->as_doj))}}</span>
                <span style="display:block;font-size:9px">Previous ID: {{$associate->as_oracle_code}}</span>
            </div>
            <br>
            <div style="width:100%;height:40px;padding:10px 5px 0 10px">
                <strong style="display:block;font-size:12px">
                    @php
                        $strId = (!empty($associate->associate_id)?
                    (substr_replace($associate->associate_id, "<big style='font-size:18px'>".$associate->temp_id."</big>", 3, 6)):
                    '');
                    @endphp
                    {!!$strId!!}
                </strong>
                <strong style="display:block;font-size: 11px;">Blood Group: {{$associate->med_blood_group}}</strong>
            </div>
            <div style="padding: 0px 10px 5px 10px;">
                <br>
                <div class="col-xs-12 no-padding no-margin">
                <span style="float:right;display:inline-block;font-size:9px">Authorized Signature</span>
                </div>
            </div>
        </div>
        @endforeach
        <div class="pagebreak"></div> 
    @endforeach
@endif

@if($type == "bn")
    @php $chunkedEm = array_chunk($employees->toArray(), 4); @endphp
    @foreach($chunkedEm as $key1 => $emps)
        @foreach($emps as $key =>$associate )
            
            <div style="float:left;margin: 20px 10px;width: 200px;height: 290px;background:white;border:1px solid #333;">
                <div style="width:100%;height:10px"></div>
                <div style="width:100%;height:30px;padding:5px">
                    <div style="float:left;width:65%;line-height:16px;font-size:12px;font-weight:700">{{$associate->hr_unit_name_bn}}</div>
                    <div style="float:left;width:35%"><img style="width:55px;height:28px;display:block" src="{{url(!empty($associate->hr_unit_logo)?$associate->hr_unit_logo:'')}}" alt="Logo"></div>
                </div>
                <div style="width:100%;height:75px;margin:0 0 10px 0">
                    <img style="margin:0px auto;width:75px;height:75px;display:block" src="{{url(emp_profile_picture($associate))}}" alt="Logo">
                </div>
                <div style="height:50px;text-align:center">
                    <strong style="display:block;font-size:11px;font-weight:700">নামঃ {{($associate->hr_bn_associate_name?$associate->hr_bn_associate_name:null)}}</strong>
                    <strong style="display:block;font-size:10px">পদবীঃ {{$associate->hr_designation_name_bn?$associate->hr_designation_name_bn:null}}</strong>
                    <strong style="display:block;font-size:10px;">সেকশনঃ {{($associate->hr_section_name_bn?$associate->hr_section_name_bn:null)}}</strong>
                    <strong style="display:block;font-size:10px">যোগদানের তারিখ: 
                        {{str_replace($position, $bnValue, (date("d M, Y", strtotime($associate->as_doj))))}} ইং

                    </strong>
                    <strong style="display:block;font-size:10px;">পূর্বের আইডিঃ {{($associate->as_oracle_code?$associate->as_oracle_code:null)}}</strong>
                    
                </div>
                <div style="width:100%;height:40px;padding:10px 5px 0px 10px">
                    
                    <strong style="display:block;font-size:12px;text-align: center;">
                        @php
                        $strId = (!empty($associate->associate_id)?
                        (substr_replace($associate->associate_id, "<big style='font-size:18px'>".$associate->temp_id."</big>", 3, 6)):
                        '');
                        @endphp
                        <br>
                        আইডিঃ {!!$strId!!}
                    </strong>
                </div>
                <div style="padding: 0px 10px 5px 10px;">
                    <br><br>
                    <div class="col-xs-12 no-margin " style="padding: 0px 0px 0px 86px;"></div>
                    <div class="col-xs-12 no-margin"  style="margin-bottom: 8px;">
                    <strong style="float:left;display:inline-block;font-size:9px">শ্রমিকের স্বাক্ষর</strong>
                    <strong style="float:right;display:inline-block;font-size:9px">মালিক/ব্যবস্থাপক</strong>
                    </div>
                </div>
            </div>
            <div style="float:left;margin: 20px 10px;width: 200px;height: 290px;background:white;border:1px solid #333;">
                
                
              
                <div style="padding: 5px 10px 5px 10px;">
                    <strong style="display:block;font-size: 10px;">রক্তের গ্রুপঃ &nbsp;{{($associate->med_blood_group?$associate->med_blood_group:null)}}</strong>
                    <strong style="display:block;font-size: 10px;">স্থায়ী ঠিকানাঃ &nbsp;{{($associate->hr_bn_permanent_village?$associate->hr_bn_permanent_village.", ":null)}} {{($associate->hr_bn_permanent_po?$associate->hr_bn_permanent_po.", ":null)}}
                        @if($associate->emp_adv_info_per_upz)
                            @if(isset($upzillas[$associate->emp_adv_info_per_upz]))
                                {{$upzillas[$associate->emp_adv_info_per_upz]}},
                            @endif
                        @endif
                        @if($associate->emp_adv_info_per_dist)
                            @if(isset($districts[$associate->emp_adv_info_per_dist]))
                                {{$districts[$associate->emp_adv_info_per_dist]}}
                            @endif
                        @endif

                    </strong>
                </div>
                <div style="padding: 5px 10px 5px 10px;">
                    <strong style="display:block;font-size: 10px;">জরুরী মোবাইল নং -  
                        @if($associate->as_contact)
                            {{str_replace($position, $bnValue, $associate->as_contact)}}
                        @endif
                    </strong>
                    <strong style="display:block;font-size: 10px;">জাতীয় পরিচয়পত্রঃ  
                        @if($associate->emp_adv_info_nid)
                            {{str_replace($position, $bnValue, $associate->emp_adv_info_nid)}}
                        @endif
                    </strong>

                </div>
                <div style="padding: 5px 10px 5px 10px;">
                    <strong style="display:block;font-size: 11px; text-align: center;">
                        কারখানা/প্রতিষ্ঠানের ঠিকানাঃ <br>  
                        @if($associate->hr_unit_address_bn)
                        {!!$associate->hr_unit_address_bn!!}
                        @endif
                    </strong>
                    <br>
                    <strong style="display:block;font-size: 11px; text-align: center;">
                        টেলিফোন নং: {{$associate->hr_unit_telephone??''}}
                    </strong>  

                </div>
                <div style="padding: 5px 10px 5px 10px;">
                    <strong style="display:block;font-size: 10px; text-align: center;">
                    উক্ত পরিচয়পত্র হারাইয়া গেলে তাৎক্ষনিক ব্যবস্থাপনা কর্তৃপক্ষকে জানাইতে হইবে।
                    </strong>
                </div>
            </div>
        @endforeach
        <div class="pagebreak"></div>
    @endforeach

@endif