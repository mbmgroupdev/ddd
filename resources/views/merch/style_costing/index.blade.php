@extends('merch.layout')
@section('title', 'Style Costing')

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
                  <a href="#">Style</a>
              </li>
              <li class="active">Style Costing</li>
              <li class="top-nav-btn">
                <a href='{{ url("merch/style/bom/$style->stl_id") }}' class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-plus"></i> Style BOM</a> &nbsp;
                <a href="{{ url('merch/style_bom')}}" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Style BOM List</a> &nbsp;
                <a href="{{ url('merch/style_costing')}}" target="_blank" class="btn btn-outline-success btn-sm pull-right"> <i class="fa fa-list"></i> Style Costing List</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <input type="hidden" id="base_url" value="{{ url('/') }}">
            
            <div class="row">
              <div class="col-12">
                <div class="panel panel-success">
                    <div class="panel-body pb-2">
                        <div class="wrapper center-block">
                          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                          <div class="panel panel-default">
                            <div class="panel-heading active" role="tab" id="headingOne">
                              <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="display: block; font-size: 13px;">
                                  Style Info
                                </a>
                              </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                              <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <table class="table custom-font-table detailTable" width="50%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <th>Production Type</th>
                                                <td>{{ (!empty($style->stl_type)?$style->stl_type:null) }}</td>
                                                <th>Style Reference 1</th>
                                                <td>{{ (!empty($style->stl_no)?$style->stl_no:null) }}</td>
                                                <th>Operation</th>
                                                <td>{{ (!empty($operations->name)?$operations->name:null) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Buyer</th>
                                                <td>{!! (!empty($style->b_name)?$style->b_name:null) !!}</td>
                                                <th>SMV/PC</th>
                                                <td>{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
                                                <th>Special Machine</th>
                                                <td>{{ (!empty($machines->name)?$machines->name:null) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Style Reference 2</th>
                                                <td>{{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}</td>
                                                <th>Sample Type</th>
                                                <td>{{ (!empty($samples->name)?$samples->name:null) }}</td>
                                                <th>Description</th>
                                                <td>{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-sm-2">
                                        <a href="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" target="_blank">
                                            <img class="thumbnail" height="100px" src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" alt=""/>
                                        </a>
                                    </div>
                                </div>
                              </div>
                            </div>
                          </div>
                            
                        </div>
                        </div>
                    </div> 
                </div>
                <div class="panel panel-info table-list-section">
                        <form class="form-horizontal" role="form" method="post" id="bomForm">
                            <input type="hidden" name="stl_id" value="{{ $style->stl_id }}">
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
                                                    
                                                    <th width="80" class="vertical-align">Cost</th>
                                                    <th width="80" class="vertical-align">Extra (%)</th>
                                                    <th width="80" class="vertical-align">UOM</th>
                                                    
                                                    <th class="vertical-align">Terms</th>
                                                    <th width="80" class="vertical-align">FOB</th>
                                                    <th width="80" class="vertical-align">L/C</th>
                                                    <th width="80" class="vertical-align">Freight</th>
                                                    <th width="80" class="vertical-align">Unit Price</th>
                                                    <th width="80" class="vertical-align">Total Price</th>
                                                </tr>
                                            </thead>
                                            @foreach($itemCategory as $itemCat)
                                            <tbody>
                                                <tr class="table-active">
                                                    <td colspan="14"><h4 class="capilize">{{ $itemCat->mcat_name }}</h4></td>
                                                </tr>
                                                @if(count($groupStyleBom) > 0 && isset($groupStyleBom[$itemCat->mcat_id]))
                                                  @foreach($groupStyleBom[$itemCat->mcat_id] as $itemBom)
                                                  <tr id="itemRow-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}">
                                                      <td>
                                                          <input type="hidden" id="bomitemid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="bomitemid[]" value="{{ $itemBom->id }}">
                                                          <input type="hidden" id="itemcatid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mcat_id }}" name="itemcatid[]">
                                                          <input type="hidden" id="itemid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_cat_item_id }}" name="itemid[]">
                                                          {{ $getItems[$itemBom->mr_cat_item_id]->item_name??'' }}
                                                          {{-- <br>
                                                          {{ $getItems[$itemBom->mr_cat_item_id]->item_code??'' }} --}}
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
                                                          <input type="radio" id="FOB-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="terms-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="custom-control-input bg-primary terms" value="FOB">
                                                          <label class="custom-control-label" for="FOB-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}"> FOB </label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline ">
                                                          <input type="radio" id="CF-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="terms-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="custom-control-input bg-primary terms" value="C&F" checked>
                                                          <label class="custom-control-label" for="CF-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}"> C&F</label>
                                                        </div>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="fob[]" id="fob-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo fob" autocomplete="off" data-catid="{{ $itemBom->mcat_id}}" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" readonly>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="lc[]" id="lc-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo lc" autocomplete="off" data-catid="{{ $itemBom->mcat_id}}" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" readonly>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="freight[]" id="freight-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo freight" autocomplete="off" data-catid="{{ $itemBom->mcat_id}}" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" readonly>
                                                      </td>
                                                      <td>
                                                          <input type="text" step="any" min="0" name="unitprice[]" id="unitprice-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemBom->mcat_id}}" class="form-control changesNo unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0">
                                                      </td>
                                                      <td>
                                                        <p id="percosting-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="text-right fwb totalpercost">0</p>
                                                        <input type="hidden" step="any" min="0" name="pertotal[]" id="pertotal-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemBom->mcat_id}}" class="form-control pertotalcosting catTotalCost-{{ $itemBom->mcat_id}}" autocomplete="off" value="0">
                                                      </td>
                                                      
                                                  </tr>
                                                  @endforeach
                                                  <tr class="table-default">
                                                    <td colspan="13"><h4 class="capilize">Total {{ $itemCat->mcat_name }} Price</h4></td>
                                                    <td>
                                                      <p id="totalcosting-{{ $itemBom->mcat_id}}" class="text-right fwb categoryPrice {{ $itemCat->mcat_name }}">0</p>
                                                    </td>
                                                  </tr>
                                                @endif
                                                
                                            </tbody>
                                            @endforeach
                                            <tbody>
                                              <tr class="table-default">
                                                  <td colspan="13"><h4 class="capilize">Total Sewing and Finishing Accessories Price</h4></td>
                                                  <td>
                                                    <p id="tsewing-finishing" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>
                                              @foreach($specialOperation as $spo)
                                              <tr class="table-default">
                                                  <td colspan="5"><p class="capilize">{{ $spo->opr_name }}</p></td>
                                                  <td> 1 </td>
                                                  <td> 0 </td>
                                                  <td></td>
                                                  <td colspan="4"></td>
                                                  <td>
                                                    <p id="sp-{{ $spo->style_op_id }}" class="text-right fwb">0</p>
                                                  </td>
                                                  <td>
                                                    <input type="text" step="any" min="0" name="unitprice[]" id="spunitprice-{{ $spo->style_op_id }}" class="form-control changesNo spunitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0">
                                                </td>
                                                <td>
                                                  <p id="sp-{{ $spo->style_op_id }}" class="text-right fwb totalpercost">0</p>
                                                  <input type="hidden" step="any" min="0" name="pertotal[]" id="sprice-{{ $spo->style_op_id }}" class="form-control" autocomplete="off" value="0">
                                                </td>
                                              </tr>
                                              @endforeach
                                              <tr class="table-default">
                                                  <td colspan="13"><p class="capilize">Testing Cost</p></td>
                                                  <td>
                                                    <p id="testing-cost" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td colspan="13"><p class="capilize">CM</p></td>
                                                  <td>
                                                    <p id="cm" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td colspan="13"><p class="capilize">Commercial Cost</p></td>
                                                  <td>
                                                    <p id="commercial-cost" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td colspan="13"><h4 class="capilize">Net FOB</h4></td>
                                                  <td>
                                                    <p id="net-fob" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td colspan="13"><p class="capilize">Buyer Commission</p></td>
                                                  <td>
                                                    <p id="buyer-commission" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td colspan="13"><p class="capilize">Agent Commission</p></td>
                                                  <td>
                                                    <p id="agent-commission" class="text-right fwb">0</p>
                                                  </td>
                                              </tr>

                                              <tr class="table-default">
                                                  <td colspan="13" class="tsticky-bottom"><h4 class="capilize ">Total FOB</h4></td>
                                                  <td class="tsticky-bottom">
                                                    <p id="totalfob" class="text-right fwb ">0</p>
                                                  </td>
                                              </tr>
                                            </tbody>
                                            
                                        </table>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="submit-invoice invoice-save-btn pull-right">
                                            <button type="button" class="btn btn-outline-success btn-md text-center saveBom" onclick="saveBOM('manual')"><i class="fa fa-save"></i> Save</button>
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

@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>

<script src="{{ asset('assets/js/costing.js')}}"></script>
<script>
    function saveBOM(savetype) {
        if(savetype =='manual' ) $(".app-loader").show();
        var curStep = $(this).closest("#bomForm"),
          curInputs = curStep.find("input[type='text'],input[type='hidden'],input[type='number'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
          isValid = true;
        $(".form-group").removeClass("has-error");
        // for (var i = 0; i < curInputs.length; i++) {
        //    if (!curInputs[i].validity.valid) {
        //       isValid = false;
        //       $(curInputs[i]).closest(".form-group").addClass("has-error");
        //    }
        // }
        var form = $("#bomForm");
        if (isValid){
           $.ajax({
              type: "GET",
              url: '{{ url("/merch/style/bom-ajax-store") }}',
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              data: form.serialize(), // serializes the form's elements.
              success: function(response)
              {
                if(savetype =='manual' ){
                    $.notify(response.message, response.type);
                }else{
                    $.notify('Item has been '+savetype, response.type);
                }
                if(response.type === 'success'){
                    var bomindex = $('input[name="bomitemid[]"]');
                    $.each(response.value, function(i, el) {
                        var bomid = bomindex[i].getAttribute('id');
                        $("#"+bomid).val(el);
                    });
                   
                }
                $(".app-loader").hide();
              },
              error: function (reject) {
                $(".app-loader").hide();
                // console.log(reject);
                if( reject.status === 400) {
                    var data = $.parseJSON(reject.responseText);
                     $.notify(data.message, {
                        type: data.type,
                        allow_dismiss: true,
                        delay: 100,
                        timer: 300
                    });
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
            $.notify("Some field are required", {
              type: 'error',
              allow_dismiss: true,
              delay: 100,
              z_index: 1031,
              timer: 300
           });
        }
    };
</script>
@endpush
@endsection