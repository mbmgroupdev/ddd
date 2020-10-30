
	      
//adds extra table rows
var i=$('table tr').length;
$(".addmore").on('click',function(){
	// check exists empty item
	var lastId = i-1;
	var lastItem = $("#associate_"+lastId).val();
	if(lastItem !== ''){
		html = '<tr id="itemRow_'+i+'">';
		html += '<td><button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem'+i+'" onClick="deleteItem(this.id)"><i class="las la-trash"></i></button></td>';
		html += '<td>'+i+'</td>';
		html += '<td><input type="text" data-type="associateid" name="associate[]" id="associate_'+i+'" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" ></td>';
		html += '<td><input type="text" data-type="empname" name="name[]" id="name_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
		html += '<td><input type="text" name="designation[]" id="designation_'+i+'" class="form-control " autofocus="autofocus" autocomplete="off" readonly></td>';
		html += '<td><input type="text" name="department[]" id="department_'+i+'" class="form-control " autofocus="autofocus" autocomplete="off" readonly></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="advdeduct[]" id="advdeduct_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="cgdeduct[]" id="cgdeduct_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="fooddeduct[]" id="fooddeduct_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="otherdeduct[]" id="otherdeduct_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '<td><input type="number" step="any" min="0" value="0" name="salaryadd[]" id="salaryadd_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
		html += '</tr>';
		$('table').append(html);
		$('#associate_'+i).focus();
		i++;
	}else{
		$('#associate_'+lastId).focus();
	}
	
});


function deleteItem(itemId) {
	$("#"+itemId).parent().parent().remove();
}

var base_url = $("#base_url").val();
var month_year = $("#month_year").val();

//auto-complete script
$(document).on('focus keyup','.autocomplete_txt',function(){
	type = $(this).data('type');
	typeId = $(this).attr('id');
	// console.log(typeId);
	inputIdSplit = typeId.split("_");

	if(type =='associateid' )autoTypeNo=0;
	if(type =='empname' )autoTypeNo=1; 	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : base_url+'/hr/payroll/monthly-salary-adjustment-employee',
				//dataType: "json",
				method: 'get',
				data: {
				  keyvalue: request.term,
				  type: type,
				  month_year:month_year
				},
				 success: function( data ) {
					 response( $.map( data, function( item ) {
					 	if(type =='associateid') autoTypeShow = item.associate;
					 	if(type =='empname') autoTypeShow = item.name;
						return {
							label: autoTypeShow+' - '+item.name,
							value: autoTypeShow,
							data : item
						}
					}));
				}
			});
		},
		autoFocus: true,	      	
		minLength: 0,
		select: function( event, ui ) {
			console.log(ui.item.data);
			var item = ui.item.data;						
			id_arr = $(this).attr('id');
	  		id = id_arr.split("_");
			$('#associate_'+id[1]).val(item.associate);
			$('#name_'+id[1]).val(item.name);
			$('#designation_'+id[1]).val(item.designation);
			$('#department_'+id[1]).val(item.department);
			$('#advdeduct_'+id[1]).val(item.advdeduct);
			$('#cgdeduct_'+id[1]).val(item.cgdeduct);
			$('#fooddeduct_'+id[1]).val(item.fooddeduct);
			$('#otherdeduct_'+id[1]).val(item.otherdeduct);
			$('#salaryadd_'+id[1]).val(item.salaryadd);
			
			setTimeout(function() { $('#advdeduct_'+id[1]).focus().select(); }, 200);
			$(".addmore").click();
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
