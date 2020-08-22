@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
    .input_height{
        height: 32px !important;
    }
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
                    <a href="#">Operation</a>
                </li>
                <li class="active">Unit Change Section</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Opeartion<small><i class="ace-icon fa fa-angle-double-right"></i>Unit Change Section</small></h1>
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-sm-12 no-padding no-margin">
                    <!-- PAGE CONTENT BEGINS -->
                {{ Form::open(['url'=>'hr/operation/employee_unit_change_save', 'class'=>'form-horizontal']) }}
                <table id="unit_change_table" class="col-xs-12 table table-responsive table-striped table-bordered unit_change_table">
                    <thead>
                        <tr>
                            <th colspan="4" class="align-center" style="background-color: darkgrey;border-right-width: 0px;"><h5>Employee Unit Change</h5></th>
                            <th colspan="3" class="align-center" style="background-color: darkgrey;padding-left: 0px;padding-right: 0px;border-left-width: 0px;">
                                <a href="{{url('hr/operation/employee_unit_change_list')}}"  class="btn btn-sm btn-info" style=" width: 200px; ">List of Unit Change</a>
                            </th>
                        </tr>
                        <tr>   
                            <th style="width: 25%;">Employee ID</th>
                            <th style="width: 20%;">Unit</th>
                            <th style="width: 20%;">Change Unit</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Salary</th>
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
                                                <option value="{{$em->as_id}}">{{$em->associate_id}} - {{$em->as_name}}</option>
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
                                    <select class="col-xs-12 changed_unit " id="changed_unit" name="changed_unit[]" required="required">
                                        <option value="">Select Unit</option>units
                                         @if($units)
                                            @foreach($units as $unt)
                                                <option value="{{$unt->hr_unit_id}}">{{$unt->hr_unit_name}}</option>
                                            @endforeach
                                        @else
                                            <option value="">No Data</option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                   <input class="col-xs-12  from_date input_height " type="date" name="from_date[]" required="required"> 
                                </td>
                                <td>
                                   <input class="col-xs-12  to_date input_height " type="date" name="to_date[]" required="required"> 
                                </td>
                                <td>
                                    <input type="checkbox" name="salary_marked_for[]" class="salary_marked_for" style="width: 25px;height: 25px;margin-left: 4px;" >
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
                                                <option value="{{$em->as_id}}">{{$em->associate_id}} - {{$em->as_name}}</option>\
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
                                    <select class="col-xs-12 changed_unit" id="changed_unit" name="changed_unit[]" required="required">\
                                        <option value="">Select Unit</option>\
                                         @if($units)\
                                            @foreach($units as $unt)\
                                                <option value="{{$unt->hr_unit_id}}">{{$unt->hr_unit_name}}</option>\
                                            @endforeach\
                                        @else\
                                            <option value="">No Data</option>\
                                        @endif\
                                    </select>\
                                </td>\
                                <td>\
                                   <input class="col-xs-12  from_date input_height " type="date" name="from_date[]"  required="required">\
                                </td>\
                                <td>\
                                   <input class="col-xs-12  to_date input_height " type="date" name="to_date[]"  required="required">\
                                </td>\
                                <td>\
                                    <input type="checkbox" name="salary_marked_for[]" class="salary_marked_for" style="width: 25px;height: 25px;margin-left: 4px;" >\
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
  
    });
</script>
@endsection