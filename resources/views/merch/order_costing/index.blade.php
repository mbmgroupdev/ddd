@extends('merch.layout')
@section('title', 'Order Costing')

@section('main-content')
@push('css')
    <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        .table td {
            padding: 5px 5px !important;
        }
        .table-active, .table-active > th, .table-active > td {
            box-shadow: 0 2px 2px -1px rgb(0 0 0 / 40%);
            color: #000;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="#">Merchandising</a>
              </li>
              <li>
                  <a href="#">Order</a>
              </li>
              <li class="active">Order Costing</li>
              <li class="top-nav-btn">
                <a href='{{ url("merch/style/costing/$order->mr_style_stl_id") }}' class="btn btn-outline-primary btn-sm pull-right" target="_blank"> <i class="fa fa-eye"></i> Style Costing</a> &nbsp;
                <a href='{{ url("merch/order/bom/$order->order_id") }}' class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-plus"></i> Order BOM</a> &nbsp;
                <a href="{{ url('merch/order/bom-list')}}" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Order BOM List</a> &nbsp;
                <a href="{{ url('merch/order/costing-list')}}" target="_blank" class="btn btn-outline-success btn-sm pull-right"> <i class="fa fa-list"></i> Order Costing List</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <input type="hidden" id="base_url" value="{{ url('/') }}">
            <input type="hidden" id="blade_type" value="order">
            <div class="row">
              <div class="col-12">
                <div class="panel panel-success">
                    <div class="panel-body pb-2">
                        @include('merch.common.order_info')
                    </div> 
                </div>
                <div class="panel panel-info table-list-section">
                        <form class="form-horizontal" role="form" method="post" id="costingForm">
                            <input type="hidden" name="stl_id" value="{{ $order->mr_style_stl_id }}">
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <input type="hidden" id="order-qty" value="{{ $order->order_qty??0 }}">
                            <input type="hidden" id="change-flag" value="0">
                            {{ csrf_field() }} 
                            <div class="panel-body">
                                
                                <div class='row'>
                                    <div class='col-sm-12 table-wrapper-scroll-y table-custom-scrollbar'>
                                        <table class="table table-bordered table-hover table-fixed table-head" id="itemList">
                                            <thead>
                                                <tr class="text-center active">
                                                    
                                                    <th width="150" class="vertical-align">Item Description</th>
                                                    <th width="100" class="vertical-align">Color</th>
                                                    <th width="80" class="vertical-align">Size / Width</th>
                                                    <th width="130" class="vertical-align">Supplier</th>
                                                    <th width="130" class="vertical-align">Article</th>
                                                    
                                                    {{-- <th width="80" class="vertical-align">Cost</th> --}}
                                                    <th width="70" class="vertical-align p-1" >Consum- ption</th>
                                                    <th width="80" class="vertical-align">Extra (%)</th>
                                                    <th width="80" class="vertical-align">UOM</th>
                                                    
                                                    <th width="70" class="vertical-align">Terms</th>
                                                    <th width="80" class="vertical-align">FOB</th>
                                                    <th width="80" class="vertical-align">L/C</th>
                                                    <th width="80" class="vertical-align">Freight</th>
                                                    <th width="80" class="vertical-align">Unit Price</th>
                                                    <th width="80" class="vertical-align">Total Price</th>
                                                    <th width="80" class="vertical-align">Style Cost</th>
                                                    <th width="80" class="vertical-align">Req. Qty</th>
                                                    <th width="80" class="vertical-align">Total Value</th>
                                                </tr>
                                            </thead>

                                            @foreach($itemCategory as $itemCat)
                                            <tbody>
                                                <tr class="table-active">
                                                    <td colspan="17"><h5 class="capilize">{{ $itemCat->mcat_name }}</h5></td>
                                                </tr>
                                                @php $totalStylePrice = 0; @endphp
                                                @if(count($groupBom) > 0 && isset($groupBom[$itemCat->mcat_id]))
                                                  @foreach($groupBom[$itemCat->mcat_id] as $itemBom)
                                                    @php 
                                                      $itemStyleUnitPrice = 0;
                                                      $itemPrice = $itemBom->precost_unit_price;
                                                    @endphp
                                                    @if(isset($styleCosting[$itemBom->stl_bom_id]) && ($styleCosting[$itemBom->stl_bom_id]->mr_cat_item_id == $itemBom->mr_cat_item_id))
                                                      @php
                                                        $itemStyleUnitPrice = $styleCosting[$itemBom->stl_bom_id]->unitprice;
                                                        if($bomCosting == 0){
                                                          $itemPrice = $styleCosting[$itemBom->stl_bom_id]->precost_unit_price??0;
                                                        }
                                                      @endphp
                                                    @endif
                                                    @php
                                                      $totalStylePrice += $itemStyleUnitPrice;
                                                    @endphp
                                                    
                                                  <tr id="itemRow-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}">
                                                      <td>
                                                          <input type="hidden" id="bomitemid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="bomitemid[]" value="{{ $itemBom->id }}">
                                                          <input type="hidden" id="itemcatid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mcat_id }}" name="itemcatid[]">
                                                          <input type="hidden" id="itemid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_cat_item_id }}" name="itemid[]">
                                                          {{ $getItem[$itemBom->mr_cat_item_id]->item_name??'' }}
                                                          {{-- <br>
                                                          {{ $getItem[$itemBom->mr_cat_item_id]->item_code??'' }} --}}
                                                          <br>
                                                          {{ $itemBom->item_description }}
                                                      </td>
                                                      <td> {{ $getColor[$itemBom->clr_id]->clr_name??'' }} </td>
                                                      <td> {{ $itemBom->size }} </td>
                                                      <td> {{ $getSupplier[$itemBom->mr_supplier_sup_id]->sup_name??'' }} </td>
                                                      <td> {{ $getArticle[$itemBom->mr_article_id]->art_name??'' }} </td>
                                                      <td><p class="consumption">{{ $itemBom->consumption }}</p></td>
                                                      <td><p class="extra">{{ $itemBom->extra_percent }}</p></td>
                                                      <td> {{ $itemBom->uom }} </td>
                                                      <td>
                                                        <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline ">
                                                          <input type="radio" id="FOB-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="terms-{{ $itemBom->mcat_id}}{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="custom-control-input bg-primary terms" value="FOB" @if($itemBom->bom_term == 'FOB') checked @endif >
                                                          <label class="custom-control-label" for="FOB-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}"> FOB </label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline ">
                                                          <input type="radio" id="CF-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="terms-{{ $itemBom->mcat_id}}{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="custom-control-input bg-primary terms" value="C&F" @if($itemBom->bom_term != 'FOB') checked @endif>
                                                          <label class="custom-control-label" for="CF-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}"> C&F</label>
                                                        </div>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="precost_fob[]" id="fob-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo fob" autocomplete="off" data-catid="{{ $itemBom->mcat_id}}" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->precost_fob??'0' }}" readonly>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="precost_lc[]" id="lc-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo lc" autocomplete="off" data-catid="{{ $itemBom->mcat_id}}" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->precost_lc??'0' }}" readonly>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="precost_freight[]" id="freight-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo freight" autocomplete="off" data-catid="{{ $itemBom->mcat_id}}" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->precost_freight??'0' }}" readonly>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="precost_unit_price[]" id="unitprice-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemBom->mcat_id}}" class="form-control changesNo unitprice action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemPrice??'0' }}">
                                                      </td>
                                                      <td>
                                                        <p id="percosting-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="text-right fwb totalpercost">0</p>
                                                        <input type="hidden" step="any" min="0" name="pertotal[]" id="pertotal-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemBom->mcat_id}}" class="form-control pertotalcosting catTotalCost-{{ $itemBom->mcat_id}}" autocomplete="off" value="0">
                                                      </td>
                                                      <td class="table-warning">
                                                        <p class="text-right fwb">
                                                          {{ number_format((float)$itemStyleUnitPrice, 6, '.', '') }}
                                                        </p>
                                                      </td>
                                                      <td>
                                                        <p id="perqty-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="text-right fwb totalperqty">0</p>
                                                      </td>
                                                      <td>
                                                        <p id="pervalue-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="text-right fwb totalpervalue">0</p>
                                                      </td>
                                                  </tr>
                                                  @endforeach
                                                  <tr class="table-default">
                                                    <td colspan="13"><h5 class="capilize">Total {{ $itemCat->mcat_name }} Price</h5></td>
                                                    <td>
                                                      <p id="totalcosting-{{ $itemBom->mcat_id}}" class="text-right fwb categoryPrice {{ $itemCat->mcat_name }}">0</p>
                                                    </td>
                                                    <td class="table-warning">
                                                      <p class="text-right fwb">
                                                        {{ number_format((float)$totalStylePrice, 6, '.', '') }}
                                                      </p>
                                                    </td>
                                                    <td colspan="2"></td>
                                                  </tr>
                                                @endif
                                                
                                            </tbody>
                                            @endforeach
                                            <tbody>
                                              <tr class="table-default">
                                                  <td colspan="13"><h5 class="capilize">Total Sewing and Finishing Accessories Price</h5></td>
                                                  <td>
                                                    <p id="tsewing-finishing" class="text-right fwb">0</p>
                                                  </td>
                                                  <td></td>
                                                  <td colspan="2"></td>
                                              </tr>
                                              
                                              @foreach($specialOperation as $spo)
                                              <tr class="table-default">
                                                <td colspan="5"><p class="capilize">{{ $spo->opr_name }}</p></td>
                                                <td> 1 </td>
                                                <td> 0 </td>
                                                <td>
                                                  <select name="spuom[]" id="spuom-{{ $spo->op_id }}" class="form-control" >
                                                    @foreach($uom as $key => $um)
                                                      <option value="{{ $um }}" @if($um == $spo->uom) selected @endif>{{ $um }}</option>
                                                    @endforeach
                                                  </select>
                                                </td>
                                                <td colspan="4"></td>
                                                
                                                <td>
                                                  <input type="text" step="any" min="0" name="spunitprice[]" id="spunitprice-{{ $spo->op_id }}" class="form-control sp_price spunitprice action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $spo->unit_price??'0' }}">
                                                  <input type="hidden" name="order_op_id[]" value="{{ $spo->op_id }}">
                                                  <input type="hidden" name="opr_type[]" value="{{ $spo->opr_type }}">
                                                  <input type="hidden" name="mr_operation_opr_id[]" value="{{ $spo->mr_operation_opr_id }}">
                                                </td>
                                                <td>
                                                  <p id="sp-{{ $spo->op_id }}" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($spo->unit_price??'0'), 6,'.','') }}</p>
                                                  
                                                </td>
                                                <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)(isset($styleSpOperation[$spo->op_id])?($styleSpOperation[$spo->op_id]->unit_price):'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td colspan="2"></td>
                                              </tr>
                                              @endforeach
                                              <tr class="table-default">
                                                <td colspan="5"><p class="capilize">Testing Cost</p></td>
                                                <td> 1 </td>
                                                <td> 0 </td>
                                                <td>Piece</td>
                                                <td colspan="4"></td>
                                                <td>
                                                  <input type="text" step="any" min="0" name="testing_cost" id="tcunitprice" class="form-control sp_price tcunitprice action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $otherCosting->testing_cost??'0' }}">
                                                </td>
                                                <td>
                                                  <p id="testing-cost" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($otherCosting->testing_cost??'0'), 6,'.','') }}</p>
                                                  
                                                </td>
                                                <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($styleOtherCosting->testing_cost??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td colspan="2"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td colspan="5"><p class="capilize">CM</p></td>
                                                <td> 1 </td>
                                                <td> 0 </td>
                                                <td>Piece</td>
                                                <td colspan="4"></td>
                                                <td>
                                                  <input type="text" step="any" min="0" name="cm" id="cmunitprice" class="form-control sp_price cmunitprice action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $otherCosting->cm??'0' }}">
                                                </td>
                                                <td>
                                                  <p id="cm-cost" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($otherCosting->cm??'0'), 6,'.','') }}</p>
                                                  
                                                </td>
                                                <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($styleOtherCosting->cm??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td colspan="2"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td colspan="8"><p class="capilize">Commercial Cost</p></td>
                                                <td colspan="4"></td>
                                                <td>
                                                  <input type="text" step="any" min="0" name="commercial_cost" id="commercialunitprice" class="form-control sp_price commercialunitprice action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $otherCosting->commercial_cost??'0' }}">
                                                </td>
                                                <td>
                                                  <p id="commercial-cost" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($otherCosting->commercial_cost??'0'), 6,'.','') }}</p>
                                                  
                                                </td>
                                                <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($styleOtherCosting->commercial_cost??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td colspan="2"></td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td colspan="13"><h5 class="capilize">Net FOB</h5></td>
                                                  <td>
                                                    <p id="net-fob" class="text-right fwb">0</p>
                                                    <input type="hidden" id="net_fob" name="net_fob" value="0">
                                                  </td>
                                                  <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($styleOtherCosting->net_fob??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                  <td colspan="2"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td colspan="5"><h5 class="capilize">Buyer FOB</h5></td>
                                                <td>
                                                  <input type="text" step="any" min="0" name="buyer_comission_percent" id="buyer-commission-percent" class="form-control commission buyer-commission-percent" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $otherCosting->buyer_comission_percent??'0' }}">
                                                </td>
                                                <td>%</td>
                                                <td></td>
                                                <td colspan="4"></td>
                                                <td>
                                                  <input type="text" step="any" min="0" id="buyer-commission-unitprice" class="form-control buyer-commission-unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" readonly>
                                                  <input type="hidden" name="buyer_fob" value="0" id="buyer_fob">
                                                </td>
                                                <td>
                                                  <p id="buyer-fob" class="text-right fwb totalpercost">0</p>
                                                  
                                                </td>
                                                <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($styleOtherCosting->buyer_fob ??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td colspan="2"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td colspan="5"><h5 class="capilize">Agent FOB</h5></td>
                                                <td>
                                                  <input type="text" step="any" min="0" name="agent_comission_percent" id="agent-commission-percent" class="form-control commission agent-commission-percent" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $otherCosting->agent_comission_percent??'0' }}">
                                                </td>
                                                <td>%</td>
                                                <td></td>
                                                <td colspan="4"></td>
                                                <td>
                                                  <input type="text" step="any" min="0" id="agent-commission-unitprice" class="form-control agent-commission-unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" readonly>

                                                  <input type="hidden" step="any" min="0" name="agent_fob" id="agent_fob" class="form-control agent-commission-unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0">
                                                </td>
                                                <td>
                                                  <p id="agent-fob" class="text-right fwb totalpercost">0</p>
                                                  
                                                </td>
                                                <td class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($styleOtherCosting->agent_fob??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td colspan="2"></td>
                                              </tr>

                                              <tr class="table-default">
                                                  <td colspan="13" class="tsticky-bottom"><h5 class="capilize ">Total FOB</h5></td>
                                                  <td class="tsticky-bottom">
                                                    <p id="totalfob" class="text-right fwb ">0</p>
                                                  </td>
                                                  <td class="tsticky-bottom table-warning">
                                                    <p class="text-right fwb">
                                                      {{ number_format((float)($styleOtherCosting->agent_fob??'0'), 6,'.','') }}
                                                    </p>
                                                  </td>
                                                  <td colspan="2" class="tsticky-bottom"></td>
                                              </tr>
                                            </tbody>
                                            
                                        </table>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="submit-invoice invoice-save-btn pull-right">
                                            <button type="button" class="btn btn-outline-success btn-md text-center saveBom" onclick="saveCosting('manual')"><i class="fa fa-save"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> 
              </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<div class="calculator_section">
  @include('common.calculator')
</div>
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>

<script src="{{ asset('assets/js/costing.js')}}"></script>
<script>
    function saveCosting(savetype) {
        if(savetype =='manual' ) $(".app-loader").show();
        var curStep = $(this).closest("#costingForm"),
          curInputs = curStep.find("input[type='text'],input[type='hidden'],input[type='number'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
          isValid = true;
        $(".form-group").removeClass("has-error");
        // for (var i = 0; i < curInputs.length; i++) {
        //    if (!curInputs[i].validity.valid) {
        //       isValid = false;
        //       $(curInputs[i]).closest(".form-group").addClass("has-error");
        //    }
        // }
        var form = $("#costingForm");
        if (isValid){
           $.ajax({
              type: "POST",
              url: '{{ url("/merch/order/costing-ajax-store") }}',
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              data: form.serialize(), // serializes the form's elements.
              success: function(response)
              {
                console.log(response);
                if(response.type === 'success'){
                  if(savetype =='manual' ){
                      $.notify(response.message, response.type);
                  }else{
                      $.notify('Costing Save '+savetype, response.type);
                  }
                }else{
                  $.notify(response.message, response.type);
                }
                $(".app-loader").hide();
              },
              error: function (reject) {
                $(".app-loader").hide();
                // console.log(reject);
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
    };
</script>
@endpush
@endsection