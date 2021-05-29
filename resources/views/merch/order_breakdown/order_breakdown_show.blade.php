@extends('merch.layout')
@push('css')
	<style>
		.alert.alert-info {
		    padding-top: 10px;
		    padding-bottom: 10px;
		    margin-bottom: 5px;
		}

		.alert.alert-info > h4 {
		    margin: 0;
		    font-weight: bold;
		}

		.alert.alert-info > h4 > i {
		    font-size: 20px;
		}
		td input[type=text], input[type=number], .text-custom-style {
		    color: #000;
		    font-weight: bold;
		    text-align: center;
		    border: navajowhite;
		    cursor: default;
			width: 30px;
		}
		.inputBgLight {
			background: #fff;
		}
		.inputBgLightGolder {
			background: lightgoldenrodyellow !important;
		}

		@media only screen and (max-width: 480px) {

        .col-sm-12{overflow-x: auto; display: block; width: 100%;}

    }
	</style>
@endpush
@section('main-content')

<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Order Break Down Show</a>
				</li>
				<li class="active">Order Color & Size Break Down Show</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">
			<div class="panel panel-info">
                    <div class="panel-heading page-headline-bar">
                        <h6>Order Color & Size Break Down Show
	                        {{-- <div class="text-right pull-right">
		                        <a href='{{ url("merch/orders/order_edit/"  .$order->order_id  ) }}' class="btn btn-xs btn-success" rel='tooltip' data-tooltip-location='top' data-tooltip="Edit Order"><i class="glyphicon glyphicon-pencil"></i> Edit Order</a>
		                    </div> --}}
	                    </h6>
                    </div>
                    <div class="panel-body">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                        <div class="panel panel-default">
                            <div class="panel-body" style="background-color: ghostwhite;">
                            	<div class="widget-body">
								<div class="row" style="background-color: ghostwhite;">
									<div class="col-sm-12">
										<table class="table" width="50%" cellpadding="0" cellspacing="0" border="0">
											<tr>
												<th>Order No</th>
												<td>{{ (!empty($order->order_code)?$order->order_code:null) }}</td>
												<th>Unit</th>
												<td>{{ (!empty($order->hr_unit_name)?$order->hr_unit_name:null) }}</td>
												<th>Buyer</th>
												<td>{{ (!empty($order->b_name)?$order->b_name:null) }}</td>
											</tr>
											<tr>
												<!-- <th>Brand</th>
												<td>{{ (!empty($order->br_name)?$order->br_name:null) }}</td> -->
												<th>Order Quantity</th>
												<td>{{ (!empty($order->order_qty)?$order->order_qty:null) }}</td>
												<th>Season</th>
												<td>{{ (!empty($order->se_name)?$order->se_name:null) }}</td>
												<th>Style No</th>
												<td>{{ (!empty($order->stl_no)?$order->stl_no:null) }}</td>
											</tr>
											<tr>

												<th>Delivery Date</th>
												<td>{{ (!empty($order->order_delivery_date)?$order->order_delivery_date:null) }}</td>
												<th>Reference No</th>
												<td>{{ (!empty($order->order_ref_no)?$order->order_ref_no:null) }}</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
                            </div>
                        </div>
                        <br>
                        @if(!empty($poList->toArray()))
							@php
								$poTotalNoSize = [];
							@endphp
							<div class="alert alert-info">
								<h4><i class="ace-icon glyphicon glyphicon-align-right"></i> Purchase Order Breakdown</h4>
							</div>
							<div id="accordion" class="accordion-style1 panel-group">
								@foreach($poList as $key=>$poSingle)
									@php
										$color_ids = [];
										if(isset($poSizeQtyListC[$poSingle->po_id])){
											$color_ids = array_keys($poSizeQtyListC[$poSingle->po_id]);
										}
									@endphp
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" aria-expanded="false">
													<i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
													&nbsp;Purchase Order No: {{ $poSingle->po_no }} | Country: {{ $poSingle->cnt_name }} | Color: {{ $poSingle->clr_name }}
												</a>
											</h4>
										</div>

										<div class="panel-collapse collapse" id="collapse{{$key}}" aria-expanded="false" style="height: 0px;">
											<div class="panel-body">
												<div class="table-responsive">
													<table id="bomCostingTable" class="table table-bordered table-condensed">
														<thead>
														<tr>
															<th>Main Category</th>
															<th>Item</th>
															<th>Description</th>
															<th>Article</th>
															<th>Supplier</th>
															<th>Consumption</th>
															<th>Extra (%)</th>
															<th>(Consumption & Extra)</th>
															<th>Uom</th>
															<th>Unit Price</th>
															<th>Color</th>
															<th>Size</th>
															<th>Qty</th>
															<th>Req Qty</th>
														</tr>
														</thead>
														<tbody>
														@php
															$itemIndex = 0;
														@endphp
														@foreach($boms as $bom)
															@if($bom->po_no == $poSingle->po_id || $bom->po_no == null)
															@php
																$poTrStyle = '#FFF';
																if($bom->po_no == $poSingle->po_id) {
																	$poTrStyle = 'lightgoldenrodyellow;';
																}
															@endphp
															<tr style="background-color: {{ $poTrStyle }}">
																<td class="vertical-align-center">
																	{{ $bom->mcat_name }}
																</td>
																<td class="vertical-align-center">{{ $bom->item_name }}
																	<input type="hidden" name="items[]" value="{{ $bom->item_name }}">
																</td>
																<td class="vertical-align-center">{{ $bom->item_description }}</td>
																<td class="vertical-align-center">{{ $bom->art_name }}</td>
																<th class="vertical-align-center">{{ $bom->sup_name }}</th>
																<td class="vertical-align-center">{{ $bom->consumption }}</td>
																<td class="vertical-align-center">{{ $bom->extra_percent }}</td>
																<td id="ce" class="vertical-align-center">
																	@php
																		$ptotal = ($bom->consumption * $bom->extra_percent)/100;
								                                        $total = $ptotal + $bom->consumption;
																	    echo $total;
																	@endphp
																</td>
																<td class="vertical-align-center">{{ $bom->uom }}</td>
																<td class="vertical-align-center">{{ $bom->precost_unit_price }}</td>

																<input type="hidden" name="bom_costing_booking_id-{{ $itemIndex }}[]" value="{{ $bom->id }}" class="form-control">

																<input type="hidden" name="order_entry_order_id-{{ $itemIndex }}[]" value="{{ $bom->order_id }}" class="form-control">

																<input type="hidden" name="cat_item_id-{{ $itemIndex }}[]" value="{{  $bom->mr_cat_item_id }}" class="form-control">

																<input type="hidden" name="cat_item_mcat_id-{{ $itemIndex }}[]" value="{{  $bom->mr_material_category_mcat_id  }}" class="form-control">

																<!--COlor input field-->
																<td>
																	@if($bom->depends_on == 1)
																		@php $colorF = 0; @endphp
																		@foreach($colors as $color)
																			@if(in_array($color->clr_id, $color_ids))
																				@if($colorF == 0)
																					@php $colorF = 1; @endphp
																					{{ $color->clr_name }}
																					<input type="hidden" name="clr_name-{{ $itemIndex }}[]" value="{{ $color->clr_name }}" class="form-control">
																					<input type="hidden" value="{{ $color->clr_id }}" name="clr_id-{{ $itemIndex }}[]" >

																					@else
																					<hr style="margin-top: 6px;margin-bottom: 6px;">
																					{{ $color->clr_name }}
																					<input type="hidden" name="clr_name-{{ $itemIndex }}[]" value="{{ $color->clr_name }}" class="form-control">
																					<input type="hidden" value="{{ $color->clr_id }}" name="clr_id-{{ $itemIndex }}[]" >

																				@endif
																			@endif
																		@endforeach
																	@elseif($bom->depends_on == 3)
																		<span style="line-height:20px;"></span>
																		@php
																			$care_colorF = 0;
																			$index=0;
																			$filtersize=count($filter)-1;
																		@endphp
																		@foreach($filter as $clrId=>$cl)
																			@if(in_array($clrId, $color_ids))
																			    {{ $cl }}
																				<?php $counts = (count($sizes));
																				if($index<$filtersize){
																				    if($index == 0){$value=($counts-1)*35+14;?>
																					<hr style="margin-top: {{$value}}px;margin-bottom: 14px;">
																					<?php }elseif($index == 1){$value=($counts-1)*35+15;?>
																					<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																					<?php }elseif($index == 2){$value=($counts-1)*35+6;?>
																					<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																					<?php }else{$value=($counts-1)*36-3;?>
																					<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																					<?php }  ?>
																				<?php } $index++; ?>
																			@endif
																		@endforeach
																	@else
																		<p></p>
																	@endif
																</td>

																<!--size input field-->
																<td>
																	@if($bom->depends_on == 2)
																		@php $sizeF = 0; @endphp
																		@foreach($sizes as $size)
																			@if($sizeF == 0)
																				@php $sizeF = 1; @endphp
																				<input type="text" name="sizer-{{ $itemIndex }}[]" value="{{ $size->mr_product_pallete_name }}" class="form-control" readonly="readonly">

																			@else
																				<hr style="margin-top: 6px;margin-bottom: 6px;">
																				<input type="text" name="sizer-{{ $itemIndex }}[]" value="{{ $size->mr_product_pallete_name }}" class="form-control" readonly="readonly">

																			@endif
																		@endforeach
																	@elseif($bom->depends_on == 3)
																		<span style="line-height:30px;"></span>
																	    @php $care_sizeF = 0 @endphp
																		@foreach($care_label as $color)
																			@if(in_array($color->clr_id, $color_ids))
																				@if($care_sizeF == 0)
																					@php $care_sizeF = 1; @endphp
																					<input type="text" name="sizes-{{$itemIndex}}[]" value="{{ $color->mr_product_pallete_name }}" class="form-control" size="1" readonly="readonly">

																					<input type="hidden" name="clr_ids-{{$itemIndex}}[]" value="{{ $color->clr_id }}" class="form-control">
																				@else
																					<hr style="margin-top: 6px;margin-bottom: 6px;">
																					<input type="text" name="sizes-{{$itemIndex}}[]" value="{{ $color->mr_product_pallete_name }}" class="form-control" size="1" readonly="readonly">

																					<input type="hidden" name="clr_ids-{{$itemIndex}}[]" value="{{ $color->clr_id }}" class="form-control">

																				@endif
																			@endif
																		@endforeach
																	@else
																		<p></p>
																	@endif
																</td>

																<!--qty input field-->
																<td class="text-custom-style">
																	@if($bom->depends_on == 1)
																		@php $qtyF = 0; @endphp
																		@foreach($colors as $un)
																			@if(in_array($un->clr_id, $color_ids))
																				@php
																					$posSubTotalQty = 0;
																					if(isset($poColorQtyListC[$poSingle->po_id][$un->clr_id][$poSingle->po_id])) {
																						foreach($poColorQtyListC[$poSingle->po_id][$un->clr_id][$poSingle->po_id] as $pos_id=>$pos_value) {
																							$posSubTotalQty += $pos_value;
																						}
																					}
																				@endphp
																				@if($qtyF == 0)
																					@php $qtyF = 1; @endphp
																					{{ $posSubTotalQty }}
																					<input type="hidden" name="qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty }}" class="form-control" readonly="readonly">
																				@else
																					<hr style="margin-top: 6px;margin-bottom: 6px;">
																					{{ $posSubTotalQty }}
																					<input type="hidden" name="qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty }}" class="form-control" readonly="readonly">
																				@endif
																			@endif
																		@endforeach
																	@elseif($bom->depends_on == 2)
																		@php $sizeF = 0; @endphp
																		@foreach($sizes as $size)
																			@php $poSizeQty = 0;
																				if(isset($poSizeQtyListS[$poSingle->po_id])){
																					if(isset($poSizeQtyListS[$poSingle->po_id][$size->id])){
																						$poSizeQty = array_sum($poSizeQtyListS[$poSingle->po_id][$size->id]);
																						if(!isset($poTotalNoSize[$size->id])) {
																							$poTotalNoSize[$size->id] = 0;
																						}
																						$poTotalNoSize[$size->id] += array_sum($poSizeQtyListS[$poSingle->po_id][$size->id]);
																					}
																				}
																			@endphp
																			@if($sizeF == 0)
																				@php $sizeF = 1; @endphp
																				<input type="text" name="size_qtyss-{{$itemIndex}}[]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1" readonly="readonly">
																			@else
																				<hr style="margin-top: 6px;margin-bottom: 6px;">
																				<input type="text" name="size_qtyss-{{$itemIndex}}[]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1" readonly="readonly">
																			@endif
							                                         	@endforeach
																	@elseif($bom->depends_on == 3)
																		@php $care_labelF  = 0; @endphp
																		@foreach($care_label as $color)
																			@if(in_array($color->clr_id, $color_ids))
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
																				@if($care_labelF  == 0)
																					@php
																						$care_labelF  = 1;
																					@endphp
																					<input type="text" name="s_qtyss-{{$itemIndex}}[]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $posSubTotalQtyCS }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1">
																				@else
																					<hr style="margin-top: 6px;margin-bottom: 6px;">
																					<input type="text" name="s_qtyss-{{$itemIndex}}[]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $posSubTotalQtyCS }}" class="form-control global-qty {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1">
																				@endif
																			@endif
																		@endforeach
																	@elseif($bom->depends_on == 0)
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
																			@endphp
																			{{ $noDependTotalQty }}
																		    <input type="hidden" value="{{ $noDependTotalQty }}" name="order_qty-{{ $itemIndex }}[]" class="form-control">
																	    @else
																	    	@php
																				$noDependTotalQty = 0;
																				if(isset($poSizeQtyListS[$poSingle->po_id])){
																					if(!empty($poSizeQtyListS[$poSingle->po_id])){
																						foreach($poSizeQtyListS[$poSingle->po_id] as $eachNoDependQty){
																							$noDependTotalQty += array_sum($eachNoDependQty);
																						}
																					}
																				}
																	    	@endphp
																			{{ $noDependTotalQty }}
																		    <input type="hidden" value="{{ $noDependTotalQty }}" name="order_qty-{{ $itemIndex }}[]" class="form-control">
																	    @endif
																	@else
																		<p>Nothing Found</p>
																	@endif
																</td>

																<!--req qty input field-->
																<td class="text-custom-style">
																	@php $r_qtyF = 0; @endphp
																	@php
																		$ptotal = ($bom->consumption * $bom->extra_percent)/100;
																	    $total = $ptotal + $bom->consumption;
																	@endphp
																	@if($bom->depends_on == 1)
																		@php $r_qtyF = 0; @endphp
																		@foreach($colors as $un)
																			@if(in_array($un->clr_id, $color_ids))
																			@php
																				$posSubTotalQty = 0;
																				if(isset($poColorQtyListC[$poSingle->po_id][$un->clr_id][$poSingle->po_id])) {
																					foreach($poColorQtyListC[$poSingle->po_id][$un->clr_id][$poSingle->po_id] as $pos_id=>$pos_value) {
																						$posSubTotalQty += $pos_value;
																					}
																				}
																			@endphp
																				@if($r_qtyF == 0)
																					@php $r_qtyF = 1; @endphp
																					{{ $posSubTotalQty * $total}}
																				   <input type="hidden" name="req_qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty * $total }}" class="form-control" size="1">

																				@else
																					<hr style="margin-top: 6px;margin-bottom: 6px;">
																					{{ $posSubTotalQty * $total}}
																				  <input type="hidden" name="req_qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty * $total }}" class="form-control" size="1">

																				@endif
																			@endif
																		@endforeach
																	@elseif($bom->depends_on == 2)
																		@php $sizeF = 0; @endphp
																		@foreach($sizes as $size)
																			@php $poSizeQty = 0;
																				if(isset($poSizeQtyListS[$poSingle->po_id])){
																					if(isset($poSizeQtyListS[$poSingle->po_id][$size->id])){
																						$poSizeQty = array_sum($poSizeQtyListS[$poSingle->po_id][$size->id])*$total;
																					}
																				}
																			@endphp
																			@if($sizeF == 0)
																				@php $sizeF = 1; @endphp
																				<input type="text" name="req_qtyr-{{ $itemIndex }}[]" id="req_qtyr-{{$size->mr_product_pallete_name}}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global_total {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1" readonly="readonly">
																			@else
																				<hr style="margin-top: 6px;margin-bottom: 6px;">
																				<input type="text" name="req_qtyr-{{ $itemIndex }}[]" id="req_qtyr-{{$size->mr_product_pallete_name}}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global_total {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1" readonly="readonly">
																			@endif
						                                        		@endforeach
																	@elseif($bom->depends_on == 3)
																		<span style="margin-top: 6px;display: block;"></span>
																		@php $care_labelF  = 0; @endphp
																		@foreach($care_label as $color)
																			@if(in_array($color->clr_id, $color_ids))
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
																				@endphp
																				@if($care_labelF  == 0)
																					@php $care_labelF  = 1; @endphp
																					<input type="text" value="{{ $posSubTotalQtyCS }}" name="req_qtys-{{$itemIndex}}[]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1" readonly="readonly">
																				@else
																					<hr style="margin-top: 6px;margin-bottom: 6px;">
																					<input type="text" value="{{ $posSubTotalQtyCS }}" name="req_qtys-{{$itemIndex}}[]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals {{ $bom->po_no!=null?'inputBgLightGolder':'inputBgLight' }}" size="1" readonly="readonly">
																				@endif
																			@endif
																		@endforeach
																	@elseif($bom->depends_on == 0)
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
																			@endphp
																			{{ $noDependTotalQty * $total }}
																			<input type="hidden" value="{{ $noDependTotalQty * $total }}" name="order_req_qty-{{ $itemIndex }}[]" class="form-control" readonly="readonly">
																	    @else
																	    	@php
																	    		$noDependTotalQty = 0;
																				if(isset($poSizeQtyListS[$poSingle->po_id])){
																					if(!empty($poSizeQtyListS[$poSingle->po_id])){
																						foreach($poSizeQtyListS[$poSingle->po_id] as $eachNoDependQty){
																							$noDependTotalQty += array_sum($eachNoDependQty);
																						}
																					}
																				}
																	    	@endphp
																			{{ $noDependTotalQty }}
																			<input type="hidden" value="{{ $noDependTotalQty }}" name="order_req_qty-{{ $itemIndex }}[]" class="form-control" readonly="readonly">
																	    @endif
																	@else
																		<p>Nothing Found</p>
																	@endif
																</td>
															</tr>
															@php $itemIndex++; @endphp
															@endif
														@endforeach

														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								@endforeach
							</div>
						@endif


						{{-- Total Breakdown --}}
						<div class="alert alert-info">
							<h4><i class="ace-icon glyphicon glyphicon-align-right"></i> Total Breakdown</h4>
						</div>
						<div class="table-responsive">
							<table id="bomCostingTable" class="table table-bordered table-condensed">
								<thead>
								<tr>
									<th>Main Category</th>
									<th>Item</th>
									<th>Description</th>
									<th>Article</th>
									<th>Supplier</th>
									<th>Consumption</th>
									<th>Extra (%)</th>
									<th>(Consumption & Extra)</th>
									<th>Uom</th>
									<th>Unit Price</th>
									<th>Color</th>
									<th>Size</th>
									<th>Qty</th>
									<th>Req Qty</th>
								</tr>
								</thead>
								<tbody>
									@foreach($itemUnique as $item)
										@php
											$itemIndex = 0;
										@endphp
										@foreach($boms as $bom)
											@if($bom->item_name == $item)
											<tr>
												@if($catCount[$bom->mcat_name] > 0)
												<td rowspan="{{ $catCount[$bom->mcat_name] }}" class="vertical-align-center">
													@php $catCount[$bom->mcat_name] = 0; @endphp
													{{ $bom->mcat_name }}
												</td>
												@endif
												<td class="vertical-align-center">{{ $bom->item_name }}
													<input type="hidden" name="items[]" value="{{ $bom->item_name }}">
												</td>
												<td class="vertical-align-center">{{ $bom->item_description }}</td>
												<td class="vertical-align-center">{{ $bom->art_name }}</td>
												<th class="vertical-align-center">{{ $bom->sup_name }}</th>
												<td class="vertical-align-center">{{ $bom->consumption }}</td>
												<td class="vertical-align-center">{{ $bom->extra_percent }}</td>
												<td id="ce" class="vertical-align-center">
													@php
														$ptotal = ($bom->consumption * $bom->extra_percent)/100;
				                                        $total = $ptotal + $bom->consumption;
													    echo $total;
													@endphp
												</td>
												<td class="vertical-align-center">{{ $bom->uom }}</td>
												<td class="vertical-align-center">{{ $bom->precost_unit_price }}</td>

												<input type="hidden" name="bom_costing_booking_id-{{ $itemIndex }}[]" value="{{ $bom->id }}" class="form-control">

												<input type="hidden" name="order_entry_order_id-{{ $itemIndex }}[]" value="{{ $bom->order_id }}" class="form-control">

												<input type="hidden" name="cat_item_id-{{ $itemIndex }}[]" value="{{  $bom->mr_cat_item_id }}" class="form-control">

												<input type="hidden" name="cat_item_mcat_id-{{ $itemIndex }}[]" value="{{  $bom->mr_material_category_mcat_id  }}" class="form-control">

												{{-- COlor input field --}}
												<td>
													@if($bom->depends_on == 1)
														@php $colorF = 0; @endphp
														@if($bom->po_pos_cid != null)
															@php
																$color_ids = [];
																$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
															@endphp
															@foreach($colors as $color)
																@if(in_array($color->clr_id, $color_ids))
																	@if($colorF == 0)
																		@php $colorF = 1; @endphp
																		<span>{{ $color->clr_name }}</span>
																		<input type="hidden" name="clr_name-{{ $itemIndex }}[]" value="{{ $color->clr_name }}" class="form-control">
																		<input type="hidden" value="{{ $color->clr_id }}" name="clr_id-{{ $itemIndex }}[]" >

																		@else
																		<hr style="margin-top: 6px;margin-bottom: 6px;">
																		<span>{{ $color->clr_name }}</span>
																		<input type="hidden" name="clr_name-{{ $itemIndex }}[]" value="{{ $color->clr_name }}" class="form-control">
																		<input type="hidden" value="{{ $color->clr_id }}" name="clr_id-{{ $itemIndex }}[]" >
																	@endif
																@endif
															@endforeach
														@else
															@foreach($colors as $color)
																@if($colorF == 0)
																	@php $colorF = 1; @endphp
																	<span>{{ $color->clr_name }}</span>
																	<input type="hidden" name="clr_name-{{ $itemIndex }}[]" value="{{ $color->clr_name }}" class="form-control">
																	<input type="hidden" value="{{ $color->clr_id }}" name="clr_id-{{ $itemIndex }}[]" >

																	@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																	<span>{{ $color->clr_name }}</span>
																	<input type="hidden" name="clr_name-{{ $itemIndex }}[]" value="{{ $color->clr_name }}" class="form-control">
																	<input type="hidden" value="{{ $color->clr_id }}" name="clr_id-{{ $itemIndex }}[]" >

																@endif
															@endforeach
														@endif
														@elseif($bom->depends_on == 3)
															<span style="line-height:20px;"></span>
															@php
																$care_colorF = 0;
																$index=0;
																$filtersize=count($filter)-1;
															@endphp
															@if($bom->po_pos_cid != null)
																@php
																	$color_ids = [];
																	$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
																@endphp
																@foreach($filter as $clrId=>$cl)
																	@if(in_array($clrId, $color_ids))
																	    {{ $cl  }}
																		<?php $counts = (count($sizes));
																		if($index<$filtersize){
																		    if($index == 0){$value=($counts-1)*35+14;?>
																			<hr style="margin-top: {{$value}}px;margin-bottom: 14px;">
																			<?php }elseif($index == 1){$value=($counts-1)*35+15;?>
																			<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																			<?php }elseif($index == 2){$value=($counts-1)*35+6;?>
																			<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																			<?php }else{$value=($counts-1)*36-3;?>
																			<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																			<?php }  ?>
																		<?php } $index++; ?>
																	@endif
																@endforeach
															@else
																@foreach($filter as $clrId=>$cl)
																    {{ $cl  }}
																	<?php $counts = (count($sizes));
																	if($index<$filtersize){
																	    if($index == 0){$value=($counts-1)*35+14;?>
																		<hr style="margin-top: {{$value}}px;margin-bottom: 14px;">
																		<?php }elseif($index == 1){$value=($counts-1)*35+15;?>
																		<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																		<?php }elseif($index == 2){$value=($counts-1)*35+6;?>
																		<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																		<?php }else{$value=($counts-1)*36-3;?>
																		<hr style="margin-top: {{$value}}px;margin-bottom: 13px;">
																		<?php }  ?>
																	<?php } $index++; ?>
																@endforeach
															@endif
														@else
														<p></p>
													@endif
												</td>

												{{-- size input field --}}
												<td>
													@if($bom->depends_on == 2)
														@php $sizeF = 0; @endphp
														@foreach($sizes as $size)
															@if($sizeF == 0)
																@php $sizeF = 1; @endphp
																<input type="text" name="sizer-{{ $itemIndex }}[]" value="{{ $size->mr_product_pallete_name }}" class="form-control" readonly="readonly">

															@else
																<hr style="margin-top: 6px;margin-bottom: 6px;">
																<input type="text" name="sizer-{{ $itemIndex }}[]" value="{{ $size->mr_product_pallete_name }}" class="form-control" readonly="readonly">
															@endif
														@endforeach
													@elseif($bom->depends_on == 3)
														<span style="line-height:30px;"></span>
													    @php $care_sizeF = 0 @endphp
													    @if($bom->po_pos_cid != null)
													    	@php
																$color_ids = [];
																$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
															@endphp
															@foreach($care_label as $color)
																@if(in_array($color->clr_id, $color_ids))
																	@if($care_sizeF == 0)
																		@php $care_sizeF = 1; @endphp
																		<span style="line-height:24px;">{{ $color->mr_product_pallete_name }}</span>
																		<input type="hidden" name="sizes-{{$itemIndex}}[]" value="{{ $color->mr_product_pallete_name }}" class="form-control" size="1">

																		<input type="hidden" name="clr_ids-{{$itemIndex}}[]" value="{{ $color->clr_id }}" class="form-control">
																		<hr style="margin-top: 6px;margin-bottom: 6px;">
																	@else
																		<span style="line-height:24px;">{{ $color->mr_product_pallete_name }}</span>
																		<input type="hidden" name="sizes-{{$itemIndex}}[]" value="{{ $color->mr_product_pallete_name }}" class="form-control" size="1">

																		<input type="hidden" name="clr_ids-{{$itemIndex}}[]" value="{{ $color->clr_id }}" class="form-control">
																		<hr style="margin-top: 6px;margin-bottom: 6px;">

																	@endif
																@endif
															@endforeach
														@else
															@foreach($care_label as $color)
																@if($care_sizeF == 0)
																	@php $care_sizeF = 1; @endphp
																	<input type="text" name="sizes-{{$itemIndex}}[]" value="{{ $color->mr_product_pallete_name }}" class="form-control" size="1" readonly="readonly">

																	<input type="hidden" name="clr_ids-{{$itemIndex}}[]" value="{{ $color->clr_id }}" class="form-control">
																@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																	<input type="text" name="sizes-{{$itemIndex}}[]" value="{{ $color->mr_product_pallete_name }}" class="form-control" size="1" readonly="readonly">

																	<input type="hidden" name="clr_ids-{{$itemIndex}}[]" value="{{ $color->clr_id }}" class="form-control">
																@endif
															@endforeach
														@endif
													@else
														<p></p>
													@endif
												</td>

												{{-- qty input field --}}
												<td class="text-custom-style">
													@if($bom->depends_on == 1)
														@php $qtyF = 0; @endphp
														@if($bom->po_pos_cid != null)
															@php
																$color_ids = [];
																$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
															@endphp
															@foreach($colors as $un)
																@if(in_array($un->clr_id, $color_ids))
																	@php
																		$posSubTotalQty = 0;
																		if(isset($poColorQtyList[$bom->po_pos_cid])){
																			foreach($poColorQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
																				$posSubTotalQty += array_sum($poSizeQtyListSingle);
																			}
																		}
																	@endphp
																	@if($qtyF == 0)
																		@php $qtyF = 1; @endphp
																		<input type="text" name="qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty }}" class="form-control" readonly="readonly">
																	@else
																		<hr style="margin-top: 6px;margin-bottom: 6px;">
																		<input type="text" name="qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty }}" class="form-control" readonly="readonly">
																	@endif
																@endif
															@endforeach
														@else
															@foreach($colors as $un)
																@php
																	$posSubTotalQty = 0;
																	if(isset($poColorQtyListCN[$bom->order_id])){
																		foreach($poColorQtyListCN[$bom->order_id][$un->clr_id] as $poSizeQtyListSingle) {
																			$posSubTotalQty += $poSizeQtyListSingle;
																		}
																	}
																@endphp
																@if($qtyF == 0)
																	@php $qtyF = 1; @endphp
																	<input type="text" name="qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty }}" class="form-control" readonly="readonly">
																@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																	<input type="text" name="qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty }}" class="form-control" readonly="readonly">
																@endif
															@endforeach
														@endif
													@elseif($bom->depends_on == 2)
														@php $sizeF = 0; @endphp
														@if($bom->po_po_id != null)
															@foreach($sizes as $size)
																@php
																	$poSizeQty = 0;
																	if(isset($poSizeQtyListS[$bom->po_po_id])){
																		if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
																			$poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
																		}
																		// dump($poSizeQtyListS[$bom->po_po_id]);
																	}
																@endphp
																@if($sizeF == 0)
																	@php $sizeF = 1; @endphp
																<input type="text" name="size_qtyss-{{$itemIndex}}[]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty" size="1" readonly="readonly">
																@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																<input type="text" name="size_qtyss-{{$itemIndex}}[]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty" size="1" readonly="readonly">
																@endif
						                                 	@endforeach
					                                 	@else
															@foreach($sizes as $size)
									                            @php
									                                $poSizeQty = 0;
									                                if(isset($poSizeQtyListSN[$bom->order_id])){
									                                    if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
									                                        foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $sizeQty) {
																				$poSizeQty += array_sum($sizeQty);
																			}
									                                    }
									                                }
									                            @endphp
									                            @if($sizeF == 0)
									                                @php $sizeF = 1; @endphp
									                            <input type="text" name="size_qtyss-{{$itemIndex}}[]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty" size="1" readonly="readonly">
									                            @else
									                                <hr style="margin-top: 6px;margin-bottom: 6px;">
									                            <input type="text" name="size_qtyss-{{$itemIndex}}[]" id="size_qty-{{ $size->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $poSizeQty }}" class="form-control global-qty" size="1" readonly="readonly">
									                            @endif
									                        @endforeach
					                                 	@endif
													@elseif($bom->depends_on == 3)
														@php $care_labelF  = 0; @endphp
														@if($bom->po_pos_cid != null)
															@php
																$color_ids = [];
																$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
															@endphp
															@foreach($care_label as $color)
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
																	@if($care_labelF  == 0)
																		@php
																			$care_labelF  = 1;
																		@endphp
																		<input type="text" name="s_qtyss-{{$itemIndex}}[]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{$posSubTotalQtyCS}}" class="form-control global-qty" size="1" readonly="readonly">
																	@else
																		<hr style="margin-top: 6px;margin-bottom: 6px;">
																		<input type="text" name="s_qtyss-{{$itemIndex}}[]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{$posSubTotalQtyCS}}" class="form-control global-qty" size="1" readonly="readonly">
																	@endif
																@endif
															@endforeach
														@else
															@foreach($care_label as $color)
																@php
																	$posSubTotalQtyCS = 0;
																	if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
																		if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
																			$posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
																		}
																	}
																@endphp
																@if($care_labelF  == 0)
																	@php
																		$care_labelF  = 1;
																	@endphp
																	<input type="text" name="s_qtyss-{{$itemIndex}}[]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $posSubTotalQtyCS }}" class="form-control global-qty" size="1" readonly="readonly">
																@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																	<input type="text" name="s_qtyss-{{$itemIndex}}[]" id="size_qtys-{{ $color->clr_name }}_{{ $color->mr_product_pallete_name }}_{{$itemIndex}}" value="{{ $posSubTotalQtyCS }}" class="form-control global-qty" size="1" readonly="readonly">
																@endif
															@endforeach
														@endif
													@elseif($bom->depends_on == 0)
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
															@endphp
															{{ $noDependTotalQty }}
													    	<input type="hidden" value="{{ $noDependTotalQty }}" name="order_qty-{{ $itemIndex }}[]" class="form-control">
														@else
														{{ (!empty($order->order_qty)?$order->order_qty:null) }}
															{{-- {{ $order->order_qty }} --}}
													    	<input type="hidden" value="{{ (!empty($order->order_qty)?$order->order_qty:null) }}" name="order_qty-{{ $itemIndex }}[]" class="form-control">
														@endif
													@else
														<p>Nothing Found</p>
													@endif
												</td>

												{{-- req qty input field --}}
												<td class="text-custom-style">
													@php $r_qtyF = 0; @endphp
													@php
														$ptotal = ($bom->consumption * $bom->extra_percent)/100;
													    $total = $ptotal + $bom->consumption;
													@endphp
													@if($bom->depends_on == 1)
														@php $r_qtyF = 0; @endphp
														@if($bom->po_pos_cid != null)
															@php
																$color_ids = [];
																$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
															@endphp
															@foreach($colors as $un)
																@if(in_array($un->clr_id, $color_ids))
																@php
																	$posSubTotalQty = 0;
																	if(isset($poColorQtyList[$bom->po_pos_cid])){
									                                    foreach($poColorQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
									                                        $posSubTotalQty += array_sum($poSizeQtyListSingleS);
									                                    }
									                                }
																@endphp
																	@if($r_qtyF == 0)
																		@php $r_qtyF = 1; @endphp
																		{{ $posSubTotalQty * $total}}
																	   <input type="hidden" name="req_qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty * $total }}" class="form-control" size="1">

																	@else
																		<hr style="margin-top: 6px;margin-bottom: 6px;">
																		{{ $posSubTotalQty * $total}}
																	  <input type="hidden" name="req_qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty * $total }}" class="form-control" size="1">

																	@endif
																@endif
															@endforeach
														@else
															@foreach($colors as $un)
																@php
																	$posSubTotalQty = 0;
																	if(isset($poColorQtyListCN[$bom->order_id])){
										                                foreach($poColorQtyListCN[$bom->order_id][$un->clr_id] as $poSizeQtyListSingle) {
										                                    $posSubTotalQty += $poSizeQtyListSingle;
										                                }
										                            }
																@endphp
																@if($r_qtyF == 0)
																	@php $r_qtyF = 1; @endphp
																   <input type="text" name="req_qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty * $total }}" class="form-control" size="1" readonly="readonly">

																@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																  <input type="text" name="req_qty-{{$itemIndex}}[]" value="{{ $posSubTotalQty * $total }}" class="form-control" size="1" readonly="readonly">

																@endif
															@endforeach
														@endif
													@elseif($bom->depends_on == 2)
														@php $sizeF = 0; @endphp
														@foreach($sizes as $size)
															@php
																$poSizeQty = 0;
																if($bom->po_po_id != null) {
																	if(isset($poSizeQtyListS[$bom->po_po_id])){
																		if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
																			$poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
																		}
																	}
																} else {
																	if(isset($poSizeQtyListSN[$bom->order_id])){
									                                    if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
									                                        foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $sizeQty) {
																				$poSizeQty += array_sum($sizeQty);
																			}
									                                    }
									                                }
																}
															@endphp
															@if($sizeF == 0)
																@php $sizeF = 1; @endphp
																<input type="text" name="req_qtyr-{{ $itemIndex }}[]" id="req_qtyr-{{$size->mr_product_pallete_name}}_{{$itemIndex}}" value="{{ $poSizeQty * $total }}" class="form-control global_total" size="1" readonly="readonly">
															@else
																<hr style="margin-top: 6px;margin-bottom: 6px;">
																<input type="text" name="req_qtyr-{{ $itemIndex }}[]" id="req_qtyr-{{$size->mr_product_pallete_name}}_{{$itemIndex}}" value="{{ $poSizeQty * $total }}" class="form-control global_total" size="1" readonly="readonly">
															@endif
				                                        @endforeach
													@elseif($bom->depends_on == 3)
														<span style="margin-top: 6px;display: block;"></span>
														@php $care_labelF  = 0; @endphp
														@if($bom->po_pos_cid != null)
															@php
																$color_ids = [];
																$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
															@endphp
															@foreach($care_label as $color)
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
																	@if($care_labelF  == 0)
																		@php $care_labelF  = 1; @endphp
																		<input type="text" value="{{ $posSubTotalQtyCS * $total }}" name="req_qtys-{{$itemIndex}}[]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals" size="1" readonly="readonly">
																	@else
																		<hr style="margin-top: 6px;margin-bottom: 6px;">
																		<input type="text" value="{{ $posSubTotalQtyCS * $total }}" name="req_qtys-{{$itemIndex}}[]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals" size="1" readonly="readonly">
																	@endif
																@endif
															@endforeach
														@else
															@foreach($care_label as $color)
																@php
																	$posSubTotalQtyCS = 0;
																	if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
																		if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
																			$posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
																		}
																	}
																@endphp
																@if($care_labelF  == 0)
																	@php $care_labelF  = 1; @endphp
																	<input type="text" value="{{ $posSubTotalQtyCS * $total }}" name="req_qtys-{{$itemIndex}}[]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals" size="1" readonly="readonly">
																@else
																	<hr style="margin-top: 6px;margin-bottom: 6px;">
																	<input type="text" value="{{ $posSubTotalQtyCS * $total }}" name="req_qtys-{{$itemIndex}}[]" id="req_qtyr-{{ $color->clr_name }}_{{$color->mr_product_pallete_name}}_{{$itemIndex}}"  class="form-control global_totals" size="1" readonly="readonly">
																@endif
															@endforeach
														@endif
													@elseif($bom->depends_on == 0)
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
															@endphp
															{{ $noDependTotalQty * $total }}
															<input type="hidden" value="{{ $noDependTotalQty * $total }}" name="order_req_qty-{{ $itemIndex }}[]" class="form-control">
														@else
														{{ (!empty($order->order_qty)?$order->order_qty * $total:null) }}
															{{-- {{ $order->order_qty * $total }} --}}
															<input type="hidden" value="{{ (!empty($order->order_qty)?$order->order_qty * $total:null) }}" name="order_req_qty-{{ $itemIndex }}[]" class="form-control">
														@endif
													@else
														<p>Nothing Found</p>
													@endif
												</td>
											</tr>
											@php $itemIndex++; @endphp
											@endif
										@endforeach
									@endforeach
								</tbody>
							</table>
						</div>
                    </div>
             </div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
//        var ce = $('#ce td').val();
//        console.log(ce);
        $('#bomCostingTable tr').each(function() {
            var ce = $(this).find("#ce").html();
            //console.log(ce);
            $(this).find("#ce").each(function(){

                //console.log(ce);

			})

        });
    });
</script>
@push('js')
	<script>
		$(document).on('change keyup blur','.global-qty',function(){
			var id = $(this).attr('id');
			var idSplit = id.split('-');
			var idF = idSplit[0];
			//console.log(idF);
			var idS = idSplit[1];
			//console.log(idS);
			var qty = $(this).val();
			qty = qty == '' ? '0' : qty;
			var ce = $(this).parent().parent().find("#ce").html();
			var totalReq = parseFloat(parseFloat(ce) * parseFloat(qty)).toFixed(2);
			$("#req_qtyr-"+idS).val(totalReq);
		});

//        $(document).on('change keyup blur','.global-qtys',function(){
//            var id = $(this).attr('id');
//            var idSplit = id.split('-');
//            var idF = idSplit[0];
//            //console.log(idF);
//            var idS = idSplit[1];
//            //console.log(idS);
//            var qty = $(this).val();
//            qty = qty == '' ? '0' : qty;
//            var ce = $(this).parent().parent().find("#ce").html();
//
//            var totalReq = parseFloat(parseFloat(ce) * parseFloat(qty)).toFixed(2);
//            $("#req_qtys-"+idS).val(totalReq);
//
//        });
	</script>
@endpush
@endsection
