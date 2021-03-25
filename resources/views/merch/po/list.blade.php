@extends('merch.layout')
@section('title', 'Order PO LIST')
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
      width: 100px !important;
    }
    #example th:nth-child(3) select{
      width: 120px !important;
    } 
    #example th:nth-child(4) select{
      width: 80px !important;
    }
    #example th:nth-child(5) input{
      width: 80px !important;
    }
    #example th:nth-child(6) input{
      width: 80px !important;
    }
    #example th:nth-child(7) input{
      width: 80px !important;
    }
    #example th:nth-child(8) input{
      width: 80px !important;
    }
    #example th:nth-child(11) input{
      width: 70px !important;
    }
    
    .text-warning {
        color: #c49090!important;
    }
    table.dataTable thead>tr>td.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc {
        padding-right: 16px;
    }
</style>
@endpush
@php
  $unitList = unit_by_id();
  $unitList = collect($unitList)->pluck('hr_unit_name', 'hr_unit_id');
  $buyerList  = buyer_by_id();
  $buyerList = collect($buyerList)->pluck('b_name', 'b_id');
@endphp
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
              <li class="active">PO List</li>
              <li class="top-nav-btn">
                <a class="btn btn-sm btn-primary add-new text-white" href="{{ url('merch/po/create') }}"><i class="las la-plus"></i> Create PO</a>
              </li>
          </ul><!-- /.breadcrumb -->

      </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <table id="example" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
                            <thead>
                                <tr class="success">
                                    <th width="5%">SL.</th>
                                    <th width="10%">MBM Order No</th>
                                    <th width="10%">Unit</th>
                                    <th width="10%">Buyer</th>
                                    <th width="10%">Style No</th>
                                    <th width="10%">PO No</th>
                                    <th width="10%">Color</th>
                                    <th width="10%">Country</th>
                                    <th width="10%">Qty.</th>
                                    <th width="10%">Value</th>
                                    <th width="10%">Ex-fty</th>
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

@push('js')

<script type="text/javascript">

$(document).on('click', '.add-new', function() {
    type = $(this).data('type');
    $('#right_modal_item').modal('show');
    $('#modal-title-right').html(' Add New '+type);
    $("#content-result").html(loaderContent);
    var url = '';
    if(type === 'po create'){
      url = '/merch/po/create';
    }
    $.ajax({
        type: "GET",
        url: "{{ url('/')}}"+url,
        success: function(response)
        {
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
$(document).ready(function(){ 
    
    var searchable = [1,4,5, 6, 7,10];
    var selectable = [2,3];
    var exportColName = ['Order No','Unit','Buyer', 'Style No','PO Number', 'Color', 'Country', 'Quantity', 'Ex-fty Date'];
    var dropdownList = {
        '2' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '3' :[@foreach($buyerList as $e) <?php echo "'$e'," ?> @endforeach],
    };
    var exportCol = [0,1,2,3,4,5,6,7];
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
           url: '{!! url("merch/po-list") !!}',
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
            { data: 'order_code', name: 'order_code' },
            { data: 'hr_unit_name', name: 'hr_unit_name' },
            { data: 'b_name',  name: 'b_name' },
            { data: 'stl_no', name: 'stl_no' },
            { data: 'po_no', name: 'po_no' },
            { data: 'po_color', name: 'po_color' },
            { data: 'po_country', name: 'po_country' },
            { data: 'po_qty', name: 'po_qty' },
            { data: 'country_fob', name: 'country_fob' },
            { data: 'po_ex_fty', name: 'po_ex_fty' },
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

                var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;" class="filter"><option value="">'+$(column.header()).text()+'</option></select>')
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
