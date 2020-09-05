@extends('hr.layout')
@section('title', 'Holiday Planner List')
@section('main-content')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.css')}}">
    <style>
        .radio-inline{
            margin:0 10px;
            padding:0 10px;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li> 
                <li>
                    <a href="#">Operation</a>
                </li> 
                <li class="active">Yearly Holiday List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Yearly Holiday List<a href="{{ url('hr/operation/yearly-holidays/create')}}" class="pull-right btn btn-sm btn-success"><i class="fa fa-plus"></i> Yearly Holiday Planner</a></h6></div> 
                  <div class="panel-body">

                    <div class="row">
                          <!-- Display Erro/Success Message -->
                        <div class="col">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class=" iq-card-body">
                                <div class="output"></div>
                                <!-- PAGE CONTENT BEGINS --> 
                                <table id="dataTables" class="table table-striped table-bordered table-head w-100" style="">
                                    <thead>
                                        <tr>
                                            <th width="8%">Sl. No</th>
                                            <th width="30%">Unit</th>
                                            <th width="12%">Date</th>
                                            <th width="20%">Comment</th>
                                            <th width="30%">Open Status</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead> 
                                </table>
                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->

                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Date</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="yhp_id" id="yhp_id" value=""/>
        <div class="form-group" style="padding-bottom: 20px;">
            <div class="col-sm-3"></div>
            <label class="col-sm-2 control-label no-padding-right">Date</label>
            <div class="col-sm-6">
                <input type="text" id="yhp_date"  name="yhp_date" class="datepicker" placeholder="Y-m-d" data-validation="required" />
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="date_save" class="btn btn-xs btn-primary">Save changes</button>
        <button type="button" id="date_delete" class="btn btn-xs btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>
@push('js')
<script type="text/javascript">
    $(document).ready(function(){ 
        var selectable = [1];
        var dropdownList = {
            '1':[@foreach($unit as $u) <?php echo "'$u'," ?> @endforeach],
        };
        $('#dataTables').DataTable({ 
            order: [], //reset auto order
            processing: true,
            responsive: true,
            serverSide: true,
            pagingType: "full_numbers",
            dom: 'lBfrtip',
            buttons: [
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
                orientation: 'portrait',
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
                  '<h2 class="text-center">Consecutive ' +$("#type option:selected").text()+' Report</h2>'+
                  '<h3 class="text-center">'+'Unit: '+$("#unit option:selected").text()+'</h3>'+
                  '<h5 class="text-center">'+'Total: '+dTable.data().length+'</h5>'+
                  '<h6 style = "margin-left:80%;">'+'Printed on: '+new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate()+'</h6><br>'
                  ;

                },
                messageBottom: null,
                exportOptions: {
                  columns: [0,1,3,4,5,6,7,8,9],
                  stripHtml: false
                },
              }
            ], 
            ajax: {
                url: '{!! url("hr/timeattendance/operation/yearly_holidays/data") !!}',
                type: "POST",
                headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                } 
            },
            columns: [  
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'hr_unit_name', name: 'hr_unit_name' }, 
                { data: 'date',  name: 'date' },
                { data: 'hr_yhp_comments', name: 'hr_yhp_comments' },  
                { data: 'open_status', name: 'open_status' },  
                // { data: 'action', name: 'action', orderable: false, searchable: false }
            ], 
            
            initComplete: function () {   
                var api =  this.api();

                // each column select list
                api.columns(selectable).every( function (i, x) {
                    var column = this; 

                    var select = $('<select  style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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


        // holiday open status
        $("body").on("click", ".open_status", function(){ 
            $.ajax({
                url: "{{ url('hr/timeattendance/operation/yearly_holidays/open_status') }}",
                data: {
                    id: $(this).data("id"),
                    status: $(this).val()
                },
                success: function(data) {
                    $(".output").html(data); 
                },
                error: function(xhr)
                {
                    alert("Please wait...");
                }
            });
        });

        // $("body").on("click", ".date_edit", function(){ 
        //     $('#yhp_id').val($(this).val());
        //     $('#myModal').modal('show');
        //     $.ajax({
        //         url: "{{ url('hr/timeattendance/operation/yearly_holidays/modal_data') }}",
        //         data: {
        //             id: $(this).val()
        //         },
        //         success: function(data) {
        //             $("#yhp_date").val(data); 
        //         },
        //         error: function(xhr)
        //         {
        //             alert("Data empty...");
        //         }
        //     });
        // });

        $("body").on("click", "#date_save", function(){ 
            $.ajax({
                url: "{{ url('hr/timeattendance/operation/yearly_holidays/modal_save') }}",
                data: {
                    id: $('#yhp_id').val(),
                    date: $('#yhp_date').val()
                },
                success: function(data) {
                    $('#myModal').modal('hide');
                    $(".output").html(data);
                    window.location.reload();
                },
                error: function(xhr)
                {
                    alert("Please Try Again...");
                }
            });
        });

        $("body").on("click", "#date_delete", function(){ 
            
            $.ajax({
                url: "{{ url('hr/timeattendance/operation/yearly_holidays/modal_delete') }}",
                data: {
                    id: $('#yhp_id').val(),
                    date: $('#yhp_date').val()
                },
                success: function(data) {
                    $('#myModal').modal('hide');
                    $(".output").html(data);
                    window.location.reload();
                },
                error: function(xhr)
                {
                    //console.log(xhr);
                    alert("Please Try Again...");
                }
            });
        });

        // $( ".datepicker" ).datepicker({ 
        // stepMonths: 0,
        // });

        $("#dataTables tbody").on("click", ".holiday_date", function(){ 
            $('#yhp_id').val($(this).data('id'));
            $('#myModal').modal('show');
            $.ajax({
                url: "{{ url('hr/timeattendance/operation/yearly_holidays/modal_data') }}",
                data: {
                    id: $(this).data('id')
                },
                success: function(data) {
                    $("#yhp_date").val(data); 
                },
                error: function(xhr)
                {
                    alert("Data empty...");
                }
            });
        });


    });
</script>
@endpush
@endsection