@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        #processStyle span#percentage {
            color: #fff;
            float: right;
            padding-right: 10px;
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Salary Sheet 2 View</li>
            </ul>
            <!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <?php $type='salary_sheet'; ?>
                @include('hr/reports/attendance_radio')

                <div class="page-header">
                    <h1>Reports<small><i class="ace-icon fa fa-angle-double-right"></i> Salary Sheet 2 View</small></h1>
                </div>
                <div class="row">
                    <div class="col-sm-10">
                        <?php dump($employee_list);?>
                        <p>{{ $start_date }}</p>
                        <input type="hidden" id="array_count" value="{{ count($employee_list) }}">
                    </div>
                </div>

                <div class="row" id="salary_content_section">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                    <div id="processStyle"><span id="percentage" class="text-center">0%</span></div>
                </div>

        </div>
        <!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">
   $(document).ready(function() {
        var array_count = $('#array_count').val();
        var emp_data    = <?php echo json_encode($employee_list); ?>;
        var percentage  = 0;
        for(var i = 0; i < array_count; i++) {
            (function(i){
                setTimeout(function(){
                    $.ajax({
                        url: '{{ url('hr/reports/save_salary_sheet2_data') }}',
                        type: 'POST',
                        datatype: 'json',
                        data: {
                            employeelist: emp_data[i],
                            unit: {{$unit}},
                            start_date: '{{$start_date}}',
                            end_date: '{{$end_date}}',
                            disbursed_date: '{{$disbursed_date}}'
                        },
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(res) {
                            percentage = (i-0)*100/(array_count-0);
                            console.log(array_count,percentage,emp_data[i]);
                            $('#processStyle').css({width: percentage+'%', height: '15px', backgroundColor: 'green'});
                            $('#processStyle span#percentage').text(percentage+'%');
                        },
                        error: function() {
                            alert('Error Occurred');
                        }
                    });
                }, 500 * i);
            }(i));
        }
   });
</script>
@endsection