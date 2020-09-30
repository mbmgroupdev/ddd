@extends('hr.layout')
@section('title', 'Late Count Customize')
@section('main-content')
@push('css')
<style>
    .active-edit {
        background-color: #6faed9;
        color: #fff;
    }
    #dataTables_wrapper{border: 1px solid #dff0d8;}
    #dataTables{width: 100% !important;}
    table.dataTable { margin-top: 0px !important; }

    @media only screen and (max-width: 767px) and (min-width: 480px) {
        .select_div .select2 {width:330px !important;}
    }
</style>
@endpush

@php
$lcc_single = $lateCountCustomize_single;
@endphp
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
				<li class="active"> Late Count Customize </li>
			</ul>
		</div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                <div class="panel">
                    <div class="panel-body">
                        @php
                                // update form
                            if(!empty($lcc_single->id)) {
                                $url = url('hr/setup/update_late_count_customize/'.$lcc_single->id);
                            } else {
                                $url = url('hr/setup/save_late_count_customize');
                            }
                        @endphp
                        <form class="form-horizontal" role="form" method="post" action="{{ $url }}">
                            @csrf
                            <div class="form-group has-float-label has-required select-search-group">
                                <select name="hr_unit_id" class="form-control" id="unit_id" required='required'>
                                    <option value="">Select Unit</option>
                                    @foreach($unit_list as $id=>$unit)
                                    <option value="{{ $id }}" {{Custom::sselected($lcc_single->hr_unit_id, $id)}}>{{ $unit }}</option>
                                    @endforeach
                                </select> 
                                <label for="hr_unit_id"> Unit Name </label>
                            </div>

                            <div class="form-group has-float-label has-required select-search-group">
                                <select name="hr_shift_name" class="form-control" id="shift_id" required='required'>
                                    <option value="">Select Shift</option>
                                </select>
                                <label for="hr_shift_name"> Shift </label>
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="date" name="date_from" id="date_from" class="form-control" id="date_from" value="{{ $lcc_single->date_from }}" required='required'>
                                <label for="date_from"> Date From </label>
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="date" name="date_to" id="date_to" class="form-control" id="date_to" value="{{ $lcc_single->date_to }}" required='required'>
                                <label for="date_to"> Date To </label>
                            </div>

                        

                            <div class="form-group has-required has-float-label">
                                <input type="text" name="time" class="form-control" id="time" value="{{ $lcc_single->time }}" required='required'> 
                                <label for="time"> Time </label>
                            </div>

                            <div class="form-group has-required has-float-label">
                                <label for="comment"> Comment </label>
                                <textarea name="comment" id="comment" class="form-control">{{ $lcc_single->comment }}</textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success btn-xs" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> {{ !empty($lcc_single->id)?'Update':'Submit' }}
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <?php $type='late_count_customize'; ?>
                <div class="panel">
                    <div class="panel-body">
                        <table id="dataTables" class="table table-bordered table-striped" style="display:block;overflow-x: auto;white-space: nowrap; width: 100%;">
                                <thead style="width: 100%;">
                                    <tr>
                                        <td width="20%">#Sl</td>
                                        <td width="20%">Unit</td>
                                        <td width="20%">Shift</td>
                                        <td width="20%">Date Range</td>
                                        <td width="20%">Time</td>
                                        <td width="20%">Comment</td>
                                        <td width="20%">Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lateCountCustomize_list as $k=>$lateCountCustomize)
                                    @php
                                    $dayFrom    = date('d',strtotime($lateCountCustomize->date_from));
                                    $dayTo      = date('d',strtotime($lateCountCustomize->date_to));
                                    $monthYear  = date('m-Y',strtotime($lateCountCustomize->date_from));
                                    @endphp
                                    <tr class="{{ $lcc_single->id == $lateCountCustomize->id?'active-edit':'' }}">
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $lateCountCustomize->unit['hr_unit_name'] }}</td>
                                        <td>{{ $lateCountCustomize->shift['hr_shift_name'] }}</td>
                                        <td>{!! $dayFrom.' <b>to</b> '.$dayTo.'-'.$monthYear !!}</td>
                                        <td>{{ $lateCountCustomize->time }}</td>
                                        <td>{{ $lateCountCustomize->comment }}</td>
                                        <td>
                                            @if($lcc_single->id !== $lateCountCustomize->id)
                                            <a href="{{ url('hr/setup/edit_late_count_customize/'.$lateCountCustomize->id) }}" class="btn btn-info btn-xs">
                                                <span class="fa fa-pencil"></span>
                                            </a>
                                            @endif
                                            <a href="{{ url('hr/setup/delete_late_count_customize/'.$lateCountCustomize->id) }}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure, you want to delete?')">
                                                <span class="fa fa-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.page-content -->
</div>
@push('js')
<script>
    $(document).ready(function(){
        $('#dataTables').DataTable();
        var llc_single = '<?php echo json_encode($lcc_single) ?>';
        var parsed_llc = JSON.parse(llc_single);
        var _token = $('input[name="_token"]').val();
        //on change unit fetching the shifts
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


        //Dates entry alerts....
        $('#date_from').on('dp.change',function(){
            $('#date_to').val($('#date_from').val());    
        });

        //tooltip
        $('#time').tooltip({'trigger':'focus', 'title': 'Minutes Allowed'});


        $('#date_to').on('dp.change', function(){
            var to_date     = new Date($(this).val());
            var from_date   = new Date($('#date_from').val());
            if($('#date_from').val() == '' || $('#date_from').val() == null){
                alert("Please enter From-Date first");
                $('#date_to').val('');
            }
            else{
                if(to_date < from_date){
                    alert("Invalid!!\n From-Date is latest than To-Date");
                    $('#date_to').val('');
                }
            }
        });

        setTimeout(function(){
            // console.log(parsed_llc['date_to']);
            $('#date_to').val(parsed_llc['date_to']);
        }, 1000); 
    });

    function attLocation(loc){
        window.location = loc;
    }
</script>
@endpush
@endsection