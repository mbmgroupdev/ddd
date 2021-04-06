@extends('merch.layout')
@section('title', 'Style List')
@section('main-content')
@push('css')
  <style>
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}
    th{
        font-size: 12px;
        font-weight: bold;
    }
    #example th:nth-child(3) input{
      width: 65px !important;
    }
    #example th:nth-child(5) input{
      width: 85px !important;
    }
    #example th:nth-child(4) input{
      width: 140px !important;
    } 
    #example th:nth-child(6) select{
      width: 80px !important;
    }
    #example th:nth-child(7) input{
      width: 80px !important;
    }
    #example th:nth-child(8) input{
      width: 120px !important;
    }
    #example th:nth-child(9) input{
      width: 60px !important;
    }
    #example th:nth-child(10){
      width: 60px !important;
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
  <div class="main-content">
      <div class="main-content-inner">
          <div class="col-sm-12">
              <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                      <li>
                          <i class="ace-icon fa fa-home home-icon"></i>
                          <a href="#">Merchandising</a>
                      </li>
                      <li>
                          <a href="#">Style</a>
                      </li>
                      <li class="active">Style List</li>
                      <li class="top-nav-btn">
                        <a class="btn btn-sm btn-primary" href="{{ url('merch/style/create') }}"><i class="las la-plus"></i> New Style</a>
                        </li>
                  </ul><!-- /.breadcrumb -->
       
              </div>
              <div class="panel">

                <div class="panel-body">
                  <div class="style_section">
                    <div class="col-xs-12 table-responsive worker-list">
                        <table id="example" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    {{-- <th>Style Type</th> --}}
                                    <th>Product Type</th>
                                    <th>Style Reference 1</th>
                                    <th>Season</th>
                                    <th>Buyer</th>
                                    <th>Brand</th>
                                    <th>Style Reference 2</th>
                                    <th>Sewing</th>
                                    <th>FOB</th>
                                    <th>Action</th>
                            </thead>

                        </table>
                    </div><!-- /.col -->
                  </div>
                </div>
              </div>
          </div>
      </div>
  </div>

{{-- @include('merch.common.list-page-freeze') --}}
@push('js')
<script type="text/javascript">

$(document).ready(function(){ 
    var searchable = [2,3,4,6,7,8];
    var selectable = [5];
    var exportColName = ['Image','Product Type','Style Reference 1', 'Season', 'Buyer','Brand','Style Reference 2'];
    var dropdownList = {

        '5' :[@foreach($buyerList as $e) <?php echo "\"$e\"," ?> @endforeach],
        //'5' :[@foreach($seasonList as $e) <?php echo "\"$e\"," ?> @endforeach]
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
               url: '{!! url("merch/style/style_list_data") !!}',
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
                  title: 'Style list',
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
                  title: 'Style list',
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
                  title: 'Style list',
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
                  messageTop: customReportHeader('Style list', { })
              } 
          ],
          columns: [  
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                {data: 'stl_img_link', name: 'stl_img_link'},
                // {data: 'stl_type',  name: 'stl_type'},
                {data: 'prd_type_name',  name: 'prd_type_name'},
                {data: 'stl_no', name: 'stl_no'},
                {data: 'se_name', name: 'se_name'},
                {data: 'b_name', name: 'b_name'},
                {data: 'br_name', name: 'br_name'},
                {data: 'stl_product_name',  name: 'stl_product_name'},
                {data: 'stl_smv', name: 'stl_smv'},
                {data: 'agent_fob', name: 'agent_fob'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
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


