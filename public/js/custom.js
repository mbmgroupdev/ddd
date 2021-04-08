$(function() {
    "use strict";

    const baseurl = window.location.protocol + "//" + window.location.host + "/",
          loader = '<p class="display-1 m-5 p-5 text-center text-warning">'+
                        '<i class="fas fa-circle-notch fa-spin "></i>'+
                    '</p>';

    $('select.associates').select2({
        placeholder: 'Select Employee',
        ajax: {
            url: baseurl+'hr/adminstrator/employee/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.user_name,
                            id: item.associate_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });

    $('select.allassociates').select2({
        placeholder: 'Select Employee',
        ajax: {
            url: baseurl+'hr/adminstrator/all-employee/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.user_name,
                            id: item.associate_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });

    $('.female-associates').select2({
        placeholder: 'Select Employee',
        ajax: {
            url: baseurl+'hr/adminstrator/employee/female-associates',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.user_name,
                            id: item.associate_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });
    $('select.mbm-order-no').select2({
        placeholder: 'Select MBM Order No - Style No',
        ajax: {
            url: baseurl+'merch/search/mbm-order-no',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.order_stl,
                            id: item.order_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });
    $('select.bulk-style-no').select2({
        placeholder: 'Select Style No',
        ajax: {
            url: baseurl+'merch/search/bulk-style-no',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.stl_no,
                            id: item.stl_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });
    $('select.users').select2({
        ajax: {
            url: baseurl+'hr/adminstrator/user/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.user_name,
                            id: item.associate_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });

    function formatState (state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
        '<span><img /> <span></span></span>'
        );
        var targetName = state.name;
        $state.find("span").text(targetName);
        return $state;
    };
    // Associate Search
    $('select.img-associates').select2({
        templateSelection:formatState,
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: baseurl+'hr/payroll/promotion-associate-search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    }) 
                };
          },
          cache: true
        }
    }); 

    $(document).on("change", ".file-type-validation", function () {
        var allow = $(this).data('file-allow'),
            f_name = $(this).val(),
            ext = f_name.substring(f_name.lastIndexOf('.')+1).toLowerCase();
        if ($.inArray( ext, allow) == -1) {
            $(this).val('');
            $.notify('Only '+allow.toString()+' type files are allowed!','error');
            if($(this).parent().find('.file-input-error').length){
                $(this).parent().find('.file-input-error').text('Only '+allow.toString()+' type files are allowed!')
            }else{
                $(this).parent().append('<p class="file-input-error">Only '+allow.toString()+' type files are allowed!</p>')
            }
        }
        else{
            $(this).parent().find('.file-input-error').remove();
        }
    }); 

    $('#global-datatable').DataTable({
        pagingType: "full_numbers" ,
        "sDom": 'lftip'

    }); 
    $('#global-trash').DataTable({
        pagingType: "full_numbers" ,
        "sDom": 'lftip'

    });

});

/* custom all row export*/
function allExport(e, dt, button, config) 
{
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        dt.one('preDraw', function (e, settings) {
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};


function customReportHeader(title, parameter)
{
    var header = '<p></p>'+
                 '<h3 style="text-align:center;">'+title+'</h3>';
    if(parameter.unit){
        header += '<h5 style="text-align:center;">Unit'+parameter.unit+'</h5>';

    }

    return header;
           
}

function operationReportHeader(title, parameter)
{
    var header = '<h3 style="text-align:center;">'+title+'</h3>';
    if(parameter.report == 'holiday_roster'){

        header += '<h4 style="text-align:center;">Month: <b>'+parameter.month;
    }else{
        header += '<h4 style="text-align:center;">Date: <b>'+parameter.report_from;
        
        if(parameter.report_from != parameter.report_to){     
            header += '- '+parameter.report_to;
        }
    }
    header += '</b></h4>';

    /*common parameters*/
    if(parameter.unit){
        header += '<h5 style="text-align:center;">Unit: '+parameter.unit+'</h5>';
    }

    header += '<table style="width:100%";><tr><td>'; 
    if(parameter.area){
        header += 'Area: '+parameter.area;
    }
    header += '</td><td style="text-align:center;">';
    if(parameter.department){
        header += 'Department: '+parameter.department;
    }
    header += '</td><td style="text-align:right;">';
    if(parameter.floor_id){
        header += 'Floor: '+parameter.floor_id;
    }
    header += '</td></tr><tr><td>';
    if(parameter.section){
        header += 'Section: '+parameter.section;
    }
    header += '</td><td style="text-align:center;">';
    if(parameter.subSection){
        header += 'Sub-section: '+parameter.subSection;
    }
    header += '</td><td style="text-align:right;">';
    if(parameter.line_id){
        header += 'Line: '+parameter.line_id;
    }
    header += '</td></tr></table>'; 

    return header;
}

function rosterReportHeader(title, parameter)
{
    var header = '<h3 style="text-align:center;">'+title+'</h3>';
    if(parameter.month){
        header += '<h4 style="text-align:center;">Month: <b>'+parameter.month+'</b>';
    }
    if(parameter.date){
        header += ' Date: <b>'+parameter.date+'</b>';
    }
    if(parameter.day){
        header += ' Day: <b>'+parameter.day+'</b>';
    }
    header += '</h4>';

    /*common parameters*/
    if(parameter.unit){
        header += '<h5 style="text-align:center;">Unit: '+parameter.unit+'</h5>';
    }

    header += '<table style="width:100%";><tr><td>'; 
    if(parameter.area){
        header += 'Area: '+parameter.area;
    }
    header += '</td><td style="text-align:center;">';
    if(parameter.department){
        header += 'Department: '+parameter.department;
    }
    header += '</td><td style="text-align:right;">';
    if(parameter.floor_id){
        header += 'Floor: '+parameter.floor_id;
    }
    header += '</td></tr><tr><td>';
    if(parameter.section){
        header += 'Section: '+parameter.section;
    }
    header += '</td><td style="text-align:center;">';
    if(parameter.subSection){
        header += 'Sub-section: '+parameter.subSection;
    }
    header += '</td><td style="text-align:right;">';
    if(parameter.line_id){
        header += 'Line: '+parameter.line_id;
    }
    header += '</td></tr></table>'; 

    return header;
}


function printMe(el)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<html><head></head><body style="font-size:9px;">');
    myWindow.document.write(document.getElementById(el).innerHTML);
    myWindow.document.write('</body></html>');
    // myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}



/*---------------------------------------------------------------------
   circle progress bar
-----------------------------------------------------------------------*/
$(function() {

    $(".progress-round").each(function() {

        var value = $(this).attr('data-value');
        var left = $(this).find('.progress-left .progress-bar');
        var right = $(this).find('.progress-right .progress-bar');

        if (value > 0) {
            if (value <= 50) {
                right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
            } else {
                right.css('transform', 'rotate(180deg)')
                left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
            }
        }

    })

    function percentageToDegrees(percentage) {

        return percentage / 100 * 360

    }

});



/*---------------------------------------------------------------------
Form Validation
-----------------------------------------------------------------------*/


window.addEventListener('load', function() {
    var forms = document.getElementsByClassName('needs-validation');
    var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}, false);



/*---------------------------------------------------------------------
    Form Wizard - 1
-----------------------------------------------------------------------*/

var current_fs, next_fs, previous_fs;
var opacity;
var current = 1;
var steps = jQuery("fieldset").length;

setProgressBar(current);

$(".next").click(function() {

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();


    jQuery("#top-tab-list li").eq(jQuery("fieldset").index(next_fs)).addClass("active");
    jQuery("#top-tab-list li").eq(jQuery("fieldset").index(current_fs)).addClass("done");


    next_fs.show();
    current_fs.animate({
        opacity: 0
    }, {
        step: function(now) {
            opacity = 1 - now;

            current_fs.css({
                'display': 'none',
                'position': 'relative',

            });

            next_fs.css({
                'opacity': opacity
            });
        },
        duration: 500
    });
    setProgressBar(++current);
});

jQuery(".previous").click(function() {

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();


    jQuery("#top-tab-list li").eq(jQuery("fieldset").index(current_fs)).removeClass("active");
    jQuery("#top-tab-list li").eq(jQuery("fieldset").index(previous_fs)).removeClass("done");


    previous_fs.show();

    current_fs.animate({
        opacity: 0
    }, {
        step: function(now) {
            opacity = 1 - now;

            current_fs.css({
                'display': 'none',
                'position': 'relative'
            });
            previous_fs.css({
                'opacity': opacity
            });
        },
        duration: 500
    });
    setProgressBar(--current);
});

function setProgressBar(curStep) {
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    jQuery(".progress-bar")
        .css("width", percent + "%")
}



jQuery(".submit").click(function() {
    return false;
})


/*---------------------------------------------------------------------
   validate form wizard
-----------------------------------------------------------------------*/

var navListItems = jQuery('div.setup-panel div a'),
    allWells = jQuery('.setup-content'),
    allNextBtn = jQuery('.nextBtn');

allWells.hide();

navListItems.click(function(e) {
    e.preventDefault();
    var $target = jQuery(jQuery(this).attr('href')),
        $item = jQuery(this);

    if (!$item.hasClass('disabled')) {
        navListItems.addClass('active');
        $item.parent().addClass('active');
        allWells.hide();
        $target.show();
        $target.find('input:eq(0)').focus();
    }
});

allNextBtn.click(function() {
    var curStep = jQuery(this).closest(".setup-content"),
        curStepBtn = curStep.attr("id"),
        nextStepWizard = jQuery('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
        curInputs = curStep.find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],textarea,select"),
        isValid = true;

    jQuery(".form-group").removeClass("has-error");
    for (var i = 0; i < curInputs.length; i++) {
        if (!curInputs[i].validity.valid) {
            isValid = false;
            jQuery(curInputs[i]).closest(".form-group").addClass("has-error");
        }
    }

    if (isValid){
        nextStepWizard.removeClass('disabled').trigger('click');
    }else{
        $.notify("Some field are required", {
          type: 'error',
          allow_dismiss: true,
          delay: 100,
          z_index: 1031,
          timer: 300
        });
    }
});

jQuery('div.setup-panel div a.active').trigger('click');

/*---------------------------------------------------------------------
   Vertical form wizard
-----------------------------------------------------------------------*/


var current_fs, next_fs, previous_fs; //fieldsets
var opacity;
var current = 1;
var steps = jQuery("fieldset").length;

setProgressBar(current);

$(".next").click(function() {

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();


    jQuery("#top-tabbar-vertical li").eq(jQuery("fieldset").index(next_fs)).addClass("active");


    next_fs.show();
    current_fs.animate({
        opacity: 0
    }, {
        step: function(now) {
            opacity = 1 - now;

            current_fs.css({
                'display': 'none',
                'position': 'relative'
            });
            next_fs.css({
                'opacity': opacity
            });
        },
        duration: 500
    });
    setProgressBar(++current);
});

jQuery(".previous").click(function() {

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();

    jQuery("#top-tabbar-vertical li").eq(jQuery("fieldset").index(current_fs)).removeClass("active");

    previous_fs.show();

    current_fs.animate({
        opacity: 0
    }, {
        step: function(now) {
            opacity = 1 - now;

            current_fs.css({
                'display': 'none',
                'position': 'relative'
            });
            previous_fs.css({
                'opacity': opacity
            });
        },
        duration: 500
    });
    setProgressBar(--current);
});

function setProgressBar(curStep) {
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    jQuery(".progress-bar")
        .css("width", percent + "%")
}

jQuery(".submit").click(function() {
    return false;
})

