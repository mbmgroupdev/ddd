@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
<style type="text/css">
    .input_height{
        height: 32px !important;
    }
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active">Outside Entry</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Opeartion<small><i class="ace-icon fa fa-angle-double-right"></i>Outside Entry</small></h1>
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-sm-12 no-padding no-margin">
                    <!-- PAGE CONTENT BEGINS -->
                {{ Form::open(['url'=>'hr/operation/location_change/entry', 'class'=>'form-horizontal', 'method' => 'POST']) }}
                <input type="hidden" name="applied_on" id="applied_on" value="{{date('Y-m-d H:i:s')}}">
                <table id="unit_change_table" class="col-xs-12 table table-responsive table-striped table-bordered unit_change_table">
                    <thead>
                        <tr>
                            <th colspan="6" class="align-center" style="background-color: #e6e8e6;border-right-width: 0px;"><h5>Outside Entry</h5></th>
                            <th colspan="3" class="align-center" style="background-color: #e6e8e6;padding-left: 0px;padding-right: 0px;border-left-width: 0px;">
                                <a href="{{url('hr/operation/location_change/list')}}"  class="btn btn-sm btn-info" style=" width: 200px; ">Outside List</a>
                            </th>
                        </tr>
                        <tr>   
                            <th>Employee ID</th>
                            <th>Unit</th>
                            <th>Changed Location</th>
                            <th>Changed Place</th>
                            <th>Type</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Comment</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody  id="emp_unit_change_tbody" class="no-margin no-padding">
                            <tr>
                                <td>
                                    <select class="col-xs-12 employee_id " id="employee_id" name="employee_id[]" required="required">
                                        <option value="">Select Employee</option>
                                        @if($employees)
                                            @foreach($employees as $em)
                                                <option value="{{$em->associate_id}}">{{$em->associate_id}} - {{$em->as_name}}</option>
                                            @endforeach
                                        @else
                                            <option value="">No Data</option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <input class="col-xs-12 previous_unit input_height" type="text" name="previous_unit[]" placeholder="Auto" readonly="readonly">
                                </td>
                                <td>
                                    <select class="col-xs-12 requested_location " id="requested_location" name="requested_location[]" required="required">
                                        <option value="">Select Location</option>
                                         @if($locationList)
                                            @foreach($locationList as $key =>  $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No Data</option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                   <input class="col-xs-12 input_height requested_place" type="text" name="requested_place[0]" value="" readonly="readonly"> 
                                </td>
                                <td>
                                    <select class="col-xs-12 type" id="type" name="type[]" required="required">
                                        <option value="">Select Type</option>
                                        <option value="1">Full Day</option>
                                        <option value="2">1st Half</option>
                                        <option value="3">2nd Half</option>
                                    </select>
                                </td>
                                <td>
                                   <input class="col-xs-12  from_date input_height " type="date" name="from_date[]" required="required"> 
                                </td>
                                <td>
                                   <input class="col-xs-12  to_date input_height " type="date" name="to_date[]" required="required"> 
                                </td>
                                <td>
                                    <input class="col-xs-12 input_height " type="text" name="comment[]"> 
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-xs more_button" title="More" style=" height: 32px !important; width: -webkit-fill-available;"><b style="font-size: 14px;">+</b></button>
                                </td>
                            </tr>
                    </tbody>
                </table>
                <div class="row no-padding no-margin" style=" background-color: whitesmoke;">
                    <button class="pull-right btn btn-sm btn-success" type="submit"><i class="fa fa-check bigger-110"></i> Submit</button>
                </div>

                </form>
                </div>
            </div>

        </div>  {{-- Page content end --}}
    </div>   {{-- main-content-inner-end --}}
</div> {{-- main-content-end --}}
<script type="text/javascript">
    $(document).ready(function(){ 

        // date picker
        $('.datepicker').datepicker({
          changeMonth: true,
          changeYear: true,
          yearRange: "-100:+0",
          onSelect: function() {
            // Keep in mind that maybe the $(this) now reference to something else so you need to serach for the relvent Node
            handleInput($('.from_date'));
          }
        });

        function handleInput(elm) {
          tmpval = elm.val();
          if (tmpval == '') {
            elm.removeClass('active')
              .siblings('label').removeClass('active');
          } else {
            elm.addClass('active')
              .siblings('label').addClass('active');
          }
        }
        //datepicker end

        $('body').on('change', '.employee_id', function(){           
            var emp_id = $(this).val();
            var path = $(this).parent().next();
            var path_for_salary_marked = $(this).parent().next().next().next().next().next();
            // console.log(emp_id, path);
            $.ajax({
                url : "{{ url('hr/operation/get_unit') }}",
                type: 'json',
                method: 'get',
                data: {emp_id: emp_id },
                success: function(data)
                {
                    // console.log("Returned", data);
                    path.find('.previous_unit').val(data['hr_unit_name']);
                    path_for_salary_marked.find('.salary_marked_for').val(emp_id);
                },
                error: function()
                {
                    alert('No Unit');
                }
            });

        });


        $('body').on('change', '.to_date', function(){           

            var to_dt = $(this).val();
            var frm_dt = $(this).parent().prev().find('.from_date').val();
            // console.log("From: ",frm_dt, "To:",to_dt );

            if(frm_dt == '' || frm_dt == null){ 
                    alert("Please Enter From Date"); $(this).val(null);
                }
            else{

                if(frm_dt>to_dt){
                    alert("Please Enter To Date Properly (From date is greater than To Date)"); $(this).val(null);   
                }
            }
        });


        $('body').on('click', '.more_button', function(){
            var more_tr = '<tr>\
                                <td>\
                                    <select class="col-xs-12 employee_id" id="employee_id" name="employee_id[]" required="required">\
                                        <option value="">Select Employee</option>\
                                        @if($employees)\
                                            @foreach($employees as $em)\
                                                <option value="{{$em->associate_id}}">{{$em->associate_id}} - {{$em->as_name}}</option>\
                                            @endforeach\
                                        @else\
                                            <option value="">No Data</option>\
                                        @endif\
                                    </select>\
                                </td>\
                                <td>\
                                    <input class="col-xs-12 previous_unit input_height" type="text" name="previous_unit[]" placeholder="Auto" readonly="readonly">\
                                </td>\
                                <td>\
                                    <select class="col-xs-12 requested_location" id="requested_location" name="requested_location[]" required="required">\
                                        <option value="">Select Location</option>\
                                         @if($locationList)\
                                            @foreach($locationList as $key => $value)\
                                                <option value="{{ $key }}">{{ $value }}</option>\
                                            @endforeach\
                                        @else\
                                            <option value="">No Data</option>\
                                        @endif\
                                    </select>\
                                </td>\
                                <td>\
                                   <input class="col-xs-12 input_height requested_place" type="text" name="requested_place[]" readonly="readonly">\
                                </td>\
                                <td>\
                                    <select class="col-xs-12 type" id="type" name="type[]" required="required">\
                                        <option value="">Select Type</option>\
                                        <option value="1">Full Day</option>\
                                        <option value="2">1st Half</option>\
                                        <option value="3">2nd Half</option>\
                                    </select>\
                                </td>\
                                <td>\
                                   <input class="col-xs-12  from_date input_height " type="date" name="from_date[]"  required="required">\
                                </td>\
                                <td>\
                                   <input class="col-xs-12  to_date input_height " type="date" name="to_date[]"  required="required">\
                                </td>\
                                <td>\
                                    <input class="col-xs-12 input_height " type="text" name="comment[]"> \
                                </td>\
                                <td>\
                                    <button type="button" class="btn btn-danger btn-xs less_button"  style="padding-right: 6px;padding-left: 6px;" title="Delete"><i class="fa fa-trash"></i></button>\
                                </td>\
                            </tr>';

                $('#emp_unit_change_tbody').append(more_tr);
                $('select').select2();
                // $('input').datepicker();

        });

        $('body').on('click', '.less_button', function(){
            $(this).parent().parent().remove();
        });

        //on select outside location make place name mandatory
        $("body").on("change", ".requested_location", function(){
            
            if($(this).val()== "Outside"){
                $(this).parent().next().find(".requested_place").prop("required", true);
                $(this).parent().next().find(".requested_place").removeAttr("readonly");
            }
            else{
                $(this).parent().next().find(".requested_place").removeAttr("required", true);
                $(this).parent().next().find(".requested_place").prop("readonly", "readonly");
            }
        });
  
    });
</script>
@endsection