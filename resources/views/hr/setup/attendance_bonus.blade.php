@extends('hr.layout')
@section('title', 'Attendance Bonus')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Attendance Bonus </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Attendance Bonus</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/attendance_bonus_save')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'unit_id', 'class'=> 'form-control', 'required'=>'required', ]) }} 
                                <label for="unit_id"> Unit Name  </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                 <input type="number" id="late_count" name="late_count" placeholder="Enter Late Count" class="form-control"
                                  />
                                <label for="late_count" > Late Count </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                
                                 <input type="number" id="leave_count" name="leave_count" placeholder="Enter Leave Count" class="form-control"/>
                                <label for="leave_count" > Leave Count </label>
                                
                            </div>
                            <div class="form-group has-required has-float-label">
                                
                                 <input type="number" id="absent_count" name="absent_count" placeholder="Enter Absent Count" class="form-control"/>
                                <label for="absent_count" > Absent Count </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                
                                 <input type="number" id="first_month" name="first_month" placeholder="Enter First Month Bonus" value="0" class="form-control"/>
                                <label for="first_month" > Primary (1st Month) </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                
                                 <input type="number" id="second_month" name="second_month" placeholder="Enter Second Month Bonus" value="0" class="form-control"/>
                                <label for="second_month" > Fixed (2nd Month to onward) </label>
                            </div>

                            <div class="form-group">
                                <button class="btn  btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                            </div>                                 
                        </form>  
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                        <table id="global-datatable" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Unit Name</th>
                                    <th>Late Count</th>
                                    <th>Leave Count</th>
                                    <th>Absent Count</th>
                                    <th>First Month</th>
                                    <th>Second Month</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($attBonusData)
                                    @foreach($attBonusData as $data)
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>{{$data->hr_unit_name}}</td>
                                        <td>{{$data->late_count}}</td>
                                        <td>{{$data->leave_count}}</td>
                                        <td>{{$data->absent_count}}</td>
                                        <td>{{$data->first_month??''}}</td>
                                        <td>{{$data->second_month??''}}</td>
                                    </tr>

                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 

  
    var _token = $('input[name="_token"]').val();
    $('#unit_id').on('change',function(){
        $.ajax({
                url : "{{ url('hr/setup/get_values') }}",
                type: 'json',
                method: 'post',
                data: {
                    _token : _token,
                    unit_id: $(this).val()
                },
                success: function(data) {
                    // console.log(data);
                    $('#late_count').val(data.late_count);
                    $('#leave_count').val(data.leave_count);
                    $('#absent_count').val(data.absent_count);
                    $('#first_month').val(data.first_month);
                    $('#second_month').val(data.second_month);
                },
                error: function() {
                    console.log('failed...');
                }
        });
    });
});
</script>
@endpush
@endsection