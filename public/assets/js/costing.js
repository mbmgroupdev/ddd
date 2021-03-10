var base_url = $("#base_url").val();
// fob and c&f check 

// change terms

$(document).on('change', '.terms:radio', function(){
    termsCondition($(this));
    changeCost($(this), 'radio');
    
}); 
$(document).ready(function() {
    $(".terms:checked").each(function(){
        termsCondition($(this));
    });
});

function termsCondition(thisvalue){
    if(thisvalue.val() == "C&F"){
        thisvalue.parent().parent().parent().find('.fob').attr('disabled', true).val(0);
        thisvalue.parent().parent().parent().find('.lc').attr('disabled', true).val(0);
        thisvalue.parent().parent().parent().find('.freight').attr('disabled', true).val(0);
    }else{
        thisvalue.parent().parent().parent().find('.fob').removeAttr('disabled readonly').addClass('highlight');
        thisvalue.parent().parent().parent().find('.lc').removeAttr('disabled readonly').addClass('highlight');
        thisvalue.parent().parent().parent().find('.freight').removeAttr('disabled readonly').addClass('highlight');
    }
    
}
// on change input cost
$(document).on("keyup blur change", ".changesNo", function(){
    changeCost($(this), 'input');
});

function changeCost(thisvalue, type) {
    if(type === 'input'){
        var index = thisvalue.parent().parent();
    }else{
        var index = thisvalue.parent().parent().parent();
    }
    var fob = index.find(".fob").val();
    var lc = index.find(".lc").val();
    var freight = index.find(".freight").val();
    var consumption = index.find(".consumption").text();
    var extraCon = index.find(".extra").text();
    var unitprice = index.find(".unitprice").val();
    fob = (isNaN(fob) || fob == '')?'0':fob;
    lc = (isNaN(lc) || lc == '')?'0':lc;
    freight = (isNaN(freight) || freight == '')?'0':freight;
    consumption = (isNaN(consumption) || consumption == '')?'0':consumption;
    extraCon = (isNaN(extraCon) || extraCon == '')?'0':extraCon;
    unitprice = (isNaN(unitprice) || unitprice == '')?'0':unitprice;
    unitprice = parseFloat(parseFloat(unitprice)+parseFloat(fob)+parseFloat(lc)+parseFloat(freight)); 
    // console.log(unitprice)
    var comsumptionPer = parseFloat((parseFloat(consumption) * parseFloat(extraCon)) / 100).toFixed(6);
    var comsumptionEx = parseFloat(consumption) + parseFloat(comsumptionPer);   
    var totalpercost = parseFloat(parseFloat(unitprice)*parseFloat(comsumptionEx)).toFixed(6); 
    // set total price
    index.find(".totalpercost").html(totalpercost);
    index.find(".pertotalcosting").val(totalpercost);
    var catid = index.find(".unitprice").data('catid');
    var total = 0;
    var tSewFin = 0;
    // check cat wise
    $(".catTotalCost-"+catid).each(function(i, v) {
        if($(this).val() != '' )total += parseFloat( $(this).val() ); 
    });

    total = parseFloat(total).toFixed(6);
    $("#totalcosting-"+catid).html(total);
    // Total Sewing and Finishing Accessories Price
    var sewing = $('tbody').find('.Sewing').html();
    var finishing = $('tbody').find('.Finishing').html();
    sewing = (isNaN(sewing) || sewing == '')?'0':sewing;
    finishing = (isNaN(finishing) || finishing == '')?'0':finishing;
    tSewFin = parseFloat(parseFloat(finishing) + parseFloat(sewing)).toFixed(6);
    $("#tsewing-finishing").html(tSewFin);

    calculateFOB();
}
// calculate total and net fob price
function calculateFOB(){
    var categoryFob = 0;
    $(".categoryPrice").each(function(i, v) {
        if($(this).html() != '' )categoryFob += parseFloat( $(this).html() ); 
    });
    var totalFob = parseFloat(parseFloat(categoryFob)).toFixed(6); 
    $("#totalfob").html(totalFob);
}

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

//It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    //console.log( keyCode );
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}

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



