@extends('hr.layout')
@section('title', 'End of Job Benefits List')
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
                    <a href="#">Payroll</a>
                </li>
                <li class="active">End of Job Benefit List</li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/payroll/benefits')}}" class="btn btn-sm btn-primary  pull-right" >End of Job Benefit</a>
                </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        @include('inc/message')
        <div class="page-content"> 
            <div class="panel panel-info">
                <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered table-responsive" style="display:block;overflow-x: scroll;width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Earn leave Amount</th>
                                <th>Service Benefits</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    
                    </table> 
                
                </div>
            </div>

        </div>
    </div>
</div>
@push('js')
<script type="text/javascript" src="{{asset('assets/js/sweetalert2.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){ 
    var searchable = [1,2];
    var selectable = [3]; 
    var dropdownList = {
         '3' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach]
    };

    var exportColName = ['Sl.','Associate ID','Name','Unit','Type','Date','Earn Leave Amount','Service Benefits', 'Total Amount'];
    var exportCol = [1,2,3,4,5,6,7,89];

    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        // responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        language: {
            processing: '<i class="fa fa-spinner fa-spin f-60 fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        ajax: {
            url: '{!! url("hr/payroll/get_given_benefit_data_list") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "lBfrtip", 
        buttons: [   
              {
                  extend: 'csv', 
                  className: 'btn btn-sm btn-success',
                  title: 'End of job benefit list',
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
                  title: 'End of job benefit list',
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
                  title: 'End of job benefit list',
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
                  messageTop: function () {
                      return customReportHeader('End of job benefit list', {});
                  },
                  customize: function(win)
                    {
         
                        var last = null;
                        var current = null;
                        var bod = [];
         
                        var css = '@page { size: landscape; }',
                            head = win.document.head || win.document.getElementsByTagName('head')[0],
                            style = win.document.createElement('style');
         
                        style.type = 'text/css';
                        style.media = 'print';
         
                        if (style.styleSheet)
                        {
                          style.styleSheet.cssText = css;
                        }
                        else
                        {
                          style.appendChild(win.document.createTextNode(css));
                        }
         
                        head.appendChild(style);
                 }
              } 
          ],
        columns: [ 

            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, 
            { data: 'associate_id', name: 'associate_id' }, 
            { data: 'as_name', name: 'as_name' }, 
            { data: 'unit_name',  name: 'unit_name' }, 
            { data: 'benefit_on',  name: 'benefit_on' }, 
            { data: 'status_date',  name: 'status_date' }, 
            { data: 'earn_leave_amount', name: 'earn_leave_amount' }, 
            { data: 'service_benefits', name: 'service_benefits' }, 
            { data: 'total_amount', name: 'total_amount' },
            { data: 'action', name: 'action' }
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; height:25px; border:1px solid whitesmoke;');

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

                var select = $('<select style="width: 110px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val : '', true, false ).draw();
                        e.stopPropagation();
                    });

                // column.data().unique().sort().each( function ( d, j ) {
                // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
                // });
                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                }); 
            });
        }   
    }); 


    $(document).on('click','.rollback', function(){
        var associate_id = $(this).data('associate');

        swal({
            title: associate_id,
            text: "Rollback! Are you sure?",
            icon: "warning",
            buttons: ['Cancel','Continue'],
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {

                $.ajax({
                    url: '{{ url('hr/payroll/benefit-rollback') }}',
                    type: "POST",
                    data: {
                        associate_id : associate_id,
                        _token : $("[name=csrf-token]").val()
                    },
                    success: function(response){
                        
                    },
                    error: function(reject)
                    {
                       console.log('k');
                    }
                });
                
            } else {
                swal("Your imaginary file is safe!");
            }
        })
        .then(results => {
        })
        .catch(err => {
            if (err) {
                console.log(err);
                swal("Oh noes!", "The AJAX request failed!", "error");
            } else {
                swal.stopLoading();
                swal.close();
            }
        });

    });
});
</script>
@endpush
@endsection
