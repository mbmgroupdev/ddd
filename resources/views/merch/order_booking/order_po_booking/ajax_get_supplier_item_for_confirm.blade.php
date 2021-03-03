@foreach($itemUnique as $item)
    @php
        $itemIndex = 0;
    @endphp
    @foreach($boms as $bom)
        @if($bom->item_name == $item)
            @php
                $poTrStyle = '#FFF';
                // $poTrStyle = 'lightgoldenrodyellow;';
                $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                $total  = $ptotal + $bom->consumption;
            @endphp
            @if($bom->po_pos_cid != null)
                @php
                    $color_ids = [];
                    $color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
                @endphp
            @endif
            {{-- color dependancy --}}
            @if($bom->depends_on == 1)
                @foreach($colors as $color) 
                    {{-- 0 check --}}
                    @if($bom->po_pos_cid != null)
                        @if(in_array($color->clr_id, $color_ids))
                            @php
                                $posSubTotalQty = 0;
                                if(isset($poColorQtyList[$bom->po_pos_cid])){
                                    foreach($poColorQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
                                        $posSubTotalQty += array_sum($poSizeQtyListSingleS);
                                    }
                                }
                            @endphp
                        @endif
                    @else
                        @php
                            $posSubTotalQty = 0;
                            if(isset($poColorQtyListCN[$bom->order_id])){
                                foreach($poColorQtyListCN[$bom->order_id][$color->clr_id] as $poSizeQtyListSingle) {
                                    $posSubTotalQty += $poSizeQtyListSingle;
                                }
                            }
                        @endphp
                    @endif
                    @php
                        $posSubTotalQtyData = Custom::getOrderBookingReQtyColor($bom->id,$bom->item_id,$color->clr_id);
                        $posSubTotalQtyRemainCheck = '';
                        if(!empty($posSubTotalQtyData)) {
                            $posSubTotalQtyRemainCheck = $posSubTotalQtyData['reqQty']->req_qty - $posSubTotalQtyData['bookingQty'];
                            $posSubTotalQtyRemainCheck = Custom::fixedNumber($posSubTotalQtyRemainCheck,2,true);
                        }
                        if(is_numeric($posSubTotalQtyRemainCheck)) {
                            $posSubTotalQtyRemainCheck = (int)$posSubTotalQtyRemainCheck <= 0?0:$posSubTotalQtyRemainCheck;
                        } else {
                            $posSubTotalQtyRemainCheck = (int)$posSubTotalQty <= 0?0:$posSubTotalQty;
                        }
                        $posSubTotalQtyBookingCheck = false;
                        if(isset(request()->mode)) {
                            $posSubTotalQtyRemainCheck = 1;
                            $posSubTotalQtyBooking = Custom::getOrderBookingQtyColor($poBookingId, $bom->id,$bom->item_id,$color->clr_id);
                            if(empty($posSubTotalQtyBooking)) {
                                $posSubTotalQtyBookingCheck = true;
                            }
                        }
                    @endphp
                    {{-- 0 check --}}
                    {{-- 0 remain check --}}
                    {{-- edit mode check --}}
                    @if($posSubTotalQty != 0 && $posSubTotalQtyRemainCheck != 0 && $posSubTotalQtyBookingCheck == false)
                        @php
                            $id = $bom->id.$bom->order_id.$bom->item_id.$color->clr_id;
                            $title = $order->order_code.', '.$bom->mcat_name.', '.$bom->item_name.', '.$color->clr_name.', No Size';
                        @endphp
                        <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                            <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                            <td class="vertical-align-center order-cat-class">
                                {{ $bom->mcat_name }}
                            </td>
                            <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                            <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                            <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                            <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                            <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                            <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                            <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                            <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{ $bom->item_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                            <input type="hidden" name="mr_cat_item_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->item_id }}">
                            <input type="hidden" name="mr_cat_item_mcat_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->mcat_id }}">

                            <!--COlor input field-->
                            <td style="color: {{$color->clr_name}}; font-weight: bold;">
                                @if($bom->po_pos_cid != null)
                                    @if(in_array($color->clr_id, $color_ids))
                                        {{ $color->clr_name }}
                                        <input type="hidden" value="{{ $color->clr_id }}" name="mr_material_color_clr_id[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]"  class="{{$id}}">
                                    @endif
                                @else
                                    {{ $color->clr_name }}
                                    <input type="hidden" value="{{ $color->clr_id }}" name="mr_material_color_clr_id[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]"  class="{{$id}}">
                                @endif
                            </td>

                            {{-- size input --}}
                            <td></td>

                            <!--qty input field-->
                            <td class="text-custom-style">
                                @php
                                    $posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);
                                @endphp
                                {{ $posSubTotalQty }}
                                <input type="hidden" value="{{ $posSubTotalQty }}" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]" readonly="readonly" class="qty_rm_class {{$id}}" data-rm="{{$id}}">
                            </td>

                            <!--req qty input field-->
                            <td class="text-custom-style">
                                @php
                                    $posSubTotalQtyData = Custom::getOrderBookingReQtyColor($bom->id,$bom->item_id,$color->clr_id);
                                    $posSubTotalQtyRemain = '';
                                    if(!empty($posSubTotalQtyData)) {
                                        $posSubTotalQty = $posSubTotalQtyData['reqQty']->req_qty;
                                        $posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);

                                        $posSubTotalQtyRemain = $posSubTotalQtyData['reqQty']->req_qty - $posSubTotalQtyData['bookingQty'];
                                        $posSubTotalQtyRemain = Custom::fixedNumber($posSubTotalQtyRemain,2,true);
                                    } else {
                                        $posSubTotalQty = Custom::fixedNumber(($posSubTotalQty * $total),2,true);
                                    }
                                    echo $posSubTotalQty;
                                    if(is_numeric($posSubTotalQtyRemain)) {
                                        $posSubTotalQtyRemain = (int)$posSubTotalQtyRemain <= 0?0:$posSubTotalQtyRemain;
                                    } else {
                                        $posSubTotalQtyRemain = (int)$posSubTotalQty <= 0?0:$posSubTotalQty;
                                    }
                                @endphp
                                <input type="hidden" value="{{ $posSubTotalQty }}" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]" class="form-control {{$id}}" readonly="readonly">
                            </td>

                            <!--remaining qty input field-->
                            <td class="text-custom-style">
                                {{$posSubTotalQtyRemain}}
                                <input type="hidden" value="{{ $posSubTotalQtyRemain }}" class="req_qty" readonly="readonly">
                            </td>

                            {{-- booking qty input field --}}
                            <td class="text-custom-style">
                                @php
                                    $posSubTotalQty = Custom::getOrderBookingQtyColor($poBookingId, $bom->id,$bom->item_id,$color->clr_id);
                                    $posSubTotalQtyDataZero = 1;
                                    if(gettype($posSubTotalQty) == 'double') {
                                        if((int)$posSubTotalQty == 0) {
                                            $posSubTotalQtyDataZero = 0;
                                        }
                                    }
                                    if(empty($posSubTotalQty) && $posSubTotalQtyDataZero==1) {
                                        $posSubTotalQty = 0;
                                        if($bom->po_pos_cid != null) {
                                            if(isset($poColorQtyList[$bom->po_pos_cid])){
                                                foreach($poColorQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
                                                    $posSubTotalQty += array_sum($poSizeQtyListSingleS);
                                                }
                                            }
                                        } else {
                                            if(isset($poColorQtyListCN[$bom->order_id])){
                                                foreach($poColorQtyListCN[$bom->order_id][$color->clr_id] as $poSizeQtyListSingle) {
                                                    $posSubTotalQty += $poSizeQtyListSingle;
                                                }
                                            }
                                        }
                                        $posSubTotalQty = $posSubTotalQty * $total;
                                        $posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);
                                        if(!isset(request()->mode)) {
                                            $posSubTotalQty = $posSubTotalQtyRemain;
                                        }
                                    }
                                @endphp
                               <input type="number" value="{{ $posSubTotalQty }}" id="bqty_{{$id}}" name="booking_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]" class="boqtychange {{$id}}" max="{{$posSubTotalQty+$posSubTotalQtyRemain}}" data-uprice="{{$bom->precost_unit_price}}" data-toggle="tooltip" title="{{$title}}" />
                            </td>

                            <!--total value input field-->
                            <td class="text-custom-style">
                                
                                <input type="number" value="{{ $posSubTotalQty*$bom->precost_unit_price}}" id="tvalue_{{$id}}" name="value[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]" class="tvalueC {{$id}}" readonly="readonly"/>
                            </td>
                        </tr>
                    @endif
                @endforeach
            {{-- size dependancy --}}
            @elseif($bom->depends_on == 2)
                @foreach($sizes as $size)
                    {{-- 0 check --}}
                    @php
                        $poSizeQty = 0;
                        if($bom->po_po_id != null){
                            if(isset($poSizeQtyListS[$bom->po_po_id])){
                                if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
                                    $poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
                                }
                            }
                        } else {
                            if(isset($poSizeQtyListSN[$bom->order_id])){
                                if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
                                    foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $eachPoSizeQty) {
                                        $poSizeQty += array_sum($eachPoSizeQty);
                                    }
                                }
                            }
                        }
                    @endphp
                    @php
                        $poSizeQtyData = Custom::getOrderBookingReQtySize($bom->id,$bom->item_id,$size->id);
                        $posSizeRemainCheck = '';
                        if(!empty($poSizeQtyData)) {
                            $posSizeRemainCheck = $poSizeQtyData['reqQty']->req_qty - $poSizeQtyData['bookingQty'];
                            $posSizeRemainCheck = Custom::fixedNumber($posSizeRemainCheck,2,true);
                        }
                        if(is_numeric($posSizeRemainCheck)) {
                            $posSizeRemainCheck = (int)$posSizeRemainCheck <= 0?0:$posSizeRemainCheck;
                        } else {
                            $posSizeRemainCheck = (int)$poSizeQty <= 0?0:$poSizeQty;
                        }
                        $poSizeQtyBookingCheck = false;
                        if(isset(request()->mode)) {
                            $posSizeRemainCheck = 1;
                            $poSizeQtyBooking = Custom::getOrderBookingQtySize($poBookingId,$bom->id,$bom->item_id,$size->id);
                            if(empty($poSizeQtyBooking)) {
                                $poSizeQtyBookingCheck = true;
                            }
                        }
                    @endphp
                    {{-- 0 check --}}
                    {{-- 0 remain check --}}
                    {{-- edit mode check --}}
                    @if($poSizeQty != 0 && $posSizeRemainCheck != 0 && $poSizeQtyBookingCheck == false)
                        @php
                            $id = $bom->id.$bom->order_id.$bom->item_id.$size->id;
                            $title = $order->order_code.', '.$bom->mcat_name.', '.$bom->item_name.', No Color, '.$size->mr_product_pallete_name;

                        @endphp
                        <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                            <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                            <td class="vertical-align-center order-cat-class">
                                {{ $bom->mcat_name }}
                            </td>
                            <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                            <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                            <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                            <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                            <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                            <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                            <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                            <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{ $bom->item_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                            <input type="hidden" name="mr_cat_item_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->item_id }}">
                            <input type="hidden" name="mr_cat_item_mcat_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->mcat_id }}">

                            {{-- color section --}}
                            <td></td>

                            {{-- size section --}}
                            <td>
                                {{ $size->mr_product_pallete_name }}
                                <input type="hidden" value="{{ $size->mr_product_pallete_name }}" name="size[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$size->id}}]" class="{{$id}}">
                            </td>

                            {{-- qty section --}}
                            <td class="text-custom-style">
                                @php
                                    $poSizeQty = Custom::fixedNumber($poSizeQty,2,true);
                                @endphp
                                {{ $poSizeQty }}
                                <input type="hidden" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$size->id}}]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} qty_rm_class {{$id}}" size="1" readonly="readonly" data-rm="{{$id}}">
                            </td>


                            {{-- required qty section --}}
                            <td class="text-custom-style">
                                @php
                                    $poSizeQtyData = Custom::getOrderBookingReQtySize($bom->id,$bom->item_id,$size->id);
                                    $posSizeRemain = '';
                                    if(!empty($poSizeQtyData)) {
                                        $poSizeQty = $poSizeQtyData['reqQty']->req_qty;
                                        $poSizeQty = Custom::fixedNumber($poSizeQty,2,true);

                                        $posSizeRemain = $poSizeQtyData['reqQty']->req_qty - $poSizeQtyData['bookingQty'];
                                        $posSizeRemain = Custom::fixedNumber($posSizeRemain,2,true);
                                    } else {
                                        $poSizeQty = Custom::fixedNumber(($poSizeQty * $total),2,true);
                                    }
                                    echo $poSizeQty;
                                    if(is_numeric($posSizeRemain)) {
                                        $posSizeRemain = (int)$posSizeRemain <= 0?0:$posSizeRemain;
                                    } else {
                                        $posSizeRemain = (int)$poSizeQty <= 0?0:$poSizeQty;
                                    }
                                @endphp
                                <input type="hidden" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$size->id}}]" id="req_qtyr-{{$size->mr_product_pallete_name}}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global_total {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} {{$id}}" size="1" readonly="readonly">
                            </td>

                            <!--remaining qty input field-->
                            <td class="text-custom-style">
                                {{$posSizeRemain}}
                                <input type="hidden" value="{{ $posSizeRemain }}" class="req_qty">
                            </td>

                            {{-- booking qty section --}}
                            <td class="text-custom-style">
                                @php
                                    $poSizeQty = Custom::getOrderBookingQtySize($poBookingId,$bom->id,$bom->item_id,$size->id);
                                    $poSizeQtyDataZero = 1;
                                    if(gettype($poSizeQty) == 'double') {
                                        if((int)$poSizeQty == 0) {
                                            $poSizeQtyDataZero = 0;
                                        }
                                    }
                                    if(empty($poSizeQty) && $poSizeQtyDataZero==1) {
                                        $poSizeQty = 0;
                                        if($bom->po_po_id != null){
                                            if(isset($poSizeQtyListS[$bom->po_po_id])){
                                                if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
                                                    $poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
                                                }
                                            }
                                        } else {
                                            if(isset($poSizeQtyListSN[$bom->order_id])){
                                                if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
                                                    foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $eachPoSizeQty) {
                                                        $poSizeQty += array_sum($eachPoSizeQty);
                                                    }
                                                }
                                            }
                                        }
                                        $poSizeQty = $poSizeQty * $total;
                                    }
                                    $poSizeQty = Custom::fixedNumber($poSizeQty,2,true);
                                    if(!isset(request()->mode)) {
                                        $poSizeQty = $posSizeRemain;
                                    }
                                @endphp
                                <input type="number" value="{{$poSizeQty}}" id="bqty_{{$id}}" name="booking_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$size->id}}]" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} boqtychange {{$id}}" size="1" max="{{$poSizeQty+$posSizeRemain}}" data-uprice="{{$bom->precost_unit_price}}" data-toggle="tooltip" title="{{$title}}" />
                            </td>

                            {{-- total qty section --}}
                            <td class="text-custom-style">
                                
                                <input type="number" value="{{$poSizeQty*$bom->precost_unit_price}}" name="value[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$size->id}}]" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} tvalueC {{$id}}" id="tvalue_{{$id}}" size="1" readonly="readonly">
                            </td>
                        </tr>
                    @endif
                @endforeach
            {{-- color + size dependancy --}}
            @elseif($bom->depends_on == 3)
                @foreach($care_label as $color)
                    {{-- 0 check --}}
                    @if($bom->po_pos_cid != null)
                        @if(in_array($color->clr_id, $color_ids))
                            @php
                                $posSubTotalQtyCS = 0;
                                if(isset($poSizeQtyListC[$bom->po_no][$color->clr_id])){
                                    foreach($poSizeQtyListC[$bom->po_no][$color->clr_id] as $clrId=>$pos_value) {
                                        foreach($pos_value as $sizId=>$pos_clr_value) {
                                            if(isset($pos_clr_value[$color->product_size_id])){
                                                $posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
                                            }
                                        }
                                    }
                                }
                            @endphp
                        @endif
                    @else
                        @php
                            $posSubTotalQtyCS = 0;
                            if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
                                if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
                                    $posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
                                }
                            }
                        @endphp
                    @endif
                    @php
                        $posSubTotalQtyCSData = Custom::getOrderBookingReQtyColorSize($bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
                        $posCSRemainCheck = '';
                        if(!empty($posSubTotalQtyCSData)) {
                            $posCSRemainCheck = $posSubTotalQtyCSData['reqQty']->req_qty - $posSubTotalQtyCSData['bookingQty'];
                            $posCSRemainCheck = Custom::fixedNumber($posCSRemainCheck,2,true);
                        }
                        if(is_numeric($posCSRemainCheck)) {
                            $posCSRemainCheck = (int)$posCSRemainCheck <= 0?0:$posCSRemainCheck;
                        } else {
                            $posCSRemainCheck = (int)$posSubTotalQtyCS <= 0?0:$posSubTotalQtyCS;
                        }
                        $posSubTotalQtyCSBookingCheck = false;
                        if(isset(request()->mode)) {
                            $posCSRemainCheck = 1;
                            $posSubTotalQtyCSBooking = Custom::getOrderBookingQtyColorSize($poBookingId,$bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
                            if(empty($posSubTotalQtyCSBooking)) {
                                $posSubTotalQtyCSBookingCheck = true;
                            }
                        }
                    @endphp
                    {{-- 0 check --}}
                    {{-- 0 remain check --}}
                    {{-- edit mode check --}}
                    @if($posSubTotalQtyCS != 0 && $posCSRemainCheck != 0 && $posSubTotalQtyCSBookingCheck == false)
                        @php
                            $id = $bom->id.$bom->order_id.$bom->item_id.$color->clr_id.$color->product_size_id;
                            $title = $order->order_code.', '.$bom->mcat_name.', '.$bom->item_name.', '.$color->clr_name.', '.$color->mr_product_pallete_name;
                        @endphp
                        <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                            <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                            <td class="vertical-align-center order-cat-class">
                                {{ $bom->mcat_name }}
                            </td>
                            <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                            <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                            <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                            <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                            <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                            <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                            <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                            <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{ $bom->item_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                            <input type="hidden" name="mr_cat_item_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->item_id }}">
                            <input type="hidden" name="mr_cat_item_mcat_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->mcat_id }}">

                            {{-- COlor input field --}}
                            @if($bom->po_pos_cid != null)
                                @if(in_array($color->clr_id, $color_ids))
                                    <input type="hidden" value="{{ $color->clr_id }}" name="mr_material_color_clr_id[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]" id="{{$id}}">
                                @endif
                            @else
                                <input type="hidden" value="{{ $color->clr_id }}" name="mr_material_color_clr_id[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}]" id="{{$id}}">
                            @endif
                            <td class="order-clr-class" style="color: {{$color->clr_name}}; font-weight: bold;">
                                {{$color->clr_name}}
                            </td>

                            {{-- size input field --}}
                            <td>
                                @if($bom->po_pos_cid != null)
                                    @if(in_array($color->clr_id, $color_ids))
                                        {{ $color->mr_product_pallete_name }}
                                        <input type="hidden" value="{{ $color->mr_product_pallete_name }}" name="size[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" class="{{$id}}">
                                    @endif
                                @else
                                    {{ $color->mr_product_pallete_name }}
                                    <input type="hidden" value="{{ $color->mr_product_pallete_name }}" name="size[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" class="{{$id}}">
                                @endif
                            </td>

                            {{-- qty input field --}}
                            <td class="text-custom-style">
                                @php
                                    $posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);
                                @endphp
                                {{$posSubTotalQtyCS}}
                                <input type="hidden" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $posSubTotalQtyCS }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} qty_rm_class {{$id}}" size="1" readonly="readonly" data-rm="{{$id}}">
                            </td>

                            {{-- req qty input field --}}
                            <td class="text-custom-style">
                                @php
                                    $posSubTotalQtyCSData = Custom::getOrderBookingReQtyColorSize($bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
                                    $posCSRemain = '';
                                    if(!empty($posSubTotalQtyCSData)) {
                                        $posSubTotalQtyCS = $posSubTotalQtyCSData['reqQty']->req_qty;
                                        $posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);

                                        $posCSRemain = $posSubTotalQtyCSData['reqQty']->req_qty - $posSubTotalQtyCSData['bookingQty'];
                                        $posCSRemain = Custom::fixedNumber($posCSRemain,2,true);
                                    } else {
                                        $posSubTotalQtyCS = Custom::fixedNumber(($posSubTotalQtyCS * $total),2,true);
                                    }
                                    echo $posSubTotalQtyCS;
                                    if(is_numeric($posCSRemain)) {
                                        $posCSRemain = (int)$posCSRemain <= 0?0:$posCSRemain;
                                    } else {
                                        $posCSRemain = (int)$posSubTotalQtyCS <= 0?0:$posSubTotalQtyCS;
                                    }
                                @endphp
                                <input type="hidden" value="{{ $posSubTotalQtyCS }} " name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} {{$id}}" size="1" readonly="readonly">
                            </td>

                            <!--remaining qty input field-->
                            <td class="text-custom-style">
                                {{$posCSRemain}}
                                <input type="hidden" value="{{ $posCSRemain }}" class="req_qty">
                            </td>

                            {{-- booking qty input field --}}
                            <td class="text-custom-style">
                                @php
                                    $posSubTotalQtyCS = Custom::getOrderBookingQtyColorSize($poBookingId,$bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
                                    $posSubTotalQtyCSDataZero = 1;
                                    if(gettype($posSubTotalQtyCS) == 'double') {
                                        if((int)$posSubTotalQtyCS == 0) {
                                            $posSubTotalQtyCSDataZero = 0;
                                        }
                                    }
                                    if(empty($posSubTotalQtyCS) && $posSubTotalQtyCSDataZero==1) {
                                        $posSubTotalQtyCS = 0;
                                        if($bom->po_pos_cid != null) {
                                            if(isset($poSizeQtyListC[$bom->po_no][$color->clr_id])){
                                                foreach($poSizeQtyListC[$bom->po_no][$color->clr_id] as $clrId=>$pos_value) {
                                                    foreach($pos_value as $sizId=>$pos_clr_value) {
                                                        if(isset($pos_clr_value[$color->product_size_id])){
                                                            $posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
                                                if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
                                                    $posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
                                                }
                                            }
                                        }
                                        $posSubTotalQtyCS = $posSubTotalQtyCS * $total;
                                    }
                                    $posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);
                                    if(!isset(request()->mode)) {
                                        $posSubTotalQtyCS = $posCSRemain;
                                    }
                                @endphp
                                <input type="number" value="{{$posSubTotalQtyCS}}" name="booking_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" class="global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} boqtychange {{$id}}" id="bqty_{{$id}}" size="1" max="{{$posSubTotalQtyCS+$posCSRemain}}" data-uprice="{{$bom->precost_unit_price}}" data-toggle="tooltip" title="{{$title}}">
                            </td>

                            <!--total value input field-->
                            <td>
                                
                                <input type="number" value="{{$posSubTotalQtyCS*$bom->precost_unit_price}}" name="value[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" id="tvalue_{{$id}}" class="{{$id}} form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} tvalueC" size="1" readonly="readonly">
                            </td>
                        </tr>
                    @endif
                @endforeach
            {{-- no dependancy --}}
            @elseif($bom->depends_on == 0)
                {{-- 0 check --}}
                @php
                    $noDependTotalQty = 0;
                    if($bom->po_no != null) {
                        if(isset($poSizeQtyListS[$bom->po_no])){
                            if(!empty($poSizeQtyListS[$bom->po_no])){
                                foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
                                    $noDependTotalQty += array_sum($eachNoDependQty);
                                }
                            }
                        }
                    } else {
                        $noDependTotalQty = $order->order_qty;
                    }
                    $itemPoCount = Custom::getItemPoCount($bom->id,$bom->order_id);
                    $itemPoCount = !empty($itemPoCount)?count($itemPoCount):0;
                    $noDependTotalQty = $noDependTotalQty*$itemPoCount;
                @endphp
                @php
                    $noDependTotalQtyData = Custom::getOrderBookingReQtyNoDepend($bom->id,$bom->item_id);
                    $noDependTotalRemainCheck = '';
                    if(!empty($noDependTotalQtyData)) {
                        $noDependTotalRemainCheck = $noDependTotalQtyData['reqQty']->req_qty - $noDependTotalQtyData['bookingQty'];
                        $noDependTotalRemainCheck = Custom::fixedNumber($noDependTotalRemainCheck,2,true);
                    }
                    if(is_numeric($noDependTotalRemainCheck)) {
                        $noDependTotalRemainCheck = (int)$noDependTotalRemainCheck <= 0?0:$noDependTotalRemainCheck;
                    } else {
                        $noDependTotalRemainCheck = (int)$noDependTotalQty <= 0?0:$noDependTotalQty;
                    }
                    $noDependTotalQtyBookingCheck = false;
                    if(isset(request()->mode)) {
                        $noDependTotalRemainCheck = 1;
                        $noDependTotalQtyBooking = Custom::getOrderBookingQtyNoDepend($poBookingId,$bom->id,$bom->item_id);
                        if(empty($noDependTotalQtyBooking)) {
                            $noDependTotalQtyBookingCheck = true;
                        }
                    }
                @endphp
                {{-- 0 check --}}
                {{-- 0 remain check --}}
                {{-- edit mode check --}}
                @if($noDependTotalQty != 0 && $noDependTotalRemainCheck != 0 && $noDependTotalQtyBookingCheck == false)
                    @php
                        $id = $bom->id.$bom->order_id.$bom->item_id;
                        $title = $order->order_code.', '.$bom->mcat_name.', '.$bom->item_name;
                    @endphp
                    <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                        <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                        <td class="vertical-align-center order-cat-class">
                            {{ $bom->mcat_name }}
                        </td>
                        <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                        <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                        <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                        <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                        <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                        <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                        <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                        <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{ $bom->item_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                        <input type="hidden" name="mr_cat_item_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->item_id }}">
                        <input type="hidden" name="mr_cat_item_mcat_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->item_id}}]" value="{{ $bom->mcat_id }}">

                        {{-- COlor input field --}}
                        <td></td>

                        {{-- size input field --}}
                        <td></td>

                        {{-- qty input field --}}
                        <td class="text-custom-style">
                            @php
                                $noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);
                            @endphp
                            {{$noDependTotalQty}}
                            <input type="hidden" value="{{ $noDependTotalQty }}"name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][]" class="form-control qty_rm_class {{$id}}" readonly="readonly" data-rm="{{$id}}">
                        </td>

                        {{-- req qty input field --}}
                        <td class="text-custom-style">
                            @php
                                $noDependTotalQtyData = Custom::getOrderBookingReQtyNoDepend($bom->id,$bom->item_id);
                                $noDependTotalRemain = '';
                                if(!empty($noDependTotalQtyData)) {
                                    $noDependTotalQty = $noDependTotalQtyData['reqQty']->req_qty;
                                    $noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);

                                    $noDependTotalRemain = $noDependTotalQtyData['reqQty']->req_qty - $noDependTotalQtyData['bookingQty'];
                                    $noDependTotalRemain = Custom::fixedNumber($noDependTotalRemain,2,true);
                                } else {
                                    $noDependTotalQty = Custom::fixedNumber(($noDependTotalQty * $total),2,true);
                                }

                                echo $noDependTotalQty;
                                if(is_numeric($noDependTotalRemain)) {
                                    $noDependTotalRemain = (int)$noDependTotalRemain <= 0?0:$noDependTotalRemain;
                                } else {
                                    $noDependTotalRemain = (int)$noDependTotalQty <= 0?0:$noDependTotalQty;
                                }
                            @endphp
                            <input type="hidden" value="{{ $noDependTotalQty }}" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][]" class="form-control {{$id}}" readonly="readonly">
                        </td>

                        <!--remaining qty input field-->
                        <td class="text-custom-style">
                            {{$noDependTotalRemain}}
                            <input type="hidden" value="{{ $noDependTotalRemain }}" class="req_qty">
                        </td>

                        {{-- booking qty input field --}}
                        <td>
                            @php
                                // $noDependTotalQtyData = Custom::getOrderBookingReQtyNoDepend($bom->id,$bom->item_id);
                                // if(!empty($noDependTotalQtyData)) {
                                //     $noDependTotalQty = $noDependTotalQtyData['reqQty']->req_qty - $noDependTotalQtyData['bookingQty'];
                                //     $noDependTotalQty = Custom::fixedNumber($noDependTotalQty);
                                // } else {
                                    $noDependTotalQty = Custom::getOrderBookingQtyNoDepend($poBookingId,$bom->id,$bom->item_id);
                                    $noDependTotalQtyZero = 1;
                                    if(gettype($noDependTotalQty) == 'double') {
                                        if((int)$noDependTotalQty == 0) {
                                            $noDependTotalQtyZero = 0;
                                        }
                                    }
                                    if(empty($noDependTotalQty) && $noDependTotalQtyZero==1) {
                                        $noDependTotalQty = 0;
                                        // first time set default booking qty (required qty)
                                        if($bom->po_no != null) {
                                            if(isset($poSizeQtyListS[$bom->po_no])){
                                                if(!empty($poSizeQtyListS[$bom->po_no])){
                                                    foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
                                                        $noDependTotalQty += array_sum($eachNoDependQty);
                                                    }
                                                }
                                            }
                                            $noDependTotalQty = $noDependTotalQty * $total;
                                        } else {
                                            $noDependTotalQty = $order->order_qty * $total;
                                        }
                                        $noDependTotalQty = $noDependTotalQty * $itemPoCount;
                                    }
                                    $noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);
                                    if(!isset(request()->mode)) {
                                        $noDependTotalQty = $noDependTotalRemain;
                                    }
                                // }
                            @endphp
                            <input type="number" value="{{$noDependTotalQty}}" id="bqty_{{$id}}" name="booking_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][]" class="form-control boqtychange {{$id}}" max="{{$noDependTotalQty+$noDependTotalRemain}}" data-uprice="{{$bom->precost_unit_price}}" data-toggle="tooltip" title="{{$title}}" />
                        </td>

                        <!--total value input field-->
                        <td>
                           
                            <input type="number" data-qty="{{$noDependTotalQty*$bom->precost_unit_price}}" value="{{$noDependTotalQty}}" id="tvalue_{{$id}}" name="value[{{ $bom->id }}][{{ $bom->order_id }}][{{ $bom->item_id }}][]" class="form-control tvalueC {{$id}}" readonly="readonly" />
                        </td>
                    </tr>
                @endif
            @endif
            @php $itemIndex++; @endphp
        @endif
    @endforeach
@endforeach

<script>
    classEach('order-code-class');
    classEach('order-po-class');
    classEach('order-sup-class');
    classEach('order-cat-class');
    classEach('order-item-class');
    classEach('order-desc-class');
    classEach('order-art-class');
    classEach('order-uom-class');
    classEach('order-uprice-class');
    classEach('order-clr-class');
    function classEach(clsName) {
        var orderCode = '';
        var nextOrCode = '';
        var orLenght = 0;
        $('.'+clsName).each(function(i,v){
            if(i == 0) {
                orderCode = $(this).text();
            }
            if(i > 0){
                nextOrCode = $(this).text();
                if(orderCode == nextOrCode) {
                    orLenght += 1;
                    $(this).text('');
                    orLenght = $('.'+clsName).length;
                    if(i < orLenght-1) {
                        // $(this).css("border-bottom-color", "#fff");
                    }
                } else {
                    orderCode = $(this).text();
                }
            }
        });
    }
</script>