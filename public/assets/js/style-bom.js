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
