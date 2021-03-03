@extends('merch.index')
@section('content')

<div class="main-content">
<style type="text/css">
    input{height:34px !important;}
    tr#chart_header > th {
        width: 20%;
    }
    .steps-div{
        border-left:none!important;
    }
    .order-content .panel-title {margin-top: 3px; margin-bottom: 3px;}
    .order-content .panel-title a{font-size: 15px; display: block;}
    .select2{width: 100% !important;}
    .panel-group { margin-bottom: 5px;}
    .min_sal{height: auto !important;}
    .max_sal{height: auto !important;}
    h3.smaller {font-size: 13px;}
    .header {margin-top: 0;}
    .text-right{display: inline; text-align: right;}

    @media only screen and (max-width: 480px) {

        #purchase_table{display: block; overflow-x: auto; width: 100%;}
    }
</style>
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
                <li class="active"> Order Edit & PO</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="panel panel-success">
                <div class="panel-heading"><h6>Order Edit & PO
                    <div class="text-right pull-right">
                        <a href='{{ url("merch/reservation/reservation_edit/$order->res_id") }}'  rel='tooltip' data-tooltip-location='top' data-tooltip='Reservation' style="margin-right: 10px;"><i class="fa fa-briefcase bigger-150" style="color: forestgreen;"></i> </a>
                        @if($isBom)
                        <a href='{{ url("merch/order_bom/".$order->order_id."/create") }}' class="btn btn-xx btn-success"  rel='tooltip' data-tooltip-location='top' data-tooltip='Edit Order BOM'><i class="glyphicon glyphicon-pencil"></i> Edit Order BOM</a>
                        @else
                        <a href='{{ url("merch/order_bom/".$order->order_id."/create") }}' class="btn btn-xx btn-success"  rel='tooltip' data-tooltip-location='top' data-tooltip='Add Order BOM'><i class="glyphicon glyphicon-bold"></i> Add Order BOM</a>
                        @endif
                        <a href='{{ url("merch/orders/order_list") }}' class="btn btn-xx btn-info" rel='tooltip' data-tooltip-location='top' data-tooltip='Order List'><i class="glyphicon glyphicon-th-list"></i> Order List</a>
                        <a href='{{ url("merch/orders/order_copy/".$order->order_id) }}' class="btn btn-xx btn-primary"  rel='tooltip' data-tooltip-location='top' data-tooltip='Order Copy'><i class="glyphicon glyphicon-copy"></i> Order Copy</a>
                    </div>

                </h6>
                </div>

                <div class="panel-body">
                    {!!$steps!!}

                    <div id="accordion" class="accordion-style panel-group">
                        <div class="panel panel-info">
                            <div class="panel-heading order-content">
                                <h2 class="panel-title">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#individual" aria-expanded="false">
                                        <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                        &nbsp;Order Edit
                                    </a>
                                </h2>
                            </div>

                            <div class="panel-collapse collapse" id="individual" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body">
                                    <h3 class="header smaller lighter green">
                                        <i class="ace-icon fa fa-bullhorn"></i>
                                        All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                                    </h3>
                                    <div class="row">

                                        <div class="col-xs-12">
                                            <form class="form-horizontal" role="form" method="post" action="{{ url('merch/orders/order_update') }}">
                                                {{ csrf_field() }}

                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

                                                    <input type="hidden" name="order_id" id="order_id" value="{{ $order->order_id }}">
                                                    <input type="hidden" name="res_id" value="{{ $order->res_id }}">

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="order_code"> Order No<span style="color: red">&#42;</span> </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="order_code" name="order_code" class="col-xs-12" value="{{ $order->order_code }}" readonly />
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="hr_unit_name">Unit<span style="color: red">&#42;</span> </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="hr_unit_name" name="hr_unit_name" value="{{ $order->hr_unit_name }}" class="col-xs-12" disabled/>
                                                            <input type="hidden" name="unit_id" value="{{ $order->unit_id }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="mr_buyer_name" >Buyer Name<span style="color: red">&#42;</span> </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="mr_buyer_name" name="mr_buyer_name" value="{{ $order->b_name }}" class="col-xs-12" disabled/>
                                                            <input type="hidden" name="mr_buyer_b_id" value="{{ $order->mr_buyer_b_id }}">
                                                        </div>
                                                    </div>

                                                    <!-- <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="mr_brand_br_id" >Brand<span style="color: red">&#42;</span></label>
                                                        <div class="col-sm-8">
                                                            {{ Form::select('mr_brand_br_id', $brandList, $order->mr_brand_br_id, ['id'=> 'mr_brand_br_id', 'placeholder' => 'Select Brand', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                                        </div>
                                                    </div> -->


                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="pcd" >PCD{{-- <span style="color: red">&#42;</span> --}}</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="pcd" id="pcd" class="col-sm-12 datepicker" placeholder="Enter Date" value="{{ $order->pcd }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="order_month"> Month<span style="color: red">&#42;</span> </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="order_month" name="order_month" class="col-xs-4 monthpicker no-margin" placeholder="Month" value="{{ $order->res_month }}" data-validation="required"/>

                                                            <label class="col-xs-3" style="text-align: right">Year<span style="color: red">&#42;</span></label>
                                                            <input type="text" id="order_year" name="order_year"  class="col-xs-4 yearpicker no-margin" placeholder="Year" value="{{ $order->res_year }}" data-validation="required"/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="mr_season_se_id"> Season <span style="color: red">&#42;</span></label>
                                                        <div class="col-sm-8">
                                                            {{ Form::select('mr_season_se_id', $seasonList, $order->mr_season_se_id, [ 'id'=> 'mr_season_se_id', 'placeholder' => 'Select Season', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="mr_style_stl_id"> Style <span style="color: red">&#42;</span></label>
                                                        <div class="col-sm-8">
                                                            {{ Form::select('mr_style_stl_id', $styleList, $order->mr_style_stl_id, [ 'id'=> 'mr_style_stl_id', 'placeholder' => 'Select Style', 'style'=> 'width: 100%', 'data-validation' => 'required', 'disabled'=> "disabled"]) }}
                                                        </div>
                                                    </div>


                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="order_ref_no"> Reference No<span style="color: red">&#42;</span> </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="order_ref_no" name="order_ref_no" class="col-xs-12" placeholder="Reference No" value="{{ $order->order_ref_no }}" data-validation="required length" data-validation-length="1-60"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="order_qty"> Quantity<span style="color: red">&#42;</span> </label>
                                                        <!-- This is the actual Reservation Quantity -->
                                                        <input type="hidden" class="qty_check" name="qty_check" id="qty_check" value="{{ $order->res_quantity }}">
                                                        <!-- This Quantity will be used to check if order qty is greater than reservation qty -->
                                                        <div class="col-sm-8">
                                                            <input type="text" id="order_qty" name="order_qty" value="{{ $order->order_qty }}" data-validation="required length number" data-validation-length="1-11" placeholder="Quantity" class="col-xs-12"/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label no-padding-right" for="order_delivery_date"> Delivery Date <span style="color: red">&#42;</span> </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="order_delivery_date" name="order_delivery_date" class="col-xs-12 datepicker" value="{{ date('Y-m-d', strtotime($order->order_delivery_date)) }}" placeholder="Delivery Date" data-validation="required"/>
                                                        </div>
                                                    </div>

                                                    <div class="clearfix form-actions no-padding">
                                                        <div class="col-sm-offset-4 col-sm-8 text-right no-padding">
                                                            <button class="btn btn-info btn-xs btn-round" type="submit">
                                                                <i class="ace-icon fa fa-check bigger-110"></i> Update
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
                            </div>
                        </div>
                        <div class="panel panel-info">
                            <div class="panel-heading order-content">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#multi-search">
                                        <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                        &nbsp;Purchase Order (PO)
                                    </a>
                                </h4>
                            </div>

                            <div class="panel-collapse collapse in" id="multi-search">
                                <div class="panel-body">
                                    @if($ck_BOM_Cost['bom'] && $ck_BOM_Cost['costing'])
                                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/orders/order_po_store') }}">
                                        {{ csrf_field() }}
                                        <div class="space-8"></div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                                                    <div class="widget-header">
                                                       {{--  <h3>Purchse Order(PO)</h3> --}}
                                                        <button type="button" class="btn btn-xs btn-primary btn-round add_po" id="add_po" data-totalfob="{{$order->agent_fob}}"><i class="glyphicon  glyphicon-plus" ></i>Add Purchse Order(PO)</button>
                                                        <a href='{{ url("merch/orders/po_bom_list") }}' target="_blank" class="btn btn-xs btn-info btn-round" rel='tooltip' data-tooltip-location='top' data-tooltip="PO BOM List"><i class="glyphicon glyphicon-th-list"></i> PO BOM List</a>
                                                    </div>

                                            </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-responsive table-hover" id="purchase_table">
                                                    <thead class="thead-info">
                                                        <tr>
                                                            <th class="text-center">PO No</th>
                                                            <th class="text-center">Country</th>
                                                            <th class="text-center" width="140px;">Port</th>
                                                            <th class="text-center">Color</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Remark</th>
                                                            <th class="text-center">FOB</th>
                                                            <th class="text-center">Ex-fty</th>
                                                            <th width="140px;" class="text-center">Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody id="po_table_body">
                                                        @foreach($purchase_orders AS $po)
                                                        {{-- <input type="hidden" name="edit_check" id="edit_check" value="edit"> --}}
                                                        <tr class="" id="{{$po->po_id}}" >
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <span class="show_po_text">{{$po->po_no}}</span>
                                                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                                                <input type="hidden" class="po_id" name="po_id" value="{{$po->po_id}}">
                                                                <input type="text" id="po_number" name="po_number[]" style="height:34px;" value="{{$po->po_no}}" class="hide col-xs-12 po_input form-control po_number" placeholder="PO Number" data-validation="required length" data-validation-length="1-20" data-validation-regexp="^([a-z A-Z0-9]+)$" />
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <span class="show_po_text">{{$po->cnt_name}}</span>
                                                                {{ Form::select('po_delivery_country[]', $countryList, $po->po_delivery_country, ['id'=>'po_delivery_country', 'placeholder'=> 'Delivery Country', 'class' => 'hide no-select form-control delivery_country po_input', 'style'=>'width:97%', 'data-validation'=>'required']) }}
                                                            </td>
                                                            <td>
                                                                <span class="show_po_text">{{$po->port_name}}</span>
                                                                <select class="hide no-select form-control delivery_port po_input" style="width:90%;" name="delivery_port[]" required="required">
                                                                    <option>Ports</option>
                                                                    <option value="{{$po->port_id}}" selected="selected">{{$po->port_name}}</option>
                                                                </select>
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                {{$po->clr_name}}
                                                                <input type="hidden" name="clr_ids[]" id="clr_ids" value="{{$po->clr_id}}" class="hide col-xs-12 po_input form-control" readonly="readonly">
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <span class="show_po_text">{{$po->po_qty}}</span>
                                                                <input type="text" id="po_qty" name="po_qty[]" style="height:34px;" class="hide col-xs-12 po_input form-control po_qty" value="{{$po->po_qty}}" placeholder="Total PO Quantity" data-validation="required number length" data-validation-length="1-11" data-clr_id="{{$po->clr_id}}"/>
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <span class="show_po_text">{{$po->remarks}}</span>
                                                                <textarea style="width: 143px; height: 34px;" name="po_remarks[]" id="po_remarks" class="hide col-xs-12 po_input form-control po_remarks" placeholder="PO Remarks"  data-validation="required length custom" data-validation-length="1-60"  data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$" data-validation-optional="true" style="height: ; width: 160px; padding-left: 10px">{{$po->remarks}}</textarea>
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <span class="show_po_text">{{$po->country_fob}}</span>
                                                                <input type="text" id="country_fob" name="country_fob[]" value="{{$po->country_fob}}" style="height:34px;" class="hide col-xs-12 po_input form-control country_fob" placeholder="Country FOB" data-validation="custom"  data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$"/>
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <span class="show_po_text">{{$po->po_ex_fty}}</span>
                                                                <input type="text" id="po_ex_fty" name="po_ex_fty[]" class="hide datetimepicker po_ex_fty col-xs-12 po_input form-control" value="{{$po->po_ex_fty}}" placeholder="Ex-Fty Date" style="height:34px;" data-validation="required" autocomplete="off" />
                                                            </td>
                                                            <td style="margin: 0px; padding: 4px;" class="text-center">
                                                                <a href="{{ url('merch/order_bom/'.$order->order_id.'/create/'.$po->po_id) }}" class="btn btn-xs btn-info add_po_item" data-toggle="tooltip" data-tooltip="Add PO Item" target="_blank"><i class="ace-icon fa fa-plus bigger-120"></i></a>
                                                                <a href="#" class="btn btn-xs btn-success edit_po" data-toggle="tooltip" data-tooltip="Edit"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                                <a href="{{ url('merch/orders/po/'.$order->order_id.'/'.$po->po_id.'/delete') }}" type="button" class='btn btn-xs btn-danger' onclick="return confirm('Are you sure you want to delete this PO?');" data-tooltip="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                                                <a href="#" class="btn btn-warning btn-xs btn-round po_input hide color_select_modal_button" style="height: 33px;" data-toggle="modal" id="colorButton" data-tooltip="Color Add">Color</a>
                                                            </td>
                                                        </tr>

                                                        @endforeach
                                                        <tr id="new_row"></tr>
                                                    </tbody>
                                                </table>
                                                <table class="table table-striped table-bordered table-responsive table-hover"
                                                    style="width: 40%; margin-left: 20%;">
                                                    <tbody>
                                                        <tr>
                                                            <th align="right">
                                                                Order Qty:
                                                                <span style="color: maroon;" id="odr_qty_view">{{$order->order_qty}}</span>
                                                            </th>
                                                            <th align="center" >
                                                                Total PO Qty:
                                                                <span style="color: maroon;" id="po_qty_total"></span>
                                                            </th>

                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- Color Size row --}}
                                        <div class="space-8"></div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                                                <div class="widget-header">
                                                    <h3 class="text-left" id="breakdown_of_po">Size Color Breakdown</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-responsive table-hover" id="color_size_breakdown" style="display: block; overflow-x: auto; width: 100%;">
                                                    <thead class="thead-info">
                                                        <tr id="chart_header">
                                                            <th>PO No</th>
                                                            <th>Country</th>
                                                            <th>Color</th>
                                                            <th>Qty</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach($purchase_orders AS $eachPo)
                                                        <?php $it=0; ?>
                                                            @foreach($SelectedColors AS $selCol)
                                                                @if($selCol->po_id==$eachPo->po_id)
                                                                    <tr id="row_{{$eachPo->po_id}}" class="row_{{$eachPo->po_id}} breakdown_row row_determiner_{{$selCol->po_sub_style_id}}" color-id="{{$selCol->clr_id}}" row-determiner="{{$selCol->po_sub_style_id}}">
                                                                        @if($it==0)
                                                                        <td rowspan="{{ $eachPo->rowSpan }}">
                                                                            {{ $eachPo->po_no }}
                                                                        </td>
                                                                        <td rowspan="{{ $eachPo->rowSpan }}">{{ $eachPo->cnt_name }}</td>
                                                                        @endif
                                                                        <td>
                                                                           <a href="{{url('merch/orders/po_bom/'.$eachPo->po_id.'/'.$order->order_id.'/'.$selCol->clr_id.'/view')}}" target="_blank" class="btn btn-primary btn-xx" data-tooltip="PO BOM" data-tooltip-location="right" style="border-radius: 30px;"><i class="fa fa-plus bigger-120"></i></a>|

                                                                            {{ $selCol->clr_name }}

                                                                            {{-- <span class="col-xs-3">{{ $selCol->clr_name }}</span> --}}
                                                                            {{-- <div class="col-xs-9">
                                                                                <span class="color_qty_span">{{ $selCol->po_sub_style_qty }}</span>
                                                                            </div> --}}
                                                                        </td>
                                                                        <td>
                                                                            <span class="color_qty_span">{{ $selCol->po_sub_style_qty }}</span>
                                                                        </td>
                                                                        @foreach($selCol->sizes AS $size)
                                                                            <td><span class="size_qty_span">{{ $size->qty }}</span>
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                    <?php $it++; ?>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                                <table class="table table-striped table-bordered table-responsive table-hover hide" id="color_size_breakdown_edit_table" style="display: block; overflow-x: auto; width: 100%;" >
                                                    <thead class="thead-info">
                                                        <tr id="edit_chart_header">
                                                            <td>Color</td>
                                                            <td>Total</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="color_size_breakdown_edit_rows"></tbody>
                                                </table>
                                                <div id="breakdown_footer" class="widget-footer text-right hide">
                                                    <button id="breakdown_submit" type='submit' class='btn btn-primary btn-xs btn-round breakdown_submit'>Save</button>
                                                 </div>
                                            </div>
                                        </div>
                                    </form>
                                    @else
                                        <h4 style="color: darkgrey;">Please Complete Order BOM and Costing</h4>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            {{-- <div class="page-header roe">
                <h1 class="col-sm-8">Order <small><i class="ace-icon fa fa-angle-double-right"></i> Order Edit</small></h1>
                <div class="text-right">
                    <a href='{{ url("merch/orders/purchase_order/".$order->order_id) }}' class="btn btn-sm btn-primary" title="Add Purchase Order"><i class="glyphicon  glyphicon-plus"></i></a>
                    @if($isBom)
                    <a href='{{ url("merch/order_bom/".$order->order_id."/create") }}' class="btn btn-sm btn-success" title="Edit BOM"><i class="glyphicon glyphicon-pencil"></i></a>
                    @else
                    <a href='{{ url("merch/order_bom/".$order->order_id."/create") }}' class="btn btn-sm btn-success" title="BOM"><i class="glyphicon glyphicon-bold"></i></a>
                    @endif
                    <a href='{{ url("merch/orders/order_list") }}' class="btn btn-sm btn-info" title="Order List"><i class="glyphicon glyphicon-th-list"></i></a>
                    <a href='{{ url("merch/orders/order_copy/".$order->order_id) }}' class="btn btn-sm btn-primary" title="Order Copy"><i class="glyphicon glyphicon-copy"></i></a>
                </div>
            </div> --}}

        </div><!-- /.page-content -->
    </div>
</div>


<!-- Color Select Modal -->
<div class="modal fade" id="colorModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-xs" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Available Colors</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        <div class="row" style="padding: 20px;">
            {!! $colorListData !!}
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="colorModalDone" class="btn btn-primary btn-sm colorModalDone">Done</button>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript">
    var stl_id= $("#mr_style_stl_id").val();
    var po_quantity= 0;
    $(document).ready(function(){
        $("body").on('click', '.po_ex_fty', function(){
            $(this).datetimepicker({
                format: 'YYYY-MM-DD',
                allowInputToggle: true
            });
        });
        //Previous po qty sum and view
        var add=0;
        $('.po_qty').each(function(u, v){
            // console.log(u, parseInt(v.value));
            add += parseInt(v.value);
        });
        $('#po_qty_total').html(add);

        $('body').on('change','.delivery_country', function(){
            var cnt_id = $(this).val();
            var par = $(this).parent();
            // console.log(cnt_id);
            $.ajax({
                url: '{{url('merch/order_edit/get_port_country_wise')}}',
                type: 'get',
                dataType: 'json',
                data:{cnt_id: cnt_id},
                success: function(data){
                    // console.log(data);
                    var ports = "<option value=\"\">Select Port</option>";
                    for(var i=0; i<data.length; i++){
                        ports +="<option value=\""+data[i].id+"\" >"+data[i].port_name+"</option>";
                    }

                    par.next().children('.delivery_port').html(ports);

                },
                error: function(data){
                    alert('Error', data);
                }
            });
        });


        //Order Total quantity can not be greater than Projected quantity
        $('#order_qty').on('keyup', function(){
            $('#odr_qty_view').html($(this).val());
            var sum = 0;
            var total_qty= parseInt($(this).val());
            var projected_qty= parseInt($("#qty_check").val());
            var current_qty= "{{ $order->order_qty }}";
            current_qty= parseInt(current_qty);
            if(total_qty> projected_qty+current_qty){
                alert('Can not greater than Remaining Reservation Quantity('+(projected_qty+current_qty)+")");
                $(this).val(current_qty);
            }
        });

        //get size list for making color size breakdown table columns
        sizeList = function () {
            var tmp = null;
            $.ajax({
                    url : "{{ url('merch/orders/get_size_list') }}",
                    type: 'get',
                    async: false,
                    data: {stl_id},
                    success: function(data)
                    {
                       tmp=data;
                    }
            });
            return tmp;
        }();

        $.each(sizeList, function(i, v){
            var str= "<th>"+v+"</th>";
            $("#chart_header").append(str);
            $("#edit_chart_header").append(str);
        });

        //get PO Quantity For further Calculation
        $("body").on('keyup', ".po_qty", function(e){
            var total_po_qty=0;
            var that;
            var x=0;
            var clr_id = $(this).data('clr_id');
            $("input[name=\"color_qty["+clr_id+"]\"").val($(this).val());

            $("body").find('.po_qty').each(function(){
                x= parseInt($(this).val());
                po_quantity= x;
                 that= $(this);
                if(!isNaN(x)){
                    total_po_qty+=x;
                }
            });
            var ordered_qty= parseInt($("#order_qty").val());
            if(total_po_qty > ordered_qty){
                $('#po_qty_total').html('--');
                $("input[name=\"color_qty["+clr_id+"]\"").val('');
                $(this).val('');
                alert("PO quantity must be less or equal to Order quantity!");
                // that.val(parseInt(ordered_qty-(total_po_qty-x)));
                // po_quantity= parseInt(ordered_qty-(total_po_qty-x));
                // console.log(that.val());
                e.preventDefault();
            }
            else{
                $('#po_qty_total').html(total_po_qty);
            }
        });



        //Add Po
        //Add new Purchase order row
        $("#add_po").on("click", function(){

            var fob = $('#add_po').data('totalfob');
            // console.log(fob);

            $('body').find('.po_input').each(function(){
                $(this).addClass('hide');
                $(this).prop('disabled', true);
            });

            $('body').find('.show_po_text').each(function(){
                $(this).removeClass('hide');
            });

            $("#breakdown_footer").removeClass("hide");
            var add_po_code= '<td style="margin: 0px; padding: 4px;">\
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">\
                            <input type="hidden" class="po_id" id="po_id" name="po_id" value="-1">\
                            <input type="text" id="po_number" name="po_number" class="col-xs-12 po_input form-control po_number" placeholder="PO Number" data-validation="required custom length" style="height:34px;" data-validation-length="1-20" data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$"/>\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <a href="#" class="btn btn-warning btn-xs btn-round" style="height: 33px;" data-toggle="modal" id="colorButton" data-tooltip="Color Add">Color</a>\
                            </td>';
            // var add_po_code= '<td style="margin: 0px; padding: 4px;">\
            //                 <input type="hidden" name="order_id" value="{{ $order->order_id }}">\
            //                 <input type="hidden" class="po_id" id="po_id" name="po_id" value="-1">\
            //                 <input type="text" id="po_number" name="po_number" class="col-xs-12 po_input form-control po_number" placeholder="PO Number" data-validation="required custom length" style="height:34px;" data-validation-length="1-20" data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$"/>\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     {{ Form::select("po_delivery_country", $countryList, null, ["id"=>"po_delivery_country", "placeholder"=> "Delivery Country", "class" => "no-select po_input form-control delivery_country", "style"=>"width:97%", "data-validation"=>"required"]) }}\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     <select class="no-select form-control delivery_port" name="delivery_port" id="delivery_port" required="required">\
            //                         <option>Ports</option>\
            //                     </select>\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     color name\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     <input type="text" id="po_qty" name="po_qty" class="col-xs-12 po_input form-control po_qty" style="height:34px;" placeholder="Total PO Quantity" data-validation="required number length" data-validation-length="1-11"/>\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     <textarea style="width: 143px; height: 34px;" name="po_remarks" id="po_remarks" class="col-xs-12 po_input form-control po_remarks" placeholder="PO Remarks"  data-validation="required length custom" data-validation-length="1-60"  data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$" data-validation-optional="true" style="height: 34px; width: 160px; padding-left: 10px"></textarea>\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     <input type="text" id="country_fob" style="height:34px;" name="country_fob" class="col-xs-12 po_input form-control country_fob" placeholder="Country FOB" data-validation="custom"  data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$"/>\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     <input type="text" name="po_ex_fty" id="po_ex_fty" style="height:34px;" class="datetimepicker po_ex_fty col-xs-12 po_input form-control" placeholder="Ex-Fty Date" data-validation="required" autocomplete="off" />\
            //                 </td>\
            //                 <td style="margin: 0px; padding: 4px;">\
            //                     <a href="#" class="btn btn-warning btn-xs btn-round" style="height: 33px;" data-toggle="modal" id="colorButton" data-tooltip="Color Add">Color</a>\
            //                 </td>';
            $("#new_row").html(add_po_code);
            $(".country_fob").val(fob);
            // $('.datetimepicker').datetimepicker({ format: 'YYYY-MM-DD' });

            // var $firstCal = $("#new_row").find('.po_ex_fty');
            // $firstCal.removeClass('cal').datepicker();

            $("#color_size_breakdown").addClass('hide');

            $("#color_size_breakdown_edit_table").removeClass("hide");

            $("#color_size_breakdown_edit_rows").html("");
            $("#breakdown_of_po").text("Size Color Breakdown");
        });



        //==========PO Edit Part ================//

        //Purchase Order Edit Button events
        /*on click edit button
            * 1.hide new PO(if exists)
            * 2.hide other po edit option(if opened) except this
            * 3.disable other po edit input(if opened) except this
            * 4.Enable Breakdown edit option for this PO
            * 5. re-initialize po_quantity and color size breakdown quantity
        */

        $("body").on('click', '.edit_po', function(e){
            e.preventDefault();
            $('.color_select_modal_button').hide();

            var that= $(this).parent().parent();
            var parent_id= that.attr('id');

            //hide and disable other PO edit option
            that.parent().find('.po_input').each(function(){
                $(this).addClass('hide');
                $(this).prop('disabled', true);
            });
            that.parent().find('span').each(function(){
                $(this).removeClass('hide');
            });

            //disable all po_id
            that.parent().find(".po_id").each(function(){
                $(this).prop("disabled", true);
            });

            that.find('.po_id').removeAttr('disabled');

            //clear new row
            $("#new_row").html("");

            //show this PO edit option
            that.find('.po_input').each(function(){
                $(this).removeClass('hide');
                $(this).removeAttr('disabled');
            });
            that.find('span').each(function(){
                $(this).addClass('hide');
            });

            //hide breakdown show div
            $("#color_size_breakdown").addClass('hide');
            $("#color_size_breakdown_edit_table").removeClass("hide");

            //Show breakdown edit option for selected PO

            var order_id= parseInt($("#order_id").val());
            var po_id = parseInt(parent_id);
            var po_name= that.find('.po_number').val();
            var country_name= that.find('.delivery_country option:selected').text();

            $("#breakdown_of_po").text("Size Color Breakdown of "+po_name+" Country: "+country_name);
            $.ajax({
                    url : "{{ url('merch/orders/get_po_edit_options') }}",
                    type: 'get',
                    // async: false,
                    data: {stl_id, order_id, po_id},
                    success: function(data)
                    {
                        // console.log(data);
                        var edit_table_rows='';
                       $.each(data['po_subStyles'], function(i, color){
                            var row_code= '<tr class="breakdown_row row_'+color['clr_id']+'" color-id="'+color['clr_id']+'"><td><div class="input-group"><span class="input-group-addon">'+color['clr_name']+'</span><input type="text" class="form-control color_qty" name="color_qty['+color['clr_id']+']" value="'+color['po_sub_style_qty']+'" placeholder="Color Qty" data-validation="required number length" data-validation-length="1-11" readonly /></div></td>';

                            var totalSizeQty = 0;
                            var colorSizeCount = 0;
                            $.each(data['po_sizeQunatity'], function(j, size){
                                if(size['mr_po_sub_style_id'] == color['po_sub_style_id']){
                                    if(colorSizeCount===0) {
                                        $.each(data['po_sizeQunatity'], function(k, size1){
                                            if(size1['mr_po_sub_style_id'] == color['po_sub_style_id']){
                                                totalSizeQty += size1['qty'];
                                            }
                                        });
                                        row_code+='<td style="width: 5%"><input type="text" id="totalColorSizeQty_'+color['clr_id']+'" value="'+totalSizeQty+'" style="width: 20%;" disabled/></td>';
                                        totalSizeQty = 0;
                                        colorSizeCount += 1;
                                    }
                                    row_code+= '<td><input type="text" id="size_qty" name="size_qty['+color['clr_id']+']['+size['mr_product_size_id']+']" value="'+size['qty']+'" class="col-xs-12 form-control size_qty" placeholder="Size Qty" data-validation="required number length" data-validation-length="1-11"></td>';

                                }
                            });
                           row_code+='</tr>';
                           edit_table_rows+=row_code;

                       });
                       $("#color_size_breakdown_edit_rows").html(edit_table_rows);
                    }
            });

            $("#breakdown_footer").removeClass("hide");

            //reset po quantity and color quantity variable
            po_quantity= parseInt(that.find('.po_qty').val());
            var class_name= 'row_'+parent_id;
            var new_color_sum=0;
            $('.'+class_name).find('.color_qty').each(function(){
                new_color_sum+=parseInt($(this).val());
            });
            color_qty= new_color_sum;
        });


        //Open Color Select Modal with Color List
        var global_new_po_no_entry = "";
        var global_new_country_id_entry = "";
        var global_new_remarks_entry = "";
        var global_new_fob_entry = "";
        var global_new_ex_dt_entry = "";

        $('body').on('click', '#colorButton', function(){
            var par = $(this).parent().parent();
            var d = 1;
            par.find('td').each(function(u, v){
                // console.log("test",v,$(this).find(".po_number").val(), d++);
                var po = $(this).find(".po_number").val();
                if(typeof po !== 'undefined'){
                     global_new_po_no_entry = po;

                }
                global_new_fob_entry = $(this).find(".country_fob").val();

            });
            // console.log("Showing..", global_new_po_no_entry);

            // po_number
            // po_delivery_country
            // delivery_port
            // country_fob
            // po_ex_fty
        });

        $('body').on('click', '.color_select_modal_button', function(){
            $("#colorModal").find('.color_array').each(function(){
                $(this).prop("checked", false);
            });

            var po_id= $(this).parent().parent().attr('id');


            $.ajax({
                    url : "{{ url('merch/orders/get_selected_colors') }}",
                    type: 'get',
                    data: {po_id},
                    success: function(data)
                    {
                       $("#colorModal").find(".color_array").each(function(){
                            var x = parseInt($(this).val());
                            if(jQuery.inArray(x, data)>=0){
                                $(this).prop("checked", true);
                            }
                       });
                    }
            });
        });

        //If PO Number and Delivery country is selected then show the modal
        var colorModal = $("#colorModal");

        $('body').on('click', '#colorButton', function(e){
            e.preventDefault();
            $("#colorModal").find('.color_array').each(function(){
                $(this).prop("checked", false);
            });
            var existingColorsId = [];
            $("#color_size_breakdown_edit_rows").find('.breakdown_row').each(function(){
                    existingColorsId.push(parseInt($(this).attr('color-id')));
            });
            // console.log(existingColorsId);

            $("#colorModal").find(".color_array").each(function(){
                var x = parseInt($(this).val());
                if(jQuery.inArray(x, existingColorsId)>=0){
                    $(this).prop("checked", true);
                }
            });



            po_number= $(this).parent().parent().find(".po_number").val();
            delivery_country_id= $(this).parent().parent().find(".delivery_country").val();
            delivery_country_name= $(this).parent().parent().find(".delivery_country option:selected").text();
            // if((po_number!= null) && (delivery_country_id>=1)){
            //     colorModal.modal("show");
            // }
            // else
            // {
            //     alert("Please Input PO Number and Country!");
            // }

            if(po_number!= null){
                colorModal.modal("show");
            }
            else
            {
                alert("Please Input PO Number!");
            }
        });


        //Generate Color Size Breakdown
        $("body").on("click", ".colorModalDone", function(e) {

            var fob = $('#add_po').data('totalfob');

            var existingColorsId = [];
            // var existingColorsIdDeteminer = [];

            $("#color_size_breakdown_edit_rows").find('.breakdown_row').each(function(){
                    existingColorsId.push(parseInt($(this).attr('color-id')));
                    // existingColorsIdDeteminer.push(parseInt($(this).attr('row-determiner')));
            });
            // console.log(existingColorsId);

            selectedColorsId =[];
            selectedColorsName =[];
            colorModal.find('.modal-body input[type=checkbox]').each(function(i,v) {
                if ($(this).prop("checked") == true) {
                    selectedColorsId.push(parseInt($(this).val()));
                    selectedColorsName.push($(this).next('span').text());
                }
            });
            // console.log(selectedColorsId, selectedColorsName);

            var new_breakdown='';
            $("#new_row").html('');
            const dateToString = d => `${d.getFullYear()}-${('00' + (d.getMonth() + 1)).slice(-2)}-${('00' + d.getDate()).slice(-2)}`
            const myDate = new Date(Date.parse($('#order_delivery_date').val()))
            var oderDelivaryDate = dateToString(myDate);
            $.each(selectedColorsId, function(i, v){
                //Adding POs
                var add_po_code= '<tr><td style="margin: 0px; padding: 4px;">\
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">\
                            <input type="hidden" class="po_id" id="po_id" name="po_id" value="-1">\
                            <input type="text" id="po_number" name="po_number[]" value=\"'+global_new_po_no_entry+'\" class="col-xs-12 po_input form-control po_number" placeholder="PO Number" data-validation="required length" style="height:34px;" data-validation-length="1-20" data-validation-regexp="^([a-z A-Z0-9]+)$" />\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                {{ Form::select("po_delivery_country[]", $countryList, null, ["id"=>"po_delivery_country", "placeholder"=> "Delivery Country", "class" => "no-select po_input form-control delivery_country", "style"=>"width:97%", "data-validation"=>"required"]) }}\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <select class="no-select form-control delivery_port" name="delivery_port[]" id="delivery_port" required="required">\
                                    <option>Ports</option>\
                                </select>\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <input type="hidden" name="clr_ids[]" id=clr_ids value="'+selectedColorsId[i]+'"/>\
                                '+selectedColorsName[i]+'\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <input type="text" id="po_qty" name="po_qty[]" class="col-xs-12 po_input form-control po_qty" style="height:34px;" placeholder="Total PO Quantity" data-validation="required number length" data-validation-length="1-11" data-clr_id="'+selectedColorsId[i]+'"/>\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <textarea style="width: 143px; height: 34px;" name="po_remarks[]" id="po_remarks" class="col-xs-12 po_input form-control po_remarks" placeholder="PO Remarks"  data-validation="required length custom" data-validation-length="1-60"  data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$" data-validation-optional="true" style="height: 34px; width: 160px; padding-left: 10px"></textarea>\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <input type="text" id="country_fob" style="height:34px;" name="country_fob[]" class="col-xs-12 po_input form-control country_fob" placeholder="Country FOB" data-validation="custom"  data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$" value="'+fob+'"/>\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <input type="date" name="po_ex_fty[]" id="po_ex_fty" style="height:34px;" class="col-xs-12 po_input form-control datepicker" placeholder="Ex-Fty Date" data-validation="required" autocomplete="off" value="'+dateToString(myDate)+'"/>\
                            </td>\
                            <td style="margin: 0px; padding: 4px;">\
                                <a href="#" class="btn btn-warning btn-xs btn-round hide" style="height: 33px;" data-toggle="modal" id="colorButton" data-tooltip="Color Add">Color</a>\
                            </td><tr>';
                            // $('.datetimepicker').datetimepicker({ format: 'YYYY-MM-DD' });
                            $('#po_table_body').append(add_po_code);
                //Adding POs End..
                //Hiding the Add PO button
                $("#add_po").hide();

                var position= jQuery.inArray(v,existingColorsId);
                // console.log(position);
                if(position>=0){
                    new_breakdown+=$('.row_'+v)[0].outerHTML;
                    // console.log($('.row_'+v)[0].outerHTML);
                }
                else{
                    new_breakdown+= '<tr class="breakdown_row row_'+v+'" color-id="'+v+'"><td><div class="input-group"><span class="input-group-addon">'+selectedColorsName[i]+'</span><input type="text" class="form-control color_qty" name="color_qty['+v+']" value="" placeholder="Color Qty" data-validation="required number length" data-validation-length="1-11" readonly="readonly"/></div></td>';

                    new_breakdown+= '<td style="width: 5%"><input type="text" id="totalColorSizeQty_'+v+'" value="" style="width: 20%;" disabled/></td>';

                    $.each(sizeList, function(j, w){
                        new_breakdown+='<td><input type="text" id="size_qty" name="size_qty['+v+']['+j+']" value="0" class="col-xs-12 form-control size_qty" placeholder="Size Qty" data-validation="required number length" data-validation-length="1-11"></td>';
                    });

                    new_breakdown+='</tr>';
                }
            });
            $("#color_size_breakdown_edit_rows").html(new_breakdown);

            colorModal.modal('hide');
        });

        $(document).on("keyup", ".size_qty", function(e){
            var parentColorId = $(this).closest('tr').attr('color-id');
            var colorQty = $(this).closest('tr').find('.color_qty').val();
            var eachRowSizeTotalQty = 0;
            $(this).closest('tr').find('.size_qty').each(function(){
                eachRowSizeTotalQty += parseInt($(this).val());
            });
            if(eachRowSizeTotalQty > colorQty) {
                $(this).closest('tr').find('#totalColorSizeQty_'+parentColorId).attr('style','border: 2px solid red; width: 20%;');
            } else if(colorQty > eachRowSizeTotalQty ) {
                $(this).closest('tr').find('#totalColorSizeQty_'+parentColorId).attr('style','border: 2px solid orange; width: 20%;');
            } else {
                $(this).closest('tr').find('#totalColorSizeQty_'+parentColorId).attr('style','border: 2px solid green; width: 20%;');
            }
            $(this).closest('tr').find('#totalColorSizeQty_'+parentColorId).val(eachRowSizeTotalQty);
        });

        $("#breakdown_submit").on("click", function(e){
            var total_color_qty=0;
            var size_qty_check=true;
            // console.log(po_quantity);

            var row_id = 0;
            $('#color_size_breakdown_edit_rows').find('.breakdown_row').each(function(){
                var this_color_qty = parseInt($(this).find('.color_qty').val());
                total_color_qty += this_color_qty;

                var this_size_qty = 0;
                $(this).find('.size_qty').each(function(){
                    this_size_qty += parseInt($(this).val());
                });

                if(this_size_qty> this_color_qty){
                    size_qty_check = false;
                }
            });
            // console.log("color "+total_color_qty);
            // console.log("po "+po_quantity);

            // if(total_color_qty!= po_quantity){
            //     alert("PO quantity and Color Quantity did not match!");
            //     e.preventDefault();
            // }

            if(size_qty_check==false){
                alert("Size quantity can not be greater than color quantity!");
                e.preventDefault();
            }
        });

    });
</script>
@endsection
