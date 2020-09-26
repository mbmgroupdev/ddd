@extends('hr.layout')
@section('title', 'Yearly Employee Leave')
@section('main-content')
@push('css')
<style>
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
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
          <a href="#">Reports</a>
        </li>
        <li class="active"> Yearly Employee Leave Log</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col">
                <form role="form" method="get" action="{{ url('hr/reports/leave_log') }}" id="searchform" >
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="year" class="report_date form-control" id="year" name="year" placeholder="Y" required="required" value="{{ date('Y') }}" autocomplete="off" />
                                        
                                        <label  for="year"> Year </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    <div id="print_pdf" class="custom-control-inline" style="display: none;">
                                        @if (!empty($info)) 
                                        <button type="button" onClick="printMe1('PrintArea')" class="inline btn btn-warning btn-sm" title="Print">
                                            <i class="fa fa-print"></i> 
                                        </button> 
                                        <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="inline btn btn-danger btn-sm" title="PDF">
                                            <i class="fa fa-file-pdf-o"></i> 
                                        </a>
                                        <button type="button"  id="excel"  class="showprint inline btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                       </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col">
              @if(!empty($info))
                <div class="panel">
                    <div class="panel-body">
                        <div id="leave_content_section" class="row">
                            <!-- Display Erro/Success Message -->
                            @include('inc/message')

                            <div class="offset-1 col-sm-10 col-xs-12" id="PrintArea" style="padding-top: 20px;">
                                
                                <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto">
                                    <div class="col-sm-12 text-center page-header" style="margin-bottom: 15px;">
                                        <h3 {{-- style="margin:4px 10px;text-align:center;font-weight:600" --}}>{{ $info->unit }}</h3>
                                        <h5 {{-- style="margin:4px 10px;text-align:center;font-weight:600" --}}>Leave Log</h5>
                                        <h5 {{-- style="margin:4px 10px;text-align:center;font-weight:600" --}}>For The Year : {{ request()->year }}</h5>
                                    </div>
                                    <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left;"  cellpadding="5">
                                        <tr>
                                            <th style="width:40%">
                                               <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $info->name }}</p>
                                               <p style="margin:0;padding:4px 10px"><strong>ID </strong>: {{ $info->associate }}</p>
                                            </th>
                                            <th style="text-align: right;">
                                               <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $info->designation }} </p> 
                                               <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $info->section }} </p> 
                                               <p style="margin:0;padding:4px 10px"><strong>Date of Join </strong>: {{ date("d-m-Y", strtotime($info->doj)) }}</p> 
                                            </th>
                                        </tr> 
                                    </table>


                                    <table class="table" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                                      <thead>
                                        <tr>
                                          <th rowspan="2" width="30%">Month</th>
                                          <th colspan="3" width="30%">Casual Leave</th>
                                          <th colspan="3" width="30%">Medical Leave</th>
                                          <th colspan="3" width="30%">Meternity Leave</th>
                                          <th colspan="3" width="30%">Earn Leave</th>
                                        </tr> 
                                        <tr>
                                          <th>Due</th>
                                          <th>Enjoyed</th>
                                          <th>Balance</th>
                                          <th>Due</th>
                                          <th>Enjoyed</th>
                                          <th>Balance</th>
                                          <th>Due</th>
                                          <th>Enjoyed</th>
                                          <th>Balance</th>
                                          <th>Due</th>
                                          <th>Enjoyed</th>
                                          <th>Balance</th>
                                        </tr> 
                                      </thead>
                                      <tbody>
                                        <?php
                                        $casual_due     = 10;
                                        $casual_enjoyed = 0;
                                        $casual_balance = 0;
                                        $medical_due     = 14;
                                        $medical_enjoyed = 0;
                                        $medical_balance = 0;
                                        $maternity_due     = 112;
                                        $maternity_enjoyed = 0;
                                        $maternity_balance = 0;
                                        $earned_due     = $earned_due?$earned_due:0;
                                        $earned_enjoyed = 0;
                                        $earned_balance = 0;
                                        ?>
                                        @if(!empty($leaves) && sizeof($leaves) > 0)
                                        @foreach($leaves as $leave)
                                        <?php
                                            $casual_due     = $casual_due-$casual_enjoyed;
                                            $casual_enjoyed = $leave->casual?$leave->casual:0;
                                            $casual_balance = $casual_due-$casual_enjoyed;
                                            $medical_due     = $medical_due-$medical_enjoyed;
                                            $medical_enjoyed = $leave->medical?$leave->medical:0;
                                            $medical_balance = $medical_due-$medical_enjoyed;
                                            $maternity_due     = $maternity_due-$maternity_enjoyed;
                                            $maternity_enjoyed = $leave->maternity?$leave->maternity:0;
                                            $maternity_balance = $maternity_due-$maternity_enjoyed;
                                            $earned_due     = $earned_due-$earned_enjoyed;
                                            $earned_enjoyed = $leave->earned?$leave->earned:0;
                                            $earned_balance = $earned_due-$earned_enjoyed;
                                        ?>
                                        <tr> 
                                          <th>{{ $leave->month_name }}</th>
                                          <th>{{ $casual_due }}</th>
                                          <th>{{ $casual_enjoyed }}</th>
                                          <th>{{ $casual_balance }}</th>
                                          <th>{{ $medical_due }}</th>
                                          <th>{{ $medical_enjoyed }}</th>
                                          <th>{{ $medical_balance }}</th>
                                          <th>{{ $maternity_due }}</th>
                                          <th>{{ $maternity_enjoyed }}</th>
                                          <th>{{ $maternity_balance }}</th>  
                                          <th>{{ $earned_due }}</th>
                                          <th>{{ $earned_enjoyed }}</th>
                                          <th>{{ $earned_balance }}</th>   
                                        </tr> 
                                        @endforeach
                                        @endif
                                      </tbody>
                                    </table>
                                </div>
                                
                            </div>
                        </div> 
                    </div>
                </div>
              @endif
            </div>
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript"> 

    $(document).ready(function(){

      // loader visibility
      // $('#searchform').submit(function() {
      //   $('#load').css('visibility', 'visible');
      //   }); 
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

        $('select.associates').select2({
            templateSelection:formatState,
            placeholder: 'Select Associate\'s ID',
            ajax: {
                url: '{{ url("hr/associate-search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { 
                        keyword: params.term
                    }; 
                },
                processResults: function (data) { 
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                                id: item.associate_id,
                                name: item.associate_name
                            }
                        }) 
                    };
              },
              cache: true
            }
        });

   // excel conversion -->
   $('#excel').click(function(){
    var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
    location.href=url
    return false
      })

    })

  function attLocation(loc){
    window.location = loc;
   } 

  //  Loader 
     document.onreadystatechange = function () {
      var state = document.readyState
      // if (state == 'interactive') {
      //      document.getElementById('leave_content_section').style.visibility="hidden";
      // } else 
      if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('leave_content_section').style.visibility="visible";
             document.getElementById('leave_content_section').scrollIntoView();
          },1000);
      }
    }
    
  function printMe1(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<style>.page-header{text-align:center;}</style>');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
 
</script>
@endpush
@endsection