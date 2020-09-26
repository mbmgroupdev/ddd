@extends('hr.layout')
@section('title', '')
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

		<div class="page-content"> 
            <?php $type='late_count_customize'; ?>
            <div class="panel panel-info">
                <div class="panel-heading page-headline-bar"><h5> Late Count Customize <a href="{{URL::to('hr/setup/late_count_default')}}" class="btn btn-info btn-xs" rel='tooltip' data-tooltip-location='top' data-tooltip='Late Count Default'><i class="fa fa-plus"></i> Default</a>
                    <a>&nbsp;</a>
                    <a href="{{URL::to('hr/setup/shift')}}" class="btn btn-warning btn-xs" rel='tooltip' data-tooltip-location='top' data-tooltip='Add Shift'><i class="fa fa-plus"></i>  Shift</a></h5> </div>
                <div class="panel-body" style="padding-bottom: 0; padding-top: 5px;">
                    <div class="row">
                        <!-- Display Erro/Success Message -->
                        @include('inc/message')
                        <div class="col-sm-8 col-sm-offset-2">
                            <div class="panel panel-info" style="margin-bottom: 5px;">
                                <div class="panel-body">
                                    <!-- PAGE CONTENT BEGINS -->
                                    @php
                                // update form
                                    if(!empty($lcc_single->id)) {
                                        $url = url('hr/setup/update_late_count_customize/'.$lcc_single->id);
                                    } else {
                                // insert form
                                        $url = url('hr/setup/save_late_count_customize');
                                    }
                                    @endphp
                                    <form class="form-horizontal" role="form" method="post" action="{{ $url }}">
                                        {{ csrf_field() }}
                                        <div class="form-group required">
                                            <label class="col-sm-3 control-label no-padding-right"> Unit Name </label>
                                            <div class="col-sm-9">
                                                <select name="hr_unit_id" class="form-control" id="unit_id" data-validation='required'>
                                                    <option value="">Select Unit</option>
                                                    @foreach($unit_list as $id=>$unit)
                                                    <option value="{{ $id }}" {{Custom::sselected($lcc_single->hr_unit_id, $id)}}>{{ $unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>

                                        <div class="form-group required">
                                            <label class="col-sm-3 control-label no-padding-right"> Shift </label>
                                            <div class="col-sm-9">
                                                <select name="hr_shift_name" class="form-control" id="shift_id" data-validation='required'>
                                                    <option value="">Select Shift</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group required">
                                            <label class="col-sm-3 col-xs-12 control-label no-padding-right"> From-To </label>

                                            <div class="col-sm-9" style="padding: 0px;">
                                                <div class="col-sm-6 col-xs-6" >
                                                    <input type="text" name="date_from" id="date_from" class="form-control datepicker" id="date_from" value="{{ $lcc_single->date_from }}" data-validation='required'>
                                                </div>
                                                <div class="col-sm-6 col-xs-6">
                                                    <input type="text" name="date_to" id="date_to" class="form-control datepicker" id="date_to" value="{{ $lcc_single->date_to }}" data-validation='required'>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group required">
                                            <label class="col-sm-3 control-label no-padding-right"> Time </label>

                                            <div class="col-sm-9">
                                                <input type="text" name="time" class="form-control" id="time" value="{{ $lcc_single->time }}" data-validation='required'>
                                            </div>  
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right"> Comment </label>
                                            <div class="col-sm-9 col-xs-12">
                                                <textarea name="comment" id="" cols="30" rows="5" class="form-control">{{ $lcc_single->comment }}</textarea>
                                            </div> 
                                        </div>

                                        <div class="space-4"></div>
                                        <div class="space-4"></div>


                                        <div class="row">
                                            <div class="{{ !empty($lcc_single->id)?'col-sm-offset-1 col-sm-10':'col-sm-offset-3 col-sm-6' }}">
                                                @if(!empty($lcc_single->id))
                                                <a href="{{ url('hr/setup/late_count_customize') }}" class="btn btn-danger btn-xs">
                                                    <i class="ace-icon fa fa-ban bigger-110"></i> Cancel
                                                </a>
                                                @endif
                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-success btn-xs" type="submit">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> {{ !empty($lcc_single->id)?'Update':'Submit' }}
                                                </button>
                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-xs" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
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

                    <div class="row">    
                        <div class="panel panel-info col-sm-12">
                          <div class="panel-body table-responsive">
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
            
        </div>
    </div><!-- /.page-content -->
</div>
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