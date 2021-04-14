
var base_url = $("#base_url").val();      
//adds extra table rows
var i=$('table tr').length;
$(document).on('click', '.addmore',function(){
	let moretype = $(this).data('type');
	var i=$('table.'+moretype+' tbody tr').length;
	// check exists empty item
	var lastId = i-1;
	var lastItem = $('#'+moretype+'_'+lastId).val();
	if(lastItem !== ''){
		var rowIndex = entryRow(moretype, i);
		$('table.'+moretype+' tbody').append(rowIndex);
		i++;
	}else{
		$('#'+moretype+'_'+lastId).focus();
	}
	
});


$(document).on('click', '.removeRow', function(){
	$(this).parent().parent().remove();
});

$(document).on('click', '.removeContent', function(){
	let retype = $(this).data('type');
	$("#target-"+retype).remove();
	$(this).parent().parent().remove();	
});


$(document).on('change keyup blur','.changesNo',function(e){
	id_arr = $(this).attr('id');
	id = id_arr.split("_");

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
    $("#syncBtn").show();
    // $("#type-for").attr('disabled', 'false');
    $("#appendType").show();
  }else{
    $("#syncBtn").hide();
    // $("#type-for").attr('disabled', 'true');
    $("#appendType").hide();
  }
});
$(document).on('click', '#sync-type', function () {
    var type = $("#type-for").val();
    var typeText = $("#type-for option:selected" ).text();
    console.log(type)
    if(type !== '' && type !== null){
        if($('#target-'+type).length && $('#target-'+type).val().length){
            $.notify(typeText+' Already Exists', 'error');
        }else{
            var typeWisePrepend = loadContent(type, typeText);
            $("#appendType").prepend(typeWisePrepend);
            $("#targettype").append('<input type="hidden" name="'+type+'" id="target-'+type+'" value="'+type+'">');   
        }
        

    }
});

function loadContent(type, typeText){
    var html = '';
    html += '<div class="row"><div class="col-sm-12 table-wrapper-scroll-y table-custom-scrollbar">';
    html += '<table class="table table-bordered table-hover table-fixed '+type+'" id="itemList">';
    html += '<button title="Remove this!" data-type="'+type+'" type="button" class="fa fa-close close-button removeContent"></button>';
    html += '<thead>';
    html += '<tr class="text-center active"><th width="2%"><button class="btn btn-sm btn-outline-success addmore" data-type="'+type+'" type="button"><i class="las la-plus-circle"></i></button></th><th width="38%">'+typeText+' Name</th><th width="20%"> Eligible Month</th><th width="20%">Amount</th><th width="20%">Or, % of Basic</th><th width="20%">Cut of Date</th></tr>';
    html += '</thead>';
    html += '<tbody>';
    html += entryRow(type, 0);
    html += '</tbody>';
    html += '</table>';
    html += '</div></div>';
    return html;
}

function entryRow(type, index){
	return '<tr><td><button class="btn btn-sm btn-outline-danger delete removeRow" type="button" id="delete'+type+index+'"><i class="las la-trash"></i></button></td><td><input type="hidden" data-type="'+type+'" value="" name="id_'+type+'[]" id="id_'+type+'_'+index+'"><input type="text" data-type="'+type+'" name="'+type+'[]" id="'+type+'_'+index+'" class="form-control autocomplete_txt" autocomplete="off"></td><td><input type="number" step="any" min="0" value="0" name="eligible_'+type+'[]" id="eligible'+type+'_'+index+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="number" step="any" min="0" value="0" name="amount_'+type+'[]" id="amount'+type+'_'+index+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="number" step="any" min="0" value="0" name="basic_'+type+'[]" id="basic'+type+'_'+index+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="date" value="" name="cutdate_'+type+'[]" id="cut'+type+'_'+index+'" class="form-control"></td></tr>';
}

$(document).on('focus keyup','.autocomplete_txt',function(){
    type = $(this).data('type');
    typeId = $(this).attr('id');

    // console.log(type);
    inputIdSplit = typeId.split("_");

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
            var item = ui.item.data;
            id_arr = $(this).attr('id');
            type_arr = $(this).data('type');
            eligibleMonth = $("#eligible-month").val();
            bonusAmount = $("#bonus_amount").val();
            bonusPercent = $("#bonus_percent").val();
            cutDate = $("#cut_date").val();
            id = id_arr.split("_");
            $("#id_"+type_arr+'_'+id[1]).val(item.id);
            $("#eligible"+type_arr+'_'+id[1]).val(item.id);
            $("#eligible"+type_arr+'_'+id[1]).val(eligibleMonth);
            $("#amount"+type_arr+'_'+id[1]).val(bonusAmount);
            $("#basic"+type_arr+'_'+id[1]).val(bonusPercent);
            $("#cut"+type_arr+'_'+id[1]).val(cutDate);
        }               
    });
});