@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
  <style>
    html {
     scroll-behavior: smooth;
    }
    #load{
        width:100%;
        height:50%;
        position:fixed;
        z-index:9999;
        background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 70%  rgba(192,192,192,0.1);  
        visibility: hidden;  
    }
  </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Reports </a>
                </li>
                <li class="active"> Absent Status </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content"> 
             <div id="load"></div>  
            <?php $type='absent'; ?>
                  @include('hr/reports/attendance_radio')
           <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Absent Status </small></h1>
            </div>
        
            <div class="row">
                @include('inc/message')
                
                    <form role="form" method="get" action="{{ url('hr/reports/absent_status') }}" id="searchform" >
                        <div class="col-sm-11"> 
                            <div class="form-group">
                                <div class="col-sm-3" style="padding-bottom: 10px;">
                                    {{ Form::select('associate_id', [Request::get('associate_id') => Request::get('associate_id')], Request::get('associate_id'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required']) }}  
                                </div>
                                <div class="col-sm-2" style="padding-bottom: 40px;">
                                    <input type="text" name="absent_from" id="absent_from" class="col-xs-12 monthYearpicker" value="{{ Request::get('absent_from') }}" data-validation="required" placeholder="Absent From" style="height: 32px;"/>
                                </div>
                                <div class="col-sm-2" style="padding-bottom: 40px;">
                                    <input type="text" name="absent_to" id="absent_to" class="col-xs-12 monthYearpicker" value="{{Request::get('absent_to')}}" data-validation="required" placeholder="Absent to" style="height: 32px;"/>
                                </div>
                                <div class="col-sm-5">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    @if(!empty(request()->associate_id) && !empty(request()->absent_from) && !empty(request()->absent_to))
                                    <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                        <i class="fa fa-print"></i> 
                                    </button> 
                                    <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                        <i class="fa fa-file-pdf-o"></i> 
                                    </a>
                                    <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                   </button>
                                   
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
            
                <br><br>
            </div>

            <div id="absent_content_section"  class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                    @if(!empty(request()->associate_id) && !empty(request()->absent_from) && !empty(request()->absent_to))
                    <div class="col-xs-12" id="PrintArea">
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc; width: 100%">
                            <div class="page-header" style="text-align:left;border-bottom:2px double #666">
                                <h4 style="margin:4px 10px">{{ !empty($report->unit)?$report->unit:null }}</h4>
                                <h6 style="margin:4px 10px">From {{ !empty($report->from)?$report->from:null}} To {{ !empty($report->to)?$report->to:null }}</h6>
                                <p style="margin:4px 10px; font-size: 10px;">As on {{ !empty($report->print_date)?$report->print_date:null}}</hp>
                            </div>
                            <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
                                <tr>
                                    <th style="width:40%">
                                       <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ !empty($report->associate_id)?$report->associate_id:null }}</p>
                                       <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ !empty($report->name)?$report->name:null }}</p>
                                    </th>
                                    <th>
                                       <p style="margin:0;padding:4px 10px"><strong>Date of Join </strong>: {{ !empty($report->doj)?date("d-F-Y", strtotime($report->doj)):null }} </p> 
                                       <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ !empty($report->designation)?$report->designation:null }} </p> 
                                    </th>
                                </tr> 
                            </table>

                            <table class="table" style="width:100%;border:1px solid #ccc;font-size:12px;"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Absent</th>
                                    <th>Leave</th>
                                    <th>Late</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <?php
                                    if (!empty($report->month) && is_array($report->month) && sizeof($report->month)>0 ){
                                        for($i=0;$i<sizeof($report->month);$i++)
                                        {
                                            echo "<tr><td>".$report->month[$i]."</td><td>".$report->year[$i]."</td><td>".$report->absent[$i]."</td><td>".$report->leave[$i]."</td><td>".$report->late[$i]."</td></tr>";
                                        }
                                    }
                                ?>
 
                            <!-- ends of report -->
                            </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                <!-- //ends of info  -->
            </div> 
        </div><!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">

$(document).ready(function(){ 
    // loader visibility
      // $('#searchform').submit(function() {
      //   $('#load').css('visibility', 'visible');
      //   }); 

    $('select.associates').select2({
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
                            text: item.associate_name,
                            id: item.associate_id
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
   
});
 function attLocation(loc){
    window.location = loc;
   }
//  Loader 
     document.onreadystatechange = function () {
      var state = document.readyState
      if (state == 'interactive') {
           document.getElementById('absent_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('absent_content_section').style.visibility="visible";
             document.getElementById('absent_content_section').scrollIntoView();
          },1000);
      }
    }
    
function printMe(divName)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write(document.getElementById(divName).innerHTML); 
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

//Dates entry alerts....
    $('#absent_from').on('dp.change',function(){
        $('#absent_to').val($('#absent_from').val());    
    });

    $('#absent_to').on('dp.change', function(){
        var to_date = new Date($(this).val());
        var from_date  = new Date($('#absent_from').val());
        if(from_date == '' || from_date == null){
            alert("Please enter From-Month-Year first");
            $('#absent_to').val('');
        }
        else{
            if(to_date < from_date){
                alert("Invalid!!\n From-Month-Year is latest than To-Month-Year");
                $('#absent_to').val('');
            }
        }
    });

</script>


@endsection
