 <tr class="row-t tr_booking_id_{{$BookingInfo->id}}" style="display:none;"><input type="hidden" name="booking_id[]" value="{{$BookingInfo->id}}"> 
@foreach($bookCo As $cat)
	@foreach($cat As $key => $items)
			@php 
				$count = 0;
				$rowCounts= 0;
				$class_sl= 'item-'.substr(md5(mt_rand()), 0, 7);
			@endphp
			@foreach($items As $key => $item)
				@php
					$piItemQty = Custom::getPiItemsQty($BookingInfo->id,$item->id)??0;
					$remain = $item->booking_qty - $piItemQty;
					if(isset($pi_id) && $pi_id !=''){
						$piItemQtyThis = Custom::getPiItemsQtyByPi($BookingInfo->id,$item->id,$pi_id)??0;
						$remain = $remain+$piItemQtyThis;

					}
					if($remain>0){
						$rowCounts++;
					}

				@endphp

			@endforeach
			@foreach($items As $key => $item)
				@php 
				if($item->mcat_name == 'Fabric'){
					$classNamePre = 'fab';
				}else if($item->mcat_name == 'Sewing Accessories'){
					$classNamePre = 'sw';
				}else if($item->mcat_name == 'Finishing Accessories'){
					$classNamePre = 'fin';
				} 

				$piItemQty = Custom::getPiItemsQty($BookingInfo->id,$item->id)??0;
				$remain = $item->booking_qty - $piItemQty;
				if(isset($pi_id) && $pi_id !=''){
					$piItemQtyThis = Custom::getPiItemsQtyByPi($BookingInfo->id,$item->id,$pi_id)??0;
					$remain = $remain+$piItemQtyThis;

				}
				@endphp
				@if($remain>0)
				@php $count++; @endphp
					@if($count==1)
					<tr class="row-t tr_booking_id_{{$BookingInfo->id}}">
						<td rowspan="{{$rowCounts}}" class="vertical-align-center">
							{{$BookingInfo->booking_ref_no}} <br>
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
						<td rowspan="{{$rowCounts}}" class="vertical-align-center ">
							<input type="text" name="item_price[{{$BookingInfo->id}}][{{$item->item_id}}]" class="new-price" data-target="{{$class_sl}}"  value="{{$item->precost_unit_price}}">
						</td>
						<td>{{$item->clr_name}}</td>
						<td>{{$item->mr_product_pallete_name}}</td>
						<td>
							<input type="hidden" name="booking[{{$BookingInfo->id}}][{{$item->id}}][id]" value="{{$item->id}}">
							<select name="booking[{{$BookingInfo->id}}][{{$item->id}}][currency]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true">
	                            <option value="USD" selected="selected">USD</option>
	                            <option value="EUR">EUR</option>
	                            <option value="GBP">GBP</option>
	                            <option value="TK">TK</option>
	                        </select>
						</td>
						<td>
							<input type="text" class="{{$classNamePre}}_booking_qty" value="{{ $remain }}"  readonly="readonly">
						</td>
						<td>
							<input type="text" data-cost="{{($item->precost_unit_price)}}" class="{{$classNamePre}}_pi_qty {{$class_sl}}" value="{{ $remain }}" name="booking[{{$BookingInfo->id}}][{{$item->id}}][pi_qty]" >
						</td>

						</td>
						<td>
							<input type="text" class="{{$classNamePre}}_pi_value" value="{{ round(($remain)*($item->precost_unit_price),6)}}"  readonly="readonly">	
						</td>
						<td>
							<input type="text" placeholder="Y-m-d" name="booking[{{$BookingInfo->id}}][{{$item->id}}][shipped_date]"  class="ship-date col-xs-12 form-control input-sm " data-validation="required" data-validation-optional="true" >
							<input type="hidden"  name="booking[{{$BookingInfo->id}}][{{$item->id}}][order_id]" value="{{$item->order_id}}">
						</td>
					</tr>
					@else

					<tr class="tr_booking_id_{{$BookingInfo->id}}">
						<td>{{$item->clr_name}}</td>
						<td>{{$item->mr_product_pallete_name}}</td>
						<td>
							<input type="hidden" name="booking[{{$BookingInfo->id}}][{{$item->id}}][id]" value="{{$item->id}}">
							<select name="booking[{{$BookingInfo->id}}][{{$item->id}}][currency]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true">
	                            <option value="USD" selected="selected">USD</option>
	                            <option value="EUR">EUR</option>
	                            <option value="GBP">GBP</option>
	                            <option value="TK">TK</option>
	                        </select>
						</td>
						<td>
							<input type="text" class="{{$classNamePre}}_booking_qty" value="{{ $remain }}"  readonly="readonly">
						</td>
						<td>
							<input type="text" data-cost="{{($item->precost_unit_price)}}" class="{{$classNamePre}}_pi_qty {{$class_sl}}" value="{{ $remain }}" name="booking[{{$BookingInfo->id}}][{{$item->id}}][pi_qty]" >
						</td>
						<td>
							<input type="text" class="{{$classNamePre}}_pi_value " value="{{ round(($remain)*($item->precost_unit_price),6)}}"  readonly="readonly">						
						</td>
						<td>
							<input type="text" placeholder="Y-m-d" name="booking[{{$BookingInfo->id}}][{{$item->id}}][shipped_date]" class="ship-date col-xs-12 form-control input-sm " data-validation="required" data-validation-optional="true" >
							<input type="hidden"  name="booking[{{$BookingInfo->id}}][{{$item->id}}][order_id]" value="{{$item->order_id}}">
						</td>
					</tr>
					@endif
				@endif
			@endforeach
	@endforeach
@endforeach