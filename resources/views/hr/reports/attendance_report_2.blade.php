@extends('hr.layout')
@section('title', 'Attendance Summary Report')
@section('main-content')
@push('css')
<style>
   html {
     scroll-behavior: smooth;
    }
    #load{
        width:100%;
        height:100%;
        position:fixed;
        z-index:9999;
        background:url({{asset('assets/img/loader.gif')}}) no-repeat 35% 70%  rgba(192,192,192,0.1);
        visibility: hidden;

    }
    .tbl-header{
        border: 1px solid;
        font-weight: bold;
    }
    .tbl-header th{
        border-color: #31708f;
        padding: 10px !important;
        font-size: 12px;
    }
    .grand_total{
        /*font-weight: bold;*/
        font-size: 12px;
        color: #fff;
        height: 20px;
        padding: 5px !important;
    }
    .grand_total td{
        /*font-weight: bold;*/
        font-size: 12px;
        color: #fff;
        height: 20px;
        padding: 5px !important;
    }

    tbody>tr>td{
        padding-left: 10px !important;
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        padding-right: 10px !important;
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
        <li class="active"> Attendance Summary Report</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div id="load"></div>
        <div class="row">
            <div class="col">
                <form role="form"  id="searchform" method="get" action="#">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        
                                        {{ Form::select('unit', $unitList, request()->unit, ['placeholder'=>'Select Unit', 'id'=>'unit', 'class'=> ' no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="unit"> Unit </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="date" class=" form-control" id="date" name="date" placeholder="Y" required="required" value="{{ request()->date??date('Y-m-d') }}" autocomplete="off" />
                                        <label  for="date"> Date </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary btn-sm" id="report"><i class="fa fa-save"></i> Generate</button>
                                    <div class="buttons hide inline" style="display: initial;">
    
                                    <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                    </button>
                                    
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
            <div class="col" id="html-2-pdfwrapper">
                <div class="result-data" id="generate-report"></div>
            </div>
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript">
    const loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';

    $(document).ready(function(){
        @if(request()->unit != null && request()->date != null)
            loaddata($('#report'));
        @endif
        $('#excel').click(function(){
            var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
                    location.href=url
                return false
            });

        

        $(document).on("click","#report", function(){ 
            var btn = $(this);
            loaddata(btn);
        });


    });

    function loaddata(btn)
    {
        var unit = $('#unit').val(),
            date = $('#date').val()
        if(unit && date){
            $('.buttons').addClass('hide');
            btn.attr("disabled",true);
            $("#generate-report").html(loader);
            $.ajax({
                url : "{{ url('hr/reports/get_att_summary_report') }}",
                type: 'get',
                data: {unit : unit, date : date},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(data)
                {
                    $("#generate-report").html(data);
                    btn.attr("disabled",false);
                    $('.buttons').removeClass('hide');
                },
                error: function()
                {
                    $.notify('failed...', 'error');
                    btn.attr("disabled",false);
                }
            });
        }else{
            $.notify('Please select unit & date!', 'error');
            $("#generate-report").html('');

        }
    }

    //  Loader
    document.onreadystatechange = function () {
        var state = document.readyState
        if (state == 'interactive') {
           document.getElementById('html-2-pdfwrapper').style.visibility="hidden";
        } else if (state == 'complete') {
            setTimeout(function(){
                document.getElementById('interactive');
                document.getElementById('load').style.visibility="hidden";
                document.getElementById('html-2-pdfwrapper').style.visibility="visible";
                document.getElementById('html-2-pdfwrapper').scrollIntoView();
            },1000);
        }
    }


    function printMe(el)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:10px;">');
        myWindow.document.write(document.getElementById(el).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    

    

</script>
@endpush
@endsection