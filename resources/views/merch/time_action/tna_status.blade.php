@extends('merch.index')
@push('css')
<style type="text/css">
.table-bordered>thead>tr>th{border-top: none;border-right:0;border-left:0; }
.status-table>tbody>tr>td, .status-table>tbody>tr {
    text-align: center;
    vertical-align: middle;
    z-index: -1;
    border-top: none !important;
    border-bottom: none !important;
    border-right:1px dashed #191414;
    border-left:none !important;
    line-height: 0.5;
    padding: 0 !important;

}
.status-table>tbody>tr>th{padding: 5px;}
.status-table>tbody>tr>th:nth-child(1),
.status-table>thead>tr>th:nth-child(1) {
    background-color: #222a2d;
    border: 1px solid #222a2d !important;
    color:#fff;
    position: -webkit-sticky; /* for Safari */
    position: sticky;
    top: 0;
    left: 0;
    z-index: 1000 !important;
    font-weight: 100;
}

.status-table {
    height: 400px;
    overflow: auto;
    border-collapse: inherit;
    overflow-x:auto;
    display:block;
}

.status-table>tbody> .no-border > td{
    border: none;
}
th#today{
    color: #32a20d;
}
th.status-th-head {
    transform: rotate(-60deg);
    height: 80px;
    white-space: nowrap;
    padding: 0!important ;
    line-height: 0.5 !important ;
}
.lib{color:#fff;text-align: center;}
.lib-complete{background-color:#1b6104;}
.lib-late{background-color:#f76f0e;}
.lib-active{background-color:#07af07;}
.lib-next{
    background-color: #b7efa6;
    color: #000;
}
.lib-limit{background-color: #dfe6a4;}
.lib-due{background-color:#da3d19;}
.due-stripped{
    background: repeating-linear-gradient(
        45deg,
        #da3d19,
        #da3d19 0px,
        #d03a0d 5px,
        #b53309 15px
    )
}
.delivery{
    background-color: #2c6aa0;
    color: #fff;
    padding: 0 5px !important;
}
.late-stripped{
    background: repeating-linear-gradient( 
        45deg, #f76f0e, #f76f0e 0px, #f76f0e 5px, #d05318 15px );
}
span.date-float {
    position: relative;
    top: 20px;
    left: 5px;
}
.tr-limit{height: 5px;}
ul.color-bar {
    list-style: none;
    display: block;
    height: auto;
    border: 1px solid #d1d1d1;
    padding: 10px;
    margin: 5px auto;
    text-align: center;
    
}
.color-bar li {
    display: inline-block;
    vertical-align: middle;
    width: 10.5%;
    height: 20px;
}
span.color-label {
    width: 40px;
    display: block;
    height: 20px;
    float: left;
    margin: 0 5px;
}
span.lib-label {
    text-align: left;
    display: grid;
    padding-top: 2px;
}

[data-title]:hover:after {
    opacity: 1;
    transition: all 0.1s ease 0.5s;
    visibility: visible;
}
[data-title]:after {
    content: attr(data-title);
    position: absolute;
    bottom: -1.6em;
    left: 100%;
    padding: 4px 4px 4px 8px;
    color: #222;
    white-space: nowrap; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;  
    border-radius: 5px;  
    -moz-box-shadow: 0px 0px 4px #222;  
    -webkit-box-shadow: 0px 0px 4px #222;  
    box-shadow: 0px 0px 4px #222;  
    background-image: -moz-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, #f8f8f8),color-stop(1, #cccccc));
    background-image: -webkit-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -moz-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -ms-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -o-linear-gradient(top, #f8f8f8, #cccccc);
    opacity: 0;
    z-index: 99999;
    visibility: hidden;
}
[data-title] {
    position: relative;
}

@media only screen and (max-width: 767px) {
        
        .color-bar li{width: 33%;}
        
}
@media only screen and (max-width: 645px) {
        
        .color-bar li{width: 30%;}
        
}
@media only screen and (max-width: 480px) {
        
        .color-bar li{width: 50%;}
        
}
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Time & Action </a>
                </li>
                  
                <li class="active">TNA Status</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content" style="padding-bottom:0;"> 
            <!-- <div class="page-header">
                <h1>Time & Action <small><i class="ace-icon fa fa-angle-double-right"></i> TNA Status</small></h1>
            </div> -->

            
        
            <div class="row">
                <div class="col-sm-12">
                    <ul class="color-bar">
                       <li><span class="color-label lib-active"></span><span class="lib-label">  On Process</span></li>
                       <li><span class="color-label lib-limit"></span><span class="lib-label"> Offset</span></li>
                       <li><span class="color-label lib-complete"></span><span class="lib-label"> Completed</span></li>
                       <li><span class="color-label lib-late"></span><span class="lib-label"> Late</span></li> 
                       <li><span class="color-label late-stripped"></span><span class="lib-label">Late Day</span></li>
                       <li><span class="color-label lib-due"></span><span class="lib-label"> Due</span></li>
                       <li><span class="color-label due-stripped"></span><span class="lib-label"> Due Delay</span></li> 
                       <li><span class="color-label lib-next"></span><span class="lib-label"> Upcoming</span></li>
                       <li><span class="color-label delivery"></span><span class="lib-label"> Delivery</span></li>
                    </ul>
                    @if(!empty($tnatable))
                    <table class="table table-responsive table-bordered status-table" >
                        <thead>
                            <tr>
                                <th style="white-space:nowrap">Order Id</th>
                                {!! $tnatable['head'] !!}
                            </tr>
                        </thead>
                        <tbody>

                        @foreach($tnainfo as $key => $tnainf)
                            <tr  class="no-border">
                                <th style="border: none !important;background: transparent"></th>
                                <td colspan="{{$tnatable['column']}}"></td>
                                
                            </tr>
                            <tr class="tr-limit">
                                <th rowspan="2">{{$key}}</th>
                                @if($tnainf->start_period>0)
                                <td style="background:#d1d1d1;" colspan="{{$tnainf->start_period}}"></td>
                                @endif
                                @foreach($tnainf->data as $key1 => $lib)
                                <td class="lib lib-limit" colspan="{{$lib->offset}}" title="{{$key1}} offset: {{$lib->offset}} "></td>
                                @endforeach
                                <td class="lib lib-limit" colspan="{{$tnainf->offset_period}}"></td>
                                <td style="background:#dfe6a4;" ></td>
                                @if($tnainf->end_period>0)
                                <td style="background:#d1d1d1;" colspan="{{$tnainf->end_period}}"></td>
                                @endif
                            </tr>
                            <tr>
                                
                                @if($tnainf->start_period>0)
                                <td style="background:#d1d1d1;" colspan="{{$tnainf->start_period}}"></td>
                                @endif
                                @foreach($tnainf->data as $key1 => $lib)
                                    @if($lib->status=='Completed')
                                        <td class="lib lib-complete" title="
                                        {{$key1}} completed timely. 
System Start: {{ $lib->start_date }} 
System End : {{ $lib->end_date }}" colspan="{{$lib->column}}">
                                            {{$key1}}

                                        </td>
                                    @elseif($lib->status=='Next')
                                        <td class="lib lib-next " title="
                                        # Upcoming  
System Start: {{ $lib->start_date }} 
System End : {{ $lib->end_date }}" colspan="{{$lib->column}}">
                                            {{$key1}}
                                        </td>
                                    @elseif($lib->status=='Late')
                                        <td class="lib lib-late " title="
                                        # {{$key1}} completed in {{$lib->rowaffect}} day(s) delay.  
System Start: {{ $lib->start_date }} 
System End : {{ $lib->end_date }}" colspan="{{$lib->column}}">
                                            {{$key1}}
                                        </td>
                                        <td class="lib late-stripped" title="{{$key1}} delay" colspan="{{$lib->rowaffect}}">
                                            +{{$lib->rowaffect}}
                                        </td>
                                     @elseif($lib->status=='Due')
                                        <td class="lib lib-due "  title="
                                        # {{$key1}} on process  
System Start: {{ $lib->start_date }} 
System End : {{ $lib->end_date }}" colspan="{{$lib->column}}">
                                            {{$key1}}
                                        </td>
                                        <td class="lib due-stripped" title="{{$key1}} on process" colspan="{{$lib->rowaffect}}">
                                            +{{$lib->rowaffect}}
                                        </td>
                                    @elseif($lib->status=='Active')
                                        <td class="lib lib-active " title="
                                        # {{$key1}} on process  
System Start: {{ $lib->start_date }} 
System End : {{ $lib->end_date }}" colspan="{{$lib->column}}">
                                            {{$key1}} on process
                                        </td>
                                    @elseif($lib->status=='Fastdone')
                                        <td class="lib lib-complete " title="
                                        # {{$key1}} completed in {{$lib->fastdone}} day(s) earliar  
System Start: {{ $lib->start_date }} 
System End : {{ $lib->end_date }}" colspan="{{$lib->column}}">
                                            {{$key1}} [ {{$lib->fastdone}} day(s) early ]
                                        </td>
                                    @else
                                        <td class="lib" title="" colspan="{{$lib->column}}">
                                            {{$key1}}
                                        </td>
                                    @endif

                                @endforeach
                                <td class="lib lib-limit" colspan="{{$tnainf->offset_period}}">Offset Period</td>
                                <td class="delivery">Delivery</td>
                                @if($tnainf->end_period>0)
                                <td style="background:#d1d1d1;" colspan="{{$tnainf->end_period}}"></td>
                                @endif

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <center>No data found!</center>
                    @endif
                </div>
            </div><!--- /. Row Form 1---->
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
    var container = $('.status-table');
        scrollTo = $('#today');
        container.animate({
            scrollLeft: scrollTo.offset().left - container.offset().left-300 + container.scrollLeft()
        });
</script>
@endpush
@endsection