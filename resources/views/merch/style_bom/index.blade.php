@extends('merch.layout')
@section('title', 'Style BOM')

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
              <li class="active">Style BOM</li>
              <li class="top-nav-btn">
                <a href='{{ url("merch/style/costing/$style->stl_id")}}' class="btn btn-outline-success btn-sm pull-right"> <i class="fa fa-plus"></i> Add Costing</a>
                <a href="{{ url('merch/style_bom')}}" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Style BOM List</a> &nbsp;
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
                                                    <th width="2%">
                                                        
                                                    </th>
                                                    <th width="150">Item Name</th>
                                                    <th width="100">Description</th>
                                                    <th width="100">Color</th>
                                                    <th width="80">Size/Width</th>
                                                    <th width="130">Supplier</th>
                                                    <th width="130">Article</th>
                                                    
                                                    <th width="80">UOM</th>
                                                    <th width="80">Consumption</th>
                                                    <th width="80">Extra (%)</th>
                                                    <th width="80">Extra Qty</th>
                                                    <th width="80">Total</th>
                                                </tr>
                                            </thead>
                                            @foreach($itemCategory as $itemCat)
                                            <tbody class="xyz-body">
                                                <tr class="table-active">
                                                    <td colspan="12"><h4 class="capilize">{{ $itemCat->mcat_name }}</h4></td>
                                                </tr>
                                                @if(count($groupStyleBom) > 0 && isset($groupStyleBom[$itemCat->mcat_id]))
                                                @foreach($groupStyleBom[$itemCat->mcat_id] as $itemBom)
                                                    <tr id="itemRow-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}">
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
                                                                    <a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemBom->mcat_id }}" id="additem-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}"><i class="las la-folder-plus"></i> Add New Item</a>
                                                                </li>
                                                                </ul>
                                                            </div>
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="hidden" id="bomitemid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" name="bomitemid[]" value="{{ $itemBom->id }}">
                                                            <input type="hidden" id="itemcatid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mcat_id }}" name="itemcatid[]">
                                                            <input type="hidden" id="itemid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_cat_item_id }}" name="itemid[]">
                                                            <input type="text" data-category="{{ $itemBom->mcat_id }}" data-type="item" name="item[]" id="item-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control autocomplete_txt items-{{ $itemBom->mcat_id}}" autocomplete="off" onClick="this.select()" value="{{ $getItems[$itemBom->mr_cat_item_id]->item_name??'' }}">
                                                        </td>
                                                        <td>
                                                          <input type="text" data-type="description" name="description[]" id="description-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" autocomplete="off" value="{{ $itemBom->item_description }}">
                                                        </td>
                                                        <td>
                                                          <select name="color[]" id="color-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value">
                                                              <option value=""> - Select - </option>
                                                              @foreach($getColor as $color)
                                                              <option value="{{ $color->id }}" @if($itemBom->clr_id == $color->id) selected @endif>{{ $color->text }}</option>
                                                              @endforeach
                                                          </select>
                                                        </td>
                                                        <td>
                                                          <input type="text" name="size_width[]" id="sizewidth-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" autocomplete="off" value="{{ $itemBom->size }}" >
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="supplierid[]" id="supplierid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_supplier_sup_id }}">
                                                            <div class="row m-0">
                                                                <div class="col-9 p-0">
                                                                    <select name="supplier[]" id="supplier-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-category="{{ $itemBom->mcat_id }}" class="form-control supplier" >
                                                                      <option value=""> - Select - </option>
                                                                      @if(isset($getSupplier[$itemBom->mcat_id]))
                                                                      @foreach($getSupplier[$itemBom->mcat_id] as $supplier)
                                                                      <option value="{{ $supplier->sup_id }}" @if($supplier->sup_id == $itemBom->mr_supplier_sup_id) selected @endif>{{ $supplier->sup_name }}</option>
                                                                      @endforeach
                                                                      @endif
                                                                    </select>
                                                                    
                                                                </div>
                                                                <div class="col-3 pl-0 pr-0 pt-2">
                                                                    <a class="btn btn-xs btn-primary text-white addSupplier add-new" data-type="supplier" id="addsupplier-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemBom->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Supplier">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row m-0">
                                                                <div class="col-9 p-0">
                                                                    
                                                                    <select name="article[]" id="article-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control articlechange" >
                                                                      <option value=""> - Select - </option>
                                                                      @if(isset($getArticle[$itemBom->mr_supplier_sup_id]))
                                                                      @foreach($getArticle[$itemBom->mr_supplier_sup_id] as $itemArticle)
                                                                      <option value="{{ $itemArticle->id }}" @if($itemArticle->id == $itemBom->mr_article_id) selected @endif>{{ $itemArticle->art_name }}</option>
                                                                      @endforeach
                                                                      @endif
                                                                    </select>
                                                                    <input type="hidden" class="articleid" name="articleid[]" id="articleid-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->mr_article_id }}">
                                                                </div>
                                                                <div class="col-3 pl-0 pr-0 pt-2">
                                                                    <a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-catid="{{ $itemCat->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            
                                                        </td>
                                                        <td>
                                                            <select name="uom[]" id="uom-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control uomchange" >
                                                              <option value=""> - Select - </option>
                                                              @if(isset($getItems[$itemBom->mr_cat_item_id]))
                                                              @foreach($getItems[$itemBom->mr_cat_item_id]->uom as $key => $itemuom)
                                                              <option value="{{ $itemuom }}" @if($itemuom == $itemBom->uom) selected @endif>{{ $itemuom }}</option>
                                                              @endforeach
                                                              @endif
                                                            </select>
                                                            <input type="hidden" class="uomname" name="uomname[]" id="uomname-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" value="{{ $itemBom->uom }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" name="consumption[]" id="consumption-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" data-category="{{ $itemBom->mcat_id }}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->consumption }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" data-category="{{ $itemBom->mcat_id }}" name="extraper[]" id="extraper-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="{{ $itemBom->extra_percent }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" name="extraqty[]" id="extraqty-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly value="{{ $itemBom->qty }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" step="any" min="0" name="total[]" id="total-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly value="{{ $itemBom->total }}">
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                                @endif
                                                <tr id="itemRow-{{ $itemCat->mcat_id}}_1">
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
                                                                <a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemCat->mcat_id }}" id="additem-{{ $itemCat->mcat_id}}_1"><i class="las la-folder-plus"></i> Add New Item</a>
                                                            </li>
                                                            </ul>
                                                        </div>
                                                        
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="bomitemid-{{ $itemCat->mcat_id}}_1" name="bomitemid[]" value="">
                                                        <input type="hidden" id="itemcatid-{{ $itemCat->mcat_id}}_1" value="{{ $itemCat->mcat_id}}" name="itemcatid[]">
                                                        <input type="hidden" id="itemid-{{ $itemCat->mcat_id}}_1" value="" name="itemid[]">
                                                        <input type="text" data-category="{{ $itemCat->mcat_id }}" data-type="item" name="item[]" id="item-{{ $itemCat->mcat_id}}_1" class="form-control autocomplete_txt items-{{ $itemCat->mcat_id}}" autocomplete="off" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                      <input type="text" data-type="description" name="description[]" id="description-{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off">
                                                    </td>
                                                    <td>
                                                      <select name="color[]" id="color-{{ $itemCat->mcat_id}}_1" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value">
                                                          <option value=""> - Select - </option>
                                                          
                                                      </select>
                                                    </td>
                                                    <td>
                                                      <input type="text" name="size_width[]" id="sizewidth-{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off" >
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="supplierid[]" id="supplierid-{{ $itemCat->mcat_id}}_1">
                                                        <div class="row m-0">
                                                            <div class="col-9 p-0">
                                                                <select name="supplier[]" id="supplier-{{ $itemCat->mcat_id}}_1" data-category="{{ $itemCat->mcat_id }}" class="form-control supplier" disabled>
                                                                  <option value=""> - Select - </option>
                                                                </select>
                                                                
                                                            </div>
                                                            <div class="col-3 pl-0 pr-0 pt-2">
                                                                <a class="btn btn-xs btn-primary text-white addSupplier add-new" data-type="supplier" id="addsupplier-{{ $itemCat->mcat_id}}_1" data-catid="{{ $itemCat->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Supplier">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="row m-0">
                                                            <div class="col-9 p-0">
                                                                
                                                                <select name="article[]" id="article-{{ $itemCat->mcat_id}}_1" class="form-control articlechange " disabled>
                                                                  <option value=""> - Select - </option>
                                                                </select>
                                                                <input type="hidden" class="articleid" name="articleid[]" id="articleid-{{ $itemCat->mcat_id}}_1" value="">
                                                            </div>
                                                            <div class="col-3 pl-0 pr-0 pt-2">
                                                                <a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle-{{ $itemCat->mcat_id}}_1" data-catid="{{ $itemCat->mcat_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                    <td>
                                                        
                                                        <select name="uom[]" id="uom-{{ $itemCat->mcat_id}}_1" class="form-control uomchange" disabled>
                                                          <option value=""> - Select - </option>
                                                        </select>
                                                        <input type="hidden" class="uomname" name="uomname[]" id="uomname-{{ $itemCat->mcat_id}}_1" value="">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption-{{ $itemCat->mcat_id}}_1" data-category="{{ $itemCat->mcat_id }}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="5" data-category="{{ $itemCat->mcat_id }}" name="extraper[]" id="extraper-{{ $itemCat->mcat_id}}_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="extraqty[]" id="extraqty-{{ $itemCat->mcat_id}}_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="total[]" id="total-{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
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