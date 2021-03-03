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
                    <a href="{{ url('hr/operation/shift_assign')}}" target="_blank" class="btn btn-success btn-sm pull-right"> <i class="fa fa-list"></i> Style BOM List</a>
                    {{-- <a href="#" class="iq-waves-effect" id="btnFullscreen"><i class="ri-fullscreen-line"></i></a> --}}
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
                        <form class="form-horizontal" role="form" method="post" >
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
                                                    <th width="150">Description</th>
                                                    <th width="100">Color</th>
                                                    <th width="80">Size/Width</th>
                                                    <th width="120">Supplier</th>
                                                    <th width="120">Article</th>
                                                    
                                                    <th width="120">UOM</th>
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
                                                <tr id="itemRow-{{ $itemCat->mcat_id}}_1">
                                                    <td class="right-btn">
                                                        <a class="btn btn-sm btn-outline-primary delete arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title='Right Click Action'><i class="las la-arrows-alt"></i></a>
                                                        <div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;">
                                                            <ul>
                                                              <li>
                                                                <a class="textblack arrows-context add-arrows" data-catid="{{ $itemCat->mcat_id }}"><i class="las la-cart-plus"></i> Add Row</a>
                                                              </li>   
                                                              <li>
                                                                <a class="textblack arrows-context remove-arrows"  data-catid="{{ $itemCat->mcat_id }}" ><i class="las la-trash"></i> Remove Row</a>
                                                              </li>           
                                                              <li>
                                                                <a class="textblack arrows-context add-item"  data-catid="{{ $itemCat->mcat_id }}" ><i class="las la-folder-plus"></i> Add New Item</a>
                                                            </li>
                                                            </ul>
                                                        </div>
                                                        
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="itemid-{{ $itemCat->mcat_id}}_1" value="" name="itemid[]">
                                                        <input type="text" data-category="{{ $itemCat->mcat_id }}" data-type="item" name="item[]" id="item-{{ $itemCat->mcat_id}}_1" class="form-control autocomplete_txt items-{{ $itemCat->mcat_id}}" autocomplete="off" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                      <input type="text" data-type="description" name="description[]" id="description-{{ $itemCat->mcat_id}}_1" class="form-control" autocomplete="off" required>
                                                    </td>
                                                    <td>
                                                      <select name="color[]" id="color-{{ $itemCat->mcat_id}}_1" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value">
                                                          <option value=""> - Select - </option>
                                                          @foreach($getColor as $color)
                                                          <option value="{{ $color->clr_id }}">{{ $color->clr_name }}</option>
                                                          @endforeach
                                                      </select>
                                                    </td>
                                                    <td>
                                                      <input type="text" name="size_width[]" id="size_width_1" class="form-control" autocomplete="off" >
                                                    </td>
                                                    <td>
                                                        <select name="" id="supplier-{{ $itemCat->mcat_id}}_1" data-category="{{ $itemCat->mcat_id }}" class="form-control supplier" disabled>
                                                          <option value=""> - Select - </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="" id="article-{{ $itemCat->mcat_id}}_1" class="form-control" disabled>
                                                          <option value=""> - Select - </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="" id="uom-{{ $itemCat->mcat_id}}_1" class="form-control" disabled>
                                                          <option value=""> - Select - </option>
                                                          
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption-{{ $itemCat->mcat_id}}_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="extraper[]" id="extraper_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="extraqty[]" id="extraqty_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                    </td>
                                                    <td>
                                                        <input type="text" step="any" min="0" value="0" name="total[]" id="total_1" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                    </td>
                                                    
                                                </tr>
                                                
                                            </tbody>
                                            @endforeach
                                            
                                        </table>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="submit-invoice invoice-save-btn">
                                            <button type="button" class="btn btn-outline-success btn-md text-center"><i class="fa fa-save"></i> Save</button>
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
      <div class="modal-body" style="padding-top: 0;">
        <div class="modal-content-result" id="content-result">
          
        </div>
      </div>
      
    </div>
  </div>
</div>
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script>
    var i=$('table tr').length;
    $(document).on('click','.add-arrows',function(){
        // check exists empty item
        var lastId = i-1;
        var catid = $(this).data('catid');
        var flag = 0;
        var totalRow = $('.items-'+catid).length;
        // if(totalRow <= 1){
        //     $(".items-"+catid).each(function(key){
        //         if($(this).val() === null || $(this).val() === ''){
        //             flag = 1;
        //             $(this).focus();
        //             return false;
        //         }
        //     });
        // }else{
        //     $(".items-"+catid).each(function(key){
        //         if(key !== (totalRow - 1) && ($(this).val() === null || $(this).val() === '')){
        //             flag = 1;
        //             return false;
        //         }
        //     }); 
        // }
        
        if(flag === 0){
            html = '<tr id="itemRow-'+catid+'_'+i+'">';
            html += '<td class="right-btn"><a class="btn btn-sm btn-outline-primary delete arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title=\'Right Click Action\'><i class="las la-arrows-alt"></i></a><div class="context-menu " id="context-menu-file-" style="display:none;position:absolute;z-index:1;"><ul><li><a class="textblack arrows-context add-arrows"  data-catid="'+catid+'" ><i class="las la-cart-plus"></i> Add Row</a></li><li><a class="textblack arrows-context remove-arrows" data-catid="'+catid+'" ><i class="las la-trash"></i> Remove Row</a></li><li><a class="textblack arrows-context add-item"  data-catid="'+catid+'" ><i class="las la-folder-plus"></i> Add New Item</a></li></ul></div></td>';
            html += '<td><input type="hidden" id="itemid-'+catid+'_'+i+'" value="" name="itemid[]"><input type="text" data-category="'+catid+'" data-type="item" name="item[]" id="item-'+catid+'_'+i+'" class="form-control autocomplete_txt items-'+catid+'" autocomplete="off" onClick="this.select()"></td>';
            html += '<td><input type="text" data-type="empname" name="name[]" id="name_1" class="form-control " autocomplete="off" required></td>';
            html += '<td><select name="" id="" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value"><option value=""> - Select - </option>@foreach($getColor as $color) <option value="{{ $color->clr_id }}">{{ $color->clr_name }}</option>@endforeach</select></td>';
            html += '<td><input type="text" name="department[]" id="department_1" class="form-control" autocomplete="off" ></td>';
            html += '<td><select name="" id="" class="form-control"><option value=""> - Select - </option></select></td>';
            html += '<td><select name="" id="uom-'+catid+'_'+i+'" class="form-control"><option value=""> - Select - </option></select></td>';
            html += '<td><select name="" id="" class="form-control"><option value=""> - Select - </option></select></td>';
            html += '<td><input type="number" step="any" min="0" value="0" name="otherdeduct[]" id="otherdeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="number" step="any" min="0" value="0" name="salaryadd[]" id="salaryadd_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="number" step="any" min="0" value="0" name="cgdeduct[]" id="cgdeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="number" step="any" min="0" value="0" name="fooddeduct[]" id="fooddeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
            html += '</tr>';
            $(this).parent().parent().parent().parent().parent().after(html);
            // $('#').append(html);
            $('#description-'+catid+'_'+i).focus();
            i++;
        }else{
            $('#item-'+catid+'_'+i).focus();
        }
        
    });

    // supplier 
    $(document).on('change', '.supplier', function(){
        var id_arr = $(this).attr('id'),
            id = id_arr.split("_"),
            itemCategory = $(this).data('category');
        $('#article-'+itemCategory+'_'+id[1]).val(null).trigger('change').attr('disabled', true);
        if($(this).val() !== ''){
            $.ajax({
                type: "GET",
                url: '{{ url("/merch/search/ajax-supplier-article-search") }}',
                data: {
                    mr_supplier_sup_id: $(this).val()
                },
                success: function(response)
                {
                    if(response !== ''){
                        $('#article-'+itemCategory+'_'+id[1]).select2({
                            data: response
                        }).removeAttr('disabled');
                    }
                },
                error: function (reject) {
                  console.log(reject);
                }
            });
        }
    });
    //auto-complete script
    $(document).on('focus keyup','.autocomplete_txt',function(){
        type = $(this).data('type');
        typeId = $(this).attr('id');
        itemCat = $(this).data('category');
        name = $(this).val();
        // console.log(itemCat);
        inputIdSplit = typeId.split("_");

        if(type =='item' )autoTypeNo=0;  
        
        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url : '{{ url("/merch/search/ajax-item-search") }}',
                    //dataType: "json",
                    method: 'get',
                    data: {
                      keyvalue: request.term,
                      type: type,
                      category: itemCat
                    },
                    success: function( data ) {
                        
                        response( $.map( data.items, function( item ) {
                            if(item.item_name !== ''){
                                if(type =='item') autoTypeShow = item.item_name;
                                return {
                                    label: autoTypeShow+' - '+item.item_code,
                                    value: autoTypeShow,
                                    data : item,
                                    supplier: data.supplier
                                }
                            }else{
                                return {
                                    label: item.item_code,
                                    value: ' ',
                                    data : '',
                                    supplier: ''
                                }
                            }
                        }));
                    }
                });
            },
            autoFocus: true,            
            minLength: 0,
            select: function( event, ui ) {
                var item = ui.item.data;                        
                var supplier = ui.item.supplier;                        
                // console.log(item);
                // console.log(supplier);
                id_arr = $(this).attr('id');
                id = id_arr.split("_");
                itemCategory = $(this).data('category');
                $('#supplier-'+itemCategory+'_'+id[1]).val(null).trigger('change').attr('disabled', true);
                $('#article-'+itemCategory+'_'+id[1]).val(null).trigger('change').attr('disabled', true);
                $('#uom-'+itemCategory+'_'+id[1]).val(null).trigger('change').attr('disabled', true);
                if(item !== ''){
                    $('#itemid-'+item.mcat_id+'_'+id[1]).val(item.id);
                    $('#uom-'+item.mcat_id+'_'+id[1]).select2({
                        data: item.uom
                    }).removeAttr('disabled');

                    $('#supplier-'+item.mcat_id+'_'+id[1]).select2({
                        data: supplier
                    }).removeAttr('disabled');

                    setTimeout(function() { $('#description-'+item.mcat_id+'_'+id[1]).focus().select(); }, 100);
                    $(this).parent().parent().find('.right-btn .add-arrows').click();
                }
                
            }               
        });
    });


    //It restrict the non-numbers
    var specialKeys = new Array();
    specialKeys.push(8,46); //Backspace
    function IsNumeric(e) {
        var keyCode = e.which ? e.which : e.keyCode;
        //console.log( keyCode );
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
        return ret;
    }


    $(document).on('click', '.remove-arrows', function(){
        $(this).parent().parent().parent().parent().parent().remove();
    })
    $(document).on("contextmenu", ".right-btn", function(e) {
        // Show contextmenu
        $(".context-menu").hide();
        $(this).parent().find('.context-menu').toggle(100).css({
          display:"block",
            left: "15px"
        });
          
        // disable default context menu
        return false;
    });

    // Hide context menu
    $(document).bind('contextmenu click',function(){
        $(".context-menu").hide();
    });

    $(document).on('keyup', 'input, select', function(e) {
        if (e.which == 39) { // right arrow
          $(this).closest('td').next().find('input, select').focus().select();
        } else if (e.which == 37) { // left arrow
          $(this).closest('td').prev().find('input, select').focus().select();
        } else if (e.which == 40) { // down arrow
          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus().select();
        } else if (e.which == 38) { // up arrow
          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus().select();
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
    $(function () {
        $(".xyz-body").sortable({
            items: 'tr:not(tr:first-child)',
            cursor: 'pointer',
            axis: 'y',
            dropOnEmpty: false,
            start: function (e, ui) {
                ui.item.addClass("selected");
            },
            stop: function (e, ui) {
                ui.item.removeClass("selected");
                $(this).find("tr").each(function (index) {
                    if (index > 0) {
                        // $(this).find("td").eq(2).html(index);
                    }
                });
            }
        });
    });

    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    $(document).on('click', '.add-item', function() {
      var name = $(this).data('name');
      var associate = $(this).data('associate');
      var yearMonth = $(this).data('month-year');
      $("#modal-title-right").html(' New Item');
      $('#right_modal_item').modal('show');
      $("#content-result").html(loaderContent);
      // $.ajax({
      //       url: "{{ url('hr/operation/partial_job_card') }}",
      //       data: {
      //           associate: associate,
      //           month_year: yearMonth
      //       },
      //       type: "GET",
      //       success: function(response){
      //         // console.log(response);
      //           if(response !== 'error'){
      //             setTimeout(function(){
      //               $("#content-result").html(response);
      //             }, 1000);
      //           }else{
      //             console.log(response);
      //           }
      //       }
      //   });
    });

</script>
@endpush
@endsection