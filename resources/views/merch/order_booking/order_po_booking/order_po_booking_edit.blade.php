@extends('merch.index')
@section('content')
@push('css')
    <style type="text/css">
        td input[type=text], input[type=number], .text-custom-style {
            color: #000;
            font-weight: bold;
            text-align: center;
            border: navajowhite;
            cursor: default;
        }
        .addBackground::after{
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: #31708f;
            opacity: .4;
        }
        span.label {
            width: 100%;
            height: 100%;
        }
    </style>
@endpush

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Order Booking </a>
                </li>
                <li>
                    Order Booking Edit
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="panel panel-info" id="orderSection">
                <div class="panel-heading page-headline-bar"><h5> Order Booking Edit Form</h5> </div>
                <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/order_po_booking/update') }}">
                        {{ csrf_field() }}
                        <!-- 1st ROW -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="panel panel-info">
                                    <div class="panel-heading page-headline-bar"><h5> Order Booking</h5> </div>
                                    <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
                                        <div class="disabled-input-section">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right"> Reference No. </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" value="{{ $poBooking->booking_ref_no }}" readonly="readonly" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right"> Buyer </label>
                                                <div class="col-sm-8">
                                                    {{ Form::select("mr_buyer_b_id", $buyerOrderList, $poBooking->mr_buyer_b_id, ['class'=>'col-xs-12 form-control disabled-input', 'id'=>'buyer_id', 'placeholder'=>'Select Buyer', 'disabled' => 'disabled']) }}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right"> Unit </label>
                                                <div class="col-sm-8">
                                                    {{ Form::select("unit_id", $unitList, $poBooking->unit_id, ['class'=>'col-xs-12 form-control disabled-input', 'id'=>'unit_id', 'placeholder'=>'Select Unit', 'disabled' => 'disabled']) }}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right"> Supplier </label>
                                                <div class="col-sm-8">
                                                    {{ Form::select("mr_supplier_sup_id", $supplierList, $poBooking->mr_supplier_sup_id, ['class'=>'col-xs-12 form-control disabled-input', 'id'=>'mr_supplier_sup_id', 'placeholder'=>'Select Supplier', 'disabled' => 'disabled']) }}

                                                    <input type="hidden" name="mr_supplier_sup_id" value="{{ $poBooking->mr_supplier_sup_id }}">
                                                    <input type="hidden" name="unit_id" value="{{ $poBooking->unit_id }}">
                                                    <input type="hidden" name="po_booking_id" value="{{ $poBooking->id }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right"> Shipment Date </label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="delivery_date" id="delivery_date" class="form-control datepicker disabled-input" value="{{ $poBooking->delivery_date }}" placeholder="Shipment Date" data-validation='required' />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="panel panel-info">
                                    <div class="panel-heading page-headline-bar"><h5> Order List</h5> </div>
                                    <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px; overflow: auto; height: 400px;">
                                        {{-- Orders --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="">
                                                <thead>
                                                    <tr>
                                                        <th>Order No</th>
                                                        <th>Supplier/Item</th>
                                                        <th>Required Quantity</th>
                                                        <th>Booking Quantity</th>
                                                        <th>Percentage</th>
                                                        <th>Delivery Date</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="order_list2">{!! $unitOrderData !!}</tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading page-headline-bar"><h5> BOM'S</h5> </div>
                            <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
                                <!-- 2nd ROW -->
                                <div class="row">
                                    <!-- ORDERS -->
                                    <div class="col-sm-12 table-responsive">
                                        <table class="table table-bordered table-striped" id="boms_table">
                                            <thead>
                                                <tr>
                                                    <th>Order No</th>
                                                    <th>PO No</th>
                                                    <th>Main Category</th>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Article</th>
                                                    <th>Supplier</th>
                                                    <th>Uom</th>
                                                    <th>Unit Price</th>
                                                    <th>Color</th>
                                                    <th>Size</th>
                                                    <th>Qty</th>
                                                    <th>Req Qty</th>
                                                    <th>Total Value</th>
                                                    {{-- <th>Booking Qty</th> --}}
                                                </tr>
                                            </thead>
                                            {{-- fabric category --}}
                                            <tbody id="supplier_order_item_list">{!! $tableData !!}</tbody>
                                        </table>
                                    </div>
                                    {{-- if booking exist --}}
                                    @if($orderBookingExist)
                                        <div class="col-sm-12 text-center">
                                            <h3 class="btn-success" style="padding: 5px 0">Booking Confirmed</h3>
                                        </div>
                                    @else
                                        <div class="col-sm-offset-3 col-sm-9" id="submit_btn" style="display: none;">
                                            <br>
                                            <!-- SUBMIT -->
                                            {!! Custom::btn('Update & Generate',false) !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@push('js')
    <script type="text/javascript">
        var order_list = new Array();
        <?php foreach($poBookingDetails as $key => $val){ ?>
            order_list.push('<?php echo $val; ?>');
        <?php } ?>
        if(order_list.length > 0) {
            $('#submit_btn').show();
        }
        $(document).ready(function(){
            $('.qty_rm_class').each(function(i,v){
                var qtyValue = $(this).val();
                if(qtyValue == 0) {
                    var classN = $(this).data('rm');
                    // $('.'+classN).siblings('span').remove();
                    // $('.'+classN).siblings('hr').remove();
                    // $('.'+classN).remove();
                }
            });
        });
        $(document).on('click','.supplier_order_checkbox',function() {
            var order_id = $(this).data('oid');
            var supplier_id = $(this).data('supid');
            if($(this).is(":checked")) {
                $('#orderSection').addClass('addBackground');
                order_list.push(order_id+'_'+supplier_id);
                if(order_list.length == 1) {
                    $('#submit_btn').show();
                }
                $.ajax({
                    url: '<?= url('merch/order_po_booking/getPoOrderItem'); ?>',
                    type: 'get',
                    data: {
                        order_id: order_id,
                        supplier_id: supplier_id
                    },
                    success: function(res){
                        // console.log(res);
                        $('#supplier_order_item_list').hide().fadeIn('slow').append(res);
                        $('.qty_rm_class').each(function(i,v){
                            var qtyValue = $(this).val();
                            if(qtyValue == 0) {
                                var classN = $(this).data('rm');
                                $('.'+classN).siblings('span').remove();
                                $('.'+classN).siblings('hr').remove();
                                $('.'+classN).remove();
                            }
                        });
                        $('#orderSection').removeClass('addBackground');
                    },
                    error: function() {
                        console.log('error occured.');
                        $('#orderSection').removeClass('addBackground');
                    }
                });
            } else {
                $('.tr_order_id_'+order_id).remove();
                order_list = jQuery.grep(order_list, function(value) {
                      return value != order_id+'_'+supplier_id;
                    });
                if(order_list.length < 1) {
                    $('#submit_btn').hide();
                }
            }
        });

    </script>
@endpush
@endsection