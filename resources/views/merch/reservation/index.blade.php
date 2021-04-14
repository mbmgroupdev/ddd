@extends('merch.layout')
@section('title', 'Reservation List')
@section('main-content')
@push('css')
  <style>
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}
    th{
        font-size: 12px;
        font-weight: bold;
    }
    #example th:nth-child(2) select{
      width: 120px !important;
    }
    #example th:nth-child(3) select{
      width: 80px !important;
    } 
    #example th:nth-child(5) select{
      width: 80px !important;
    }
    #example th:nth-child(6) select{
      width: 80px !important;
    }
    /*#example th:nth-child(7) select{
      width: 80px !important;
    }*/
    #example th:nth-child(7) input{
      width: 110px !important;
    }
    #example th:nth-child(8) input{
      width: 80px !important;
    }
    
    .text-warning {
        color: #c49090!important;
    }
    table.dataTable thead>tr>td.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc {
        padding-right: 16px;
    }
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="#">Merchandising</a>
              </li>
              <li>
                  <a class="active">Reservation List</a>
              </li>
              <li class="top-nav-btn">
                <a class="btn btn-sm btn-primary add-new text-white" data-type="Add reservation"><i class="las la-plus"></i> New Reservation</a>
                <a href="{{ url('merch/orders')}}" class="btn btn-sm btn-success text-white" data-type="reservation"><i class="las la-list"></i> Order List</a>
              </li>
          </ul><!-- /.breadcrumb -->

      </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <table id="example" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;" border="1">
                            <thead>
                                <tr class="success">
                                    <th width="5%">SL.</th>
                                    <th width="10%">Unit</th>
                                    <th width="8%">Buyer</th>
                                    <th width="10%">Month-Year</th>
                                    <th width="10%">Product Type</th>
                                    <th width="5%">SAH</th>
                                    <th width="10%">Projection</th>
                                    <th width="10%">Confirmed</th>
                                    <th width="10%">Balance</th>
                                    {{-- <th width="5%">Status</th> --}}
                                    <th width="12%">Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@include('merch.common.right-modal')
@push('js')

<script type="text/javascript">
var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
$(document).on('click', '.add-new', function() {
    type = $(this).data('type');
    $('#right_modal_item').modal('show');
    $('#modal-title-right').html(type);
    $("#content-result").html(loaderContent);
    var url = '';
    if(type === 'Add reservation'){
      url = '/merch/reservation/create';
    }else if(type === 'Edit reservation'){
      var id = $(this).data('id');
      url = '/merch/reservation/'+id+'/edit';
    }else if(type === 'Order List'){
      var id = $(this).data('resid');
      url = '/merch/reservation/order-list/'+id;
    }else if(type === 'Order Entry'){
      var resId = $(this).data('resid');
      url = '/merch/reservation/order-entry/'+resId;
    }
    $.ajax({
        type: "GET",
        url: "{{ url('/')}}"+url,
        success: function(response)
        {
          // console.log(response);
          if(response !== 'error'){
            $('#content-result').html(response);
            $('.filter').select2({
                dropdownParent: $('#right_modal_item')
            });
          }else{
            $('#content-result').html('<h4 class="text-center">Something wrong, please close and try again!</h4>');
          }
        },
        error: function (reject) {
          console.log(reject);
        }
    });
    
});

$(document).on('click', '#itemBtn', function(event) {
  $("#app-loader").show();
  var curStep = jQuery(this).closest("#itemForm"),
    curInputs = curStep.find("input[type='text'], input[type='number'],input[type='hidden'],input[type='date'], input[type='month'],input[type='checkbox'],input[type='radio'],textarea,select"),
    isValid = true;
    
  $(".form-group").removeClass("has-error");
  for (var i = 0; i < curInputs.length; i++) {
    if (!curInputs[i].validity.valid) {
      isValid = false;
      $(curInputs[i]).closest(".form-group").addClass("has-error");
    }
  }
  var verb = 'POST';
  var url = '';
  var pageType = $("#page-type").val();
  var resQty = $("#res-quantity").val();
  var orderQty = $("#order-qty").val();
  if(parseFloat(orderQty) > parseFloat(resQty)){
    $("#res-quantity").notify('This Reservation Already Order Entry '+orderQty, 'error');
    $("#app-loader").hide();
    return false;
  }
  if(pageType === 'reservation-store'){
    if ($('#order-check').is(':checked')) {
      url = '{{ url("/merch/orders") }}';
    }else{
      url = '{{ route("reservation.store")}}';
    }
  }else if(pageType === 'reservation-update'){
    var resId = $("#res-id").val();
    url = '/merch/reservation/'+resId;
  }else if(pageType === 'order-store'){
    url = '{{ url("/merch/orders") }}';
  }
  // console.log(url);
  if (isValid){
     $.ajax({
        type: verb,
        url: url,
        data: curInputs.serialize(), // serializes the form's elements.
        success: function(response)
        {
          $("#app-loader").hide();
          console.log(response)
          $.notify(response.message, response.type);
          if(response.type === 'success'){
            setTimeout(function(){
              window.location.href=response.url;
            }, 1000);
          } 
        },
        error: function (reject) {
          $("#app-loader").hide();
          if( reject.status === 400) {
              var data = $.parseJSON(reject.responseText);
              $.notify(data.message, data.type);
          }else if(reject.status === 422){
            var data = $.parseJSON(reject.responseText);
            var errors = data.errors;
            // console.log(errors);
            for (var key in errors) {
              var value = errors[key];
              $.notify(value[0], 'error');
            }
             
          }
        }
     });
  }else{
      $("#app-loader").hide();
      $.notify("Some field are required", 'error');
  }
});

$(document).on('keyup', '.sah_cal', function(){
  var res_sewing_smv = parseInt($("#res-smv").val());
  var res_quantity= parseInt($("#res-quantity").val());
  res_sewing_smv = (isNaN(res_sewing_smv) || res_sewing_smv == '')?'0':res_sewing_smv;
  res_quantity = (isNaN(res_quantity) || res_quantity == '')?'0':res_quantity;
  var sah = parseFloat((res_sewing_smv*res_quantity)/60).toFixed(2);

  $("#sah").val(sah);
  $("#order-qty").val(res_quantity).attr('max', res_quantity);
});

$(document).on('change', '#order-check:checkbox', function(){
    if ($(this).is(':checked')) {
      $("#season").attr('required', true);
      $("#reference-no").attr('required', true);
      $("#style-no").attr('required', true);
      $("#order-entry-section").show();
    }else{
      $("#season").removeAttr('required');
      $("#reference-no").removeAttr('required');
      $("#style-no").removeAttr('required').val('').change();
      $("#order-entry-section").hide();

    }
});

$(document).on('change', '.buyerChange', function(){
  $('#season').empty().select2({data: [{id: '', text: ' Select Season'}]}).removeAttr('disabled');
  $('#style-no').empty().select2({data: [{id: '', text: ' Select Style'}]}).removeAttr('disabled');
  
  if($(this).val() !== null && $(this).val() !== ''){
    var buyerid = $(this).val();
    $.ajax({
        type: "GET",
        url: '{{ url("/merch/search/ajax-buyer-wise-style-season-search") }}',
        data: {
            b_id: $(this).val()
        },
        success: function(response)
        {
          // console.log(response)
          if(response.length !== 0){
            $('#season').select2({
                data: response
            }).removeAttr('disabled');
          }
        },
        error: function (reject) {
          $.notify(reject, 'error')
        }
    });
  }
});

$(document).on('change', '.seasonChange', function(){
  $('#style-no').empty().select2({data: [{id: '', text: ' Select Style'}]}).removeAttr('disabled');
  $("#brand").val('');
  $("#style-ref-2").val('');
  $("#mr_season_se_id").val('');
  var buyerid = $('#buyer').val();
  if($(this).val() !== null && $(this).val() !== '' && buyerid !== ''){
    $.ajax({
        type: "GET",
        url: '{{ url("/merch/search/ajax-season-wise-style-search") }}',
        data: {
          mr_buyer_b_id: buyerid,
          mr_season_se_id: $(this).val(),
          stl_type: 'Bulk'
        },
        success: function(response)
        {
          // console.log(response)
          if(response.length !== 0){
            $('#style-no').select2({
                data: response
            }).removeAttr('disabled');
          }
        },
        error: function (reject) {
          $.notify(reject, 'error')
        }
    });
  }
});

$(document).on('change', '.style-no', function(){
  $("#brand").val('');
  $("#style-ref-2").val('');
  $("#mr_season_se_id").val('');
  if($(this).val() !== null && $(this).val() !== ''){
    $.ajax({
        type: "GET",
        url: '{{ url("/merch/search/ajax-style-wise-info") }}',
        data: {
          stl_id: $(this).val(),
          key:['mr_brand_br_id', 'stl_product_name', 'mr_season_se_id']
        },
        success: function(response)
        {
          // console.log(response)
          if(response.length !== 0){
            $("#brand").val(response.brand);
            $("#style-ref-2").val(response.stl_product_name);
            $("#mr_season_se_id").val(response.mr_season_se_id);
          }
        },
        error: function (reject) {
          $.notify(reject, 'error')
        }
    });
  }
});
$(document).on('change', '#res-year-month', function(){
  $("#month").val($(this).val()).attr({
    max: $(this).val()
  });
});
$(document).ready(function(){ 
    var searchable = [2,3,6,7];
    var selectable = [1,4];
    var exportColName = ['Unit Name','Buyer Name','Unit','Month-Year', 'Product Type', 'SAH','Projection','Confirmed', 'Balance'];
    var dropdownList = {
        '1' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($prdtypList as $e) <?php echo "'$e'," ?> @endforeach]
    };
    var exportCol = [0,1,2,3,4,5,6,7];
    var dt = $('#example').DataTable({
        order: [], //reset auto order
        processing: true,
        language: {
          processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:150px;z-index:100;"></i>'
        },
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers", 
        ajax: {
           url: '{!! url("merch/reservation_list_data") !!}',
           type: "GET",
           headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
           } 
        }, 
        dom: "lBftrip",
        buttons: [   
          {
              extend: 'csv', 
              className: 'btn btn-sm btn-success',
              title: 'Order list',
              header: true,
              footer: false,
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function ( data, columnIdx ) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: ''
          }, 
          {
              extend: 'excel', 
              className: 'btn btn-sm btn-warning',
              title: 'Order list',
              header: true,
              footer: false,
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function ( data, columnIdx ) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: ''
          }, 
          {
              extend: 'pdf', 
              className: 'btn btn-sm btn-primary', 
              title: 'Order list',
              header: true,
              footer: false,
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function ( data, columnIdx ) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: ''
          }, 
          {
              extend: 'print', 
              className: 'btn btn-sm btn-default',
              title: '',
              header: true,
              footer: false,
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function ( data, columnIdx ) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: customReportHeader('Order list', { })
          } 
        ],
        columns: [  
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'hr_unit_name', name: 'hr_unit_name' }, 
            { data: 'b_name',  name: 'b_name' }, 
            { data: 'month_year', name: 'month_year' }, 
            { data: 'prd_type_name', name: 'prd_type_name' }, 
            { data: 'res_sah', name: 'res_sah' }, 
            { data: 'projection', name: 'projection' }, 
            { data: 'confirmed', name: 'confirmed' }, 
            { data: 'balance', name: 'balance' }, 
            // { data: 'status', name: 'status' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],

        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 120px; height:25px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });
            api.columns(selectable).every( function (i, x) {
                var column = this;

                var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        
                        column.search(val ? '^'+val+'$' : '', true, false ).draw();
                        column.search(val ? val.toUpperCase().replace("'S","").replace( /&/g, '&amp;' ): '', true, false ).draw();
                        e.stopPropagation();
                    });

                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                });
            // }, 1000);
            });
        }
   }); 

}); 
</script>
@endpush
@endsection
