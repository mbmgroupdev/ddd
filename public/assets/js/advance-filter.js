let rootUrl = window.location.origin;
//Load Department List By Area ID
$('#area').on("change", function(){
  if($(this).val() !== ''){
    $.ajax({
      url : rootUrl+"/hr/area-wise-department/"+$(this).val(),
      type: 'get',
      success: function(data)
      {
        if(data.status === 'success'){
          departmentLoad(data.value);
        }
      },
      error: function(reject)
      {
        console.log(reject);
      }
    });
  }else{
    departmentLoad('all');
  }
  sectionLoad('all');
  subSectionLoad('all');
});

//Load Section List By department ID
$('#department').on("change", function(){
  if($(this).val() !== ''){
    $.ajax({
      url : rootUrl+"/hr/department-wise-section/"+$(this).val(),
      type: 'get',
      success: function(data)
      {
        if(data.status === 'success') sectionLoad(data.value);
      },
      error: function(reject)
      {
        console.log(reject);
      }
    });
  }else{
    sectionLoad('all');
  }
  subSectionLoad('all');
});
//Load Sub Section List by Section
$('#section').on("change", function(){
  if($(this).val() !== ''){
    $.ajax({
      url : rootUrl+"/hr/section-wise-subsection/"+$(this).val(),
      type: 'get',
      success: function(data)
      {
        if(data.status === 'success') subSectionLoad(data.value);
      },
      error: function(reject)
      {
        console.log(reject);
      }
    });
  }else{
    subSectionLoad('all');
  }
});

function checkAllGroup(val){
  var id = $(val).attr('id')
  if($(val).is(':checked')){
    $('.'+id).each(function() {
        $(this).prop("checked", true);
    });
  }else{
    $('.'+id).each(function() {
        $(this).prop("checked", false);
    });
  }
}
$(document).on('click', '.custom-control-input', function(event) {
  let id = $(this).attr('id');
  let idsplit = id.split('-');
  let name = idsplit[0];
  let checkLength = $('.'+name+':checkbox').length;
  let selectLength = $('.'+name+':checkbox:checked').length;
  if(checkLength === selectLength){
    $('#'+name).prop("checked", true);
  }else{
    $('#'+name).prop("checked", false);
  }
});
function departmentLoad(data){
  $('#department').empty().attr('disabled', true).after(afterLoader);
  if(data === 'all'){
    data = department;
  }
  $('#department').append('<option value=""> - Choose Department - </option>');
  $.each(data, function(index, el) {
    $('#department').append('<option value="'+el.hr_department_id+'">'+el.hr_department_name+'</option>');
  });
  removeEndLoad('department');
}

function sectionLoad(data){
  $('#section').empty().attr('disabled', true).after(afterLoader);;
  if(data === 'all'){
    data = section;
  }
  $('#section').append('<option value=""> - Choose Section - </option>');
  $.each(data, function(index, el) {
    $('#section').append('<option value="'+el.hr_section_id+'">'+el.hr_section_name+'</option>');
  });
  removeEndLoad('section');
}
function subSectionLoad(data){
  $('#subSection').empty().attr('disabled', true).after(afterLoader);;
  if(data === 'all'){
    data = subSection;
  }
  $('#subSection').append('<option value=""> - Choose Sub Section - </option>');
  $.each(data, function(index, el) {
    $('#subSection').append('<option value="'+el.hr_subsec_id+'">'+el.hr_subsec_name+'</option>');
  });
  removeEndLoad('subSection');
}
function removeEndLoad(attr){
  setTimeout(function(){
    $('.loading-select').remove();
    $('#'+attr).removeAttr('disabled');
  }, 500);
}
$(document).on('click', '.filter', function(event) {
  $('#right_modal_navbar').modal('show');
  $('#navbar-title-right').html('Advanced Filter');
});
$(document).on('click', '.filterBtnSubmit', function(e) {
  e.preventDefault();
  advFilter();
});
function advFilter(){
  $(".prev_btn").click();
  $("#result-data").html(loaderContent);
  $("#single-employee-search").hide();
  var format = $('input[name="report_format"]').val();
  $('html, body').animate({
      scrollTop: $("#result-data").offset().top
  }, 2000);
  var data = $("#filterForm").serialize() + '&' + $("#formReport").serialize();
  var formAction = $("#formReport").attr("action");
  var formType = $("#formReport").attr("method");
  $.ajax({
      type: formType,
      url: formAction,
      data: data, // serializes the form's elements.
      success: function(response)
      {
        // console.log(response);
        if(response !== 'error'){
          $("#result-data").html(response);
        }else{
          $("#result-data").html('');
        }
        if(format == 0 && response !== 'error'){
          $("#single-employee-search").show();
          $('.list_view').addClass('active').attr('disabled', true);
          $('.grid_view').removeClass('active').attr('disabled', false);
        }else{
          $("#single-employee-search").hide();
          $('.grid_view').addClass('active').attr('disabled', true);
          $('.list_view').removeClass('active').attr('disabled', false);
        }
      },
      error: function (reject) {
          console.log(reject);
      }
  });
}
$(".grid_view, .list_view").click(function() {
  var value = $(this).attr('id');
  $('input[name="report_format"]').val(value);
  $('input[name="employee"]').val('');
  advFilter();
});

$("#reportGroupHead").on("change", function(){
  var group = $(this).val();
  $("#reportGroup").val(group);
  advFilter();
});

$("#yearMonth").on("change", function(){
  advFilter();
});

$(document).on('click', '.clear-filter', function(event) {
  var yearMonth = $("#yearMonth").val();
  if(confirm("Are you sure you want to clear the filter?"))
    window.location.reload();
});
