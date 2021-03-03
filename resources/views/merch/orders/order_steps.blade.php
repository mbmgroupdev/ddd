<style type="text/css">
	.steps-div{
		padding-bottom: 20px;
    	border-left: 2px solid #d1d1d1;
	}
	.steps>li.active .step, .steps>li.active:before, .steps>li.complete .step, .steps>li.complete:before{
	    border-color: #039e08;
	    
	}
	li.a{
		text-decoration: none;
	}
	.title:hover{
		cursor: pointer;
	}
</style>
<div class="steps-div">
	<ul class="steps" style="margin-left: 0">
	<?php
		$active = '';
		$url = '#'; 
	?>

	@if(isset($data['bom']))
		@php
		$active = 'class=active';
		$url = url('merch/order_bom').'/'.$id.'/create'; 
		@endphp
	@else
		@php
			$active = '';
		 	$url = url('merch/order_bom').'/'.$id.'/create'; 
		@endphp
	@endif
		<li data-step="1"  {{$active}}>
			<a href="{{ $url }}" >
				<span class="step">1</span>
				<span class="title">BOM</span>
			</a>
		</li>

	@if(isset($data['costing']))
		@php
		$active = 'class=active';
		$url = url('merch/order_costing').'/'.$id.'/edit'; 
		@endphp
	@else
		@php 
		$active = '';
		$url = url('merch/order_costing').'/'.$id.'/create'; 
		@endphp

	@endif
	@if(!isset($data['bom']))
		@php 
			$active = '';
			$url = '#'; 
		@endphp
	@endif

		<li data-step="2" {{$active}}>
			<a href="{{ $url }}" >
				<span class="step">2</span>
				<span class="title">Costing</span>
			</a>
		</li>

	@if(isset($data['po']))
		@php
		$active = 'class=active';
		$url = url('merch/orders/order_edit').'/'.$id; 
		@endphp
	@else
		@php 
		$active = '';
		$url = url('merch/orders/order_edit').'/'.$id; 
		@endphp
	@endif

	@if(!isset($data['costing']))
		@php 
			$active = '';
			$url = '#'; 
		@endphp
	@endif

		<li data-step="3" {{$active}}>
			<a href="{{ $url }}" >
				<span class="step">3</span>
				<span class="title">Purchase Order</span>
			</a>
		</li>

	@if(isset($data['po']))
		@php
		$active = 'class=active';
		$url = url('merch/order_breakdown/show').'/'.$id; 
		@endphp
	@else
		@php 
		$active = '';
		$url = url('merch/order_breakdown/show').'/'.$id; 
		@endphp
	@endif

	@if(!isset($data['costing']))
		@php 
			$active = '';
			$url = '#'; 
		@endphp
	@endif

		<li data-step="4" {{$active}}>
			<a href="{{ $url }}" >
				<span class="step">4</span>
				<span class="title">Breakdown</span>
			</a>
		</li>

	@if(isset($data['booking']))
		@php
			$active = 'class=active';
			$url1 = url('merch/order_po_booking/showForm').'?unit='.$data['unit'].'&&order='.$id.'&&buyer='.$data['buyer'];
		@endphp
	@else
		@php
			$active = '';
			$url1 = url('merch/order_po_booking/showForm').'?unit='.$data['unit'].'&&order='.$id.'&&buyer='.$data['buyer'];
		@endphp
	@endif

	@if(!isset($data['po']))
		@php 
			$active = '';
			$url1 = '#'; 
		@endphp
	@endif

		<li data-step="5" {{$active}}>
			<a href="{{ $url1 }}" >
				<span class="step">5</span>
				<span class="title">Booking</span>
			</a>
		</li>
	</ul>
</div>