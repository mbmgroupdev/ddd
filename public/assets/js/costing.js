var base_url = $("#base_url").val();
// fob and c&f check

// change terms

$(document).on('change', '.terms:radio', function(){
    termsCondition($(this));
});
$(document).ready(function() {
    $(".terms:checked").each(function(){
        termsCondition($(this));
    });
});

function termsCondition(thisvalue){
    if(thisvalue.val() == "C&F"){
        thisvalue.parent().parent().parent().find('.fob').attr('readonly', true).val(0);
        thisvalue.parent().parent().parent().find('.lc').attr('readonly', true).val(0);
        thisvalue.parent().parent().parent().find('.freight').attr('readonly', true).val(0);
        thisvalue.parent().parent().parent().find('.unitprice').removeAttr('disabled readonly').addClass('highlight action-input');
    }
    else{
        thisvalue.parent().parent().parent().find('.fob').removeAttr('disabled readonly').addClass('highlight');
        thisvalue.parent().parent().parent().find('.lc').removeAttr('disabled readonly').addClass('highlight');
        thisvalue.parent().parent().parent().find('.freight').removeAttr('disabled readonly').addClass('highlight');
        thisvalue.parent().parent().parent().find('.unitprice').attr('readonly', true).removeClass("action-input");
    }
    changeCost(thisvalue, 'radio');

}
// on change input cost
$(document).on("keyup blur", ".changesNo", function(){
    changeCost($(this), 'input');
});

$("body").on("change", ".commission, .changesNo, .sp_price", function(){
    $("#change-flag").val('1');
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
    // var total_unit_price = fob+lc+freight;
    var unitprice_for_fob_freight_lc = parseFloat(parseFloat(lc)+parseFloat(freight));
    // console.log(unitprice)
    var comsumptionPer = parseFloat((parseFloat(consumption) * parseFloat(extraCon)) / 100).toFixed(6);
    var comsumptionEx = parseFloat(consumption) + parseFloat(comsumptionPer);
    var totalpercost = '';
    if (fob > 0 || lc > 0 || freight > 0 ){
        totalpercost = parseFloat(parseFloat(unitprice_for_fob_freight_lc)+parseFloat(parseFloat(comsumptionEx)*parseFloat(fob))).toFixed(6);
    } else {
        totalpercost = parseFloat(parseFloat(unitprice)*parseFloat(comsumptionEx)).toFixed(6);
    }

    // set total price
    index.find(".totalpercost").html(totalpercost);
    index.find(".unitprice").val(unitprice_for_fob_freight_lc);
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

    // order & po costing qty, value cal
    if($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po'){
        var orderQty = $("#order-qty").val();
        var precost_req_qty = parseFloat(parseFloat(consumption) + parseFloat(comsumptionPer) * parseFloat(orderQty)).toFixed(6);
        var total_value = parseFloat(parseFloat(unitprice)*parseFloat(precost_req_qty)).toFixed(6);
        index.find(".totalperqty").html(precost_req_qty);
        index.find(".totalpervalue").html(total_value);
    }


    calculateFOB();
}

// special costing
$(document).on("keyup change blur", ".sp_price", function(){
    var sp_price = parseFloat($(this).val()).toFixed(6);
    sp_price = (isNaN(sp_price) || sp_price == '')?'0':sp_price;
    $(this).parent().parent().find(".sp_per_price").html(sp_price);
    calculateFOB();
});

// commission
$(document).on('change keyup blur','.buyer-commission-percent, .agent-commission-percent',function(){
    calculateFOB();
});

// calculate total and net fob price
function calculateFOB(){
    var categoryFob = 0;
    $(".categoryPrice").each(function(i, v) {
        if($(this).html() != '' )categoryFob += parseFloat( $(this).html() );
    });
    var netFob = parseFloat(categoryFob).toFixed(6);
    netFob = (isNaN(netFob) || netFob == '')?'0':netFob;
    $("#net-fob").html(netFob);
    $("#net_fob").val(netFob);

    //buyer fob
    var buyerPercent = $('.buyer-commission-percent').val();
    buyerPercent = (isNaN(buyerPercent) || buyerPercent == '')?'0':buyerPercent;
    var buyerPerVal = parseFloat((netFob * buyerPercent)/100).toFixed(6);
    $("#buyer-commission-unitprice").val(buyerPerVal);
    var buyerFob = parseFloat(parseFloat(netFob) + parseFloat(buyerPerVal)).toFixed(6);
    buyerFob = (isNaN(buyerFob) || buyerFob == '')?'0':buyerFob;
    $("#buyer-fob").html(buyerFob);
    $("#buyer_fob").val(buyerFob);

    //agent fob
    var agentPercent = $('.agent-commission-percent').val();
    agentPercent = (isNaN(agentPercent) || agentPercent == '')?'0':agentPercent;
    var agentPerVal = parseFloat((buyerFob * agentPercent)/100).toFixed(6);
    $("#agent-commission-unitprice").val(agentPerVal);
    var agentFob = parseFloat(parseFloat(buyerFob) + parseFloat(agentPerVal)).toFixed(6);
    $("#agent-fob").html(agentFob);
    $("#agent_fob").val(agentFob);

    // var totalFob = parseFloat(parseFloat(netFob) + parseFloat(buyerFob) + parseFloat(agentFob)).toFixed(6);
    $("#totalfob").html(agentFob);
}
// auto save
$(document).on('blur','.commission, .changesNo, .sp_price',function(){
    if($("#change-flag").val() === '1'){
        $("#change-flag").val('0');
        setTimeout(function(){
            saveCosting('auto');
        }, 600)
    }
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

// cal
$(document).on('contextmenu', 'input', function(event) {
    return false;
});

$(document).on('contextmenu', '.action-input', function(event) {
    $(".calc-wrapper").removeClass('out-of-network');

    var selectid = $(this).attr('id');
    $("#cal-input").val(selectid);
    return false;
});
$(document).on('click', '.close-cal', function(event) {
    $(".calc-wrapper").addClass('out-of-network');
    $(".calc-brown").click();
    $("#cal-input").val('');
});
$(document).on('click', '.ok-cal', function(event) {
    $(".calc-wrapper").addClass('out-of-network');
    var selectedid = $("#cal-input").val();
    var inputval = $(".calc-display span").html();
    var inputval = parseFloat(inputval).toFixed(6);
    inputval = (isNaN(inputval) || inputval == '')?'0':inputval;
    $('#'+selectedid).val(inputval);
    changeCost($('#'+selectedid), 'input');
    $("#cal-input").val('');
});



