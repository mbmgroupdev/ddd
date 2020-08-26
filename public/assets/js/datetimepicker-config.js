$(document).ready(function()
{
	$('.datetimepicker').datetimepicker({
		showClose: true,
		showTodayButton: true,
		dayViewHeaderFormat: "YYYY MMMM",
		format: "YYYY-MM-DD HH:mm"
	});
	$('.datepicker').datetimepicker({
		showClose: true,
		showTodayButton: true,
		dayViewHeaderFormat: "YYYY MMMM",
		format: "YYYY-MM-DD",
        minDate:false
	});
	$('.multidatepicker').datepicker({
		  //startDate: new Date(),
			 multidate: true,
			 format: "yyyy-mm-dd",
			 daysOfWeekHighlighted: "5,6",
			 datesDisabled: ['31/08/2017'],
			 language: 'en'
	});
	$('.singledatepicker').datepicker({
		  //startDate: new Date(),
			 multidate: false,
			 format: "yyyy-mm-dd",
			 daysOfWeekHighlighted: "5,6",
			 datesDisabled: ['31/08/2017'],
			 language: 'en'
	});
    $('.timepicker').datetimepicker({
        showClose: true,
        showTodayButton: true,
        format: "HH:mm"
    });
    $(".date_of_birth").datepicker({
        autoclose: true,
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        maxDate: '-18Y',
    });
    $('.yearpicker').datetimepicker({
        showClose: true,
        format: "YYYY"
    });

    $('.currentYearPicker').datetimepicker({
        minDate: moment(), // Current day
        viewMode: 'years',
        dayViewHeaderFormat: 'YYYY',
        format: "YYYY"
    });
    $('.currentMonthPicker').datetimepicker({
        viewMode: 'months',
        format: "MMMM"
    });

    $('.monthpicker').datetimepicker({
        showClose: true,
        format: "MMMM"
    });
	$('.monthYearpicker').datetimepicker({
		showClose: true,
        format: "MMMM-YYYY"
	});
});