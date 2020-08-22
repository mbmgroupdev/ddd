@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
    <style>
        table.dataTable { margin-top: 0px !important; }
        td input[type=text], input[type=number] {height: auto !important;}
        .form-actions {margin-bottom: 0px; margin-top: 0px; padding: 0px 11px 0px;background-color: unset; border-top: unset;}
    </style>
@endpush

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
				<li class="active"> Late Count Default </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <?php $type='late_count_default'; ?>
            <div class="panel panel-info">
                <div class="panel-heading page-headline-bar"><h5> Late Count Default <a href="{{URL::to('hr/setup/late_count_customize')}}" class="btn btn-info btn-xs" rel='tooltip' data-tooltip-location='top' data-tooltip='Late Count Customize'><i class="fa fa-plus"></i> Customize</a>
                    <a>&nbsp;</a>
                    <a href="{{URL::to('hr/setup/shift')}}" class="btn btn-warning btn-xs" rel='tooltip' data-tooltip-location='top' data-tooltip='Add Shift'><i class="fa fa-plus"></i>  Shift</a></h5> </div>
                <div class="panel-body" style="padding-bottom: 0; padding-top: 5px;">
                    <div class="row">
                        <div class="entry_setup_section">
                            <div class="form-horizontal">
                               @include('inc/notify')
                                <div class="col-sm-6 col-sm-offset-3">
                                    <div class="panel panel-info" style="margin-bottom: 5px;">
                                        <div class="panel-body">
                                            <!-- PAGE CONTENT BEGINS -->
                                            <!-- <h1 align="center">Add New Employee</h1> -->
                                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/save_late_count_default')  }}">
                                            {{ csrf_field() }}

                                                <div class="form-group required">
                                                    <label class="col-sm-3 control-label no-padding-right"> Unit Name </label>
                                                    <div class="col-sm-9">
                                                        <select name="hr_unit_id" class="form-control" id="unit_id" required>
                                                            <option value="">Select Unit</option>
                                                            @foreach($unit_list as $id=>$unit)
                                                                <option value="{{ $id }}">{{ $unit }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <label class="col-sm-3 control-label no-padding-right"> Shift </label>
                                                    <div class="col-sm-9">
                                                        <select name="hr_shift_name" class="form-control" id="shift_id" required>
                                                            <option value="">Select Shift</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <label class="col-sm-3 control-label no-padding-right"> Default Value </label>
                                                    <div class="col-sm-9">
                                                        <input type="number" name="default_value" class="form-control" id="default_value" required>
                                                    </div>
                                                </div>
                                                <div class="clearfix form-actions">
                                                    <div class="col-md-offset-3 col-md-9 no-padding"> 
                                                        <button class="btn btn-xs" type="reset">
                                                            <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                        </button>
                                                        &nbsp; &nbsp; &nbsp;
                                                        <button class="btn btn-success btn-xs" type="submit">
                                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- /.row --> 
                                            </form> 
                                            <!-- PAGE CONTENT ENDS -->
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="list_of_section">
                            <div class="panel panel-info" style="margin-bottom: 5px;">
                                <div class="panel-body table-responsive" >
                                    <table id="dataTables" class="table table-bordered  table-hover">
                                        <thead>
                                            <tr class="info">
                                                <td>#Sl</td>
                                                <td>Unit</td>
                                                <td>Shift Name</td>
                                                <td>Default time</td>
                                                <td>Form date</td>
                                                <td>To date</td>
                                                <td>Custom time</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lateCountDefault_list as $k=>$lateCountDefault)
                                                <tr>
                                                    <td>{{ $k+1 }}</td>
                                                    <td>{{ $lateCountDefault->unit->hr_unit_name }}</td>
                                                    <td>{{ $lateCountDefault->shift['hr_shift_name'] }}</td>
                                                    <td>{{ $lateCountDefault->default_value }}</td>
                                                    <td>    
                                                        @if($lateCountDefault->date_from != '0000-00-00')
                                                        {{ $lateCountDefault->date_from }}</td>
                                                        @endif
                                                    <td>
                                                        @if($lateCountDefault->date_to != '0000-00-00')
                                                        {{ $lateCountDefault->date_to }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lateCountDefault->value != 0)
                                                        {{ $lateCountDefault->value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
    <script>
         $(document).ready(function(){
            $('#dataTables').DataTable();
        });
        
        var _token = $('input[name="_token"]').val();

        //getting the shifts...
        $('#unit_id').on('change', function() {
            var unit_id = $(this).val();
            $.ajax({
                url : "{{ url('hr/setup/ajax_get_shifts') }}",
                type: 'json',
                method: 'post',
                data: {
                    _token : _token,
                    unit_id: unit_id
                },
                success: function(data) {
                    if(data.status == 'success'){
                        var data = data.value;
                        // console.log(data);
                        if(data.length > 0) {
                            var shift_list = "<option value=\"all\">All</option>";
                            for(var i=0; i<data.length; i++){
                                shift_list +='<option value="'+data[i].hr_shift_name+'">'+data[i].hr_shift_name+'</option>';
                            }
                            $('#shift_id').html(shift_list);
                        } else {
                            var shift_list = "<option value=\"\">No Shift</option>";
                            $('#shift_id').html(shift_list);
                        }
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        //Getting Default by (unit+shift)...
        $('#shift_id').on('change', function() {
            var unit_id = $('#unit_id').val();
            $.ajax({
                url : "{{ url('hr/setup/ajax_get_default_value') }}",
                type: 'json',
                method: 'post',
                data: {
                    _token : _token,
                    hr_shift_name: $(this).val(),
                    hr_unit_id: unit_id
                },
                success: function(data) {
                        console.log(data);
                    if(data.default_value) {
                        $('#default_value').val(data.default_value);
                    } else {
                        $('#default_value').val('');
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });


    function attLocation(loc){
        window.location = loc;
    }
    </script>
@endpush
@endsection