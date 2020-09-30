@extends('hr.layout')
@section('title', 'Late Count Default')
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
        @include('inc/notify')
        <div class="row">
            <div class="col-sm-4">
                <div class="panel">
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/save_late_count_default')  }}">
                            @csrf
                            <div class="form-group has-required has-float-label select-search-group">
                                <select name="hr_unit_id" class="form-control" id="unit_id" required>
                                    <option value="">Select Unit</option>
                                    @foreach($unit_list as $id=>$unit)
                                        <option value="{{ $id }}">{{ $unit }}</option>
                                    @endforeach
                                </select>
                                <label for="hr_unit_id"> Unit Name </label>
                            </div>
                            <div class="form-group has-required has-float-label select-search-group">
                                <select name="hr_shift_name" class="form-control" id="shift_id" required>
                                    <option value="">Select Shift</option>
                                </select>
                                <label for="shift_id"> Shift </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="number" name="default_value" class="form-control" id="default_value" required>
                                <label for="default_value"> Default Value </label>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success btn-xs" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                            </div>
                        </form> 
                    </div>
                </div>
                
            </div>
            <div class="col-sm-8">
                <div class="panel">
                    <div class="panel-body">
                        <table id="dataTables" class="table table-bordered  table-hover">
                            <thead>
                                <tr class="info">
                                    <td>#Sl</td>
                                    <td>Unit</td>
                                    <td>Shift Name</td>
                                    <td>Default time</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lateCountDefault_list as $k=>$lateCountDefault)
                                    <tr>
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $lateCountDefault->unit->hr_unit_name }}</td>
                                        <td>{{ $lateCountDefault->shift['hr_shift_name']??'' }}</td>
                                        <td>{{ $lateCountDefault->default_value }}</td>
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