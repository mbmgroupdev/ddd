@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
    <style type="text/css">
        .in_h{
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
                    <a href="#"> Setup </a>
                </li>
                <li class="active">Bonus Type</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                        {{-- entry --}}
                            {{Form::open(['url'=>'hr/setup/bonus_type_save', 'class'=>'form-horizontal']) }}
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Bonus Type</h6></div> 
                <div class="panel-body">
                        <div class="col-sm-offset-3 col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="bonus_type_name">Bonus Type Name <span style="color: red; vertical-align: top;">&#42;</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="bonus_type_name" id="bonus_type_name" class="col-xs-12" data-validation="required" data-validation-length="1-45" placeholder="Enter Bonus Type Name">
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-sm-3 col-xs-3 no-padding-right control-label">Month: </label>
                                <div class="col-sm-3 col-xs-3">
                                    <input type="text" name="month" id="month" class="col-xs-12 monthpicker" placeholder="Month" required="required">
                                </div>
                                <label class="col-sm-2 col-xs-2 align-right no-padding-right control-label">Year:</label>
                                <div class="col-sm-4 col-xs-4">
                                    <input type="text" name="year" id="year" class="col-xs-12 yearpicker" placeholder="Year" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 col-xs-3 control-label no-padding-right" >Amount <span style="color: red; vertical-align: top;">&#42;</span>
                                </label>
                                
                                    <div class="col-sm-3 col-xs-3">
                                        <input type="number" name="bonus_amount" id="bonus_amount"  placeholder="Enter"  class="col-xs-12 in_h" >
                                    </div>
                                    <label class="col-sm-2 col-xs-2 control-label no-padding-right align-right">OR,</label>
                                    <div class="col-sm-4 col-xs-4">
                                        <input type="number" name="bonus_percent" id="bonus_percent" class="col-xs-6 col-sm-6 in_h" placeholder="Enter">
                                         <label class="col-sm-6 col-xs-6 control-label no-padding no-margin"> &nbsp % of Basic</label>
                                    </div>
                                
                            </div>
                            
                        </div>
                        {{-- view the entry --}}
                        <div class="col-sm-12 col-xs-12">
                            
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center"> 
                                    <button class="btn btn-success btn-sm" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>

                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                            {{Form::close()}}
                </div>
            </div>
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Bonus Type List</h6></div> 
                <div class="panel-body">
                        <div class="col-sm-offset-2 col-sm-8">
                            
                                <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%; white-space: nowrap;" >
                                    <thead>
                                        <th width="20%">Bonus Type Name</th>
                                        <th width="20%">Month</th>
                                        <th width="20%">Year</th>
                                        <th width="20%">Amount</th>
                                        <th width="20%">%of Basic</th>
                                        <th width="30%">Action</th>
                                    </thead>
                                    <tbody>
                                        @if($bonus_types)
                                            @foreach($bonus_types as $bt)
                                                <tr>
                                                    <td>{{$bt->bonus_type_name}}</td>
                                                    <td>{{$bt->month}}</td>
                                                    <td>{{$bt->year}}</td>
                                                    <td>{{$bt->amount}}</td>
                                                    <td>{{$bt->percent_of_basic}}</td>
                                                    <td>
                                                        <div class="button-group">
                                                            @if(in_array($bt->id, $not_action_list))
                                                                
                                                            @else
                                                            <input type="hidden" id="edit_data_id" value="{{$bt->id}}">
                                                            <button class="btn btn-sm btn-success edit_modal_button" data-toggle="modal" data-target="#edit-modal" data-toggle="tooltip" title="Edit" style="padding: 0px 4px 0px 4px;">
                                                                <i class="fa fa-pencil"></i>
                                                                </button>
                                                            <a href="{{url('hr/setup/bonus_type_delete/'.$bt->id)}}" style="padding: 0px 4px 0px 4px;" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this?');"><i class="fa fa-trash" ></i></a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                        No Data
                                        @endif
                                        
                                    </tbody>
                                </table>  
                        </div>
                </div>
            </div>
            </div>

            {{-- edit Modal --}}
            <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edit-modal-label">Edit Data</h5>
                        </div>
                        <div class="modal-body" id="attachment-body-content">
                            <div class="row" style="padding: 4px;">
                            {{Form::open(['url'=>'hr/setup/bonus_type_update', 'class'=>'form-horizontal']) }}
                                    <div class="col-sm-offset-2 col-sm-8">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="edit_bonus_type_name">Bonus Type Name <span style="color: red; vertical-align: top;">&#42;</span>
                                            </label>
                                            <div class="col-sm-9">
                                                <input type="text" name="edit_bonus_type_name" id="edit_bonus_type_name" class="col-xs-12" data-validation="required" data-validation-length="1-45" placeholder="Enter Bonus Type Name">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-3 no-padding-right control-label">Month: </label>
                                            <div class="col-sm-3">
                                                <input type="text" name="edit_month" id="edit_month" class="col-xs-12 monthpicker" placeholder="Month" required="required">
                                            </div>
                                            <label class="col-sm-2 no-padding-right control-label">Year:</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="edit_year" id="edit_year" class="col-xs-12 yearpicker" placeholder="Year" required="required">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" >Amount <span style="color: red; vertical-align: top;">&#42;</span>
                                            </label>
                                            <div class="col-sm-9">
                                                <div class="col-sm-5 no-padding-left">
                                                    <input type="number" name="edit_bonus_amount" id="edit_bonus_amount"  placeholder="Enter"  class="col-xs-12 in_h" >
                                                </div>

                                                <div class="col-sm-7 no-padding-right">

                                                     <label class="col-xs-2 control-label no-padding-left no-padding-top">OR,</label>
                                                    <input type="number" name="edit_bonus_percent" id="edit_bonus_percent" class="col-xs-6 in_h" placeholder="Enter">
                                                     <label class="col-xs-4 control-label no-padding no-margin"> &nbsp % of Basic</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    {{-- view the entry --}}
                                    <div class="col-sm-12">
                                        
                                        <div class="clearfix form-actions">
                                            <div class="col-md-offset-4 col-md-4 text-center"> 
                                                <input type="hidden" name="edit_id" id="edit_id" >
                                                <button class="btn btn-success btn-sm" type="submit">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Update
                                                </button>

                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-sm" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                            {{Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div> {{-- Page-Content-end --}}
    </div> {{-- Main-content-inner-end --}}
</div> {{-- Main-content --}}
<script type="text/javascript">
    $(document).ready(function(){
        $('body').on('keyup', '#bonus_amount', function(){
            $('#bonus_percent').val(null);
            if($('#bonus_amount').val() < 0){
                $('#bonus_amount').val(0);
            }
        });
        $('body').on('keyup', '#bonus_percent', function(){
            $('#bonus_amount').val(null);

            if($('#bonus_percent').val() > 100){
                $('#bonus_percent').val(100);
            }
            if($('#bonus_percent').val() < 0){
                $('#bonus_percent').val(0);
            }
        });


        $('body').on('click', '.edit_modal_button', function(){
                var bt_id = $(this).parent().find('#edit_data_id').val();
                // console.log(bt_id );
                var months = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
                // var selectedMonthName = months[value['month']];
                $.ajax({
                    url: "{{url('hr/setup/bonus_type_edit')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {bt_id: bt_id},
                    success: function(data){
                          console.log(data);
                          $("#edit_id").val(data.id);
                          $("#edit_bonus_type_name").val(data.bonus_type_name);
                          $("#edit_month").val(months[data.month-1]); 
                          $("#edit_year").val(data.year); 
                          $("#edit_bonus_amount").val(data.amount); 
                          $("#edit_bonus_percent").val(data.percent_of_basic); 

                    }
                });     
        });

        $('body').on('keyup', '#edit_bonus_amount', function(){
            $('#edit_bonus_percent').val(null);
            if($('#edit_bonus_amount').val() < 0){
                $('#edit_bonus_amount').val(0);
            }
        });
        $('body').on('keyup', '#edit_bonus_percent', function(){
            $('#edit_bonus_amount').val(null);

            if($('#edit_bonus_percent').val() > 100){
                $('#edit_bonus_percent').val(100);
            }
            if($('#edit_bonus_percent').val() < 0){
                $('#edit_bonus_percent').val(0);
            }
        });

    });
</script>

<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
       // "scrollY": true,
       // "scrollX": true
       pagingType: "full_numbers" ,
        // searching: false,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'
    });
});
</script>
@endsection