@extends('hr.layout')
@section('title', 'Cost Mapping List')
@section('main-content')

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Employee</a>
                </li>
                <li class="active"> Cost Distribution(Mapping) List</li>
            </ul><!-- /.breadcrumb -->
        </div>
        @include('inc/message')
        <div class="panel panel-success">
          <div class="panel-heading">
            <h6>Cost Distribution (Mapping) List <a href="{{ url('hr/operation/cost-mapping')}}" class="pull-right btn btn-xx btn-info">Cost Distribution</a></h6></div> 
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-12 worker-list">
                        <!-- PAGE CONTENT BEGINS -->
                        <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20%">Sl</th>
                                    <th width="20%">Associate ID</th>
                                    <th width="20%">Associate Name</th>
                                    <th width="20%">Units</th>
                                    <th width="20%">Areas</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Sl</th>
                                    <th>Associate ID</th>
                                    <th>Associate Name</th>
                                    <th>Units</th>
                                    <th>Areas</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- PAGE CONTENT ENDS -->
                    </div>
                    <!-- /.col -->
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 
    var searchable = [1,2];
    var selectable = [3,4]; //use 4,5,6,7,8,9,10,11,....and * for all
    // dropdownList = {column_number: {'key':value}}; 
    var dropdownList = {
        '3' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($areaList as $e) <?php echo "'$e'," ?> @endforeach],
    };

    var exportColName = ['Sl.','Associate ID','Name','Unit','Area'];
        var exportCol = [0,1,2,3,4];

    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
            scroller: {
                loadingIndicator: false
            },
        pagingType: "full_numbers", 
        ajax: {
            url: '{!! url("hr/operation/cost_mapping_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "lBftrip", 
        buttons: [ 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Cost Distribution(Mapping) List',
                header: false,
                footer: true,
                "action" : allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Cost Distribution(Mapping) List',
                header: false,
                footer: true,
                "action" : allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Cost Distribution(Mapping) List', 
                header: false,
                footer: true,
                exportOptions: {
                    // columns: ':visible'
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Cost Distribution(Mapping) List',
                header: true,
                footer: false,
                "action" : allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      },
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'sl', name: 'sl' }, 
            { data: 'associate_id',  name: 'associate_id' }, 
            { data: 'as_name', name: 'as_name' }, 
            { data: 'units', name: 'units' }, 
            { data: 'areas', name: 'areas' },  
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

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

                var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val : '', true, false ).draw();
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
@endpush
@endsection
