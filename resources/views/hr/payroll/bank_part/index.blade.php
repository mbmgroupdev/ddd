@extends('hr.layout')
@section('title', 'Bank Part')

@section('main-content')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
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
    .modal-h3{
      line-height: 15px !important;
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
                    <a href="#">Payroll</a>
                </li>
                <li class="active"> Bank Part</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" > 
                        <div class="panel">
                            <div class="panel-heading">
                                <h6>Monthly Salary Bank Part</h6>
                            </div>
                            <div class="panel-body">
                              <div class="row">
                                <div class="col-sm-6">
                                  <div class="form-group has-float-label select-search-group">
                                      <select name="unit[]" class="form-control capitalize select-search" id="unit" multiple placeholder="Choose Unit..." required style="height: 50px;">
                                          @foreach($unitList as $key => $value)
                                          <option value="{{ $key }}">{{ $value }}</option>
                                          @endforeach
                                      </select>
                                    <label for="unit">Unit</label>
                                  </div>
                                   
                                </div>
                                <div class="col-sm-6">
                                  <div class="row">
                                    <div class="col-sm-7">
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="location[]" class="form-control capitalize select-search" id="location" multiple placeholder="Choose Unit...">
                                              @foreach($locationList as $key => $value)
                                              <option value="{{ $key }}">{{ $value }}</option>
                                              @endforeach
                                          </select>
                                        <label for="location">Location</label>
                                      </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="row">
                                            <div class="col-sm-6 ">
                                                <div class="form-group has-float-label has-required">
                                                  <input type="text" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="0" min="0" max="{{ $salaryMax }}" autocomplete="off" />
                                                  <label for="min_sal">Range From</label>
                                                </div>
                                              </div>
                                              <div class="col-sm-6">
                                                <div class="form-group has-float-label has-required">
                                                  <input type="text" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="0" max="{{ $salaryMax }}" autocomplete="off" />
                                                  <label for="max_sal">Range To</label>
                                                </div>
                                              </div>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-3">
                                      <div class="form-group has-float-label select-search-group">
                                          <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                              <option selected="" value="">Choose...</option>
                                              <option value="0">Non-OT</option>
                                              <option value="1">OT</option>
                                          </select>
                                          <label for="otnonot">OT/Non-OT</label>
                                      </div>
                                    </div>
                                    <div class="col-3 p-0">
                                      <div class="form-group has-float-label select-search-group">
                                          <?php
                                            $payType = ['rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Duch-Bangla Bank Limited.'];
                                          ?>
                                          {{ Form::select('pay_status', $payType, 'dbbl', ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType', 'required']) }}
                                          <label for="paymentType">Payment Type</label>
                                      </div>
                                    </div>
                                    <div class="col-4">
                                      <div class="form-group has-float-label has-required">
                                        <input type="month" class="report_date form-control" id="report-date" name="month" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime('-1 month')) }}"autocomplete="off">
                                        <label for="report-date">Month</label>
                                      </div>
                                    </div>
                                    <div class="col-sm-2 p-0">
                                      <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" > Generate</button>
                                        </div>
                                    </div>
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
                  <div class="iq-card">
                    <div class="iq-card-header d-flex mb-0">
                       <div class="iq-header-title w-100">
                          <div class="row">
                            <div class="col-6">
                              <span id="result-section-btn" style="display: none; ">
                                <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                {{-- <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </button> --}}
                              </span>
                              
                            </div>
                            <div class="col-3 text-center">
                              <h4 class="card-title capitalize inline">
                                
                              </h4>
                            </div>
                            <div class="col-3">
                              <div class="row">
                                <div class="col-7 pr-0">
                                  
                                </div>
                                <div class="col-5 pl-0">
                                  
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
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
   
  $(document).ready(function(){   
      var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';

      $('#activityReport').on('submit', function(e) {
        e.preventDefault();
        salaryProcess();
      });

      function salaryProcess(){
        // console.log(loader)
        $("#result-data").html(loader);
        var unit = $('select[name="unit[]"]').val();
        var payment = $('select[name="pay_status[]"]').val();
        var month = $('input[name="month"]').val();
        var form = $("#activityReport");
        var flag = 0;
        if(unit === '' || payment === ''){
          flag = 1;
        }
        if(unit === '' && location === ''){
          flag = 1;
         
        }
        if(flag === 0){
          $("#result-section-btn").show();
          $('html, body').animate({
              scrollTop: $("#result-data").offset().top
          }, 2000);
          $.ajax({
              type: "GET",
              url: '{{ url("hr/reports/monthly-salary-bank-report") }}',
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
                
              },
              error: function (reject) {
                  console.log(reject);
              }
          });
        }else{
          console.log('required');
          $.notify('Select Required Field', 'error');
          $("#result-data").html('');
        }
      }
      $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#report_section').html())
        location.href=url;
        return false;
      });
     
  });

</script>
@endpush
@endsection