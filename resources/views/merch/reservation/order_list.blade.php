<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			
			
			<div class="top_summery_section">
				
				<div class="page-header">
		            
		            <table class="table no-border f-16" border="0">
		            	<tr class="text-center">
		            		<td class="border-0">
		            		
		            		</td>
		            		<td class="border-0">
		            			Total Reservation Qty <b>: {{ $reservation->res_quantity }}</b><br>
		                		Total Order Qty <b>: {{ $orderQty }}</b>
		            		</td>
		            		<td class="border-0">
		            			
		            		</td>
		            	</tr>
		            	
		            </table>
		            
		        </div>
		        
			</div>
			<div class="content_list_section">
				
				<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					<thead>
		                <tr>
		                    <th width="5%">SL.</th>
                            <th width="10%">MBM Order No</th>
                            <th width="10%">Order Ref. No</th>
                            <th width="10%">Unit</th>
                            <th width="10%">Buyer</th>
                            <th width="10%">Brand</th>
                            <th width="10%">Season</th>
                            <th width="10%">Style No</th>
                            <th width="10%">Quantity</th>
                            <th width="10%">Delivery Date</th>
                            {{-- <th width="8%">Action</th> --}}
		                </tr>
		            </thead>
		            <tbody>
		            	@if(count($getOrder) > 0)
		            	@php $i=0; @endphp
			            @foreach($getOrder as $order)
			            <tr>
			            	<td>{{ ++$i }}</td>
			            	<td>{{ $order->order_code }}</td>
			            	<td>{{ $order->order_ref_no }}</td>
			            	<td>{{ $getUnit[$order->unit_id]['hr_unit_name']??'' }}</td>
			            	<td>{!! $getBuyer[$order->mr_buyer_b_id]->b_name??'' !!}</td>
			            	<td>{!! $getBrand[$order->style->mr_brand_br_id]->br_name??'' !!}</td>
			            	<td>{!! $getSeason[$order->style->mr_season_se_id]->se_name??'' !!}</td>
			            	<td>{!! $order->style->stl_no??'' !!}</td>
			            	<td>{{ $order->order_qty??0 }}</td>
			            	<td>{{ custom_date_format($order->order_delivery_date) }}</td>
			            </tr>
						@endforeach
						@else
						<tr>
							<td colspan="10" class="text-center">No Order Found! </td>
						</tr>
						@endif	
		            </tbody>
				</table>
				
			</div>
		</div>

		{{-- modal --}}
	</div>
</div>

