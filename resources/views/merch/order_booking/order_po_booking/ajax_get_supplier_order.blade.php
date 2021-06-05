@php
	$globalSupplierList = [];
	$checkItem = [];
@endphp
@if(!empty($buyerOrderList->toArray()))
{{--    {{dd($buyerOrderList)}}--}}
	@foreach($buyerOrderList as $singleBuyer)
		@php
			if($supId != null) {
				$itemList = Custom::getOrderItemList($singleBuyer->order_id, $supId);
			} else {
				$itemList = Custom::getOrderItemList($singleBuyer->order_id);
			}
		@endphp
		@empty(!$itemList)
			@php
				// dump($itemList);
			@endphp
			<tr>
				<td>{{ $singleBuyer->order_code }}<?php //dump($itemList);?></td>
				{{-- @if($supId != null)
					<td>{{ $singleBuyer->sup_name }}</td>
				@else
					<td>
						@php
							$supplierList = Custom::getOrderSupplierList($singleBuyer->order_id);
							$globalSupplierList[] = $supplierList;
						@endphp
						@if(!empty($supplierList))
							@foreach($supplierList as $supK=>$supplier)
								@if(!$loop->last)
									{{$supplier}},<br>
								@else
									{{$supplier}}
								@endif
							@endforeach
						@endif
					</td>
				@endif --}}
				<td>
					@foreach($itemList as $i=>$item)
						@if(strpos($item, '%') !== false)
							@php
								list($itemName,$itemQty) = array_pad(explode('~',$item),2,null);
								list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
							@endphp
							{!!'<span class="label label-info">'.$itemName.'</span><br>'!!}
						@else
							{!!'<span class="label label-info">'.$item.'</span><br>'!!}
						@endif
					@endforeach
				</td>
				<td>
					@foreach($itemList as $i=>$item)
						@if(strpos($item, '%') !== false)
							@php
								list($itemName,$itemQty) = array_pad(explode('~',$item),2,null);
								$styleBc = '';
								if(strpos($itemQty,'|0|') !== false) {
									$rQty = 0;
									$styleBc = 'style="background-color: #fff;"';
								} else {
									list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
									$rQty = Custom::fixedNumber($rQty,2,true);
								}
							@endphp
							{!!'<span class="label label-info" '.$styleBc.'>'.$rQty.'</span><br>'!!}
						@else
							{!!'<span class="label label-info" style="background-color: #fff;">0</span><br>'!!}
						@endif
					@endforeach
				</td>
				<td>
					@foreach($itemList as $i=>$item)
						@if(strpos($item, '%') !== false)
							@php
								list($itemName,$itemQty) = array_pad(explode('~',$item),2,null);
								$styleBc = '';
								if(strpos($itemQty,'|0|') !== false) {
									$bQty = 0;
									$styleBc = 'style="background-color: #fff;"';
								} else {
									list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
									$bQty = Custom::fixedNumber($bQty,2,true);
								}
							@endphp
							{!!'<span class="label label-info" '.$styleBc.'>'.$bQty.'</span><br>'!!}
						@else
							{!!'<span class="label label-info" style="background-color: #fff;">0</span><br>'!!}
						@endif
					@endforeach

{{--                    @foreach($booking_qty as $qty)

                    @endforeach--}}
				</td>
				<td>
					@foreach($itemList as $i=>$item)
						@php
							$label = 'info';
						@endphp
						@if(strpos($item, '%') !== false)
							@php
								list($itemName,$itemQty) = array_pad(explode('~',$item),2,null);
								list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
								$label = strpos($percentage,'100')!==false?'success':'warning';
							@endphp
							{!!'<span class="label label-'.$label.'">'.$percentage.'</span><br>'!!}
						@else
							{!!'<span class="label label-'.$label.'">0.00%</span><br>'!!}
						@endif
					@endforeach
				</td>
				<td>{{ $singleBuyer->order_delivery_date }}</td>
				<td>
					@if($supId != null || $orderId != null)
						@foreach($itemList as $i=>$item)
							@if(strpos($item, '%') !== false)
								@php
									list($itemName,$itemQty) = array_pad(explode('~',$item),2,null);
									list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
								@endphp
								@if($percentage != '100.00%')
									@if(!in_array($singleBuyer->order_id, $checkItem))
										@php
											$checkItem[] = $singleBuyer->order_id;
										@endphp
										<input type="checkbox" class="supplier_order_checkbox" id="order_id{{$singleBuyer->order_id}}" data-oid="{{$singleBuyer->order_id}}" data-supid="{{ $singleBuyer->mr_supplier_sup_id }}" data-order="{{$orderId}}" data-uid="{{ $singleBuyer->unit_id }}">
									@endif
								@endif
							@else
								@if(!in_array($singleBuyer->order_id, $checkItem))
									@php
										$checkItem[] = $singleBuyer->order_id;
									@endphp
									<input type="checkbox" class="supplier_order_checkbox" id="order_id{{$singleBuyer->order_id}}" data-oid="{{$singleBuyer->order_id}}" data-supid="{{ $singleBuyer->mr_supplier_sup_id }}" data-order="{{$orderId}}" data-uid="{{ $singleBuyer->unit_id }}">
								@endif
							@endif
						@endforeach
					@endif
				</td>
			</tr>
		@endempty
	@endforeach
	<tr>
		<td colspan="8">
			@php
				$singleArray = [];
				$singleArray = array_reduce($globalSupplierList, 'array_replace', array());
				if(!empty($singleArray)) {
					foreach($singleArray as $supId=>$single) {
						echo '<input type="hidden" name="suplierList[]" class="supplierList" value="'.$supId.'" data-name="'.$single.'" />';
					}
				}
			@endphp
		</td>
	</tr>
@else
	<tr>
		<td colspan="8" class="text-center">No Data Found</td>
	</tr>
@endif
