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
                <li class="active"> Order Copy </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">

            <div class="row">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h6>Order Copy
                            <a class="pull-right healine-panel" href="{{ url('merch/orders/order_list') }}" rel="tooltip" data-tooltip="Order List" data-tooltip-location="top"><i class="fa fa-list"></i></a>

                            <a class="pull-right healine-panel" type="button" calss="btn btn-warning btn-xx margin-5" href="{{ url('merch/orders/order_edit/'.$order->order_id) }}" rel="tooltip" data-tooltip="The Order" data-tooltip-location="top" style="margin-right: 10px;"><i class="glyphicon glyphicon-record"  style="color: maroon;"></i></a>
                        </h6>
                    </div>
                    <div class="panel-body">
                        <!-- Display Erro/Success Message -->
                        @include('inc/message')
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/orders/order_copy/'.$order->order_id) }}">
                            {{ csrf_field() }}

                            <div class="col-sm-6 no-padding-left">

                                <input type="hidden" name="res_id" value="{{ $order->res_id }}">
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="hr_unit_name">Unit<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" id="hr_unit_name" name="hr_unit_name" value="{{ $order->hr_unit_name }}" class="form-control" disabled/>
                                        <input type="hidden" name="unit_id" value="{{ $order->unit_id }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="mr_buyer_name" >Buyer Name<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" id="mr_buyer_name" name="mr_buyer_name" value="{{ $order->b_name }}" class="form-control" disabled/>
                                        <input type="hidden" name="mr_buyer_b_id" value="{{ $order->mr_buyer_b_id }}">
                                    </div>
                                </div>



                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="order_month"> Month<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" id="order_month" name="order_month" class="col-sm-4 monthpicker" placeholder="Month" value="{{ $order->order_month }}" data-validation="required" />

                                        <label class="col-sm-3" style="text-align: right">Year<span style="color: red">&#42;</span></label>
                                        <input type="text" id="order_year" name="order_year"  class="col-sm-4 yearpicker" placeholder="Year" value="{{ $order->order_year }}" data-validation="required" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="order_qty"> Quantity<span style="color: red">&#42;</span> </label>
                                    <!-- This is the actual Reservation Quantity -->
                                    <input type="hidden" class="qty_check" name="qty_check" id="qty_check" value="{{ $order->res_quantity }}">
                                    <!-- This Quantity will be used to check if order qty is greater than reservation qty -->
                                    <div class="col-sm-9">
                                        <input type="text" id="order_qty" name="order_qty" value="{{ $order->order_qty }}" data-validation=" required length number" data-validation-length="1-11" placeholder="Quantity" class="form-control"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="order_delivery_date"> Delivery Date <span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" id="order_delivery_date" name="order_delivery_date" class="form-control datepicker" value="{{ date('Y-m-d', strtotime($order->order_delivery_date)) }}" placeholder="Delivery Date" data-validation="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 no-padding-right">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="pcd" >PCD{{-- <span style="color: red">&#42;</span> --}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="pcd" id="pcd" class="col-sm-12 datepicker" placeholder="Enter Date" value="{{ $order->pcd }}">
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="mr_brand_br_id" >Brand<span style="color: red">&#42;</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="mr_brand_name" name="mr_brand_name" value="{{ $order->br_name }}" class="form-control" disabled/>
                                        <input type="hidden" name="mr_brand_br_id" value="{{ $order->mr_brand_br_id }}">
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="mr_season_se_id"> Season <span style="color: red">&#42;</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="se_name" name="se_name" value="{{ $order->se_name }}" class="form-control" disabled/>
                                        <input type="hidden" name="mr_season_se_id" value="{{ $order->mr_season_se_id }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="mr_style_stl_id"> Style <span style="color: red">&#42;</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="mr_style_stl_no" name="mr_style_stl_no" value="{{ $order->stl_no }}" class="form-control" disabled/>
                                        <input type="hidden" id="mr_style_stl_id" name="mr_style_stl_id" value="{{ $order->mr_style_stl_id }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="order_ref_no"> Reference No<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" id="order_ref_no" name="order_ref_no" class="form-control" placeholder="Reference No" value="{{ $order->order_ref_no }}" data-validation="required length" data-validation-length="1-60" readonly/>
                                    </div>
                                </div>
                            </div>

                            @include('merch.common.save-btn-section')
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

        $('.btn-success').on('click', function(){
            var sum = 0;
            var total_qty= parseInt($('#order_qty').val());
            if(total_qty > 0){
                var projected_qty= parseInt($("#qty_check").val());
                if(total_qty> projected_qty){
                    alert('Total quantity can not greater than Projected quantity');
                    $('#order_qty').val(projected_qty);
                    return false;
                }
            }else{
                alert('Quantity greater then 0');
                return false;
            }

        });
    });
</script>
@endsection
