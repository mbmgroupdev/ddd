
<style type="text/css">
    @media only screen and (max-width: 1280px) and (min-width: 768) {
        .search-result-div{width: 33.33%;}
    }
    @media only screen and (max-width: 445px) {
        #employeeTable{display: block; white-space: nowrap; width: 100%; overflow-x: auto;}
    }
</style>
<div class="panel panel-info col-sm-12 col-xs-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
            @if(isset($request2['as_unit_id']))
                <li>
                    <a href="#" class="search_area" data-unit="{{ $request2['as_unit_id'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_area_id']))
                <li>
                     <a href="#" class="search_dept" data-area="{{ $request2['as_area_id'] }}">
                        {{ $data['area']->hr_area_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_department_id']))
                <li>
                    <a href="#" class="search_floor" data-department="{{ $request2['as_department_id'] }}">
                        {{ $data['department']->hr_department_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_floor_id']))
                <li>
                    <a href="#" class="search_section" data-floor="{{ $request2['as_floor_id'] }}">
                        {{ $data['floor']->hr_floor_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_section_id']))
                <li>
                    <a href="#" class="search_subsection" data-section="{{ $request2['as_section_id'] }}">
                        {{ $data['section']->hr_section_name }}
                    </a>
                </li>
            @endif
            @if(isset($data['subsection']))
                <li>
                    {{ $data['subsection']->hr_subsec_name }}
                </li>
            @endif
            <li class="search_emp"> Employee </li>
            <li>{{ $info->associate_id }}</li>
        </ul><!-- /.breadcrumb -->

    </div>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row">
          <div class=" col-sm-4">
              <div class="profile-user-info no-margin">
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Name </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->as_name}} </span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Associate Id </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->associate_id }} </span>
                        </div>
                    </div>
                    
                    
                </div>
          </div>
          <div class=" col-sm-5">
                
                <div class="profile-user-info no-margin">
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Designation </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->designation['hr_designation_name'] }} </span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Unit </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->unit['hr_unit_name'] }} </span>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>
            <div class=" col-sm-3">
                <div class="profile-user-info no-margin">
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Department </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $department }} </span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name align-left"> Status</div>
                        <div class="profile-info-value">
                            <span>@if($info->as_status == 1) Active @else Inactive @endif </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                



                


                
    <div class="space-20"></div>
    <div class="row">
        <div class="col-sm-12">
             <table autosize="1" id="employeeTable" style="overflow: no-wrap" class="table  table-bordered table-hover" >
                <thead>
                    <tr style="color:hotpink;">
                        <th>SL</th>
                        <th>মাসিক বেতন/মজুরি</th>
                        <th>হাজিরা দিবস</th>
                        <th>বেতন হইতে কর্তন</th>
                        <th>মোট দেয় টাকার পরিমান</th>
                        <th>সর্বমোট টাকার পরিমান</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($salary) == 0)
                        <tr>
                            <td colspan='9'> <b><h5 class="text-center"> No data found !</h5></b></td>
                        </tr>
                    @endif
                    @php
                        $i = 0;
                    @endphp
                    @foreach($salary as $list)
                    @php
                    // get total hour with minutes calculation
                     
                    // get designation
                    if(isset($list->employee->as_designation_id)){
                        $designation = App\Models\Hr\Designation::where('hr_designation_id', $list->employee->as_designation_id)->first();
                    } else {
                        $designation = new stdClass();
                    }
                    @endphp
                    <?php //dump($list->ot_overtime_minutes);?>
                    <tr>
                        <td>{{ ++$i }} </td>
                        <td>
                            <p style="margin:0;padding:0;">{{ date("F", mktime(0, 0, 0, $list->month, 1)) }}, {{$list->year}}</p>
                            <p style="margin:0;padding:0;"></p>
                            <p style="margin:0;padding:0;color:hotpink">মূল+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                            <p style="margin:0;padding:0;">
                                {{ $list->basic.'+'.$list->house.'+'.$list->medical.'+'.$list->transport.'+'.$list->food }} 
                            </p>
                            <p>=<font style="color:hotpink">{{ $list->gross }} </font></p>
                        </td>

                        <td nowrap="nowrap">
                            <p style="margin:0;padding:0">
                               উপস্থিত দিবস =<font style="color:hotpink;float:right;" > {{ $list->present}}</font>
                                 
                            </p>
                            <p> বিলম্ব উপস্থিতিঃ = <font style="color:hotpink;float:right;"> {{ $list->late_count }} </font> </p>
                            <p style="margin:0;padding:0">                            
                              সরকারি ছুটি =
                                    <font style="color:hotpink;float:right;"> {{$list->holiday}}</font>
                              
                            </p>
                            <p style="margin:0;padding:0;">
                            অনুপস্থিত =<font style="color:hotpink;float:right;"> {{ $list->absent}}</font>
                                
                            </p>
                            <p style="margin:0;padding:0">
                            ছুটি মঞ্জুর =<font style="color:hotpink;float:right;"> {{ $list->leave }}</font>
                                
                           
                            </p>
                            <p style="margin:0;padding:0">

                                মোট দেয় =<font style="color:hotpink;float:right;"> {{ ($list->present + $list->holiday + $list->leave)}}</font>
                            
                            </p>
                        </td>
                        <td nowrap="nowrap">
                            <p style="margin:0;padding:0">
                               
                                অনুপস্থিতির জন্য = <font style="color:hotpink;float:right;">{{  $list->absent_deduct }}</font>
                                
                            </p>
                            <p style="margin:0;padding:0">

                              অর্ধ দিবসের জন্য কর্তন =
                                    <font style="color:hotpink;float:right;">{{$list->half_day_deduct }}</font>
                            </p>
                            <p style="margin:0;padding:0">

                                অগ্রিম গ্রহণ বাবদ =<font style="color:hotpink;float:right;">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->advp_deduct }} </font>
                                </span>



                              

                            </p>
                            <p style="margin:0;padding:0">

                               স্ট্যাম্প বাবদ =
                                    <font style="color:hotpink;float:right;"> 10.00</font>
                              
                            </p>
                            <p style="margin:0;padding:0">

                              ভোগ্যপণ্য ক্রয়  =
                               <font style="color:hotpink;float:right;">{{ ($list->add_deduct == null) ? '0.00' : isset($list->add_deduct['cg_product'])?$list->add_deduct['cg_product']:'' }}</font>
                               
                            </p>
                            <p style="margin:0;padding:0">
                               খাবার বাবদ কর্তন =
                                <font style="color:hotpink;float:right;">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->food_deduct }} </font>
                                
                            </p>
                            <p style="margin:0;padding:0">
                                অন্যান্য =<font style="color:hotpink;float:right;">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->others_deduct }} </font>
                                

                            </p>
                        </td>
                        <td nowrap="nowrap">
                            <p style="margin:0;padding:0">
                               
                                 বেতন/মজুরি =
                                        <font style="color:hotpink;float:right;">{{ $list->salary_payable}}</font>
                                 
                            </p>
                            <p style="margin:0;padding:0">
                          
                                অতিরিক্ত সময়ের কাজের মজুরি =
                                    <font style="color:hotpink;float:right;">{{ number_format(($list->ot_rate * ($list->ot_hour)), 2, '.', '')}}</font>
                                   
                            </p>
                            <p style="margin:0;padding:0">
                             

                                   অতিরিক্ত কাজের মঞ্জুরি হার =
                                        <font style="color:hotpink;float:right;">{{ $list->ot_rate }} </font> &nbsp;
                                    
                                        <font style="color:hotpink;float:right;"> ({{ $list->employee->as_ot==1?$list->ot_hour:'00' }}  Hour)</font>
                                        

                             
                            </p>
                            <p style="margin:0;padding:0">
                               
                                উপস্থিত বোনাস =
                                        <font style="color:hotpink;float:right;">{{$list->attendance_bonus }}</font>
                                    


                            </p>
                            <p style="margin:0;padding:0">

                                বেতন/মঞ্জুরি অগ্রিম/সমন্বয় =
                                
                                    <font style="color:hotpink;float:right;">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add }}</font>
                             


                            </p>
                        </td>
                        <td style="text-align:center;">
                            @php
                                $ot = ($list->ot_rate * ($list->ot_hour));
                                $salaryAdd = ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add;
                            @endphp
                            <font style="color:hotpink;font-size: 16px;">{{ number_format(($list->total_payable), 2, '.', '') }}</font>



                        </td>
                    </tr>
                  @endforeach
                </tbody>

            </table>
            
        </div>
    </div>
        







    


    </div>
</div>