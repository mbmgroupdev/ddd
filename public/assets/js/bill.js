var base_url = $("#base_url").val(); 

$(document).on('change', '.pay_type', function(){
	let type = $(this).val();
	let typeLabel = $(this).find("option:selected").text();
	let relative = $(this).data('duration');
	$("label[for*='"+relative+"']").html(typeLabel);
	if(type === '4'){
		$("#"+relative).prop("type", "text").addClass('outtime');
		timePicker('outtime');
	}else{
		$("#"+relative).prop("type", "number").removeClass('outtime');
		$("#"+relative).val('0')
	}
});
function timePicker(calssName){
	$('.'+calssName).datetimepicker({
	  format:'HH:mm:ss',
	  allowInputToggle: false
	});
}

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
		setTimeout(function(){ timePicker('outtime'); }, 500);
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

    if($("#bill_type_id").val() === '' || ($("#unit").val() === '')){
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
            setTimeout(function(){ timePicker('outtime'); }, 500);
            $("#"+category+"-appendType").prepend(typeWisePrepend);
            $("#"+category+"-targettype").append('<input type="hidden" id="'+category+'target-'+type+'" value="'+type+'">');   
        }
        

    }
});

function loadContent(category, type, typeText){
    var html = '';
    html += '<div class="row"><div class="col-sm-12 table-wrapper-scroll-y table-custom-scrollbar">';
    html += '<table class="table table-bordered table-hover table-fixed '+category+type+'" id="itemList-"'+category+type+'>';
    html += '<button title="Remove this!" data-type="'+type+'" data-category="'+category+'" type="button" class="fa fa-close close-button removeContent"></button>';
    html += '<thead><tr class="text-center active">';
    html += '<th width="2%"><button class="btn btn-sm btn-outline-success addmore" data-type="'+type+'" data-category="'+category+'" type="button"><i class="las la-plus-circle"></i></button></th><th width="38%">'+typeText+' </th><th width="20%"> Amount</th>';
    if(type !== 'outtime'){
    	html += '<th width="20%">Pay Type</th><th width="20%" id="duration-"'+category+type+'>Parameter</th>';
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
	raw += '<tr><td><div id="stack-'+category+'-'+type+'"></div><button class="btn btn-sm btn-outline-danger delete removeRow" type="button" id="delete'+type+index+'"><i class="las la-trash"></i></button></td><td>';
	
	if(type !== 'outtime'){
		raw += '<input type="hidden" data-type="'+type+'" data-category="'+category+'" value="" name="'+category+'_rule['+type+']['+index+'][id]" id="id-'+category+'-'+type+'-'+index+'">';
        raw += '<input type="text" data-id="'+index+'" data-type="'+type+'" data-category="'+category+'"  id="name-'+category+'-'+type+'-'+index+'" class="form-control autocomplete_txt" autocomplete="off" placeholder="Type Search and enter/selected"></td>';
    }else{
    	
        raw += '<input type="text" data-id="'+index+'" data-type="'+type+'" data-category="'+category+'" name="'+category+'_rule['+type+']['+index+'][id]" id="name-'+category+'-'+type+'-'+index+'" class="form-control outtime" autocomplete="off" placeholder="Type time (24 hour format)" onClick="this.select()"></td>';
    }
    raw += '<td><input type="number" step="any" min="0" value="0" name="'+category+'_rule['+type+']['+index+'][amount]" id="amount-'+category+'-'+type+'-'+index+'" class="form-control " autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
    if(type !== 'outtime'){
	    raw += '<td><select id="pay_type-'+category+'-'+type+'-'+index+'" class="form-control select-search no-select pay_type" data-duration="duration-'+category+'-'+type+'-'+index+'" name="'+category+'_rule['+type+']['+index+'][pay_type]"><option value="1" selected="selected" >Present</option><option value="2" >Working Hour</option><option value="3">OT Hour</option><option value="4">Out-time</option></select></td>';
	    raw += '<td><input type="text" value="0" name="'+category+'_rule['+type+']['+index+'][duration]" id="duration-'+category+'-'+type+'-'+index+'" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
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
            category_arr = $(this).data('category');
            $("#id-"+category_arr+'-'+type_arr+'-'+id).val(item.id);
            // if($("#store-"+type_arr+'-'+item.id).length && $("#store-"+type_arr+'-'+item.id).val().length){
            //     var category_check = $("#store-"+type_arr+'-'+item.text).data('category');
            //     $('#name-'+category_arr+'-'+type_arr+'-'+id).notify('Already Exists in '+category_check+' section', 'error');
            //     setTimeout(function(){
            //         $('#name-'+category_arr+'-'+type_arr+'-'+id).val('');
            //     }, 400);
            // }else{
            //     $("#id-"+category_arr+'-'+type_arr+'-'+id).val(item.id);
            //     $("#stack-"+category_arr+'-'+type_arr).append('<input type="hidden" data-category="'+category_arr+'" id="store-'+type_arr+'-'+item.id+'" value="'+item.id+'">');   
            // }
        }               
    });
});
