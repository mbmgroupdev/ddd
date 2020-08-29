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