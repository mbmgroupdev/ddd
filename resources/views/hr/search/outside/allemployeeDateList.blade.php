
<div class="panel panel-info col-sm-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all"> MBM Group </a>
            </li>
            <li>Date</li>
            <li>{{$request1['date']}}</li>
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
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Count</th>
                                <th>Purpose</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($dataList))
                                @foreach($dataList as $k=>$data)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$data['as_id']}}</td>
                                        <td>{{$data['basic']['as_name']}}</td>
                                        <td>{{Custom::getUnitName($data['requested_location'])}}</td>
                                        <td>{{$data['start_date']}}</td>
                                        <td>{{$data['end_date']}}</td>
                                        <td>{{\Carbon\Carbon::parse($data['end_date'])->diffInDays($data['start_date'])+1}}</td>
                                        <td>{{$data['requested_place']}}</td>
                                        <td>{{$data['comment']}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){ 
    var dTable =  $('#dataTables').DataTable({
        scroller: {
            loadingIndicator: false
        },
        pagingType: "full_numbers",
        dom: 'lBfrtip',
        buttons: [
          {
              extend: 'copy',
              className: 'btn-sm btn-info text-center',
              exportOptions: {
                columns: ':visible'
              }
          },
          {
              extend: 'csv',
              className: 'btn-sm btn-success',
              exportOptions: {
                columns: ':visible'
              }
          },
          {
              extend: 'excel',
              className: 'btn-sm btn-warning',
              exportOptions: {
                columns: ':visible'
              }
          },
          {
              extend: 'pdf',
              className: 'btn-sm btn-primary',
              exportOptions: {
                columns: ':visible'
              }
          },
          {
              extend: 'print',
              className: 'btn-sm btn-default print',
              title: '',
              orientation: 'landscape',
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
                    '<h4 class="text-center">Report (Day Wise)</h4>'+
                    '<h4 class="text-center">{{$showTitle}}</h4>'
                    ;
          },
          messageBottom: null,
              exportOptions: {
                columns: ':visible',
                stripHtml: false
              },
            }
        ],
    });
});
</script>