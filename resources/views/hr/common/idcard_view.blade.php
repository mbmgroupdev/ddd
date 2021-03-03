@php 
    $position = ['0','1','2','3','4','5','6','7','8','9', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $bnValue  = ['০','১','২','৩','৪','৫','৬','৭','৮','৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর ', 'ডিসেম্বর']; 
@endphp
<style type="text/css" media="all">
    .pagebreak{page-break-after: always;}
    div,p,td,span,strong{line-height: 125%;padding: 0;margin: 0;}
    p{padding: 0;margin: 0;}
    @import url(https://fonts.googleapis.com/css?family=Poppins:200,200i,300,400,500,600,700,800,900&amp;display=swap);
    body {
        font-family: Poppins,sans-serif;
    }
</style>
@if($type == "en")
    @php $chunkedEm = array_chunk($employees->toArray(), 8); @endphp
    @foreach($chunkedEm as $key1 => $emps)
        @foreach($emps as $key =>$associate )
        <table border="0" style="float:left;margin: 20px 10px;width: 200px;height: 290px;background:white;border:1px solid #333;margin-right: 0;">
            <tr>
                <td style="padding-left: 5px;">
                    <span style="width:100px;display:block;line-height:16px;font-size:12px;font-weight:700">{{$associate->hr_unit_name}}</span>
                    
                </td>
                <td style="text-align: right;padding-right:5px;"><img style="width:55px;height:28px;margin-left: auto;" src="{{url(!empty($associate->hr_unit_logo)?$associate->hr_unit_logo:'')}}" alt="Logo"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <img style="margin:0px auto;width:75px;height:75px;display:block" src="{{url(emp_profile_picture($associate))}}" >
                </td>

            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <strong style="display:block;font-size:11px;font-weight:700">{{$associate->as_name}}</strong>
                    <span style="display:block;font-size:9px">{{$associate->hr_designation_name}}</span>
                    <strong style="display:block;font-size:9px;">Sec: {{$associate->hr_section_name}}</strong>
                    <strong style="display:block;font-size:9px;">Dept: {{$associate->hr_department_name}}</strong>
                    <span style="display:block;font-size:9px">DOJ: {{date("d-M-Y", strtotime($associate->as_doj))}}</span>
                    <span style="display:block;font-size:9px">Previous ID: {{$associate->as_oracle_code}}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left: 5px;">
                    <strong style="display:block;font-size:12px">
                        @php
                            $strId = (!empty($associate->associate_id)?
                        (substr_replace($associate->associate_id, "<big style='font-size:18px'>".$associate->temp_id."</big>", 3, 6)):
                        '');
                        @endphp
                        {!!$strId!!}
                    </strong>
                    <strong style="display:block;font-size: 10px;">Blood Group: {{$associate->med_blood_group}}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;padding-left: 5px;padding-top: 20px;">
                    <strong style="font-size:9px;;">Signature</strong>
                </td>
                <td style="text-align: center;padding-right:5px;padding-top: 20px;">
                    
                    <strong style="font-size:9px">
                        Authority</strong>
                </td>
            </tr>
        </table>
        @endforeach
        <div class="pagebreak"></div> 
    @endforeach
@endif

@if($type == "bn")
    @php $chunkedEm = array_chunk($employees->toArray(), 4); @endphp
    @foreach($chunkedEm as $key1 => $emps)
        @foreach($emps as $key =>$associate )

        <table border="0" style="float:left;margin: 20px 10px;width: 200px;height: 290px;background:white;border:1px solid #333;margin-right: 0;">
            <tr>
                <td style="padding-left: 5px;">
                    <span style="width:135px;display:block;line-height:16px;font-size:12px;font-weight:700">{{$associate->hr_unit_name_bn}}</span>
                    
                </td>
                <td style="text-align: right;padding-right:5px;"><img style="width:55px;height:28px;margin-left: auto;" src="{{url(!empty($associate->hr_unit_logo)?$associate->hr_unit_logo:'')}}" alt="Logo"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <img style="margin:0px auto;width:75px;height:75px;display:block" src="{{url(emp_profile_picture($associate))}}" >
                </td>

            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <strong style="display:block;font-size:10px;font-weight:700"> {{($associate->hr_bn_associate_name?$associate->hr_bn_associate_name:null)}}</strong>
                    <strong style="display:block;font-size:9px">পদবীঃ {{$associate->hr_designation_name_bn?$associate->hr_designation_name_bn:null}}</strong>
                    <strong style="display:block;font-size:9px;">সেকশনঃ {{($associate->hr_section_name_bn?$associate->hr_section_name_bn:null)}}</strong>
                    <strong style="display:block;font-size:9px;">বিভাগ {{($associate->hr_department_name_bn?$associate->hr_department_name_bn:null)}}</strong>
                    <strong style="display:block;font-size:9px">যোগদানের তারিখ: 
                        {{str_replace($position, $bnValue, (date("d M, Y", strtotime($associate->as_doj))))}} ইং

                    </strong>
                    <strong style="display:block;font-size:9px;">পূর্বের আইডিঃ {{($associate->as_oracle_code?$associate->as_oracle_code:null)}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left: 5px;text-align: center;">
                    <strong style="display:block;font-size:12px">
                        @php
                            $strId = (!empty($associate->associate_id)?
                        (substr_replace($associate->associate_id, "<big style='font-size:18px'>".$associate->temp_id."</big>", 3, 6)):
                        '');
                        @endphp
                        আইডিঃ {!!$strId!!}
                    </strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;padding-left: 5px;padding-top: 25px;">
                    <strong style="font-size:9px;;">শ্রমিকের স্বাক্ষর</strong>
                </td>
                <td style="text-align: center;padding-right:5px;position: relative;">
                    @if($associate->hr_unit_authorized_signature)
                    <img style="height: 30px;margin-top: -14px;position: absolute;right: 5px;" src="{{asset($associate->hr_unit_authorized_signature)}}">
                    @else
                    <div style="height: 6px;margin-top: -8px;margin-left: auto;"></div>
                    {{-- <img style="height: 30px;margin-top: -8px;margin-left: auto;" src=""></img> --}}
                    @endif
                    <br>
                    <strong style="font-size:9px;position: absolute;right: 0;width: 100px;">
                    মালিক/ব্যবস্থাপক</strong>
                </td>
            </tr>
        </table>
        <table border="0" style="float:left;margin: 20px 10px;width: 200px;height: 290px;background:white;border:1px solid #333;margin-right: 0;">
            
            <tr>
                <td style="padding-left:5px;">
                    
                    <strong style="display:block;font-size: 10px;padding-bottom:5px;">রক্তের গ্রুপঃ &nbsp;{{($associate->med_blood_group?$associate->med_blood_group:null)}}</strong>
                    <strong style="display:block;font-size: 10px;padding-bottom:10px;">স্থায়ী ঠিকানাঃ &nbsp;{{($associate->hr_bn_permanent_village?$associate->hr_bn_permanent_village.", ":null)}} {{($associate->hr_bn_permanent_po?$associate->hr_bn_permanent_po.", ":null)}}
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
                    <strong style="display:block;font-size: 10px;padding-bottom:5px;">জরুরী মোবাইল নং -  
                        @if($associate->as_contact)
                            {{str_replace($position, $bnValue, $associate->as_contact)}}
                        @endif
                    </strong>
                    <strong style="display:block;font-size: 10px;">জাতীয় পরিচয়পত্রঃ  
                        @if($associate->emp_adv_info_nid)
                            {{str_replace($position, $bnValue, $associate->emp_adv_info_nid)}}
                        @endif
                    </strong>
                </td>
            </tr>
            <tr>
                <td style="padding-left:5px;">
                    
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
                </td>
            </tr>
            <tr>
                <td style="padding-left:5px;">
                    
                    <strong style="display:block;font-size: 10px; text-align: center;">
                    উক্ত পরিচয়পত্র হারাইয়া গেলে তাৎক্ষনিক ব্যবস্থাপনা কর্তৃপক্ষকে জানাইতে হইবে।
                    </strong>
                </td>
            </tr>
            
        </table>
            
        @endforeach
        <div class="pagebreak"></div>
    @endforeach

@endif