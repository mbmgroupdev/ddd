@extends('hr.layout')
@section('title', 'Maternity Leave Application List')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="#">Maternity Leave</a>
                </li>
                <li class="active">Application List</li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/operation/maternity-leave')}}" target="_blank" class="btn btn-primary pull-right" >Application</i></a>
                </li>
            </ul>
        </div>

        @include('inc/message')
        <div class="panel panel-success mb-3">
            <div class="panel-body mb-3"> 
                    <table id="mat-leave" class="table table-striped table-bordered" style="display:table;overflow-x: auto;width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Applied Date</th>
                                <th>EDD</th>
                                <th>Status</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead> 
                        
                    </table>
                
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">

$(document).ready(function(){ 
       ///Filter
    var searchable = [1,2];
    var selectable = [];
    var orderable = [];
    var exportColName = ['Sl.','Associate ID','Name','Applied Date','EDD','Status'];
    var exportCol = [0,1,2,3,4,5];

    var dt = $('#mat-leave').DataTable({
        order: [], //reset auto order
        processing: true,
        language: {
          processing: '<i class="fa fa-spinner fa-spin f-60" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
        },
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/operation/maternity-leave/listData") !!}',
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
                  title: 'Maternity Leave List',
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
                  title: 'Maternity Leave List',
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
                  title: 'Maternity Leave List',
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
                      return customReportHeader('Maternity Leave Application List', {});
                    }
              } 
          ],
        columns: [ 
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, 
            { data: 'associate_id', name: 'associate_id' }, 
            { data: 'as_name',  name: 'as_name' }, 
            { data: 'applied_date',  name: 'applied_date' }, 

            { data: 'edd',  name: 'edd' }, 
            { data: 'status', name: 'status' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]  
         
    }); 
});
</script>
@endpush
@endsection