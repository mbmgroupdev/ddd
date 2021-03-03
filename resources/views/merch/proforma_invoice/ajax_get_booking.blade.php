@if(!empty($bookingList->toArray()))
	@php $countBooking=0; @endphp
	@foreach($bookingList as $booking)
		@php
			$bookedPi = Custom::getPiQty($booking->id) ?? 0;
			$remain = round($booking->booking,2)-($bookedPi);
			$checked = '';
			$dataPi = '';
			if(!empty($bookingList)) {
				if(in_array($booking->id, $checkedBooking)) {
					$checked = 'checked';
					$bookedThisPi = Custom::getPiQtyByPi($booking->id,$pi_id) ?? 0;
					//$remain = $remain+$bookedThisPi;
					$dataPi = 'data-pi='.$pi_id;
				}
			}
		@endphp
		@if($remain>0)
			@php $countBooking++; @endphp
			<tr >
				<td class="vertical-align-center">
					{{ $booking->booking_ref_no }} <br>
					@php
						$orders = Custom::getOrderByBookingId($booking->id);
						echo '('.implode(', ',$orders).')';
					@endphp
				</td>
				<td class="vertical-align-center">{{ $booking->sup_name }}</td>
				<td >
					<!-- Get Item Names -->
					@php
					$items = Custom::getBookingItemNames($booking->id);
					foreach($items As $i){
						echo $i.'<br>';
					}
					@endphp
				</td>
				<td class="vertical-align-center">{{ round($booking->booking,2) }} </td>
				<td class="vertical-align-center">{{ $remain }}</td>
				<td class="vertical-align-center">{{ $booking->delivery_date }}</td>
				<td class="vertical-align-center">
					
					@if($supId != null)
						<input type="checkbox" class="supplier_order_checkbox" id="booking_id{{$booking->id}}" data-bookid="{{$booking->id}}" data-supid="{{ $booking->mr_supplier_sup_id }}" data-uid="{{ $booking->unit_id }} " {{ $dataPi }} {{ $checked }}>
					@endif
				</td>
			</tr>
		@endif
	@endforeach
	@if($countBooking==0)
	<tr>
		<td colspan="8" class="text-center">No Data Found</td>
	</tr>
	@endif
@else
	<tr>
		<td colspan="8" class="text-center">No Data Found</td>
	</tr>
@endif