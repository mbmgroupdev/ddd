@if(!empty($poList->toArray()))
    @php
        $poTotalNoSize = [];
        $grandTotalValue = 0;
    @endphp
    @foreach($poList as $key=>$poSingle)
        @php
                $color_ids = [];
                if(isset($poSizeQtyListC[$poSingle->po_id])) {
                    $color_ids = array_keys($poSizeQtyListC[$poSingle->po_id]);
                }
        @endphp
        @php
                $itemIndex = 0;
        @endphp


        @foreach($boms as $bom)

            @if($bom->po_no == $poSingle->po_id || $bom->po_no == null)
                @if(Custom::getPoDetailItemExist($poSingle->po_id, $bom->id))
                    @php
                        $poTrStyle = '#FFF';
                        if($bom->po_no == $poSingle->po_id) {
                            $poTrStyle = 'lightgoldenrodyellow;';
                        }
                    @endphp
                    {{-- color dependancy --}}
{{--                    @if($bom->depends_on == 1)
                        @foreach($colors as $color)
                            @if(in_array($color->clr_id, $color_ids))
                                --}}{{-- 0 check --}}{{--
                                @php
                                    $posSubTotalQty = 0;
                                    if(isset($poSizeQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id])) {
                                        foreach($poSizeQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id] as $pos_id=>$pos_value) {
                                            $posSubTotalQty += array_sum($pos_value);
                                        }
                                    }
                                @endphp
                                --}}{{-- 0 check --}}{{--
                                @if($posSubTotalQty != 0)
                                    <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                                        <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                                        <td class="vertical-align-center order-po-class">{{ $bom->po_no!=null?$bom->po_po_no:$poSingle->po_no }}</td>
                                        <td class="vertical-align-center order-cat-class">
                                            {{ $bom->mcat_name }}
                                        </td>
                                        <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                                        <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                                        <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                                        <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                                        <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                                        <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                                        <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                                        <input type="hidden" name="mr_purchase_order_po_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}]" value="{{ $poSingle->po_id }}">
                                        <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                                        <input type="hidden" name="mr_order_bom_costing_booking_id_another[{{ $bom->id }}]" value="{{ $bom->ord_bom_id }}">
                                        <input type="hidden" name="mr_supplier[{{ $bom->sup_id }}]" value="{{ $bom->sup_id }}">

                                        <!--COlor input field-->
                                        <td style="color: {{$color->clr_name}}; font-weight: bold;">
                                            {{$color->clr_name}}
                                            <input type="hidden" value="{{ $color->clr_id }}" name="mr_material_color_clr_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}]" class="color_rm_class {{ $bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id }}" readonly="readonly">
                                        </td>
                                        --}}{{-- size input --}}{{--
                                        <td></td>

                                        <!--qty input field-->
                                        <td class="text-custom-style">
                                            @php
                                                $posSubTotalQty = 0;
                                                if(isset($poColorQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id])) {
                                                    foreach($poColorQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id] as $pos_id=>$pos_value) {
                                                        $posSubTotalQty += $pos_value;
                                                    }
                                                }
                                                $posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);
                                            @endphp
                                            {{$posSubTotalQty}}
                                            <input type="hidden" value="{{ $posSubTotalQty }}" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}]" readonly="readonly" class="qty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id}}" data-rm="{{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id}}">
                                        </td>

                                        <!--req qty input field-->
                                        <td class="text-custom-style">
                                            @php
                                                $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                                $total = $ptotal + $bom->consumption;
                                            @endphp
                                            @php
                                                $posSubTotalQty = 0;
                                                if(isset($poColorQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id])) {
                                                    foreach($poColorQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id] as $pos_id=>$pos_value) {
                                                        $posSubTotalQty += $pos_value;
                                                    }
                                                }
                                                $posSubTotalQty = Custom::fixedNumber(($posSubTotalQty * $total),2,true);
                                            @endphp
                                            {{$posSubTotalQty}}
                                           <input type="hidden" value="{{ $posSubTotalQty }}" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}]" class="form-control rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
--}}{{--                                           <input type="hidden" value="{{ $bom->booking_qty }}" name="booking_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}]" class="form-control rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">--}}{{--
                                        </td>

                                        <!--total value input field-->
                                        <td class="text-custom-style">
                                            @php $r_qtyF = 0; @endphp
                                            @php
                                                $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                                $total = $ptotal + $bom->consumption;
                                            @endphp
                                            @php
                                                $posSubTotalQty = 0;
                                                if(isset($poColorQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id])) {
                                                    foreach($poColorQtyListC[$poSingle->po_id][$color->clr_id][$poSingle->po_id] as $pos_id=>$pos_value) {
                                                        $posSubTotalQty += $pos_value;
                                                    }
                                                }
                                                $posSubTotalQty = ($posSubTotalQty * $total)*$bom->precost_unit_price;
                                                $posSubTotalQty = Custom::fixedNumber($posSubTotalQty);
                                            @endphp
                                            {{$posSubTotalQty}}
                                           <input type="hidden" value="{{ $posSubTotalQty }}" name="total_value[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}]" class="form-control tvlu_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id}}" readonly="readonly">
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    --}}{{-- size dependancy --}}{{--
                    @elseif($bom->depends_on == 2)
                        @foreach($sizes as $size)
                            --}}{{-- 0 check --}}{{--
                            @php $poSizeQty = 0;
                                if(isset($poSizeQtyListS[$poSingle->po_id])){
                                    if(isset($poSizeQtyListS[$poSingle->po_id][$size->id])){
                                        $poSizeQty = array_sum($poSizeQtyListS[$poSingle->po_id][$size->id]);
                                    }
                                }
                            @endphp
                            --}}{{-- 0 check --}}{{--
                            @if($poSizeQty != 0)
                                <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                                    <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                                    <td class="vertical-align-center order-po-class">{{ $bom->po_no!=null?$bom->po_po_no:$poSingle->po_no }}</td>
                                    <td class="vertical-align-center order-cat-class">
                                        {{ $bom->mcat_name }}
                                    </td>
                                    <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                                    <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                                    <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                                    <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                                    <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                                    <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                                    <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                                    <input type="hidden" name="mr_purchase_order_po_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}]" value="{{ $poSingle->po_id }}">
                                    <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                                    <input type="hidden" name="mr_order_bom_costing_booking_id_another[{{ $bom->id }}]" value="{{ $bom->ord_bom_id }}">
                                    <input type="hidden" name="mr_supplier[{{ $bom->sup_id }}]" value="{{ $bom->sup_id }}">

                                    <!--COlor input field-->
                                    <td>erettgrrhthryh</td>

                                    <!--size input field-->
                                    <td>
                                        {{$size->mr_product_pallete_name}}
                                        <input type="hidden" value="{{ $size->mr_product_pallete_name }}" name="size[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$size->id}}]" class="size_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$size->id}}" readonly="readonly">
                                    </td>

                                    <!--qty input field-->
                                    <td class="text-custom-style">
                                        @php $poSizeQty = 0;
                                            if(isset($poSizeQtyListS[$poSingle->po_id])){
                                                if(isset($poSizeQtyListS[$poSingle->po_id][$size->id])){
                                                    $poSizeQty = array_sum($poSizeQtyListS[$poSingle->po_id][$size->id]);
                                                }
                                            }
                                            $poSizeQty = Custom::fixedNumber($poSizeQty,2,true);
                                        @endphp
                                        {{$poSizeQty}}
                                        <input type="hidden" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$size->id}}]" value="{{ $poSizeQty }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} qty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$size->id}}" size="1" readonly="readonly" data-rm="{{$bom->id.$bom->order_id.$poSingle->po_id.$size->id}}">
                                    </td>

                                    <!--req qty input field-->
                                    <td class="text-custom-style">
                                        @php $r_qtyF = 0; @endphp
                                        @php
                                            $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                            $total = $ptotal + $bom->consumption;
                                        @endphp
                                        @php $poSizeQty = 0;
                                            if(isset($poSizeQtyListS[$poSingle->po_id])){
                                                if(isset($poSizeQtyListS[$poSingle->po_id][$size->id])){
                                                    $poSizeQty = array_sum($poSizeQtyListS[$poSingle->po_id][$size->id])*$total;
                                                }
                                            }
                                            $poSizeQty = Custom::fixedNumber($poSizeQty,2,true);
                                        @endphp
                                        {{$poSizeQty}}
                                        <input type="hidden" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$size->id}}]" value="{{ $poSizeQty }}" class="form-control global_total {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$size->id}}" size="1" readonly="readonly">
                                    </td>

                                    <!--total value input field-->
                                    <td class="text-custom-style">
                                        @php $r_qtyF = 0; @endphp
                                        @php
                                            $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                            $total = $ptotal + $bom->consumption;
                                        @endphp
                                        @php $poSizeQty = 0;
                                            if(isset($poSizeQtyListS[$poSingle->po_id])){
                                                if(isset($poSizeQtyListS[$poSingle->po_id][$size->id])){
                                                    $poSizeQty = array_sum($poSizeQtyListS[$poSingle->po_id][$size->id])*$total;
                                                }
                                            }
                                            $poSizeQty = $poSizeQty*$bom->precost_unit_price;
                                            $poSizeQty = Custom::fixedNumber($poSizeQty);
                                        @endphp
                                        {{$poSizeQty}}
                                        <input type="hidden" name="total_value[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$size->id}}]" value="{{ $poSizeQty }}" class="form-control global_total {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} tvlu_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$size->id}}" size="1" readonly="readonly">
                                    </td>
                                    <td>aosidhfoashdf</td>
                                </tr>
                            @endif
                        @endforeach
                    --}}{{-- color + size dependancy --}}{{--
                    @elseif($bom->depends_on == 3)
                        @foreach($care_label as $color)
                            @if(in_array($color->clr_id, $color_ids))
                                --}}{{-- 0 check --}}{{--
                                @php
                                    $posSubTotalQtyCS = 0;
                                    if(isset($poSizeQtyListC[$poSingle->po_id][$color->clr_id])){
                                        foreach($poSizeQtyListC[$poSingle->po_id][$color->clr_id] as $clrId=>$pos_value) {
                                            foreach($pos_value as $sizId=>$pos_clr_value) {
                                                if(isset($pos_clr_value[$color->product_size_id])){
                                                    $posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                --}}{{-- 0 check --}}{{--
                                @if($posSubTotalQtyCS != 0)
                                    <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                                        <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                                        <td class="vertical-align-center order-po-class">{{ $bom->po_no!=null?$bom->po_po_no:$poSingle->po_no }}</td>
                                        <td class="vertical-align-center order-cat-class">
                                            {{ $bom->mcat_name }}
                                        </td>
                                        <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                                        <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                                        <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                                        <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                                        <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                                        <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                                        <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                                        <input type="hidden" name="mr_purchase_order_po_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}]" value="{{ $poSingle->po_id }}">
                                        <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                                        <input type="hidden" name="mr_order_bom_costing_booking_id_another[{{ $bom->id }}]" value="{{ $bom->ord_bom_id }}">

                                        <input type="hidden" value="{{ $color->clr_id }}" name="mr_material_color_clr_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}]" class="color_rm_class {{ $bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id }}">
                                        <input type="hidden" name="mr_supplier[{{ $bom->sup_id }}]" value="{{ $bom->sup_id }}">
                                        <!--COlor input field-->
                                        <td class="order-clr-class" style="color: {{$color->clr_name}}; font-weight: bold;">
                                            {{$color->clr_name}}
                                        </td>

                                        <!--size input field-->
                                        <td>
                                            {{$color->mr_product_pallete_name}}
                                            <input type="hidden" value="{{ $color->mr_product_pallete_name }}" name="size[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" class="size_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id.$color->product_size_id}}" readonly="readonly">
                                        </td>

                                        <!--qty input field-->
                                        <td class="text-custom-style">
                                            @php
                                                $posSubTotalQtyCS = 0;
                                                if(isset($poSizeQtyListC[$poSingle->po_id][$color->clr_id])){
                                                    foreach($poSizeQtyListC[$poSingle->po_id][$color->clr_id] as $clrId=>$pos_value) {
                                                        foreach($pos_value as $sizId=>$pos_clr_value) {
                                                            if(isset($pos_clr_value[$color->product_size_id])){
                                                                $posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
                                                            }
                                                        }
                                                    }
                                                }
                                                $posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);
                                            @endphp
                                            {{$posSubTotalQtyCS}}
                                            <input type="hidden" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" value="{{ $posSubTotalQtyCS }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} qty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id.$color->product_size_id}}" data-rm="{{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id.$color->product_size_id}}" size="1" readonly="readonly">
                                        </td>

                                        <!--req qty input field-->
                                        <td class="text-custom-style">
                                            @php $r_qtyF = 0; @endphp
                                            @php
                                                $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                                $total = $ptotal + $bom->consumption;
                                            @endphp
                                            @php
                                                $posSubTotalQtyCS = 0;
                                                if(isset($poSizeQtyListC[$poSingle->po_id][$color->clr_id])){
                                                    foreach($poSizeQtyListC[$poSingle->po_id][$color->clr_id] as $clrId=>$pos_value) {
                                                        foreach($pos_value as $sizId=>$pos_clr_value) {
                                                            if(isset($pos_clr_value[$color->product_size_id])) {
                                                                $posSubTotalQtyCS += $pos_clr_value[$color->product_size_id]*$total;
                                                            }
                                                        }
                                                    }
                                                }
                                                $posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);
                                            @endphp
                                            {{$posSubTotalQtyCS}}
                                            <input type="hidden" value="{{ $posSubTotalQtyCS }}" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" class="form-control global_totals {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id.$color->product_size_id}}" size="1" readonly="readonly">
                                        </td>

                                        <!--total value input field-->
                                        <td class="text-custom-style">
                                            @php $r_qtyF = 0; @endphp
                                            @php
                                                $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                                $total = $ptotal + $bom->consumption;
                                            @endphp
                                            @php
                                                $posSubTotalQtyCS = 0;
                                                if(isset($poSizeQtyListC[$poSingle->po_id][$color->clr_id])){
                                                    foreach($poSizeQtyListC[$poSingle->po_id][$color->clr_id] as $clrId=>$pos_value) {
                                                        foreach($pos_value as $sizId=>$pos_clr_value) {
                                                            if(isset($pos_clr_value[$color->product_size_id])) {
                                                                $posSubTotalQtyCS += $pos_clr_value[$color->product_size_id]*$total;
                                                            }
                                                        }
                                                    }
                                                }
                                                $posSubTotalQtyCS = $posSubTotalQtyCS*$bom->precost_unit_price;
                                                $posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS);
                                            @endphp
                                            {{$posSubTotalQtyCS}}
                                            <input type="hidden" value="{{ $posSubTotalQtyCS }}" name="total_value[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{$color->clr_id}}][{{$color->product_size_id}}]" class="form-control global_totals {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }} tvlu_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id.$color->clr_id.$color->product_size_id}}" size="1" readonly="readonly">
                                        </td>
                                        <td>asdfasdf</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach--}}
                    {{-- no dependancy --}}
                    {{--@elseif($bom->depends_on == 0)--}}
                        {{-- 0 check --}}
                        @php
                            if($bom->po_no != null) {
                                $noDependTotalQty = 0;
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
                        @endphp
                        {{-- 0 check --}}
                        @if($noDependTotalQty != 0)
                            <tr style="background-color: {{ $poTrStyle }}" class="tr_order_id_{{ $bom->order_id }}">
                                <td class="vertical-align-center order-code-class">{{ $order->order_code }}</td>
                                <td class="vertical-align-center order-po-class">{{ $bom->po_no!=null?$bom->po_po_no:$poSingle->po_no }}</td>
                                <td class="vertical-align-center order-cat-class">
                                    {{ $bom->mcat_name }}
                                </td>
                                <td class="vertical-align-center order-item-class">{{ $bom->item_name }}</td>
                                <td class="vertical-align-center order-desc-class">{{ $bom->item_description }}</td>
                                <td class="vertical-align-center order-art-class">{{ $bom->art_name }}</td>
                                <th class="vertical-align-center order-sup-class">{{ $bom->sup_name }}</th>
                                <td class="vertical-align-center order-uom-class">{{ $bom->uom }}</td>
                                <td class="vertical-align-center order-uprice-class">{{ $bom->precost_unit_price }}</td>

                                <input type="hidden" name="mr_order_entry_order_id[{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{ $bom->id }}]" value="{{ $bom->depends_on }}">
                                <input type="hidden" name="mr_purchase_order_po_id[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}]" value="{{ $poSingle->po_id }}">
                                <input type="hidden" name="mr_order_bom_costing_booking_id[{{ $bom->id }}]" value="{{ $bom->id }}">
                                <input type="hidden" name="mr_supplier[{{ $bom->sup_id }}]" value="{{ $bom->sup_id }}">

                                <!--COlor input field-->
                                <td></td>

                                <!--size input field-->
                                <td></td>

                                <!--qty input field-->
                                <td class="text-custom-style">
                                    @if($bom->po_no != null)
                                        @php
                                            $noDependTotalQty = 0;
                                            if(isset($poSizeQtyListS[$bom->po_no])){
                                                if(!empty($poSizeQtyListS[$bom->po_no])){
                                                    foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
                                                        $noDependTotalQty += array_sum($eachNoDependQty);
                                                    }
                                                }
                                            }
                                            $noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);
                                        @endphp
                                        {{$noDependTotalQty}}
                                        <input type="hidden" value="{{ $noDependTotalQty }}" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][]" class="form-control qty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" data-rm="{{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
                                    @else
                                        @php
                                            $noDependTotalQty = Custom::fixedNumber($order->order_qty,2,true);
                                        @endphp
                                        {{$noDependTotalQty}}
                                        <input type="hidden" value="{{ $noDependTotalQty }}" name="qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][]" class="form-control qty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" data-rm="{{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
                                    @endif
                                </td>

                                <!--req qty input field-->
                                <td class="text-custom-style">
                                    @php $r_qtyF = 0; @endphp
                                    @php
                                        $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                        $total = $ptotal + $bom->consumption;
                                    @endphp
                                    @if($bom->po_no != null)
                                        @php
                                            $noDependTotalQty = 0;
                                            if(isset($poSizeQtyListS[$bom->po_no])){
                                                if(!empty($poSizeQtyListS[$bom->po_no])){
                                                    foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
                                                        $noDependTotalQty += array_sum($eachNoDependQty);
                                                    }
                                                }
                                            }
                                            $noDependTotalQty = Custom::fixedNumber(($noDependTotalQty * $total),2,true);
                                        @endphp
                                        {{$noDependTotalQty}}
                                        <input type="hidden" value="{{ $noDependTotalQty }}" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][]" class="form-control rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
                                    @else
                                        @php
                                            $noDependTotalQty = Custom::fixedNumber(($order->order_qty * $total),2,true);
                                        @endphp
                                        {{$noDependTotalQty}}
                                        <input type="hidden" value="{{ $noDependTotalQty }}" name="req_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][]" class="form-control rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
                                    @endif
                                </td>

                                <!--total value input field-->
                                <td class="text-custom-style">
                                    @php $r_qtyF = 0; @endphp
                                    @php
                                        $ptotal = ($bom->consumption * $bom->extra_percent)/100;
                                        $total = $ptotal + $bom->consumption;
                                    @endphp
                                    @if($bom->po_no != null)
                                        @php
                                            $noDependTotalQty = 0;
                                            if(isset($poSizeQtyListS[$bom->po_no])){
                                                if(!empty($poSizeQtyListS[$bom->po_no])){
                                                    foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
                                                        $noDependTotalQty += array_sum($eachNoDependQty);
                                                    }
                                                }
                                            }
                                            $noDependTotalQty = ($noDependTotalQty * $total)*$bom->precost_unit_price;
                                            $noDependTotalQty = Custom::fixedNumber($noDependTotalQty);
                                        @endphp
                                        {{$noDependTotalQty}}
                                        <input type="hidden" value="{{ $noDependTotalQty }}" name="total_value[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][]" class="form-control tvlu_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
                                    @else
                                        @php
                                            $noDependTotalQty = ($order->order_qty * $total)*$bom->precost_unit_price;
                                            $noDependTotalQty = Custom::fixedNumber($noDependTotalQty);
                                        @endphp
                                        {{$noDependTotalQty}}
                                        <input type="hidden" value="{{ $noDependTotalQty }}" name="total_value[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][]" class="form-control tvlu_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}" readonly="readonly">
                                    @endif
                                </td>
                                <td><input type="text"  name="booking_qty[{{ $bom->id }}][{{ $bom->order_id }}][{{$bom->sup_id}}][{{ $poSingle->po_id }}][{{0}}]" class="form-control rqty_rm_class {{$bom->id.$bom->order_id.$poSingle->po_id}}"></td>
                            </tr>
                        @endif
                    {{--@endif--}}
                    @php $itemIndex++; @endphp
                @endif
            @endif
        @endforeach
    @endforeach
@else
    <tr>
        <td colspan="14" class="text-center">PO Not Found.</td>
    </tr>
@endif

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
                    $(this).text('');
                    orLenght = $('.'+clsName).length;
                    // if(i < orLenght-1) {
                    //     $(this).css("border-bottom-color", "#fff");
                    // }
                } else {
                    orderCode = $(this).text();
                }
            }
        });
    }

</script>
