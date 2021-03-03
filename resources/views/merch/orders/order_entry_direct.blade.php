@extends('merch.index')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li>
                <li>
                    <a href="#">Order </a>
                </li>
                <li class="active"> Order Entry</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">

            <div class="row">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h6>Order Entry
                            <a class="pull-right healine-panel" href="{{ url('merch/orders/order_list') }}" rel="tooltip" data-tooltip="Order List" data-tooltip-location="top"><i class="fa fa-list"></i></a>

                            <a class="pull-right healine-panel" href="{{ url('merch/reservation/reservation') }}" rel="tooltip" data-tooltip="Reservation" data-tooltip-location="top" style="margin-right: 10px;"><i class="fa fa-briefcase" style="color: forestgreen;"></i> </a>
                        </h6>
                    </div>
                    <div class="panel-body">
                        <!-- Display Erro/Success Message -->
                        @include('inc/message')
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/orders/order_entry_direct_save') }}">
                            {{ csrf_field() }}

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

                                <input type="hidden" name="res_id" id="res_id" value="">

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_unit_name">Unit<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">

                                        {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'class'=> 'col-xs-12 form-control', 'id'=>"unit_id", 'data-validation' => 'required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="mr_buyer_name" >Buyer Name<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">

                                        {{ Form::select('b_id', $buyerList, null, ['placeholder'=>'Select Buyer', 'class'=> 'col-xs-12 form-control', 'id'=>"b_id", 'data-validation' => 'required']) }}

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 col-xs-4 control-label no-padding-right" for="prd_type_id" >Product Type<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8 col-xs-8">
                                        {{ Form::select('prd_type_id', $prdtypList, null, ['id'=> 'prd_type_id', 'placeholder' => 'Select Product Type', 'class' => 'col-sm-12 col-xs-12 fileter', 'data-validation'=> 'required']) }}
                                    </div>
                                </div>

                                <!-- <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="mr_brand_br_id" >Brand<span style="color: red">&#42;</span></label>
                                    <div class="col-sm-8">
                                        {{ Form::select('mr_brand_br_id', $brandList, null, ['id'=> 'mr_brand_br_id', 'placeholder' => 'Select Brand', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                    </div>
                                </div> -->

                                <!-- <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="pcd" >PCD{{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="pcd" id="pcd" class="col-sm-12 datepicker" placeholder="Enter Date">
                                    </div>
                                </div> -->
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="order_month"> Month<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="order_month" name="order_month" class="col-sm-4 monthpicker" placeholder="Month" value="" data-validation="required"/>

                                        <label class="col-sm-3" style="text-align: right">Year<span style="color: red">&#42;</span></label>
                                        <input type="text" id="order_year" name="order_year"  class="col-sm-3 yearpicker" placeholder="Year" value="" data-validation="required"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="mr_season_se_id"> Season <span style="color: red">&#42;</span></label>
                                    <div class="col-sm-8">
                                        {{ Form::select('mr_season_se_id', $seasonList, null, [ 'id'=> 'mr_season_se_id', 'placeholder' => 'Select Season', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="mr_style_stl_id"> Style <span style="color: red">&#42;</span></label>
                                    <div class="col-sm-8">
                                        {{ Form::select('mr_style_stl_id', [], null, [ 'id'=> 'mr_style_stl_id', 'placeholder' => 'Select Style', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="order_ref_no"> Reference No<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="order_ref_no" name="order_ref_no" class="col-xs-12" placeholder="Reference No" data-validation="required length" data-validation-length="1-60"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="order_qty"> Quantity<span style="color: red">&#42;</span> </label>
                                    <!-- This is the actual Reservation Quantity -->
                                    <input type="hidden" class="qty_check" name="qty_check" id="qty_check" value="">
                                    <!-- This Quantity will be used to check if order qty is greater than reservation qty -->
                                    <div class="col-sm-8">
                                        <input type="text" id="order_qty" name="order_qty" value="" data-validation=" required length number" data-validation-length="1-11" placeholder="Quantity" class="col-xs-12"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="order_delivery_date"> Delivery Date <span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="order_delivery_date" name="order_delivery_date" class="col-xs-12 datepicker" placeholder="Delivery Date" data-validation="required"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="pcd" >PCD{{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="pcd" id="pcd" class="col-sm-12 datepicker" placeholder="Enter Date">
                                    </div>
                                </div>
                                <div class="clearfix form-actions">
                                    <div class="col-sm-offset-4 col-sm-8 text-right">
                                        <button class="btn btn-info btn-sm" type="submit" id="submitbtn" disabled style="border-radius: 2px;">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>
                                    </div>
                                </div>
                                    <!-- /.row -->

                                <!-- PAGE CONTENT ENDS -->
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        //Total quantity can not be greater than Projected quantity
        $('#order_qty').on('keyup', function(){
            var sum = 0;
            var total_qty= parseInt($(this).val());
            var projected_qty= parseInt($("#qty_check").val());
            if(total_qty> projected_qty){
                alert('Total quantity can not greater than Projected quantity');
                $(this).val(projected_qty);
            }
        });
        $('#order_year').change(function(){
             //Change code!
             console.log('opppp');

        });
       $('.yearpicker').on("dp.change",function(){
         $.ajax({
             url : "{{ url('merch/orders/order_quantity_direct') }}",
             type: 'get',
             dateType: 'JSON',
             data: {
                 buyer_id : $('#b_id').val(),
                 unit_id : $('#unit_id').val(),
                 year : $('#order_year').val(),
                 month :$('#order_month').val(),
                 product_type_id : $('#prd_type_id').val()
                     },
             success: function(data)
             {
                if(data.response === 'err'){
                  swal({
                    title: "Reservation not found",
                    icon: "warning",
                    //buttons: true,
                    dangerMode: false,
                  })

                }else{
                  $('#order_qty').val(data.response);
                  $('#res_id').val(data.res_id);
                  $('#submitbtn').attr('disabled',false);

                }

             },
             error: function()
             {
                 alert('failed...');
             }
         });


       });


       $('#order_delivery_date').on('dp.change',function(){
           var dte = moment($('#order_delivery_date').val()).subtract(1, 'months').format('YYYY-MM-DD');

           $('#pcd').val(dte);
          //console.log(dte);


       });
       $('#pcd').on('dp.change',function(){
           var dtebuffer = moment($('#order_delivery_date').val()).subtract(15, 'days').format('YYYY-MM-DD');
           var dte = moment($('#order_delivery_date').val()).subtract(1, 'months').format('YYYY-MM-DD');

           //$('#pcd').val(dte);
           if(Date.parse($('#pcd').val()) > Date.parse(dtebuffer) ){
             alert('Sorry');
             $('#pcd').val(dte);
           }
          console.log($('#pcd').val(),dtebuffer);


       });
        //product type on change garment type change

        $('#mr_season_se_id').on("change", function(){

            // Sample  list
            $.ajax({
                url : "{{ url('merch/orders/season_style_direct') }}",
                type: 'get',
                dateType: 'JSON',
                data: {
                    mr_season_se_id : $(this).val(),
                    buyer_id : $('#b_id').val()
                        },
                success: function(data)
                {

                    $('#mr_style_stl_id').html(data.styleList);

                },
                error: function()
                {
                    alert('failed...');
                }
            });

        });

    });
</script>
@endsection
