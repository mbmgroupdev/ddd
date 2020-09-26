<style>
    @media print {
      #unit-info{
        display:none;
      }
        .pagebreak {
            page-break-before: always !important;
        }
    }
</style>
@if(!empty($info))
    @php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, date('Y-m-d g:i:A'));
    @endphp
    <div class="col-sm-12">
        <button type="button" onClick="printMe('html-2-pdfwrapper')" class="btn btn-warning btn-sm" title="Print">
            <i class="fa fa-print"></i> 
        </button>
        <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF">
            <i class="fa fa-file-pdf-o"></i> 
        </a>
        <button type="button"  id="excel"  class="showprint btn btn-success btn-sm">
           <i class="fa fa-file-excel-o" style="font-size:14px"></i>
        </button>
    </div>

    <div class="col-sm-12" style="margin:20px auto;border:1px solid #ccc">

        <div class="page-header" style="text-align:left;border-bottom:2px double #666">

            <h1 style="margin:4px 10px; font-weight: bold;  text-align: center; color: #FF00FF">{{ $other_info['unit'] }}</h1>
            <h3 style="margin:4px 10px; text-align: center;">উৎসব বোনাস প্রদানের শীট 
                @if(isset($other_info['bonus_lib']))
                ({{ $other_info['bonus_lib']->bonus_type_name }}, {{ $other_info['bonus_lib']->month }}-{{ $other_info['bonus_lib']->year}})
                @endif
            </h3>
            <h5 style="margin:4px 10px; text-align: center; color: #FF00FF">যোগদানের সর্বশেষ তারিখঃ ({{ !empty($other_info['join_date'])?str_replace($en,$bn,date('d-m-y', strtotime($other_info['join_date']))):null }})</h5>

            <h5 style="margin:4px 10px;text-align:center;">
                @if(isset($other_info['floor']))
                    <span style="color:lightseagreen;">ফ্লোর:</span> {{$other_info['floor']}}
                @endif
                @if(isset($other_info['area']))
                    <span style="color:lightseagreen;" class="f17">এরিয়া:</span> {{$other_info['area']}}
                @endif
                @if(isset($other_info['department']))
                    <span style="color:lightseagreen;" class="f17">ডিপার্টমেন্ট:</span> {{$other_info['department']}}
                @endif
                @if(isset($other_info['section']))
                    <span style="color:lightseagreen;">সেকশন:</span> {{$other_info['section']}}
                @endif
                @if(isset($other_info['sub_sec']))
                    <span style="color:lightseagreen;">সাব-সেকশন:</span> {{$other_info['sub_sec']}}
                @endif
            </h5>
            <h3 style="margin:4px 10px;text-align:center;">
                বোনাস প্রদানের তারিখঃ {{ Custom::engToBnConvert($other_info['bonus_process_date']) }}
            </h3>
            

            <h6 style="margin:4px 10px;text-align:center;font-weight:600;font-size:13px;">
                সর্বমোট টাকার পরিমানঃ
                <span style="color:hotpink;font-size:15px;" id="total-salary">{{  (str_replace($en, $bn,(string)number_format($other_info['bonus'],2, '.', ','))) }}</span><br/>
                মোট কর্মী/কর্মচারীঃ
                <span style="color:hotpink;font-size:15px;" id="emp-count">{{ Custom::engToBnConvert($other_info['employee']) }}</span>
            </h6>
        </div>
        <hr>
        <!-- break into location -->
        <div id="html-2-pdfwrapper">
            @foreach($location_wise as $key => $loc_data)
                <!-- break the collection in chunks -->
                @php
                    $page_data =  $loc_data->chunk(10);
                @endphp

                @foreach($page_data as $key1 => $page)
                <div class="panel panel-info">
                    <div class="panel-heading">ইউনিট :<b> {{ $other_info['unit'] }}</b> - লোকেশন :<b> {{ $location[$key]->hr_location_name??$key }}</b></div>
                    <div class="panel-body">
                        
                        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                            <tr>
                                <td style="width:29%">
                                    <p style="margin:0;padding:4px 0"><strong>তারিখঃ </strong>
                                        {{ Custom::engToBnConvert(date('Y-m-d')) }}
                                    </p>
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;সময়ঃ </strong>
                                        {{ Custom::engToBnConvert(date('g:i A')) }}
                                    </p>
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;পৃষ্ঠা নংঃ </strong>
                                        {{ Custom::engToBnConvert($key1+1)}}
                                    </p>
                                </td>
                                <td>
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:18px;">
                                        {{ $other_info['unit']->hr_unit_name_bn??'' }}
                                    </h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:16px;">উৎসব বোনাস প্রদানের শীট 
                                    <br/>
                                    তারিখঃ {{ Custom::engToBnConvert($other_info['bonus_process_date']) }}</h5>
                                </td>
                                <td width="0%"> &nbsp;</td>
                                <td style="width:30%" style="text-align: right;">
                                    @php $total = 0; @endphp
                                    @foreach($page as $key2 => $emp)
                                        @php $total += ($info[$emp->associate_id]->bonus_amount-$info[$emp->associate_id]->stamp_price ); @endphp
                                    @endforeach
                                    <p style="margin:0;padding:4px 0;text-align: right;">
                                        সর্বমোট টাকার পরিমানঃ <span style="color:hotpink" >{{Custom::engToBnConvert($total)}}</span>
                                    </p>
                                    <p style="margin:0;padding:4px 0;text-align: right;">
                                        মোট কর্মী/কর্মচারীঃ <span style="color:hotpink" >{{Custom::engToBnConvert(count($page))}}</span>
                                    </p>
                                    
                                </td>
                            </tr>
                        </table>
                        <table id="#myTable" class="table" style="width:100%;border:1px solid #ccc; font-size:12px; color: #2A86FF; display: block;overflow-x: auto;"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                            <thead>
                                <tr style="color:hotpink">
                                    <th>ক্রমিক নং</th>
                                    <th>কর্মী/কর্মচারীদের নাম যোগদানের তারিখ</th>
                                    <th>আই.ডি নং</th>
                                    <th>মাসিক বেতন/মজুরী</th>
                                    <th>বোনাস (টাকা)</th>
                                    <th>দস্তখত</th>
                                    <th >বিতরণ</th>
                                </tr> 
                            </thead>
                            <tbody>
                                @php  
                                    $count = 0; 
                                @endphp
                                
                                @foreach($page as $key3 => $emp)
                                @php $count ++; @endphp
                                <tr>
                                    <td width="5%">
                                        {{ str_replace($en, $bn, ($count)) }}
                                    </td>
                                    <td>
                                        <p style="margin: 0px; padding: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font>{{ !empty($emp->employee_bengali)?$emp->employee_bengali['hr_bn_associate_name']:null }}</font></p>

                                        <p style="margin: 0px; padding: 0px;">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span style="color:black;">
                                            {{ str_replace($en, $bn, ($emp->as_doj)) }}</span>
                                        </p>

                                        <p style="margin: 0px; padding: 0px;">
                                            @php 
                                                $years = (int) ($info[$emp->associate_id]->duration/12);
                                                $months = (int) ($info[$emp->associate_id]->duration%12);
                                            @endphp
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                            <span style="color:hotpink" >
                                            ( @if($years > 0 ){{ str_replace($en, $bn, $years) }} বৎসর @endif {{ str_replace($en, $bn, $months) }}  মাস)</span>
                                        </p>

                                        <p style="margin: 0px; padding: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;<font> {{ !empty($emp->designation)?$emp->designation['hr_designation_name_bn']:null }}</font></p>
                                    </td>

                                    <td>
                                        <?php $temp_bn= str_replace($en, $bn, $emp->temp_id)  ?>
                                        {!! !empty($emp->associate_id)?(substr_replace($emp->associate_id, "<big style='font-size:16px; font-weight:bold;'>$temp_bn</big>", 3, 6)):null !!}

                                    </td>

                                    <td>
                                        <p style="margin: 0px; padding: 0px;">
                                            <span style="color:hotpink" >{{ (str_replace($en, $bn,(string)number_format($info[$emp->associate_id]->gross_salary,2, '.', ',')))}}</span>
                                        </p>
                                        <p style="margin: 0px; padding: 0px;">মূল বেতনঃ  
                                            <span style="color:hotpink" >
                                            {{ (str_replace($en, $bn,(string)number_format($info[$emp->associate_id]->basic,2, '.', ',')))}}
                                            </span>
                                         </p>
                                        <p style="margin: 0px; padding: 0px; font-size: 8px;">স্ট্যাম্পের টাকা কাটাঃ <span style="color:hotpink" >{{Custom::engToBnConvert($info[$emp->associate_id]->stamp_price)}}</span></p>
                                    </td>
                                    <td>
                                        <p style="margin: 0px; padding: 0px; font-size: 16px;"><?php 
                                             ?>
                                             
                                            {{ (str_replace($en, $bn,(string)number_format($info[$emp->associate_id]->bonus_amount-10,2, '.', ','))) }}
                                        </p>
                                    </td>
                                    <td width="10%"></td>
                                    <td id="{{$emp->associate_id}}">
                                        @if($info[$emp->associate_id]->disburse_date == null)
                                            <a data-id="{{$info[$emp->associate_id]->id}}" class="btn btn-primary btn-sm disbursed_salary" data-eaid="{{ $emp->associate_id }}"  data-name="{{ $emp->employee_bengali['hr_bn_associate_name']??'' }}" data-post="{{ $emp->designation['hr_designation_name_bn']??'' }}"  rel='tooltip' data-tooltip-location='top' data-tooltip='বোনাস প্রদান করুন' ><i class="fa fa-money" aria-hidden="true"></i> হয় নি </a>
                                        @else
                                            হ্যাঁ 
                                            <br>
                                            <b>{{ Custom::engToBnConvert($info[$emp->associate_id]->disburse_date) }}</b>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pagebreak"> </div>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h3 class="no_margin">বোনাস বিতরণ</h3>   
            </div>
            <div class="inner_gray_close_button">
              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
            </div>
          </div>

          <div class="inner_body" id="modal-details-content" style="display: none">
            <div class="inner_body_content">
               <input type="hidden" name="id" value="" id="modal-id">
               <h1 class="text-center" ><strong class="f22" id="disbursed_name"></strong></h1>
               <h4 class="text-center" id="disbursed_post"></h4>
               <h4 class="text-center" id="disbursed_id"></h4>
               <h4 class="text-center" id="disbursed_body"></h4>
            </div>
            <div class="inner_buttons">
              <button class="okay_modal_button confirm-disbursed" id="confirm-disbursed" type="submit" tabindex="0">
                Confirm
              </button>
              <a class="cancel_modal_button cancel_details" role="button"> Cancel </a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript">
    
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    let detailsheight = $(".item_details_dialog").css("min-height", "115px");
    $(document).on('click','.disbursed_salary',function(){
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let name = $(this).data('name');
        let post = $(this).data('post');
        $("#modal-id").val(id);
        $('#disbursed_name').html(name);
        $('#disbursed_post').html(post);
        $('#disbursed_id').html('আইডি: '+associateId);
        $('#disbursed_body').html(' বোনাস প্রদান করা হচ্ছে । ');
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "40%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    $("#confirm-disbursed").click(function() {
        let associate_id = $("#modal-associateId").val();
        let id = $("#modal-id").val();
        //alert(id);
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '/hr/reports/employee-bonus-disburse',
            type: "post",
            data: { _token : _token,
                associate_id: associate_id,
                id: id
            },
            success: function(response){
                $("#"+associate_id).html('হয়েছে').effect('highlight',{},2500);
            }
        });

        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>
@endif