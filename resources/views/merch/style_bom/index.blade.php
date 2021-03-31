@extends('merch.layout')
@section('title', 'Style BOM')

@section('main-content')
@push('css')
    <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        .table td, .table th {
            padding: 4px 5px !important;
        }
        .table-active, .table-active > th, .table-active > td {
            box-shadow: 0 2px 2px -1px rgb(0 0 0 / 40%);
            color: #000;
        }
        .select2 {
            width: 100px !important;
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
              <li class="active">Style BOM</li>
              <li class="top-nav-btn">
                <a href='{{ url("merch/style/costing/$style->stl_id")}}' class="btn btn-outline-success btn-sm pull-right"> <i class="fa fa-plus"></i> Add Costing</a>
                <a href="{{ url('merch/style/bom-list')}}" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Style BOM List</a> &nbsp;
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <input type="hidden" id="base_url" value="{{ url('/') }}">
            <input type="hidden" id="blade_type" value="style">
            <div class="row">
              <div class="col-12">
                <div class="panel panel-success">
                    <div class="panel-body pb-2">
                        @include('merch.common.style_info')
                    </div> 
                </div>
                <div class="panel panel-info table-list-section">
                        <form class="form-horizontal" role="form" method="post" id="bomForm">
                            <input type="hidden" name="stl_id" value="{{ $style->stl_id }}">
                            <input type="hidden" id="change-flag" value="0">
                            {{ csrf_field() }} 
                            <div class="panel-body">
                                
                                <div class='row'>
                                    <div class='col-sm-12 table-wrapper-scroll-y table-custom-scrollbar'>
                                        <table class="table table-bordered table-hover table-fixed table-head" id="itemList">
                                            <thead>
                                                <tr class="text-center active">
                                                    <th rowspan="2" width="2%" ></th>
                                                    <th rowspan="2" width="150" class="vertical-align">Item Name</th>
                                                    <th rowspan="2" width="100" class="vertical-align">Description</th>
                                                    <th colspan="2" width="180" class="vertical-align">Specification</th>
                                                    <th rowspan="2" width="130" class="vertical-align">Supplier</th>
                                                    <th rowspan="2" width="130" class="vertical-align">Article</th>
                                                    <th rowspan="2" width="80" class="vertical-align">UOM</th>
                                                    <th rowspan="2" width="80" class="vertical-align">Consumption</th>
                                                    <th rowspan="2" width="80" class="vertical-align">Extra (%)</th>
                                                    <th rowspan="2" width="80" class="vertical-align">Extra Qty</th>
                                                    <th rowspan="2" width="80" class="vertical-align">Total</th>
                                                </tr>
                                                <tr>
                                                  <th width="100" style="top:31px;">Color</th>
                                                  <th width="80" style="top:31px;">Size/Width</th>
                                                </tr>
                                            </thead>
                                            @foreach($itemCategory as $itemCat)
                                            <tbody class="xyz-body">
                                                <tr class="table-active">
                                                    <td colspan="12"><h4 class="capilize">{{ $itemCat->mcat_name }}</h4></td>
                                                </tr>
                                                @if(count($groupStyleBom) > 0 && isset($groupStyleBom[$itemCat->mcat_id]))
                                                @foreach($groupStyleBom[$itemCat->mcat_id] as $itemBom)
                                                    <tr id="itemRow_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}">
                                                        <td class="right-btn">
                                                            <a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title='Right Click Action'><i class="las la-arrows-alt"></i></a>
                                                            <div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;">
                                                                <ul>
                                                                  <li>
                                                                    <a class="textblack arrows-context add-arrows" data-catid="{{ $itemBom->mcat_id }}"><i class="las la-cart-plus"></i> Add Row</a>
                                                                  </li>   
                                                                  <li>
                                                                    <a class="textblack arrows-context remove-arrows"  data-catid="{{ $itemBom->mcat_id }}" ><i class="las la-trash"></i> Remove Row</a>
                                                                  </li>           
                                                                  <li>
                                                                    <a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemBom->mcat_id }}" id="additem_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}"><i class="las la-folder-plus"></i> Add New Item</a>
                                                                </li>
                                                                </ul>
                                                            </div>
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="hidden" id="bomitemid_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="bomitemid[]" value="{{ $itemBom->id }}">
                                                            <input type="hidden" id="itemcatid_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mcat_id }}" name="itemcatid[]">
                                                            <input type="hidden" id="itemid_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_cat_item_id }}" name="itemid[]">
                                                            <input type="text" data-category="{{ $itemBom->mcat_id }}" data-type="item" name="item[]" id="item_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control autocomplete_txt items-{{ $itemBom->mcat_id}}" autocomplete="off" onClick="this.select()" value="{{ $getItems[$itemBom->mr_cat_item_id]->item_name??'' }}">
                                                        </td>
                                                        <td>
                                                          <input type="text" data-type="description" name="description[]" id="description_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" autocomplete="off" value="{{ $itemBom->item_description }}">
                                                        </td>
                                                        <td>
                                                          <select name="color[]" id="color_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value">
                                                              <option value=""> - Select - </option>
                                                              @foreach($getColor as $color)
                                                              <option value="{{ $color->id }}" @if($itemBom->clr_id == $color->id) selected @endif>{{ $color->text }}</option>
                                                              @endforeach
                                                          </select>
                                                        </td>
                                                        <td>
                                                          <input type="text" name="size_width[]" id="sizewidth_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" autocomplete="off" value="{{ $itemBom->size }}" >
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="supplierid[]" id="supplierid_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_supplier_sup_id }}">
                                                            <div class="row m-0">
                                                                <div class="col-9 p-0">
                                                                    <select name="supplier[]" id="supplier_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-category="{{ $itemBom->mcat_id }}" class="form-control supplier" >
                                                                      <option value=""> - Select - </option>
                                                                      @if(isset($getSupplier[$itemBom->mcat_id]))
                                                                      @foreach($getSupplier[$itemBom->mcat_id] as $supplier)
                                                                      <option value="{{ $supplier->sup_id }}" @if($supplier->sup_id == $itemBom->mr_supplier_sup_id) selected @endif>{{ $supplier->sup_name }}</option>
                                                                      @endforeach
                                                                      @endif
                                                                    </select>
                                                                    
                                                                </div>
                                                                <div class="col-3 pl-0 pr-0 pt-2">
                                                                    <a class="btn btn-xs btn-primary text-white addSupplier add-new" data-type="supplier" id="addsupplier_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemBom->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Supplier">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row m-0">
                                                                <div class="col-9 p-0">
                                                                    
                                                                    <select name="article[]" id="article_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control articlechange" >
                                                                      <option value=""> - Select - </option>
                                                                      @if(isset($getArticle[$itemBom->mr_supplier_sup_id]))
                                                                      @foreach($getArticle[$itemBom->mr_supplier_sup_id] as $itemArticle)
                                                                      <option value="{{ $itemArticle->id }}" @if($itemArticle->id == $itemBom->mr_article_id) selected @endif>{{ $itemArticle->art_name }}</option>
                                                                      @endforeach
                                                                      @endif
                                                                    </select>
                                                                    <input type="hidden" class="articleid" name="articleid[]" id="articleid_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_article_id }}">
                                                                </div>
                                                                <div class="col-3 pl-0 pr-0 pt-2">
                                                                    <a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemCat->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            
                                                        </td>
                                                        <td>
                                                            <select name="uom[]" id="uom_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control uomchange" >
                                                              <option value=""> - Select - </option>
                                                              @if(isset($getItems[$itemBom->mr_cat_item_id]))
                                                              @foreach($getItems[$itemBom->mr_cat_item_id]->uom as $key => $itemuom)
                                                              <option value="{{ $itemuom }}" @if($itemuom == $itemBom->uom) selected @endif>{{ $itemuom }}</option>
                                                              @endforeach
                                                              @endif
                                                            </select>
                                                            <input type="hidden" class="uomname" name="uomname[]" id="uomname_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->uom }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" name="consumption[]" id="consumption_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-category="{{ $itemBom->mcat_id }}" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->consumption }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" data-category="{{ $itemBom->mcat_id }}" name="extraper[]" id="extraper_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->extra_percent }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" name="extraqty[]" id="extraqty_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly value="{{ $itemBom->qty }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" name="total[]" id="total_{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly value="{{ $itemBom->total }}">
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                                @endif
                                                <tr id="itemRow_{{ $itemCat->mcat_id}}_1">
                                                    <td class="right-btn">
                                                        <a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title='Right Click Action'><i class="las la-arrows-alt"></i></a>
                                                        <div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;">
                                                            <ul>
                                                              <li>
                                                                <a class="textblack arrows-context add-arrows" data-catid="{{ $itemCat->mcat_id }}"><i class="las la-cart-plus"></i> Add Row</a>
                                                              </li>   
                                                              <li>
                                                                <a class="textblack arrows-context remove-arrows"  data-catid="{{ $itemCat->mcat_id }}" ><i class="las la-trash"></i> Remove Row</a>
                                                              </li>           
                                                              <li>
                                                                <a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemCat->mcat_id }}" id="additem_{{ $itemCat->mcat_id}}_1"><i class="las la-folder-plus"></i> Add New Item</a>
                                                            </li>
                                                            </ul>
                                                        </div>
                                                        
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="bomitemid_{{ $itemCat->mcat_id}}_1" name="bomitemid[]" value="">
                                                        <input type="hidden" id="itemcatid_{{ $itemCat->mcat_id}}_1" value="{{ $itemCat->mcat_id}}" name="itemcatid[]">
                                                        <input type="hidden" id="itemid_{{ $itemCat->mcat_id}}_1" value="" name="itemid[]">
                                                        <input type="text" data-category="{{ $itemCat->mcat_id }}" data-type="item" name="item[]" id="item_{{ $itemCat->mcat_id}}_1" class="form-control autocomplete_txt items-{{ $itemCat->mcat_id}}" autocomplete="off" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                      <input type="text" data-type="description" name="description[]" id="description_{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off">
                                                    </td>
                                                    <td>
                                                      <select name="color[]" id="color_{{ $itemCat->mcat_id}}_1" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value">
                                                          <option value=""> - Select - </option>
                                                          
                                                      </select>
                                                    </td>
                                                    <td>
                                                      <input type="text" name="size_width[]" id="sizewidth_{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off" >
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="supplierid[]" id="supplierid_{{ $itemCat->mcat_id}}_1">
                                                        <div class="row m-0">
                                                            <div class="col-9 p-0">
                                                                <select name="supplier[]" id="supplier_{{ $itemCat->mcat_id}}_1" data-category="{{ $itemCat->mcat_id }}" class="form-control supplier" disabled>
                                                                  <option value=""> - Select - </option>
                                                                </select>
                                                                
                                                            </div>
                                                            <div class="col-3 pl-0 pr-0 pt-2">
                                                                <a class="btn btn-xs btn-primary text-white addSupplier add-new" data-type="supplier" id="addsupplier_{{ $itemCat->mcat_id}}_1" data-catid="{{ $itemCat->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Supplier">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row m-0">
                                                            <div class="col-9 p-0">
                                                                
                                                                <select name="article[]" id="article_{{ $itemCat->mcat_id}}_1" class="form-control articlechange " disabled>
                                                                  <option value=""> - Select - </option>
                                                                </select>
                                                                <input type="hidden" class="articleid" name="articleid[]" id="articleid_{{ $itemCat->mcat_id}}_1" value="">
                                                            </div>
                                                            <div class="col-3 pl-0 pr-0 pt-2">
                                                                <a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle_{{ $itemCat->mcat_id}}_1" data-catid="{{ $itemCat->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                    <td>
                                                        
                                                        <select name="uom[]" id="uom_{{ $itemCat->mcat_id}}_1" class="form-control uomchange" disabled>
                                                          <option value=""> - Select - </option>
                                                        </select>
                                                        <input type="hidden" class="uomname" name="uomname[]" id="uomname_{{ $itemCat->mcat_id}}_1" value="">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption_{{ $itemCat->mcat_id}}_1" data-category="{{ $itemCat->mcat_id }}" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="5" data-category="{{ $itemCat->mcat_id }}" name="extraper[]" id="extraper_{{ $itemCat->mcat_id}}_1" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="extraqty[]" id="extraqty_{{ $itemCat->mcat_id}}_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="total[]" id="total_{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                    </td>
                                                    
                                                </tr>
                                                
                                            </tbody>
                                            @endforeach
                                            
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
<div class="modal right fade" id="right_modal_item" tabindex="-1" role="dialog" aria-labelledby="right_modal_item">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
          <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result" id="content-result"></div>

      </div>
      
    </div>
  </div>
</div>
<div class="calculator_section">
  @include('common.calculator')
</div>
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>

<script>
    var getColor = {!! json_encode($getColor) !!};
</script>
<script src="{{ asset('assets/js/bom.js')}}"></script>
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
              type: "POST",
              url: '{{ url("/merch/style/bom-ajax-store") }}',
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              data: form.serialize(), // serializes the form's elements.
              success: function(response)
              {
                
                if(response.type === 'success'){
                  if(savetype =='manual' ){
                    $.notify(response.message, response.type);
                  }else if(savetype =='cost'){
                    $.notify('Saved '+savetype, response.type);
                  }else{
                    $.notify('Item has been '+savetype, response.type);
                  }
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