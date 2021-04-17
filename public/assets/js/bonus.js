
var base_url = $("#base_url").val();      
//adds extra table rows
var i=$('table tr').length;
$(document).on('click', '.addmore',function(){
	let moretype = $(this).data('type');
    let morecategory = $(this).data('category');
	var i=$('table.'+morecategory+moretype+' tbody tr').length;
	// check exists empty item
	var lastId = i-1;
	var lastItem = $('#name-'+morecategory+'-'+moretype+'-'+lastId).val();
	if(lastItem !== ''){
		var rowIndex = entryRow(morecategory, moretype, i);
		$('table.'+morecategory+moretype+' tbody').append(rowIndex);
		i++;
	}else{
		$('#name-'+morecategory+'-'+moretype+'-'+lastId).focus();
	}
	
});


$(document).on('click', '.removeRow', function(){
	$(this).parent().parent().remove();
});

$(document).on('click', '.removeContent', function(){
	let retype = $(this).data('type');
    let recategory = $(this).data('category');
	$("#"+recategory+"target-"+retype).remove();
	$(this).parent().parent().remove();	
});


$(document).on('blur','.changesNo',function(e){
	id_arr = $(this).attr('id');
	id = id_arr.split("-");
    var extra = id_arr.split("-");
    extra.shift();
    var extraid = extra.join('-');
    var flag = 0;
    var changeField = '';
    if($(this).val() > 0){
        if(id[0] === 'amount'){
            var gobal = $("#bonus_amount").val();
            changeField = "basic-"+extraid;
        }
        if(id[0] === 'basic'){
            var gobal = $("#bonus_percent").val();
            changeField = "amount-"+extraid;
        }
        if(id[0] === 'amount' || id[0] === 'basic'){
            // special
            if(id[1] === 'special' && parseFloat($(this).val()) < parseFloat(gobal)){
                flag = 1;
                $("#"+id_arr).notify('Always better than actual');
                $(this).val(gobal);
            }

            if(id[1] === 'partial' && parseFloat($(this).val()) > parseFloat(gobal)){
                flag = 1;
                $("#"+id_arr).notify('Always less than actual');
                $(this).val(gobal);
            }

            if(flag === 0){
                $("#"+changeField).val(0);
            }

        }

    }

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
$(document).on('click','#specialCheck',function(){
  if ($(this).is(":checked")) {
    isValid = true;

    if($("#bonus_for").val() === '' || ($("#bonus_percent").val() === '' && $("#bonus_amount").val() === '')){
        $.notify('Fill-up required field!', 'error');
        setTimeout(function() {
            $("#specialCheck").click();
        }, 500);
    }else{
        $(".rule-overlay").hide();
        $(".appendType").show();
    }
    
  }else{
    $(".rule-overlay").show();
    $(".appendType").hide();
  }
});
$(document).on('click', '.sync-type', function () {
    var category = $(this).data('category');
    var type = $("#"+category+"-type-for").val();
    var typeText = $("#"+category+"-type-for option:selected" ).text();
    // console.log(type)
    if(type !== '' && type !== null){
        if($("#"+category+"target-"+type).length && $("#"+category+"target-"+type).val().length){
            $.notify(typeText+' Already Exists', 'error');
        }else{
            var typeWisePrepend = loadContent(category, type, typeText);
            $("#"+category+"-appendType").prepend(typeWisePrepend);
            $("#"+category+"-targettype").append('<input type="hidden" name="'+type+'" id="'+category+'target-'+type+'" value="'+type+'">');   
        }
        

    }
});

function loadContent(category, type, typeText){
    var html = '';
    html += '<div class="row"><div class="col-sm-12 table-wrapper-scroll-y table-custom-scrollbar">';
    html += '<table class="table table-bordered table-hover table-fixed '+category+type+'" id="itemList-"'+category+type+'>';
    html += '<button title="Remove this!" data-type="'+type+'" data-category="'+category+'" type="button" class="fa fa-close close-button removeContent"></button>';
    html += '<thead><tr class="text-center active">';
    html += '<th width="2%"><button class="btn btn-sm btn-outline-success addmore" data-type="'+type+'" data-category="'+category+'" type="button"><i class="las la-plus-circle"></i></button></th><th width="38%">'+typeText+' Name</th>';
    if(category !== 'excluding'){
        html += '<th width="20%"> Eligible Month</th><th width="20%">Amount</th><th width="20%">Or, % of Basic</th><th width="20%">Bonus Date</th>';
    }
    
    html += '</tr></thead>';
    html += '<tbody>';
    html += entryRow(category, type, 0);
    html += '</tbody>';
    html += '</table>';
    html += '</div></div>';
    return html;
}

function entryRow(category, type, index){
    var raw = '';
	
    if(category !== 'excluding'){
        raw += '<tr><td><div id="stack-'+category+'-'+type+'"></div><button class="btn btn-sm btn-outline-danger delete removeRow" type="button" id="delete'+type+index+'"><i class="las la-trash"></i></button></td><td><input type="hidden" data-type="'+type+'" data-category="'+category+'" value="" name="'+category+'_rule['+type+']['+index+'][id]" id="id-'+category+'-'+type+'-'+index+'"><input type="text" data-id="'+index+'" data-type="'+type+'" data-category="'+category+'" name="'+type+'[]" id="name-'+category+'-'+type+'-'+index+'" class="form-control autocomplete_txt" autocomplete="off" placeholder="Type Search and enter/selected"></td>';
        raw += '<td><input type="number" step="any" min="0" value="0" name="'+category+'_rule['+type+']['+index+'][month]" id="eligible-'+category+'-'+type+'-'+index+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="number" step="any" min="0" value="0" name="'+category+'_rule['+type+']['+index+'][amount]" id="amount-'+category+'-'+type+'-'+index+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="number" step="any" min="0" value="0" name="'+category+'_rule['+type+']['+index+'][basic_percent]" id="basic-'+category+'-'+type+'-'+index+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="date" value="" name="'+category+'_rule['+type+']['+index+'][cutoff_date]" id="cut-'+category+'-'+type+'-'+index+'" class="form-control"></td>';
    }else{
        raw += '<tr><td><div id="stack-'+category+'-'+type+'"></div><button class="btn btn-sm btn-outline-danger delete removeRow" type="button" id="delete'+type+index+'"><i class="las la-trash"></i></button></td><td><input type="hidden" data-type="'+type+'" data-category="'+category+'" value="" name="'+category+'_rule['+type+']['+index+']" id="id-'+category+'-'+type+'-'+index+'"><input type="text" data-id="'+index+'" data-type="'+type+'" data-category="'+category+'" name="'+type+'[]" id="name-'+category+'-'+type+'-'+index+'" class="form-control autocomplete_txt" autocomplete="off" placeholder="Type Search and enter/selected"></td>';
    }
    raw += '</tr>';
    return raw;
}

$(document).on('focus keyup','.autocomplete_txt',function(){
    type = $(this).data('type');
    typeId = $(this).attr('id');

    // console.log(type);
    inputIdSplit = typeId.split("-");

    $(this).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url : base_url+'/hr/search-type',
                //dataType: "json",
                method: 'get',
                data: {
                  keyvalue: request.term,
                  type: type
                },
                 success: function( data ) {
                    // console.log(data);
                    if(data.type === 'success'){
                        response( $.map( data.value, function( item ) {
                        
                            return {
                                label: item.text,
                                value: item.text,
                                data : item
                            }
                        }));    
                    }else{
                        $.notify(data.message, data.type);
                    }
                    
                }
            });
        },
        autoFocus: true,            
        minLength: 0,
        select: function( event, ui ) {
            var item = ui.item.data,
            id = $(this).data('id'),
            type_arr = $(this).data('type'),
            category_arr = $(this).data('category'),
            eligibleMonth = $("#eligible-month").val(),
            bonusAmount = $("#bonus_amount").val(),
            bonusPercent = $("#bonus_percent").val(),
            cutDate = $("#cut_date").val();
            if($("#store-"+type_arr+'-'+item.text).length && $("#store-"+type_arr+'-'+item.text).val().length){
                var category_check = $("#store-"+type_arr+'-'+item.text).data('category');
                $('#name-'+category_arr+'-'+type_arr+'-'+id).notify('Already Exists in '+category_check+' section', 'error');
                setTimeout(function(){
                    $('#name-'+category_arr+'-'+type_arr+'-'+id).val('');
                }, 400);
            }else{
                $("#id-"+category_arr+'-'+type_arr+'-'+id).val(item.id);
                $("#eligible-"+category_arr+'-'+type_arr+'-'+id).val(eligibleMonth);
                $("#amount-"+category_arr+'-'+type_arr+'-'+id).val(bonusAmount);
                $("#basic-"+category_arr+'-'+type_arr+'-'+id).val(bonusPercent);
                $("#cut-"+category_arr+'-'+type_arr+'-'+id).val(cutDate);
                $("#stack-"+category_arr+'-'+type_arr).append('<input type="hidden" data-category="'+category_arr+'" id="store-'+type_arr+'-'+item.text+'" value="'+item.id+'">');   
            }
        }               
    });
});