<style>
    .dataTables_wrapper .dataTables_processing {
        position: absolute;
        top: 30%;
        left: 50%;
        width: 30%;
        height: 80px;
        margin-left: -20%;
        margin-top: -25px;
        padding-top: 20px;
        text-align: center;
        font-size: 1.7em;
        background-color:White;
    }
    .dataTables_wrapper .dt-buttons {
        float:right;
        text-align:center;
    }
    .dataTables_length{
        float:left;
    }
</style>
<div class="panel panel-info col-sm-12">
    <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all"> MBM Group </a>
            </li>
            <li>Status</li>
            @php
                $reportStatus = '';
            @endphp
            @if($status == 'join')
                @php
                    $reportStatus = 'Join';
                @endphp
                <li>Employee Join</li>
            @else
                @php
                    $reportStatus = ucfirst(emp_status_name($status));
                @endphp
                <li>{{ucfirst(emp_status_name($status))}}</li>
            @endif
            <li class="active"> Employee List</li>
        </ul><!-- /.breadcrumb -->
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
        	<div class="col-sm-12">
                <div class="table-responsive">
                    <table id="dataTables" class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Date of {{$status == 'join'?'Join':ucfirst(emp_status_name($status))}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){ 
        var searchable = [];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {};
        var exportColName = ['Sl.','Associate ID','Name','Date of Join'];
        var exportCol = [0,1,2,3];
        var dt =  $('#dataTables').DataTable({
           order: [], //reset auto order
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            ajax: {
                url: '{!! url("hr/search/hr_empstatus_search_emp_data") !!}',
                data: function (d) {
                    d.data  = @json($request1),
                    d.status  = '{{$status}}'
                },
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            dom: 'lBfrtip',
            buttons: [
              {
                  extend: 'csv',
                  className: 'btn-sm btn-success',
                  "action": allExport,
                  exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  }
              },
              {
                  extend: 'excel',
                  className: 'btn-sm btn-warning',
                  "action": allExport,
                  exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  }
              },
              {
                  extend: 'pdf',
                  className: 'btn-sm btn-primary',
                  "action": allExport,
                  exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  }
              },
              {
                  extend: 'print',
                  className: 'btn-sm btn-default print',
                  title: '',
                  orientation: 'landscape',
                  "action": allExport,
                  pageSize: 'LEGAL',
                  alignment: "center",
                  // header:true,
                  messageTop: function () {
                  //printCounter++;
                      return '<style>'+
                        'input::-webkit-input-placeholder {'+
                        'color: black;'+
                        'font-weight: bold;'+
                        'font-size: 12px;'+
                        '}'+
                        'input:-moz-placeholder {'+
                        'color: black;'+
                        'font-weight: bold;'+
                        'font-size: 12px;'+
                        '}'+
                        'input:-ms-input-placeholder {'+
                        'color: black;'+
                        'font-weight: bold;'+
                        'font-size: 12px;'+
                        '}'+
                        'th{'+
                        'font-size: 12px !important;'+
                        'color: black !important;'+
                        'font-weight: bold !important;'+
                        '}</style>'+
                        '<h2 class="text-center">MBM Garments Ltd.</h2>'+
                        '<h2 class="text-center">{{$reportStatus}} Report</h2>'+
                        '<h4 class="text-center">{{$showTitle}}</h4>'
                        ;
              },
              messageBottom: null,
                  exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      },
                    stripHtml: false
                  },
                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'as_id', name: 'as_id' },
                { data: 'as_name', name: 'as_name' },
                { data: 'date', name: 'date' }
            ],
            initComplete: function () {
                var api =  this.api();

                // Apply the search
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());

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

                    var select = $('<select><option value="">'+$(column.header()).text()+'</option></select>')
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