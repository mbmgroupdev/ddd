@extends('merch.layout')
@section('title', 'Style BOM')
@section('main-content')
@push('css')
  <style>
   #dataTables thead input, #dataTables thead select {max-width: unset !important;}
    a[href]:after { content: none !important; }
    
    thead {display: table-header-group;}
    th{
        font-size: 12px;
        font-weight: bold;
    }
    #dataTables th:nth-child(2) input{
      width: 70px !important;
    }
    #dataTables th:nth-child(3) input{
      width: 150px !important;
    } 
    #dataTables th:nth-child(4) select{
      width: 80px !important;
    }
    #dataTables th:nth-child(5) select{
      width: 60px !important;
    }
    #dataTables th:nth-child(6) select{
      width: 180px !important;
    }
    #dataTables th:nth-child(7) input{
      width: 80px !important;
    }
    #dataTables th:nth-child(8) input{
      width: 50px !important;
    }
</style>
@endpush
  <div class="main-content">
      <div class="main-content-inner">
          <div class="">
              <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                      <li>
                          <i class="ace-icon fa fa-home home-icon"></i>
                          <a href="#">Merchandising</a>
                      </li>
                      <li>
                          <a href="#">Style</a>
                      </li>
                      <li class="active">Style BOM List</li>
                      <li class="top-nav-btn">
                        <a href="{{ url('merch/style/style_list')}}" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Style List</a> &nbsp;
                        
                        </li>
                  </ul><!-- /.breadcrumb -->
       
              </div>
              <div class="panel">

                <div class="panel-body">
                  @include('inc/message')
                  <div class="style_section">
                    <div class="row">
                        <div class="col-xs-12 table-responsive worker-list">
                            <table id="dataTables" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
                                <thead>
                                    <tr>
                                        <th width="8%">SL</th>
                                        <th width="10%">Production Type</th>
                                        <th width="20%">Style Reference 1</th>
                                        <th width="15%">Buyer</th>
                                        <th width="10%">Brand</th>
                                        <th width="20%">Style Reference 2</th>
                                        <th width="10%">SMV/pc</th>
                                        <th width="15%">Season</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                  </div>
                </div>
              </div>
          </div>
      </div>
  </div>

@push('js')
<script type="text/javascript">

    $(document).ready(function(){ 
        var searchable = [2,4,6];
        var selectable = [1,3,7];

        var dropdownList = {
            '1' :['Development', 'Bulk'],
            '3' :[@foreach($buyerList as $e) <?php echo "\"$e\"," ?> @endforeach],
            '7' :[@foreach($seasonList as $e) <?php echo "\"$e\"," ?> @endforeach]
        };
        var exportColName = ['Production Type','Style Reference 1','Buyer','Brand', 'Style Reference 2', 'SMV/pc','Season'];
        
        var exportCol = [1,2,3,4,5,6,7];
        var dt = $('#dataTables').DataTable({
              order: [], //reset auto order
              processing: true,
              language: {
                  processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
              },
              responsive: true,
              serverSide: true,
              pagingType: "full_numbers", 
              ajax: {
                   url: '{!! url("merch/style_bom/style_bom_data") !!}',
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
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'stl_type',  name: 'stl_type'},
                    {data: 'stl_no', name: 'stl_no'},
                    {data: 'b_name', name: 'b_name'},
                    {data: 'br_name', name: 'br_name'},
                    {data: 'stl_product_name',  name: 'stl_product_name'},
                    {data: 'stl_smv', name: 'stl_smv'},
                    {data: 'se_name', name: 'se_name'},
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
