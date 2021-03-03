@if(!empty($buyerOrderList->toArray()))
	@foreach($buyerOrderList as $singleBuyer)
		<tr>
			<td>{{ $singleBuyer->order_code }}</td>
			{{-- <td>{{ $singleBuyer->sup_name }}</td> --}}
			@php
				$itemList = Custom::getOrderItemList($singleBuyer->order_id, $singleBuyer->mr_supplier_sup_id);
			@endphp
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
							if(strpos($itemQty,'|0|') !== false) {
								$rQty = '';
							} else {
								list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
								$rQty = Custom::fixedNumber($rQty,2,true);
							}
						@endphp
						{!!'<span class="label label-info">'.$rQty.'</span><br>'!!}
					@else
						{!!'<span class="label label-info"></span><br>'!!}
					@endif
				@endforeach
			</td>
			<td>
				@foreach($itemList as $i=>$item)
					@if(strpos($item, '%') !== false)
						@php
							list($itemName,$itemQty) = array_pad(explode('~',$item),2,null);
							if(strpos($itemQty,'|0|') !== false) {
								$bQty = '';
							} else {
								list($bQty,$rQty,$percentage) = array_pad(explode('|',$itemQty),3,null);
								$bQty = Custom::fixedNumber($bQty,2,true);
							}
						@endphp
						{!!'<span class="label label-info">'.$bQty.'</span><br>'!!}
					@else
						{!!'<span class="label label-info"></span><br>'!!}
					@endif
				@endforeach
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
							$label = strpos($percentage,'100')!==false?'success':'info';
						@endphp
						{!!'<span class="label label-'.$label.'">'.$percentage.'</span><br>'!!}
					@else
						{!!'<span class="label label-'.$label.'">0.00%</span><br>'!!}
					@endif
				@endforeach
			</td>
			<td>{{ $singleBuyer->order_delivery_date }}</td>
			<td>
				@php
					$checked = '';
					$disabled = '';
					if(!empty($orderList)) {
						if(in_array($singleBuyer->order_id, $orderList)) {
							$checked = 'checked';
							$disabled = 'disabled="disabled"';
						}
					}
					// if booking exist
					if($orderBookingExist) {
						$disabled = 'disabled="disabled"';
					}
				@endphp
				<input type="checkbox" class="supplier_order_checkbox" id="order_id{{$singleBuyer->order_id}}" data-oid="{{$singleBuyer->order_id}}" data-supid="{{ $singleBuyer->mr_supplier_sup_id }}" data-uid="{{ $singleBuyer->unit_id }}" {{ $checked }} {{ $disabled }}>
			</td>
		</tr>
	@endforeach
@else
	<tr>
		<td colspan="6" class="text-center">No Data Found</td>
	</tr>
@endif