@extends('hr.layout')
@section('title', 'Disciplinary Record List')
@section('main-content')
@push('css')
<style type="text/css">
    a[href]:after { content: none !important; }
    thead {display: table-header-group;} 
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class=" fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Performance </a>
                </li>
                <li class="active"> Disciplinary Record List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Disciplinary List<a href="{{ url('hr/performance/operation/disciplinary_form')}}" class="pull-right btn btn-primary">Disciplinary Form</a></h6></div> 
                <div class="panel-body">

                    
                    <!-- Display Erro/Success Message -->
                    <table id="dataTables" class="table table-striped table-bordered" style="display:table;overflow-x: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>SL. No</th>
                                <th>Offender ID</th>
                                <th>Griever ID</th>
                                <th>Reason</th>
                                <th>Action</th>
                                <th>Requested Remedy</th>
                                <th>Discussed Date</th>
                                <th>Date of Execution</th> 
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>

                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div> 


@push('js')
<script type="text/javascript">
$(document).ready(function(){  
    var exportColName = ['SL.','Offender ID','Greiver ID','Reason','Action', 'Requested Remedy', 'Discussed Date','Date of Execution'];
    var exportCol = [0,1,2,3,4,5,6,7];

    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true, 
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        dom: "lBftrip", 
        buttons: [   
              {
                  extend: 'csv', 
                  className: 'btn btn-sm btn-success',
                  title: 'Disciplinary Record List',
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
                  title: 'Disciplinary Record List',
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
                  title: 'Disciplinary Record List',
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
                      var unit = '';
                      if($('#unit').val() != null){
                         unit = $('#unit').select2('data')[0].text; 
                      }
                      return customReportHeader('Disciplinary Record List', { });
                    }
              } 
          ],
        ajax: '{!! url("hr/performance/operation/disciplinary_data") !!}',
        columns: [  
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'offender',  name: 'offender' }, 
            { data: 'griever',  name: 'griever' }, 
            { data: 'issue',  name: 'issue' }, 
            { data: 'step', name: 'step' }, 
            { data: 'dis_re_req_remedy', name: 'dis_re_req_remedy' }, 
            { data: 'discussed_date', name: 'discussed_date' }, 
            { data: 'date_of_execution', name: 'date_of_execution' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endpush
@endsection