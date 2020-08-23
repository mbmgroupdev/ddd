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
        height:40%;
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
                <li class="active"> Leave Report </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content"> 
              
            <?php $type='leave_report'; ?>
                  @include('hr/reports/attendance_radio')
           <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Leave Report </small></h1>
            </div>
            
                <div class="row">
                    @include('inc/message')
                    
                        <form role="form" method="get" action="{{ url('hr/reports/leave_report_generate') }}" id="searchform" class="form-horizontal" >
                            <div class="col-sm-12">
                                
                                <div class="col-sm-3 no-padding-left">
                                        <label class="col-sm-3 control-label no-padding-right" for="unit_id">Unit</label>
                                        <div class="col-sm-9">
                                            <select class="col-xs-12 col-sm-12" name="unit_id" id="unit_id" required ="required" >
                                                <option value="">Select Unit</option>
                                                @foreach($unit as $u)
                                                    <option value="{{$u->hr_unit_id}}"
                                                        @if($unit_id == $u->hr_unit_id)
                                                            selected="selected" 
                                                        @endif            
                                                    >{{$u->hr_unit_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>

                                <div class="col-sm-2 no-padding-left" style="padding-bottom: 20px;">
                                   
                                        <label class="col-sm-3 control-label no-padding-right" for="from_date">From</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="from_date" id="from_date" class="col-xs-12 col-sm-12 datepicker" placeholder="Y-m-d" style="height: 32px;" value="{{isset($from)?$from:''}}">
                                        </div>
                                    
                                </div>

                                <div class="col-sm-2 no-padding-left" style="padding-bottom: 50px;">
                                   
                                      <label class="col-sm-2 control-label no-padding-right" for="from_date">To</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="to_date" id="to_date" class="col-xs-12 col-sm-12 datepicker" placeholder="Y-m-d" style="height: 32px;" value="{{isset($to)?$to:''}}">
                                        </div>
                                </div>
                              
                            
                                <div class="col-sm-5" style="padding-bottom: 30px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    @if (!empty($grouped_data)) 
                                    <button type="button" onClick="printMe('search_result')" class="btn btn-warning btn-sm" title="Print">
                                        <i class="fa fa-print"></i> 
                                    </button>
                                    <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF" disabled>
                                        <i class="fa fa-file-pdf-o"></i> 
                                    </a>
                                    <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </form>


                    
                    @if(!empty($grouped_data))
                    <div class="col-sm-12">
                        <div class="col-sm-10 col-sm-offset-1  table-responsive" id="search_result"  style="overflow-y: scroll; height:600px; border: 1px solid whitesmoke;">
                        @if($grouped_data->isEmpty())
                            <h3 class="text-center" style="margin-top: 200px;">No Data Found</h3>
                            <script type="text/javascript">
                                setTimeout(function(){
                                    $('#search_result').hide();  
                                }, 3000);
                            </script>
                        @endif
                        @foreach($grouped_data as $key => $data)
                            <h2 class="text-center" style="color: blue;">Leave Report</h2>
                            <h3 class="text-center" style="color: green;" >Unit: {{$key}}</h4><hr>
                            <p>Run Time:&nbsp;<?php echo date('l\&\\n\b\s\p\;F \&\\n\b\s\p\;d \&\\n\b\s\p\;Y \&\\n\b\s\p\;h:m a'); ?></p>
                            <table cellpadding="0" cellspacing="0" border="1" width="100%" class="table table-default">
                                <thead>
                                    <th style="font-size: 12px; font-weight: bold; width: 5%;  text-align: center; height: 32px;">##</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left; height: 32px;">Associate ID</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">Name</th>
                                    {{-- <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">Unit</th> --}}
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">Department</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">Designation</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">Leave Type</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">From</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">To</th>
                                    <th style="font-size: 12px; font-weight: bold; width: 10%;  text-align: left;">Status</th>
                                </thead>
                                <tbody>
                                    <?php $cnt=1; ?>
                                    @foreach($data As $d)
                                    <tr>
                                        <td style=" text-align: center;">{{$cnt++}}</td>
                                        <td style=" text-align: left;">{{$d->leave_ass_id }}</td>
                                        <td style=" text-align: left;">{{$d->as_name }}</td>
                                        {{-- <td style=" text-align: left;">{{$d->hr_unit_name }}</td> --}}
                                        <td style=" text-align: left;">{{$d->hr_department_name }}</td>
                                        <td style=" text-align: left;">{{$d->hr_designation_name }}</td>
                                        <td style=" text-align: left;">{{$d->leave_type }}</td>
                                        <td style=" text-align: left;">{{$d->leave_from }}</td>
                                        <td style=" text-align: left;">{{$d->leave_to }}</td>
                                        @if($d->leave_status == '1')
                                            <td style="border: 1px solid; text-align: left; color: green;">Approved</td>
                                        @elseif($d->leave_status == '0')
                                            <td style="border: 1px solid; text-align: left; color: blue;">Pending</td>
                                        @else
                                            <td style="border: 1px solid; text-align: left; color: red;">Rejected</td>
                                        @endif
                                    </tr>

                                    @endforeach

                                    
                                </tbody>
                            </table>
                            <br><br>
                        @endforeach
                        </div>
                        
                    </div>
                    @endif


                </div>

                <div id="load"></div> 
        </div> {{-- page-content --}}
    </div> {{-- main-content-inner --}}
</div> {{-- main-content --}}
<script type="text/javascript">
    $(document).ready(function(){
        
        //date validation------------------
        $('#from_date').on('dp.change',function(){
             $('#to_date').val($('#from_date').val());   
        });

        setTimeout(function(){
            var x =  {!! json_encode($to) !!};
            // console.log(x);
            $('#to_date').val(x);
        }, 2000);


        $('#to_date').on('dp.change',function(){
            var end     = new Date($(this).val());
            var start   = new Date($('#from_date').val());
            // if(start == '' || start == null){
            //     alert("Please enter From-Date first");
            //     $('#to_date').val('');
            // }
            // else{
                 if(end < start){
                    alert("Invalid!!\n From-Date is latest than To-Date");
                    $('#to_date').val('');
                // }
            }
        });
        //date validation end---------------

        $('#singe_unit_search_button').on('click',function(){

        });

        $('#excel').click(function(){
            var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#search_result').html()) 
                    location.href=url
                return false
        });

        function printMe(area){
            var myWindow=window.open('','','width=800,height=800');
            myWindow.document.write(document.getElementById(area).innerHTML); 
            myWindow.document.close();
            myWindow.focus();
            myWindow.print();
            myWindow.close();
        }

        //  Loader 
        $('#searchform').submit(function() {
            $('#load').css('visibility', 'visible');
        });
        
        document.onreadystatechange = function () {
          var state = document.readyState
          if (state == 'interactive') {
               document.getElementById('search_result').style.visibility="hidden";
          } else if (state == 'complete') {
              setTimeout(function(){
                 document.getElementById('interactive');
                 document.getElementById('load').style.visibility="hidden";
                 document.getElementById('search_result').style.visibility="visible";
                 document.getElementById('search_result').scrollIntoView();
              },1000);
          }
        }
    });
    function attLocation(loc){
        window.location = loc;
       }
</script>

@endsection