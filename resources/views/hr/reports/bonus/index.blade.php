@extends('hr.layout')
@section('title', 'Bonus Report')

@section('main-content')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 8px;
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
                    <a href="#">Bonus</a>
                </li>
                <li class="active"> Bonus Report</li>
            </ul>
        </div>
        @php
          $reFor = 1;
          $reGro = 'as_department_id';
          $bonusType = bonus_type_by_id();
        @endphp
        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" > 
                        <div class="panel">
                            <div class="panel-heading">
                                <h6>
                                  Bonus Report
                                  {{-- <a class="btn btn-primary pull-right" href="{{ url('hr/operation/salary-generate') }}"><i class="fa fa-eye"></i> Bonus Process</a> --}}
                                </h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="unit" class="form-control capitalize select-search">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
                                        </div>
                                        
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="location" class="form-control capitalize select-search" id="location">
                                                <option selected="" value="">Choose Location...</option>
                                                @foreach($locationList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="location">Location</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area">
                                                <option selected="" value="">Choose Area...</option>
                                                @foreach($areaList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="area">Area</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                <option selected="" value="">Choose Department...</option>
                                            </select>
                                            <label for="department">Department</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                <option selected="" value="">Choose Section...</option>
                                            </select>
                                            <label for="section">Section</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                                <option selected="" value="">Choose Sub Section...</option> 
                                            </select>
                                            <label for="subSection">Sub Section</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                                <option selected="" value="">Choose Floor...</option>
                                            </select>
                                            <label for="floor_id">Floor</label>
                                        </div>
                                    </div> 
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                                <option selected="" value="">Choose Line...</option>
                                            </select>
                                            <label for="line_id">Line</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                <option selected="" value="">Choose...</option>
                                                <option value="0">Non-OT</option>
                                                <option value="1">OT</option>
                                            </select>
                                            <label for="otnonot">OT/Non-OT</label>
                                        </div>
                                        <div class="row">
                                          <div class="col-5 pr-0">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                              <label for="min_sal">Range From</label>
                                            </div>
                                          </div>
                                          <div class="col-1 p-0">
                                            <div class="c1DHiF text-center">-</div>
                                          </div>
                                          <div class="col-6">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                              <label for="max_sal">Range To</label>
                                            </div>
                                          </div>
                                        </div>
                                        
                                        <input type="hidden" id="reportformat" name="report_format" value="{{ $reFor }}">
                                        <input type="hidden" id="reportGroup" name="report_group" value="{{ $reGro }}">
                                        
                                    </div>
                                    <div class="col-3">
                                        
                                        <div class="form-group has-float-label has-required select-search-group">
                                          {{ Form::select('sheet_id', $bonusSheet, Request::get('sheet_id')??null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'reportType', 'required']) }}
                                            <label for="reportType">Report Type</label>
                                        </div>
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <?php
                                              $bonusStatus = [
                                                'all'=>'All',
                                                'maternity'=>'Maternity',
                                                'lessyear'=>'Less than a Year',
                                                'partial' => 'Partial',
                                                'special' => 'Special'
                                              ];
                                            ?>
                                            {{ Form::select('emp_type', $bonusStatus, 'all', ['placeholder'=>'Select Bonus type ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus', 'required']) }}
                                            <label for="estatus">Bonus type</label>
                                        </div>
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <?php
                                              $payType = ['all'=>'All','cash' => 'Cash','dbbl'=>'DBBL','rocket'=>'Rocket'];
                                            ?>
                                            {{ Form::select('pay_status', $payType, 'all', ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType', 'required']) }}
                                            <label for="paymentType">Payment Type</label>
                                        </div>
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" ><i class="fa fa-search"></i> Filter</button>
                                        </div>

                                    </div>   
                                </div>
                                
                              

                            </div>
                            <div class="single-employee-search" id="single-employee-search" style="display: none;">
                              <div class="form-group">
                                <input type="text" name="employee" class="form-control" placeholder="Search Employee Associate ID..." id="searchEmployee" autocomplete="off">
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
                  <div class="iq-card">
                    <div class="iq-card-header d-flex mb-0">
                       <div class="iq-header-title w-100">
                          <div class="row">
                            <div class="col-5">
                              <span id="result-section-btn" style="display: none; ">
                                <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </button>
                              </span>
                             
                              <div class="salary-section text-left inline">
                                
                                
                              </div>
                            </div>
                            <div class="col-2 text-center">
                              <h4 class="card-title capitalize inline">
                                
                              </h4>
                            </div>
                            <div class="col-5">
                              <div class="row">
                                <div class="col-4 pr-0">
                                  
                                </div>
                                <div class="col-5 pr-0">
                                  <div class="format">
                                    <div class="form-group has-float-label select-search-group mb-0">
                                        <?php
                                            $type = ['as_unit_id'=>'Unit','as_department_id'=>'Department','as_designation_id'=>'Designation', 'as_section_id'=>'Section'];
                                        ?>
                                        {{ Form::select('report_group_select', $type, $reGro, ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                        <label for="reportGroupHead">Report Format</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-3 pl-0">
                                  <div class="text-right">
                                    <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                      <i class="las la-th-large"></i>
                                    </a>
                                    <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                      <i class="las la-list-ul"></i>
                                    </a>
                                    
                                  </div>
                                </div>
                              </div>
                              
                              
                            </div>
                          </div>
                       </div>
                    </div>
                    <div class="iq-card-body no-padding">
                      <div class="result-data" id="result-data">
                        
                      </div>
                    </div>
                 </div>
                  
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@include('common.right-modal')
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
 
      $('#activityReport').on('submit', function(e) {
        e.preventDefault();
        bonusProcess();
      });
      $(".grid_view, .list_view").click(function() {
          var value = $(this).attr('id');
          // console.log(value);
          $("#reportformat").val(value);
          $('input[name="employee"]').val('');
          bonusProcess();
        });
          
        $("#reportGroupHead").on("change", function(){
          var group = $(this).val();
          $("#reportGroup").val(group);
          bonusProcess();
        });
      function bonusProcess(){
        // console.log(loader)
        $("#result-data").html(loaderContent);
        $("#single-employee-search").hide();
        
        var sheetId = $('input[name="sheet_id"]').val();
        var format = $('input[name="report_format"]').val();
        var form = $("#activityReport");
        var flag = 0;
        if(sheetId === '' && sheetId === null){
          flag = 1;
        }
        
        if(flag === 0){
          $("#result-section-btn").show();
          $('html, body').animate({
              scrollTop: $("#result-data").offset().top
          }, 2000);
          $.ajax({
              type: "GET",
              url: '{{ url("hr/reports/bonus-report") }}',
              data: form.serialize(), // serializes the form's elements.
              success: function(response)
              {
                // console.log(response);
                if(response !== 'error'){
                  $("#result-data").html(response);
                }else{
                  // console.log(response);
                  $("#result-data").html('');
                }
                if(format == 0 && response !== 'error'){
                  $("#single-employee-search").show();
                  $('.list_view').addClass('active').attr('disabled', true);
                  $('.grid_view').removeClass('active').attr('disabled', false);
                }else{
                  $("#single-employee-search").hide();
                  $('.grid_view').addClass('active').attr('disabled', true);
                  $('.list_view').removeClass('active').attr('disabled', false);
                }
              },
              error: function (reject) {
                  console.log(reject);
              }
          });
        }else{
          console.log('required');
          $("#result-data").html('');
        }
      }
      
      // change unit
      $('#unit').on("change", function(){
          $.ajax({
              url : "{{ url('hr/attendance/floor_by_unit') }}",
              type: 'get',
              data: {unit : $(this).val()},
              success: function(data)
              {
                  $('#floor_id').removeAttr('disabled');
                  
                  $("#floor_id").html(data);
              },
              error: function(reject)
              {
                 console.log(reject);
              }
          });

          //Load Line List By Unit ID
          $.ajax({
             url : "{{ url('hr/reports/line_by_unit') }}",
             type: 'get',
             data: {unit : $(this).val()},
             success: function(data)
             {
                  $('#line_id').removeAttr('disabled');
                  $("#line_id").html(data);
             },
             error: function(reject)
             {
               console.log(reject);
             }
          });
      });
      //Load Department List By Area ID
      $('#area').on("change", function(){
          $.ajax({
             url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
             type: 'get',
             data: {area_id : $(this).val()},
             success: function(data)
             {
                  $('#department').removeAttr('disabled');
                  
                  $("#department").html(data);
             },
             error: function(reject)
             {
               console.log(reject);
             }
          });
      });

      //Load Section List By department ID
      $('#department').on("change", function(){
          $.ajax({
             url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
             type: 'get',
             data: {area_id: $("#area").val(), department_id: $(this).val()},
             success: function(data)
             {
                  $('#section').removeAttr('disabled');
                  
                  $("#section").html(data);
             },
             error: function(reject)
             {
               console.log(reject);
             }
          });
      });
      //Load Sub Section List by Section
      $('#section').on("change", function(){
         $.ajax({
           url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
           type: 'get',
           data: {
             area_id: $("#area").val(),
             department_id: $("#department").val(),
             section_id: $(this).val()
           },
           success: function(data)
           {
              $('#subSection').removeAttr('disabled');
              
              $("#subSection").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
         });
      });

  
</script>
@endpush
@endsection