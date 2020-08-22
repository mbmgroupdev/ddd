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
        height:100%;
        position:fixed;
        z-index:9999;
         background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 75%  rgba(192,192,192,0.1);  
        visibility: hidden; 
    }
    .zeroPadding{ padding: 0px; }

     @media only screen and (max-width: 1199px) {
        .ot_fields .col-sm-3{width: 33%;}
}
@media only screen and (max-width: 767px) {
        .ot_fields .col-sm-8{padding-left: 0px !important;}
        .ot_fields .col-sm-3{width: 50% !important}
        .generate_button{padding-left: 23px;}
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
                    <a href="#"> Operations </a>
                </li>
                <li class="active">Extra OT Sheet</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            {{-- <div id="load"></div> --}}
            <?php $type='extra_ot'; ?>
            @include('hr/reports/operations_radio')
            <div class="page-header">
                <h1>Operations<small> <i class="ace-icon fa fa-angle-double-right"></i> Extra OT Sheet</small></h1>
            </div>
            <div class="row">
                <div class="col-sm-12" >
                   <form id="searchform" class="col-sm-12 form-horizontal ot_fields"> 
                        <div class="col-sm-3 col-xs-3" style="padding-bottom: 15px;" >
                            <label class="col-sm-4 control-label no-padding-left" for="unit">Unit : </label>
                            <div class="col-sm-8">
                                {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'style'=>'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3" style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-left" for="floor">Floor: </label>
                            <div class="col-sm-8">
                                {{ Form::select('floor', !empty(Request::get('unit'))?$floorList:[], Request::get('floor'), ['placeholder'=>'Select Floor', 'id'=>'floor',  'style'=>'width:100%']) }}
                            </div>

                        </div>

                        <div class="col-sm-3 col-xs-3" style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-left" for="area">Area : </label>
                            <div class="col-sm-8">
                                {{ Form::select('area', $areaList, Request::get('area'), ['placeholder'=>'Select Area', 'id'=>'area', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}
                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3" style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-left no-padding-right" for="area">Department: </label>
                            <div class="col-sm-8">
                                {{ Form::select('department', !empty(Request::get('area'))?$deptList:[], Request::get('department'), ['placeholder'=>'Select Department ', 'id'=>'department', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Department field is required']) }}
                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3" style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-left" for="unit">Section : </label>
                            <div class="col-sm-8">
                              {{ Form::select('section', !empty(Request::get('department'))?$sectionList:[], Request::get('section'), ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Section field is required']) }}
                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3"style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-top no-padding-left no-padding-right" for="floor">Sub-Section: </label>
                            <div class="col-sm-8">
                                {{ Form::select('subSection', !empty(Request::get('section'))?$subSectionList:[], Request::get('subSection'), ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3" style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-left" for="start_date">Month : </label>
                            <div class="col-sm-8">

                                 <input type="text" name="start_date" id="start_date" class="monthpicker form-control" value="" data-validation="required" placeholder="Month" autocomplete="off" />

                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3"style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-left" for="end_date">Year : </label>
                            <div class="col-sm-8">
                                 <input type="text" name="end_date" id="end_date" class="yearpicker form-control" value="" data-validation="required" placeholder="Year" autocomplete="off" />
                            </div>
                        </div>

                        <div class="col-sm-3 col-xs-3"style="padding-bottom: 15px;">
                            <label class="col-sm-4 control-label no-padding-top no-padding-left no-padding-right" for="end_date">OT Range: </label>
                            <div class="col-sm-8">
                                 <input type="number" id="ot_range" class="form-control" placeholder="OT Range" name="ot_range" min="1" value="1" style="height: 33px;" />
                            </div>
                        </div>

                </div>

                <div class="col-sm-12" style="padding-right: 40px;" {{-- style="padding-top:20px;padding-right: 0px; "  --}}>
                    <div class="form-group pull-right">
                       <div class="col-sm-3 generate_button">
                            <button type="submit" id="generate"class="btn btn-primary btn-sm ">
                                <i class="fa fa-gear"></i>
                                Generate
                            </button>
                            <!-- <button type="button" onClick="printMe('PrintArea')" class="showprint btn btn-warning btn-sm " title="Print">
                                <i class="fa fa-print"></i>
                           </button>

                            <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                           </button>      -->
                       </div>
                    </div>
                </div><!--3rd row end-->
              </form>  
            </div>

            <div id="ot_content_section" class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12" id="PrintArea">
                    <!-- PAGE CONTENT BEGINS -->

                 <div id="html-2-pdfwrapper" style="margin:20px auto">
                      <div  id="form-element">
                        <!--Table here--->

                      </div>

                 </div>
                  <div id="loading" class="col-md-offset-4 text-center col-sm-4" style="margin-top:10%;">

                   <i class="fa fa-spinner fa-pulse fa-5x" ></i>

                  </div>

                <!-- PAGE CONTENT ENDS -->

                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
</div>
<script type="text/javascript">
   

$(document).ready(function(){
  $('.showprint').hide();

  // loader visibility
  $('#searchform').submit(function() {
    $('#load').css('visibility', 'visible');
    });

  // excel conversion -->
  $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
        location.href=url
        return false
    })

  // Result Table Based on Search Element
    var basedon = $("#generate");
    var action_place = $("#form-element");

    basedon.on("click", function(){

        var un_id = $("#unit").val();
        var month = $("#start_date").val();
        var year = $("#end_date").val();

      // check if #extra-OT div already exist then remove

          if($('#extra-OT').length)
          {
            $('#extra-OT').remove();
          }


          $('#wait').before('<h3 class="text-center prepend" id="data_fach_update">Data Faching...</h3>');
            var dataObj = {
                unitId :un_id,
                floor:$("#floor").val(),
                area:$("#area").val(),
                department:$("#department").val(),
                ot_range:$("#ot_range").val(),
                section:$("#section").val(),
                subSection:$("#subSection").val(),
                fromMonth:month,
                toYear:year
            };
            setTimeout(() => {
                $.ajax({
                    url: url+'/hr/reports/extra_ot_chank',
                    type: "GET",
                    data: dataObj,
                    success: function(response){
                        console.log(response);
                    }
                })
            },1000);

      // Url for Extra OT
        // $.ajax({
        //     url : "{{ url('hr/reports/extra_ot_list') }}",
        //     type: 'get',
        //     data: {
        //         unitId :un_id,
        //         floor:$("#floor").val(),
        //         area:$("#area").val(),
        //         department:$("#department").val(),
        //         ot_range:$("#ot_range").val(),
        //         section:$("#section").val(),
        //         subSection:$("#subSection").val(),
        //         fromMonth:month,
        //         toYear:year
        //       },
        //     // Loader
        //         beforeSend: function(){
        //            $('#loading').show();
        //           },
        //         complete: function(){
        //             $('#loading').hide();
        //            },

        //     success: function(data)
        //     {
        //         $('#wait').show();

        //         action_place.html(data);
        //         $('#wait').hide();
        //         $('.showprint').show(); //show print button
        //     },
        //     error: function()
        //     {
        //         alert('Not Found...');
        //     }
        // });

    });
 // HR Floor By Unit ID
        var unit  = $("#unit");
        var floor = $("#floor")
        unit.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getFloorListByUnitID') }}",
                type: 'get',
                data: {unit_id: $(this).val() },
                success: function(data)
                {
                    floor.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


        //Load Department List By Area ID
        var area       = $("#area");
        var department = $("#department");
        area.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
                type: 'get',
                data: {area_id: $(this).val() },
                success: function(data)
                {
                    department.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });

        //Load Section List by department
        var section= $("#section");

        department.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
                type: 'get',
                data: {area_id: area.val(), department_id: $(this).val() },
                success: function(data)
                {
                    section.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });

        //Load Sub Section List by Section
        var subSection= $("#subSection");

        section.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
                type: 'get',
                data: {area_id: area.val(), department_id: department.val(), section_id: $(this).val() },
                success: function(data)
                {
                    subSection.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });




///
});

// Radio button action
  function attLocation(loc){
    window.location = loc;
   }
//  Loader 
     document.onreadystatechange = function () {
      var state = document.readyState
      if (state == 'interactive') {
           document.getElementById('ot_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('ot_content_section').style.visibility="visible";
             document.getElementById('ot_content_section').scrollIntoView();
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

</script>
@endsection
