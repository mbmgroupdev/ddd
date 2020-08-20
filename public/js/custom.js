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
});