@extends('merch.layout')
@section('title', 'Order List')
@section('main-content')
@push('css')
  <style>
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}
    th{
        font-size: 12px;
        font-weight: bold;
    }
    #example th:nth-child(2) input{
      width: 90px !important;
    }
    #example th:nth-child(3) input{
      width: 70px !important;
    } 
    #example th:nth-child(4) select{
      width: 100px !important;
    }
    #example th:nth-child(5) select{
      width: 60px !important;
    }
    #example th:nth-child(6) input{
      width: 60px !important;
    }
    #example th:nth-child(7) input{
      width: 70px !important;
    }
    #example th:nth-child(8) input{
      width: 100px !important;
    }
    #example th:nth-child(9) input{
      width: 60px !important;
    }
    #example th:nth-child(10) input{
      width: 60px !important;
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
                  <a href="#">Order</a>
              </li>
              <li class="active">Order List</li>
              <li class="top-nav-btn">
                <a class="btn btn-sm btn-primary text-white" href="{{ url('merch/orders/create') }}"><i class="las la-plus"></i> Create Order</a>
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
                                    <th width="10%">Internal Order No</th>
                                    <th width="10%">Order Ref. No</th>
                                    <th width="10%">Unit</th>
                                    <th width="10%">Buyer</th>
                                    <th width="10%">Brand</th>
                                    <th width="10%">Season</th>
                                    <th width="10%">Style No</th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Delivery Date</th>
                                    <th width="10%">FOB</th>
                                    <th width="8%">Action</th>
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
$(document).on('click', '.add-new', function() {
    type = $(this).data('type');
    $('#right_modal_item').modal('show');
    $('#modal-title-right').html(type);
    $("#content-result").html(loaderContent);
    var url = '';
    var id = $(this).data('orderid');
    if(type === 'Order Edit'){
      url = '/merch/orders/'+id+'/edit';
    }else if(type === 'Order View'){
      url = '/merch/orders/'+id;
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
  var pageType = $("#page-type").val();
  if(pageType === 'order-update'){
    var orderId = $("#order-id").val();
    var resQty = $("#res-quantity").val();
    var poQty = $("#po-qty").val();
    var orderQty = $("#order_qty").val();
    if(parseFloat(poQty) > parseFloat(orderQty)){
      $("#order_qty").notify('Total PO Quantity '+poQty, 'error');
      $("#app-loader").hide();
      return false;
    }
    if(parseFloat(orderQty) > parseFloat(resQty)){
      $("#order_qty").notify('Reservation balance '+resQty, 'error');
      $("#app-loader").hide();
      return false;
    }
    var url = '/merch/orders/'+orderId;
  }
  
  if (isValid){
     $.ajax({
        type: verb,
        url: url,
        data: curInputs.serialize(), // serializes the form's elements.
        success: function(response)
        {
          $("#app-loader").hide();
          // console.log(response)
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

$(document).ready(function(){ 
    var searchable = [1,2,5,6,7,8,9];
    var selectable = [3,4,];
    var exportColName = ['Order No','Order Reference No','Unit','Buyer', 'Brand', 'Season','Style No','Order Qty.', 'Delivery Date', 'FOB'];
    var dropdownList = {
        '3' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($buyerList as $e) <?php echo "'$e'," ?> @endforeach],
        {{-- '5' :[@foreach($brandList as $e) <?php echo "'$e'," ?> @endforeach], --}}
        {{-- '5' :[@foreach($seasonList as $e) <?php echo "'$e'," ?> @endforeach], --}}
        
    };
    var exportCol = [0,1,2,3,4,5,6,7,8];
    var dt = $('#example').DataTable({
        order: [], //reset auto order
        processing: true,
        language: {
          processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
        },
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers", 
        ajax: {
           url: '{!! url("merch/orders-list-data") !!}',
           type: "POST",
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
            { data: 'order_code', name: 'order_code' },
            { data: 'order_ref_no', name: 'order_ref_no' },
            { data: 'hr_unit_name', name: 'hr_unit_name' },
            { data: 'b_name',  name: 'b_name' },
            { data: 'br_name', name: 'br_name' },
            { data: 'se_name', name: 'se_name' },
            { data: 'stl_no', name: 'stl_no' },
            { data: 'order_qty', name: 'order_qty' },
            { data: 'order_delivery_date', name: 'order_delivery_date' },
            { data: 'fob', name: 'fob' },
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
@if(!empty(Request::get('view')))
var orderId = "{{ Request::get('view') }}";
setTimeout(function(){
  $("#order-view-"+orderId+".add-new").click();
}, 1000)
@endif
</script>
@endpush
@endsection
