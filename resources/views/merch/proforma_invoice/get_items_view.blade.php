 <tr class="row-t tr_booking_id_{{$BookingInfo->id}}" style="display:none;"><input type="hidden" name="old_booking_id[]" value="{{$BookingInfo->id}}"> 
@foreach($bookCo As $cat)
	@foreach($cat As $key => $items)
			@php 
				$count = 0;
				$rowCounts= count($items);
			@endphp
			@foreach($items As $key => $item)
				@php 
				$count++;
				if($item->mcat_name == 'Fabric'){
					$classNamePre = 'fab';
				}else if($item->mcat_name == 'Sewing Accessories'){
					$classNamePre = 'sw';
				}else if($item->mcat_name == 'Finishing Accessories'){
					$classNamePre = 'fin';
				}
				$piItemQty = Custom::getPiItemsQty($BookingInfo->id,$item->id)??0;
				$remain = $item->booking_qty - $piItemQty + $item->pi_qty;
				$unit_price = $item->unit_price?$item->unit_price:$item->precost_unit_price; 
				@endphp
				
				@if($count==1 )
				<tr class="row-t tr_booking_id_{{$BookingInfo->id}}">
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$BookingInfo->booking_ref_no}}<br>
						({{$item->order_code}})
					</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">{{$item->mcat_name}}</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$item->item_name}}</td>
					
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$item->art_name}}
					</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$item->sup_name}}
					</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{($item->consumption)+(($item->consumption)*($item->extra_percent)/100)}}
					</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$item->uom}}
					</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$item->precost_unit_price}}
					</td>
					<td rowspan="{{$rowCounts}}" class="vertical-align-center">
						{{$unit_price}}
					</td>
					<td> {{$item->clr_name}}</td>
					<td> {{$item->mr_product_pallete_name}}</td>
					<td>
						{{$item->currency}}
					</td>
					<td style="text-align: right;">
						<input type="hidden" class="{{$classNamePre}}_booking_qty" value="{{ $remain }}"  readonly="readonly">
						{{ $remain }}
					</td>
					<td style="text-align: right;">
						<input type="hidden"  class="{{$classNamePre}}_pi_qty" value="{{ $item->pi_qty }}"  readonly="readonly">
						{{ $item->pi_qty }}
					</td>
					<td style="text-align: right;">
						<input type="hidden" class="{{$classNamePre}}_pi_value" value="{{ ($item->pi_qty)*($unit_price)}}"  readonly="readonly">
						{{ round(($item->pi_qty)*($unit_price),6)}}						
					</td>
					<td>
						{{$item->shipped_date}}
					</td>
				</tr>
				@else

				<tr class="tr_booking_id_{{$BookingInfo->id}}">
					<td>{{$item->clr_name}}</td>
					<td>{{$item->mr_product_pallete_name}}</td>
					<td>
						{{$item->currency}}
					</td>
					<td style="text-align: right;">
						<input type="hidden" class="{{$classNamePre}}_booking_qty" value="{{ $remain }}"  readonly="readonly">
						{{ $remain }}
					</td>
					<td style="text-align: right;">
						<input type="hidden"  class="{{$classNamePre}}_pi_qty" value="{{ $item->pi_qty }}"  readonly="readonly">
						{{ $item->pi_qty }}
					</td>
					<td style="text-align: right;">
						<input type="hidden" class="{{$classNamePre}}_pi_value" value="{{ ($item->pi_qty)*($unit_price)}}"  readonly="readonly">
						{{ round(($item->pi_qty)*($unit_price),6)}}						
					</td>
					<td>
						{{$item->shipped_date}}
					</td>
				</tr>
				@endif
			@endforeach
	@endforeach
@endforeach

<script type="text/javascript">
	$( document ).ready(function() {		
		$('.ship-date').datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
</script>