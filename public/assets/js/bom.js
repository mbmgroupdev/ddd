var base_url = $("#base_url").val();
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
        html += '<td class="right-btn"><a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title="Right Click Action"><i class="las la-arrows-alt"></i></a><div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;"><ul><li><a class="textblack arrows-context add-arrows" data-catid="'+catid+'"><i class="las la-cart-plus"></i> Add Row</a></li><li><a class="textblack arrows-context remove-arrows" data-catid="'+catid+'" ><i class="las la-trash"></i> Remove Row</a></li><li><a class="textblack arrows-context add-item"  data-catid="{{ $itemCat->mcat_id }}" id="additem-'+catid+'_'+i+'"><i class="las la-folder-plus"></i> Add New Item</a></li></ul></div></td>';
        html += '<td><input type="hidden" id="bomitemid-'+catid+'_'+i+'" name="bomitemid[]" value=""><input type="hidden" id="itemcatid-'+catid+'_'+i+'" value="'+catid+'" name="itemcatid[]"><input type="hidden" id="itemid-'+catid+'_'+i+'" value="" name="itemid[]"><input type="text" data-category="'+catid+'" data-type="item" name="item[]" id="item-'+catid+'_'+i+'" class="form-control autocomplete_txt items-'+catid+'" autocomplete="off" onClick="this.select()"></td>';
        html += '<td><input type="text" data-type="description" name="description[]" id="description-'+catid+'_'+i+'" class="form-control" autocomplete="off"></td>';
        html += '<td><select name="color[]" id="color-'+catid+'_'+i+'" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value"><option value=""> - Select - </option></select></td>';
        html += '<td><input type="text" name="size_width[]" id="sizewidth-'+catid+'_'+i+'" class="form-control" autocomplete="off" ></td>';
        html += '<td><input type="hidden" name="supplierid[]" id="supplierid-'+catid+'_'+i+'"><div class="row m-0"><div class="col-9 p-0"><select name="supplier[]" id="supplier-'+catid+'_'+i+'" data-category="'+catid+'" class="form-control supplier" disabled><option value=""> - Select - </option></select></div><div class="col-3 pl-0 pr-0 pt-2"><a class="btn btn-xs btn-primary text-white addSupplier" id="addsupplier-'+catid+'_'+i+'" data-category="'+catid+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Supplier"><i class="fa fa-plus"></i></a></div></div></td>';
        html += '<td><div class="row m-0"><div class="col-9 p-0"><select name="article[]" id="article-'+catid+'_'+i+'" class="form-control articlechange" disabled><option value=""> - Select - </option></select><input type="hidden" class="articleid" name="articleid[]" id="articleid-'+catid+'_'+i+'" value=""></div><div class="col-3 pl-0 pr-0 pt-2"><a class="btn btn-xs btn-primary text-white" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article"><i class="fa fa-plus"></i></a></div></div></td>';
        html += '<td><select name="uom[]" id="uom-'+catid+'_'+i+'" class="form-control uomchange" disabled><option value=""> - Select - </option></select><input type="hidden" class="uomname" name="uomname[]" id="uomname-'+catid+'_'+i+'" value=""></td>';
        html += '<td><input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption-'+catid+'_'+i+'" data-category="'+catid+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
        html += '<td><input type="text" step="any" min="0" value="5" data-category="'+catid+'" name="extraper[]" id="extraper-'+catid+'_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
        html += '<td><input type="text" step="any" min="0" value="0" name="extraqty[]" id="extraqty-'+catid+'_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly></td>';
        html += '<td><input type="text" step="any" min="0" value="0" name="total[]" id="total-'+catid+'_'+i+'" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
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
    	var supid = $(this).val();
        $.ajax({
            type: "GET",
            url: base_url+'/merch/search/ajax-supplier-article-search',
            data: {
                mr_supplier_sup_id: $(this).val()
            },
            success: function(response)
            {
                if(response !== ''){

                	$('#supplierid-'+itemCategory+'_'+id[1]).val(supid);
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
                url : base_url+'/merch/search/ajax-item-search',
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
                $('#color-'+item.mcat_id+'_'+id[1]).select2({
                    data: getColor
                }).removeAttr('disabled');

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
    itemid = $(this).attr('id');
    i_id = itemid.split("_");
    
    $("#itemForm").hide();
    $('#right_modal_item').modal('show');
    $("#content-result").html(loaderContent);
    setTimeout(function(){
        $("#content-result").html('');
        $("#itemForm").show();
    },1000);
});

$(document).on('click', '.addSupplier', function() {
    suid = $(this).attr('id');
    s_id = suid.split("_");
    suCat = $(this).data('category');
    if($('#itemid-'+suCat+'_'+s_id[1]).val() !== null){
	    $("#supplierForm").hide();
	    $('#right_modal_supplier').modal('show');
	    $("#content-result-supplier").html(loaderContent);
	    setTimeout(function(){
	        $("#content-result-supplier").html('');
	        $("#supplierForm").show();
	    },1000);
    }
    
});

$("body").on("keyup", ".changesNo", function(){
	conid = $(this).attr('id');
    coid = conid.split("_");
    itCat = $(this).data('category');
	var consumption = $('#consumption-'+itCat+'_'+coid[1]).val();
	var extra = $('#extraper-'+itCat+'_'+coid[1]).val();
	consumption = (consumption === ''?0:consumption);
	extra = (extra === null?0:extra);
	var qty   = parseFloat(((parseFloat(consumption)/100)*parseFloat(extra))).toFixed(2);
	var total = (parseFloat(qty)+parseFloat(consumption)).toFixed(2);
	$('#extraqty-'+itCat+'_'+coid[1]).val(qty);
	$('#total-'+itCat+'_'+coid[1]).val(total);
});
// article 
$(document).on('change', '.articlechange', function(){
	var article = $(this).val();
    $(this).parent().find('.articleid').val(article);
});

// uom 
$(document).on('change', '.uomchange', function(){
	var uom = $(this).val();
    $(this).parent().find('.uomname').val(uom);
});