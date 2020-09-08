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

    function printMe(el)
    { 

        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:9px;">');
        myWindow.document.write(document.getElementById(el).innerHtml);
        myWindow.document.write('</body></html>');
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    } 


    $(document).on("change", ".file-type-validation", function () {
        var allow = $(this).data('file-allow'),
            f_name = $(this).val(),
            ext = f_name.substring(f_name.lastIndexOf('.')+1);
        if ($.inArray( ext, allow) == -1) {
            $(this).val('');
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
    console.log(parameter);
    var header = '<p></p>'+
                 '<h3 style="text-align:center;">'+title+'</h3>';
    if(parameter.unit){
        header += '<h5 style="text-align:center;">Unit'+parameter.unit+'</h5>';

    }

    return header;
           
}

