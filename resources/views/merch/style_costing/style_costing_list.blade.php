@extends('merch.layout')
@section('title', 'Style Costing')
@section('main-content')
@push('css')
    <style>
        #dataTables thead input, #dataTables thead select {max-width: unset !important;}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}

    /*making place holder custom*/
    input::-webkit-input-placeholder {
        color: #827979;
        font-weight: bold;
        font-size: 12px;
    }
    input:-moz-placeholder {
        color: #827979;
        font-weight: bold;
        font-size: 12px;
    }
    input:-ms-input-placeholder {
        color: #827979;
        font-weight: bold;
        font-size: 12px;
    }
    th{
        font-size: 12px;
        font-weight: bold;
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
                  <a href="#">Style</a>
              </li>
              <li class="active">Style Costing List</li>
              <li class="top-nav-btn">
                <a href="{{ url('merch/style/style_list')}}" target="_blank" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-list"></i> Style List</a> &nbsp;
                <a href="{{ url('merch/style_bom')}}" target="_blank" class="btn btn-outline-success btn-sm pull-right"> <i class="fa fa-list"></i> Style BOM</a> &nbsp;
                
                </li>
          </ul><!-- /.breadcrumb -->

        </div>

		<div class="page-content">
            <div class="panel panel-warning">
                <div class="panel-body">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')

                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="8%">SL</th>
                                        <th width="10%">Production Type</th>
                                        <th width="20%">Style Reference 1</th>
                                        <th width="15%">Buyer</th>
                                        <th width="10%">Brand</th>
                                        <th width="20%">Style Reference 2</th>
                                        <th width="10%">SMV/PC</th>
                                        <th width="8%">Season</th>
                                        <th width="7%">Status</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
<!-- Modal -->
<div class="modal fade" id="action_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Style Costing</h2>
            </div>
            <div class="modal-body">
                <div class="delete_msg">
                    <h4 class="text-center">{{ Session::get('lavelhierarchy') }}</h4>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-offset-3 col-md-9">
                    <button class="btn btn-info" type="button" id="modal_data" data-dismiss="modal">
                        <i class="ace-icon fa fa-check bigger-110" ></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal End -->
<script type="text/javascript">
$(document).ready(function(){
    // if lavel hierachy data not exit
    @if(Session::has('lavelhierarchy'))
        $('#action_modal').modal('show');
    @endif

    var searchable = [2,4,6];
    var selectable = [1,3,7];

    var dropdownList = {
        '1' :['Development', 'Bulk'],
        '3' :[@foreach($buyerList as $e) <?php echo "\"$e\"," ?> @endforeach],
        '7' :[@foreach($seasonList as $e) <?php echo "\"$e\"," ?> @endforeach]
    };
    var exportColName = ['Production Type','Style Reference 1','Buyer','Brand', 'Style Reference 2', 'SMV/pc','Season', 'Status'];
        
    var exportCol = [1,2,3,4,5,6,7,8];
    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp",
        ajax: {
            url: '{!! url("merch/style_costing/style_costing_data") !!}',
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
			{data: 'stl_status', name: 'stl_status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],

        initComplete: function () {
            var api =  this.api();

            // Apply the search
            api.columns(searchable).every(function () {
                var column = this;
                var input = document.createElement("input");
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });

            // each column select list
            api.columns(selectable).every( function (i, x) {
                var column = this;

                var select = $('<select style="width: 110px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val.toUpperCase().replace("'S","").replace( /&/g, '&amp;' ): '', true, false ).draw();
                        e.stopPropagation();
                    });

                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                });
            });
        }
    });
});
</script>
@endsection
