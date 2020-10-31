@extends('hr.layout')
@section('title', 'Salary Adjustment List')

@section('main-content')
@push('css')
<style type="text/css">
  tr td:nth-child(2){
    display: block;
      width: 70px !important;
  }
  tr th:nth-child(3) input{
    width: 80px !important;
  }
  tr th:nth-child(6) input{
    width: 70px !important;
  }
  tr th:nth-child(7) input{
    width: 80px !important;
  }
  tr th:nth-child(8) select{
    width: 80px !important;
  }
  tr th:nth-child(9) select{
    width: 100px !important;
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
                    <a href="#">Payroll</a>
                </li>
                <li class="active"> Salary Adjustment List</li>
            </ul>
        </div>

        <div class="page-content"> 
            <input type="hidden" id="month_year" value="{{ $input['month_year']??date('Y-m')}}">
            <div class="row">
                <div class="col">
                  <div class="iq-card">
                    <div class="iq-card-header d-flex mb-0">
                     <div class="iq-header-title w-100">
                        <div class="row">
                          <div class="col-3">
                              <div class="action-section">
                                  
                              </div>
                          </div>
                          <div class="col-6 text-center">
                            <h4 class="card-title capitalize inline">
                              @php
                                  $associate = request()->associate;
                                  $nextMonth = date('Y-m', strtotime($input['month_year'].' +1 month'));
                                  $prevMonth = date('Y-m', strtotime($input['month_year'].' -1 month'));

                                  $prevUrl = url("hr/payroll/monthly-salary-adjustment-list?month_year=$prevMonth");
                                  $nextUrl = url("hr/payroll/monthly-salary-adjustment-list?month_year=$nextMonth");
                                  $month = date('Y-m');
                              @endphp
                              <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month Report" >
                                <i class="las la-chevron-left"></i>
                              </a>

                              <b class="f-16" id="result-head">{{ date('M Y', strtotime($input['month_year'])) }} </b>
                              
                              <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Month Report" >
                                <i class="las la-chevron-right"></i>
                              </a>
                              
                            </h4>
                          </div>
                          @php 
                              $yearMonth = $input['month_year']; 
                          @endphp
                          <div class="col-3">
                            <a href='{{ url("hr/payroll/monthly-salary-adjustment?month_year=$yearMonth")}}' class="btn btn-sm btn-success view next_btn pull-right" data-toggle="tooltip" data-placement="top" title="" data-original-title="Salary Adjustment" >
                                <i class="las la-chevron-right"></i> Salary Adjust
                              </a>
                          </div>
                        </div>
                     </div>
                  </div>
                  </div>
                  <div class="table d-table">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head w-100" >
                             <thead>
                                <tr>
                                  <th>Sl.</th>
                                  <th>Picture</th>
                                  <th>Unit</th>
                                  <th>Location</th>
                                  <th>Associate ID</th>
                                  <th>Name & Contact</th>
                                  <th>Designation</th>
                                  <th>Department</th>
                                  <th>Advance Deduct</th>
                                  <th>Cg Deduct</th>
                                  <th>Food Deduct</th>
                                  <th>Other Deduct</th>
                                  <th>Salary Add</th>
                                  <th>&nbsp;</th>
                                </tr>
                             </thead>
                          </table>
                       </div>
                     </div>
                   </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function(){   
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';

    var searchable = [4,5,6,7];
    var selectable = [2, 3]; 

    var dropdownList = {
      '2' :[@foreach($unitList as $unit) <?php echo "'$unit'," ?> @endforeach],
      '3' :[@foreach($locationList as $loc) <?php echo "'$loc'," ?> @endforeach],
    };
    var printCounter = 0;
    var dTable =  $('#dataTables').DataTable({

      order: [[ 10, "asc" ]], //reset auto order
      lengthMenu: [[25, 50, 100, -1], [25, 50, 100,"All"]],
      processing: true,
      responsive: false,
      serverSide: true,
      cache: false,
      language: {
        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
      },
      scroller: {
        loadingIndicator: false
      },
      pagingType: "full_numbers",
      ajax: {
        url: '{!! url("hr/payroll/monthly-salary-adjustment-data") !!}',
        data: function (d) {
          
          d.month_year = $("#month_year").val()

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
            '<h5 class="text-center">(From '+$("#report_from").val()+' '+'To'+' '+$("#report_to").val()+') </h5>'+
            '<h5 class="text-center">'+'Total: '+dTable.data().length+'</h5>'+
            '<h6 style = "margin-left:80%;">'+'Printed on: '+new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate()+'</h6><br>'
            ;

          },
          messageBottom: null,
          exportOptions: {
            columns: [0,1,3,4,5,6,7,8],
            stripHtml: false
          },
        }
      ],

      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'pic', name: 'pic' },
        { data: 'hr_unit_name',  name: 'hr_unit_name' },
        { data: 'hr_location_name',  name: 'hr_location_name' },
        { data: 'associate_id',  name: 'associate_id' },
        { data: 'as_name', name: 'as_name' },
        { data: 'hr_designation_name', name: 'hr_designation_name' },
        { data: 'hr_department_name', name: 'hr_department_name' },
        { data: 'advp_deduct', name: 'advp_deduct' },
        { data: 'cg_deduct', name: 'cg_deduct' },
        { data: 'food_deduct', name: 'food_deduct' },
        { data: 'others_deduct', name: 'others_deduct' },
        { data: 'salary_add', name: 'salary_add' },
        { data: 'action', name: 'action' },
        
      ],
      initComplete: function () {
        var api =  this.api();

        // Apply the search
        api.columns(searchable).every(function () {
          var column = this;
          var input = document.createElement("input");
          input.setAttribute('placeholder', $(column.header()).text());
          input.setAttribute('style', 'width: 80px; height:32px; border:1px solid whitesmoke; color: black;');

          $(input).appendTo($(column.header()).empty())
          .on('keyup', function (e) {
            if(e.keyCode == 13){
              column.search($(this).val(), false, false, true).draw();
            }
          });

          $('input', this.column(column).header()).on('click', function(e) {
            e.stopPropagation();
          });
        });
        api.columns(selectable).every( function (i, x) {
            var column = this;

            var select = $('<select style="width: 70px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                .appendTo($(column.header()).empty())
                .on('change', function(e){
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    column.search(val ? '^'+val+'$' : '', true, false ).draw();
                    e.stopPropagation();
                });

          // column.data().unique().sort().each( function ( d, j ) {
          // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
          // });
          // setTimeout(function(){ 

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