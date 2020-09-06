@extends('hr.layout')
@section('title', 'MMR Report')
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
        font-size: 12px;
        color: #fff;
        height: 20px;
        padding: 5px !important;
    }
    .grand_total td{
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

    <div class="panel">
        <div class="panel-heading">
            <h6>MMR Report</h6>
        </div>
        <div id="load"></div>
        <div class="row">
            <div class="col" id="html-2-pdfwrapper">
                <div class="result-data" id="generate-report">
                    {!!$report!!}
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@push('js')
<script type="text/javascript">

    $(document).ready(function(){

        $('#excel').click(function(){
            var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
                    location.href=url
                return false
            })

    })
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