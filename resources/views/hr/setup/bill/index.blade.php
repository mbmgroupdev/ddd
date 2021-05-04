@extends('hr.layout')
@section('title', 'Bill Setting')

@section('main-content')
@push('js')
  <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />

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
                    <a href="#">Setup</a>
                </li>
                <li class="active"> Bill Setting</li>
            </ul>
        </div>

        <div class="page-content"> 
          <div class="row">
             <div class="col-lg-2 pr-0">
                 <!-- include library menu here  -->
                @include('hr.setup.bill.bill_menu')
             </div>
             <div class="col-lg-10 mail-box-detail">
                  <div class="iq-accordion career-style mat-style  ">
                      <div class="iq-card iq-accordion-block mb-3 p-2">
                         <div class="active-mat clearfix">
                            <div class="container-fluid">
                               <div class="row">
                                  <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  Bill Setup </span> </a></div>
                               </div>
                            </div>
                         </div>
                         <div class="accordion-details">
                            <div class="row1">
                                <div class="col-12">
                                  <div class="panel-body pb-0">
                                     <form class="form-horizontal" id="billForm" role="form" enctype="multipart/form-data">
                                          {{ csrf_field() }} 
                                          <div class="row">
                                            <div class="offset-3 col-6">
                                                <div class="form-group has-float-label has-required select-search-group">
                                                  {{ Form::select('unit[]', $unitList,'', ['id'=>'unit', 'class'=> 'form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                  <label for="unit">Unit</label>
                                                </div>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="offset-sm-2 col-sm-8">
                                              <div class="row">
                                                <div class="col-sm-4">
                                                  <div class="form-group has-float-label has-required select-search-group">
                                                    {{ Form::select('bill_type_id', $billTypeList,'', ['id'=>'bill_type_id', 'class'=> 'form-control select-search no-select', 'required', 'placeholder'=>'Select Bill Type']) }}
                                                    <label for="bill_type_id">Bill Type</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-4">
                                                  <div class="form-group has-float-label has-required">
                                                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Entre Bill Amount"required="required" value="0" autocomplete="off" onClick="this.select()" />
                                                    <label for="amount">Bill Amount</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-4">
                                                  <div class="form-group has-float-label select-search-group">
                                                      <select name="as_ot" class="form-control capitalize select-search" id="as_ot" >
                                                          <option value="0">Non-OT</option>
                                                          <option value="1" selected>OT</option>
                                                          <option value="2">Both</option>
                                                      </select>
                                                      <label for="as_ot">OT/Non-OT/Both</label>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="row">
                                                <div class="col-sm-3 pr-0">
                                                  <div class="form-group has-float-label has-required select-search-group">
                                                    @php
                                                      $payType = ['1'=>'Present', '2'=>'Working Hour', '3'=> 'OT Hour', '4'=>'Out-time']
                                                    @endphp
                                                    {{ Form::select('pay_type', $payType,'1', ['id'=>'pay_type', 'class'=> 'form-control select-search no-select pay_type', 'required', 'data-duration'=>'duration']) }}
                                                    <label for="pay_type">Pay Type</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-3 pr-0">
                                                  <div class="form-group has-float-label has-required">
                                                    <input type="text" class="form-control" id="duration" name="duration" placeholder="Entre Bill duration" value="0" autocomplete="off" onClick="this.select()" />
                                                    <label for="duration">Present</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-3 pr-0">
                                                    <div class="form-group has-float-label has-required">
                                                        <input type="date" class="report_date datepicker form-control" id="start_date" name="start_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                        <label for="start_date">Start Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group has-float-label ">
                                                        <input type="date" class="report_date datepicker form-control" id="end_date" name="end_date" placeholder="Y-m-d" value="" autocomplete="off" />
                                                        <label for="end_date">End Date</label>
                                                    </div>
                                                </div>
                                                
                                              </div> 
                                              <div class="row">
                                                <div class="col-sm-12"><hr class="mt-0"></div>
                                                <div class="col-sm-5">
                                                  <div class="custom-control custom-switch">
                                                    <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                                    <label class="custom-control-label" for="specialCheck">Advanced</label>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="rule-section">
                                                  <div class="iq-card-spacial pb-0">
                                                      <div class="iq-sp-head">
                                                          <p class="card-title"> </p>
                                                      </div>
                                                      <div class="iq-sp-body pb-0">
                                                         <div class="row">
                                                              <div class="offset-sm-3 col-sm-9">
                                                                  <div class="specialsection" id="special-section">
                                                                      <div class="row">
                                                                          <div class="col-sm-6">
                                                                              <div class="form-group has-required has-float-label select-search-group">
                                                                                  <select name="" id="special-type-for" class="form-control">
                                                                                      <option value=""> - Select - </option>
                                                                                      <option value="as_location"> Location</option>
                                                                                      <option value="as_department_id"> Department</option>
                                                                                      <option value="as_designation_id"> Designation</option>
                                                                                      <option value="as_section_id"> Section</option>
                                                                                      <option value="as_subsection_id"> Sub Section</option>
                                                                                      <option value="outtime"> Out-time</option>
                                                                                  </select>
                                                                                  <label for="special-type-for">Type </label>
                                                                              </div>
                                                                          </div>
                                                                          <div class="col-sm-2" >
                                                                              <div class="form-group">
                                                                                  <button class="btn btn-outline-primary sync-type" data-category="special" type="button" id="special-sync-type">
                                                                                      <i class="las la-sync"></i>
                                                                                  </button> 
                                                                              </div>
                                                                          </div>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                          <div id="special-targettype"></div>
                                                          <div id="special-appendType" class="appendType"></div>
                                                      </div>
                                                  </div>

                                                  <div class="rule-overlay" id="rule-overlay"></div>
                                              </div>
                                              <div class="process-btn">
                                                <div class="form-group pull-right">
                                                    <button class="btn btn-primary" type="button" onClick="saveBill()">
                                                        <i class=" fa fa-save"></i> Save
                                                    </button>
                                                        
                                                </div>
                                            </div>
                                            </div>
                                          </div>
                                      </form> 
                                  </div>
                                </div>
                            </div>
                         </div>
                      </div>
                      <div class="iq-card iq-accordion-block p-2 accordion-active">
                         <div class="active-mat clearfix">
                            <div class="container-fluid">
                               <div class="row">
                                  <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  List Bill Setup </span> </a></div>
                               </div>
                            </div>
                         </div>
                         <div class="accordion-details">
                            <div class="row1">
                              <div class="col-12">
                                  <div class="panel-body worker-list">
                                    <table id="global-datatable" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
                                      <thead>
                                          <tr>
                                              <th width="5%">SL.</th>
                                              <th width="30%">Unit</th>
                                              <th width="15%">Bill Type</th>
                                              <th width="15%">Eligible Parameter</th>
                                              <th width="8%">Amount</th>
                                              <th width="15%">OT Status</th>   
                                              <th width="25%">Start Date</th> 
                                          </tr>
                                      </thead>
                                      <tbody>
                                        @php $i=0; @endphp
                                        @if(count($billList) > 0)
                                          @foreach($billList as $bill)
                                            @php
                                              $billName = $billType[$bill->bill_type_id]['name']??'';
                                            @endphp
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $unit[$bill->unit_id]['hr_unit_name'] }}</td>
                                            <td><a style="color:#0aa6b7; font-weight: 600" data-id="{{ $bill->id }}" data-head="{{ $billName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details" class="bill-type">{{ $billName }}</a></td>
                                            
                                            <td>
                                              @if($bill->pay_type == 1)
                                                All Present
                                              @elseif($bill->pay_type == 2)
                                                Working Hour
                                              @elseif($bill->pay_type == 3)
                                                OT Hour
                                              @elseif($bill->pay_type == 4)
                                                Out Punch
                                              @endif
                                              {{ ($bill->pay_type == 1)?'':'- '. $bill->duration }}
                                            </td>
                                            <td>{{ $bill->amount }}</td>
                                            <td>
                                              @if($bill->as_ot == 0)
                                              Non-OT
                                              @elseif($bill->as_ot == 1)
                                              OT
                                              @else
                                              Both
                                              @endif
                                            </td>
                                            <td>{{ $bill->start_date }}</td>
                                            
                                          @endforeach
                                        @else
                                          <tr>
                                            <td colspan="7" class="text-center"> No record found! </td>
                                          </tr>
                                        @endif
                                      </tbody>
                                  </table>
                                </div>
                                  <!-- PAGE CONTENT ENDS -->
                              </div>
                              <!-- /.col -->
                          </div>
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
  <script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
  <script src="{{ asset('assets/js/moment.min.js')}}"></script>
  <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
  <script src="{{ asset('assets/js/bill.js')}}"></script>
  <script>
    $(document).on('keypress', function(e) {
        var that = document.activeElement;
        if( e.which == 13 ) {
            if($(document.activeElement).attr('type') == 'submit'){
                return true;
            }else{
                e.preventDefault();
            }
        }           
    });
  
    function saveBill() {
      var curStep = $("#billForm"),
        curInputs = curStep.find("input[type='text'],input[type='hidden'],input[type='number'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
        isValid = true;
      $(".form-group").removeClass("has-error");
      for (var i = 0; i < curInputs.length; i++) {
         if (!curInputs[i].validity.valid) {
            isValid = false;
            $(curInputs[i]).closest(".form-group").addClass("has-error");
         }
      }
      if (isValid){
         $.ajax({
            type: "POST",
            url: '{{ url("/hr/setup/bill-setting") }}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: curStep.serialize(), // serializes the form's elements.
            success: function(response)
            {
              console.log(response);
              // $.each(response.message, function(index, el) {
              //   $.notify(el, response.type);
              // });
              // if(response.type === 'success'){
              //   setTimeout(function(){
              //     window.location.href = response.url;
              //   }, 500);
              // }
              $(".app-loader").hide();
            },
            error: function (reject) {
              $(".app-loader").hide();
              if( reject.status === 400) {
                  var data = $.parseJSON(reject.responseText);
                   $.notify(data.message, data.type);
              }else if(reject.status === 422){
                var data = $.parseJSON(reject.responseText);
                var errors = data.errors;
                // console.log(errors);
                for (var key in errors) {
                  var value = errors[key];
                  $.notify(value[0], 'error');
                }
                 
              }
            }
         });
      }else{
          $(".app-loader").hide();
          $.notify("Some field are required", 'error');
      }
    }

    $(document).on('click', '.bill-type', function(event) {
      $('#right_modal_jobcard').modal('show');
      let id = $(this).data('id');
      let head = $(this).data('head');
      $('#modal-title-right').html(head+' Details');
      $("#content-result").html(loaderContent);

      $.ajax({
            url: '{{ url('hr/setup/bill-setting') }}'+'/'+id,
            type: "GET",
            success: function(response){
              // console.log(response);
                if(response !== 'error'){
                  setTimeout(function(){
                    $("#content-result").html(response);
                  }, 1000);
                }else{
                  console.log(response);
                }
            }
        });
    });

    
  </script>
@endpush
@endsection