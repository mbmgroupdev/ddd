@extends('hr.layout')
@section('title', 'Bill Setting')

@section('main-content')
@push('js')
  <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        .iq-accordion-block{
            padding: 10px 0;
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
                    <a href="#">Setup</a>
                </li>
                <li class="active"> Bill Setting</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block accordion-active">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">Bill Setting </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                          <div class="col-12">
                             <form class="form-horizontal" role="form" method="post" action="{{ route('bill-setting.store') }}">
                                  {{ csrf_field() }} 
                                  <div class="panel">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                        {{ Form::select('unit[]', $unitList,'', ['id'=>'unit', 'class'=> 'form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                        <label for="unit">Unit</label>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-sm-2">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="number" class="form-control" id="tiffin-bill" name="tiffin" placeholder="Entre Tiffin Bill"required="required" value="0"autocomplete="off" onClick="this.select()" />
                                                      <label for="tiffin-bill">Tiffin Bill</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="number" class="form-control" id="dinner-bill" name="dinner" placeholder="Entre Dinner Bill"required="required" value="0"autocomplete="off" onClick="this.select()" />
                                                      <label for="dinner-bill">Dinner Bill</label>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div class="row">
                                              <div class="col">
                                                <div class="custom-control custom-switch">
                                                  <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                                  <label class="custom-control-label" for="specialCheck">Special</label>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="row">
                                              <div class="offset-sm-2 col-sm-8">
                                                <div class="specialsection" id="special-section" style="display: none;">
                                                  <div class='row'>
                                                    <div class='col-sm-12 table-wrapper-scroll-y table-custom-scrollbar'>
                                                        <table class="table table-bordered table-hover table-fixed" id="itemList">
                                                            <thead>
                                                                <tr class="text-center active">
                                                                    <th width="2%">
                                                                        <button class="btn btn-sm btn-outline-success addmore" type="button"><i class="las la-plus-circle"></i></button>
                                                                    </th>
                                                                    <th width="2%">SL.</th>
                                                                    <th>Designation Name</th>
                                                                    <th width="15%">Tiffin Bill</th>
                                                                    <th width="15%">Dinner Bill</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem1" onClick="deleteItem(this.id)">
                                                                            <i class="las la-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                    <td>1</td>
                                                                    
                                                                    <td>
                                                                      <input type="text" data-type="designation" name="designation[]" id="designation_1" class="form-control autocomplete_txt" autocomplete="off">
                                                                    </td>
                                                                    
                                                                    
                                                                    <td>
                                                                        <input type="number" step="any" min="0" value="0" name="special_tiffin[]" id="tiffin_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="any" min="0" value="0" name="special_dinner[]" id="dinner_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                                    </td>
                                                                    
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                  </div>
                                                
                                                </div>
                                              </div>
                                            </div>
                                            <div class="row">
                                                <div class="offset-sm-5 col-sm-2">
                                                    <div class="submit-invoice invoice-save-btn">
                                                        <button type="submit" class="btn btn-outline-success btn-lg text-center"><i class="fa fa-save"></i> Confirm</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                  </div>
                              </form>
                          </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card iq-accordion-block  ">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> List of bill setting </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                        <div class="col-12">
                            <form class="" role="form" id="unitWiseSalary"> 
                                <div class="panel mb-0">
                                    
                                    <div class="panel-body pb-0">
                                        <table class="table table-bordered table-hover table-head table-fixed" id="itemList">
                                            <thead>
                                                <tr class="text-center active">
                                                    
                                                    <th class="vertical-align" rowspan="2" width="2%">SL.</th>
                                                    <th class="vertical-align" rowspan="2" >Unit Name</th>
                                                    <th class="vertical-align" rowspan="2" >Status</th>
                                                    <th class="vertical-align" rowspan="2" >Tiffin Bill</th>
                                                    <th class="vertical-align" rowspan="2" >Dinner Bill</th>
                                                    <th class="vertical-align" rowspan="2">Action</th>
                                                    <th class="vertical-align" colspan="4">Special</th>
                                                </tr>
                                                <tr>
                                                  <th>Designation</th>
                                                  <th>Tiffin Bill</th>
                                                  <th>Dinner Bill</th>
                                                  <th>Status</th>
                                                  
                                                </tr>
                                            </thead>
                                            <tbody>
                                              
                                              @if(count($billList) > 0)
                                                @php 
                                                  $getUnit = unit_by_id();
                                                  $getDesignation = designation_by_id();
                                                  $i = 0;
                                                @endphp
                                                @foreach($billList as $bill)

                                                @if(count($bill->available_special) > 0)
                                                  <tr>
                                                      <td rowspan="{{ count($bill->available_special)+1 }}">{{ ++$i }}</td>
                                                      <td rowspan="{{ count($bill->available_special)+1 }}">{{ $getUnit[$bill->unit_id]['hr_unit_name']??'' }}</td>
                                                      <td rowspan="{{ count($bill->available_special)+1 }}">
                                                        {{ $bill->status==1?'Active':'Inactive' }}
                                                        {{-- <div class="custom-control custom-switch">
                                                          <input name="status" type="checkbox" class="custom-control-input" id="status-{{$bill->id}}" value="{{ $bill->status==1?1:0 }}" {{ $bill->status==1?'checked':'' }}>
                                                          <label class="custom-control-label" for="status-{{$bill->id}}"></label>
                                                        </div> --}}
                                                      </td>
                                                      <td rowspan="{{ count($bill->available_special)+1 }}">{{ $bill->tiffin_bill }}</td>
                                                      <td rowspan="{{ count($bill->available_special)+1 }}">{{ $bill->dinner_bill }}</td>
                                                      <td rowspan="{{ count($bill->available_special)+1 }}"></td>
                                                  </tr>
                                                  @foreach($bill->available_special as $special)
                                                  <tr>
                                                      <td>{{ $getDesignation[$special->designation_id]['hr_designation_name']??'' }}</td>
                                                      <td>{{ $special->tiffin_bill }}</td>
                                                      <td>{{ $special->dinner_bill }}</td>
                                                      <td>
                                                        {{ $special->status==1?'Active':'Inactive' }}
                                                        {{-- <div class="custom-control custom-switch">
                                                          <input name="status" type="checkbox" class="custom-control-input" id="statusSpecial-{{$special->id}}" value="{{ $special->status==1?1:0 }}" {{ $special->status==1?'checked':'' }} onClick="specialStatus">
                                                          <label class="custom-control-label" for="statusSpecial-{{$special->id}}"></label>
                                                        </div> --}}
                                                      </td>
                                                      
                                                  </tr>
                                                  @endforeach
                                                @else
                                                  <tr>
                                                    <td>{{ ++$i }}</td>
                                                    <td>{{ $getUnit[$bill->unit_id]['hr_unit_name']??'' }}</td>
                                                    <td>
                                                      {{ $bill->status==1?'Active':'Inactive' }}
                                                      {{-- <div class="custom-control custom-switch">
                                                        <input name="status" type="checkbox" class="custom-control-input" id="status-{{$bill->id}}" value="{{ $bill->status==1?1:0 }}" {{ $bill->status==1?'checked':'' }}>
                                                        <label class="custom-control-label" for="status-{{$bill->id}}"></label>
                                                      </div> --}}
                                                    </td>
                                                    <td>{{ $bill->tiffin_bill }}</td>
                                                    <td>{{ $bill->dinner_bill }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                  </tr>
                                                 
                                                @endif
                                                @endforeach
                                              @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                            </form>
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                   </div>
                </div>
                
             </div>
            
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
  <script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
  <script src="{{ asset('assets/js/moment.min.js')}}"></script>
  <script src="{{ asset('assets/js/bill.js')}}"></script>
  <script>
    $(document).on('click','#specialCheck',function(){
      if ($(this).is(":checked")) {
        $("#special-section").show();
        $(".autocomplete_txt").focus();
      }else{
        $("#special-section").hide();
      }
    });
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
    // function specialStatus(val) {
    //   console.log('hi');
    // }
  </script>
@endpush
@endsection