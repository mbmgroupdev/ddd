@extends('hr.layout')
@section('title', 'Job Application')
@section('main-content')
@push('css')
<style type="text/css">
   tr, td {
        font-family: Verdana,Arial,Helvetica,sans-serif !important;
        font-size: 12px !important;
    }

    @media only screen and (max-width: 771px) {
        .job_app_div{width: 100% !important;}
}

    @media only screen and (max-width: 771px) {
        .job_app_field{width: 100% !important;}
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
                <li class="active"> Job Application</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="row">
                 @include('inc/message')
                <div class="col-12">
                    <form class="" role="form" method="post" action="{{ url('hr/recruitment/job-application') }}" enctype="multipart/form-data">   
                        @csrf
                        <div class="panel">
                            <div class="panel-heading">
                                <h6>Job Application</h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    
                                    <div class="col-md-offset-2 col-4">
                                        
                                        <div class="form-group has-float-label has-required select-search-group">
                                            
                                            {{ Form::select('job_app_id', [Request::get('associate_id') => Request::get('associate_id')], Request::get('associate_id'),['placeholder'=>'Select Associate\'s ID', 'data-validation'=> 'required','id'=>'job_app_id',  'class'=> 'associates no-select col-xs-12', 'required' => 'required']) }} 
                                            <label  for="job_app_id"> Associate's ID </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                         <a id="generate" class="btn btn-primary" href="{{ url('hr/recruitment/job-application?associate_id=%ASSOCIATE_ID%') }}">Generate</a>
                                         @if(!empty(Request::get('associate_id')))
                                         <button type="button" onclick="printMe('job_application')" title="Print" class="btn btn-warning">
                                                <i class="fa fa-print"></i> 
                                        </button> 
                                        
                                        <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" title="PDF" class="btn btn-danger">
                                            <i class="fa fa-file-pdf-o"> </i> 
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
 
                        @if(!empty(Request::get('associate_id')) && $info != null)
                        <div class="panel">
                            
                            <div class="col-sm-12 job_app_div">
                                {{-- <div class="col-xs-1"></div> --}}
                                <div class="col-xs-12 no-padding-left" id="printable" style="font-size: 9px;">
                                    <div class="tinyMceLetter" name="job_application" id="job_application" style="font-size: 9px;">
                                        <?php
                                        date_default_timezone_set('Asia/Dhaka');
                                        $en = array('0','1','2','3','4','5','6','7','8','9');
                                        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
                                        $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
                                        ?>
                                        <p>
                                        <center><b style="font-size: 9px;">চাকুরীর আবেদনপত্র </b></center>
                                        <center><u>JOB APPLICATION </u> </center>
                                        <p style="font-size: 12px;">বরাবর,</p>
                                        <p style="font-size: 12px;">ব্যবস্থাপনা পরিচালক</p>
                                        <p style="font-size: 12px;">{{ (!empty($info->hr_unit_name_bn )?$info->hr_unit_name_bn:null) }}</p>
                                        <p style="font-size: 12px;">{{ (!empty($info->hr_unit_address_bn)?$info->hr_unit_address_bn:null) }}</p>
                                        <p style="font-size: 12px;"><u> <b>বিষয়ঃ {{ $info->hr_designation_name_bn }} পদে চাকুরীর জন্য আবেদন</b></u></p>
                                        <p style="font-size: 12px;"><u> <b> Sub: Application for the post of {{ (!empty($info->hr_designation_name_bn )?$info->hr_designation_name_bn:null) }}</b></u></p>
                                        <table style="border: none; font-size: 12px;" width="100%" cellpadding="3">
                                            <tr>
                                                <td width="290px" style="border: none;">নামঃ (Name)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->hr_bn_associate_name )?$info->hr_bn_associate_name:null) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">পিতা/মাতার নামঃ (Name of Father/Mother)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->hr_bn_father_name )?$info->hr_bn_father_name:null) }} / {{ (!empty($info->hr_bn_mother_name )?$info->hr_bn_mother_name:null) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">স্বামী/স্ত্রীর নামঃ (Name of Husband/Wife)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->hr_bn_spouse_name )?$info->hr_bn_spouse_name:null) }}</td>
                                            </tr>
                                            <tr>
                                                <tr>
                                                    <td width="290px" style="border: none;" rowspan="2">স্থায়ী ঠিকানাঃ (Permanent Address)</td>
                                                    <td style="border: none;">গ্রাম(Village): {{ (!empty($info->emp_adv_info_per_vill )?$info->emp_adv_info_per_vill:null) }}
                                                    </td>
                                                    <td style="border: none;">পোস্ট(P.O): {{ (!empty($info->emp_adv_info_per_po )?$info->emp_adv_info_per_po:null) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none;">থানা(P.S): {{ (!empty($info->permanent_upazilla_bn )?$info->permanent_upazilla_bn:null) }}
                                                    </td>
                                                    <td style="border: none;">জেলা(Dist.): {{ (!empty($info->permanent_district_bn )?$info->permanent_district_bn:null) }}
                                                    </td>
                                                </tr>
                                            </tr>
                                            <tr >
                                                <tr>
                                                    <td width="290px" style="border: none;" rowspan="2">বর্তমান ঠিকানাঃ (Permanent Address)</td>
                                                    <td style="border: none;">গ্রাম(Village): {{ (!empty($info->emp_adv_info_pres_house_no )?$info->emp_adv_info_pres_house_no:null) }} {{ (!empty($info->emp_adv_info_pres_road )?$info->emp_adv_info_pres_road:null) }}
                                                    </td>
                                                    <td style="border: none;">পোস্ট(P.O): {{ (!empty($info->emp_adv_info_pres_po )?$info->emp_adv_info_pres_po:null) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none;">থানা(P.S): {{ (!empty($info->present_upazilla_bn )?$info->present_upazilla_bn:null) }}
                                                    </td>
                                                    <td style="border: none;">জেলা(Dist.): {{ (!empty($info->present_district_bn )?$info->present_district_bn:null) }}
                                                    </td>
                                                </tr>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">শিক্ষাগত যোগ্যতাঃ (Edu. Qualification)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{$info->education_degree_title}}</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">জন্ম তারিখ/বয়সঃ (Date of Birth/ Age)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->as_dob )?$info->as_dob:null) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">ধর্মঃ (Religion)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->emp_adv_info_religion )?$info->emp_adv_info_religion:null) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">জাতীয়তাঃ (Nationality)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->emp_adv_info_nationality )?$info->emp_adv_info_nationality:null) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">অভিজ্ঞতাঃ (Experience)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: {{ (!empty($info->emp_adv_info_work_exp )?$info->emp_adv_info_work_exp:"0") }} Year(s)</td>
                                            </tr>
                                            <tr>
                                                <td width="290px" style="border: none;">সুপারিশকারীর নাম ও পরিচিতি/ঠিকানাঃ (Name and Address of recommender)</td>
                                                <td style="border: none; border-bottom: 1px dotted">: </td>
                                            </tr>
                                            <tr>
                                                <td style="border: none;" colspan="2">
                                                    <br>    
                                                    <p>
                                                        অতএব, অনুগ্রহ করে আমাকে উক্ত পদে নিয়োগ দান করিয়া বাধিত করিবেন।
                                                    </p>
                                                    <p>
                                                        May I, therefore pray and hope that you would be kind enough to appoint me for the above post.
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: none;" width="290px"><br>
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
                                        <table style="border: 1px solid; font-size: 12px;" width="100%" cellpadding="3" width="100%">
                                            <tr style="width: 100%">
                                                <td style="border: none; text-align: right;" colspan= "2">
                                                    (অফিস কর্তৃক পূরণীয় For office use only)
                                                </td>
                                            </tr>
                                            <tr style="width: 100%">
                                                <td style="border: none;">
                                                    ১ লাইন নং (Dept) :
                                                </td>
                                                <td style="border: none;">
                                                    ৪. নিয়োগের তারিখ (Date of App) : {{$info->as_doj}}
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
                                                    ৩. কার্ড নং (Card Number) : {{$info->associate_id}}
                                                </td>
                                            </tr>
                                            <tr style="width: 100%">
                                                <td style="border: none;"><br></td>
                                                <td style="border: none;"><br></td>
                                            </tr>
                                            
                                        </table>
                                        <table style="border: 1px solid; font-size: 12px;" width="100%" cellpadding="3" width="100%">
                                            
                                            <tr style="width: 100%">
                                                <td style="border: 1px solid; text-align: center;">
                                                    <br><br><br>
                                                    উতপাদন ব্যবস্থাপক<br>
                                                    Production Manager 
                                                </td>
                                                <td style="border: 1px solid; text-align: center;">
                                                    <br><br><br>
                                                    প্রশাসনিক কর্মকর্তা<br>
                                                    Manager HR/ Asst. Manager HR
                                                </td>
                                                <td style="border: 1px solid; text-align: center;">
                                                    <br><br><br>
                                                    জি.এম/ম্যানেজার/এ.জি.এম<br>
                                                    G.M/Manager/A.G.M 
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
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
            $("#generate").attr('href', x);
        });

    function printMe(el){ 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:8px;">');
        myWindow.document.write(document.getElementById(el).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    function attLocation(loc){
        window.location = loc;
    }

</script>
@endpush
@endsection