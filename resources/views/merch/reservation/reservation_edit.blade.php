@extends('merch.index')
@section('content')
@push('css')
    <style>
        .margin-5{margin:0 5px;}
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li class="active">Reservation</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel panel-success">
                <div class="panel-heading"><h6>Reservation Edit
                    <a href='{{ url("merch/orders/order_entry/".$reserved->res_id) }}' rel='tooltip' data-tooltip-location='top' data-tooltip='Order Entry' type="button" class="btn btn-warning btn-xx margin-5  pull-right">
                    <i class="glyphicon glyphicon-record"></i>
                    </a>
                    <a href='{{ url("merch/reservation/reservation") }}' rel='tooltip' data-tooltip-location='top' data-tooltip='Add New Reservation' type="button" class="btn btn-success btn-xx  margin-5 pull-right">
                    <i class="glyphicon  glyphicon-plus"></i>
                    </a>
                    <a href='{{ url("merch/reservation/reservation_list") }}' rel='tooltip' data-tooltip-location='top' data-tooltip='Reservation List' type="button" class="btn btn-info btn-xx margin-5  pull-right">
                    <i class="glyphicon glyphicon-th-list"></i>
                    </a>
                    
                </h6>
                </div>

                <!-- Widget Body -->
                <div class="panel-body">
                    <div class="row">
                          <!-- Display Erro/Success Message -->
                        <div class="col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
                        @include('inc/message')
                            <!-- PAGE CONTENT BEGINS --> 
                            <form class="form-horizontal" role="form" method="post" action="{{ url('merch/reservation/reservation_update') }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
         
                                <input type="hidden" name="res_id" value="{{ $reserved->res_id }}">
                                <input type="hidden" name="ordered_qty" id="ordered_qty" value="{{ $ordered_qty }}">
                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="hr_unit_id">Unit<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9 col-xs-9">
                                        {{ Form::select('hr_unit_id', $unitList, $reserved->hr_unit_id, ['id'=> 'hr_unit_id', 'placeholder'=>'Select Unit','class'=>'col-xs-12', 'data-validation'=> 'required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="b_id" >Buyer Name<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9 col-xs-9">
                                        {{ Form::select('b_id', $buyerList, $reserved->b_id, ['id'=> 'b_id', 'placeholder' => 'Select Buyer', 'class' => 'col-xs-12', 'data-validation'=> 'required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="res_month"> Month<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9 col-xs-9">
                                        <input type="text" id="res_month" name="res_month" value="{{ $reserved->edit_month }}" data-validation="required" class="col-sm-4 col-xs-4 no-padding-right monthpicker"/>
                                        <label class="col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1">Year<span style="color: red">&#42;</span></label>
                                        <input type="text" id="res_year" name="res_year" value="{{ $reserved->res_year }}" data-validation="required" class="col-sm-4 col-xs-4 no-padding-right yearpicker"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="confirmed_quantity"> Confirmed</label>
                                    <div class="col-sm-9 col-xs-9">
                                        <input type="text" id="confirmed_quantity" name="confirmed_quantity" value="{{isset($ordered_qty)?$ordered_qty:0}}"  class="col-xs-12 col-sm-12 smvCalculation" readonly="readonly" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="prd_type_id" >Product Type<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9 col-xs-9">
                                        {{ Form::select('prd_type_id', $prdtypList, $reserved->prd_type_id, ['id'=> 'prd_type_id', 'placeholder' => 'Select Product Type', 'class' => 'col-xs-12  fileter', 'data-validation'=> 'required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="res_quantity"> Quantity<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9 col-xs-9">
                                        <input type="text" id="res_quantity" name="res_quantity" value="{{ $reserved->res_quantity }}" data-validation=" required length number" data-validation-length="1-11" placeholder="Quantity" class="col-xs-3 smvCalculation"/>
                                        <label class="col-sm-4 col-sm-offset-1 col-xs-4 col-xs-offset-1 control-label no-padding-right" for="res_sewing_smv"> Sewing SMV<span style="color: red">&#42;</span> </label>
                                        <input type="text" id="res_sewing_smv" name="res_sewing_smv" data-validation="required number" data-validation-allowing="float" value="{{ $reserved->res_sewing_smv }}" placeholder="Sewing SMV" class="col-sm-3 col-xs-3 smvCalculation"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="res_sah"> SAH <span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9 col-xs-9">
                                        <input type="text" id="res_sah" name="res_sah" value="{{ $reserved->res_sah }}" placeholder="SAH" class="col-xs-12" data-validation=" required number" data-validation-allowing="float" readonly />
                                    </div>
                                </div>

                                @include('merch.common.update-btn-section')
                                <!-- /.row --> 
                            </form> 
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        //Total quantity can not be greater than Projected quantity
        $('.smvCalculation').on('keyup', function(){
            var res_sewing_smv = parseInt($("#res_sewing_smv").val());
            var res_quantity= parseInt($("#res_quantity").val());
            if(res_sewing_smv == null) res_sewing_smv=0;
            if(res_quantity == null) res_quantity=0;
            var sah= ((res_sewing_smv*res_quantity)/60).toFixed(2);
            $("#res_sah").val(sah);
        });

        $('#res_quantity').on('change', function(){

            var res_quantity= parseInt($("#res_quantity").val());
            var ordered_qty= parseInt($("#ordered_qty").val());
           
            if(res_quantity<ordered_qty){
                alert("Reservation Quantity Can not be lower than Already Ordered Quantity!");
                var x= "{{ $reserved->res_quantity }}";
                x= parseInt(x);
                $("#res_quantity").val(x);
            }
            
        });
    });
</script>
@endsection