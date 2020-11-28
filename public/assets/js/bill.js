
	      
//adds extra table rows
var i=$('table tr').length;
$(".addmore").on('click',function(){
	// check exists empty item
	var lastId = i-1;
	var lastItem = $("#designation_"+lastId).val();
	if(lastItem !== ''){
		html = '<tr id="itemRow_'+i+'">';
		html += '<td><button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem'+i+'" onClick="deleteItem(this.id)"><i class="las la-trash"></i></button></td>';
		html += '<td>'+i+'</td>';
		html += '<td><input type="text" data-type="designation" name="designation[]" id="designation_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="special_tiffin[]" id="tiffin_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="special_dinner[]" id="dinner_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '</tr>';
		$('table').append(html);
		$('#designation_'+i).focus();
		i++;
	}else{
		$('#designation_'+lastId).focus();
	}
	
});


function deleteItem(itemId) {
	$("#"+itemId).parent().parent().remove();
}

var base_url = $("#base_url").val();

//auto-complete script
$(document).on('focus keyup','.autocomplete_txt',function(){
	type = $(this).data('type');
	typeId = $(this).attr('id');
	// console.log(typeId);
	inputIdSplit = typeId.split("_");

	if(type =='designation' )autoTypeNo=0;	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : base_url+'/hr/search-designation',
				//dataType: "json",
				method: 'get',
				data: {
				  keyvalue: request.term
				},
				 success: function( data ) {
					 response( $.map( data, function( item ) {
					 	if(type =='designation') autoTypeShow = item.name;
						return {
							label: item.name,
							value: item.name,
							data : item
						}
					}));
				}
			});
		},
		autoFocus: true,	      	
		minLength: 0,
		select: function( event, ui ) {
			var item = ui.item.data;						
			id_arr = $(this).attr('id');
	  		id = id_arr.split("_");
			$('#designation_'+id[1]).val(item.designation);
			setTimeout(function() { 
				$(".addmore").click();
				$('#tiffin_'+id[1]).focus().select(); 
			}, 200);
		}		      	
	});
});

$(document).on('change keyup blur','.changesNo',function(e){
	id_arr = $(this).attr('id');
	id = id_arr.split("_");

	if( e.which == 13 ){
		if(id[0] == 'dinner'){
			$(".addmore").click();
		}else if(id[0] == 'tiffin'){
			$('#dinner_'+id[1]).focus().select();
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
