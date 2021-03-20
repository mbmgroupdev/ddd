@push('css')
	<link href="{{asset('assets/css/calculator.css') }}" rel="stylesheet" type="text/css" />
	<style>
		.calc-wrapper { /* center / center */
			position: absolute;
			top: 50%;
			left: 48%;
			margin-top: -170px;
			/*margin-left: -250px;*/
			z-index: 5;
		}
		.calc-right .calc-orange > div {
		    background-color: #04636e;
		    /* box-shadow: inset 0 -13px 1px rgb(148 77 18 / 20%), inset 0 7px 6px rgb(255 255 255 / 30%), inset 0 6px 6px rgb(255 255 255 / 10%); */
		}
	</style>
@endpush
<div class="calc-wrapper out-of-network">
	<div class="calc-layout"></div>
	<div class="calc-main ">
		<div class="calc-display">
			<span>0</span>
			<div class="calc-rad">Rad</div>
			<div class="calc-hold"></div>
			<div class="calc-buttons">
				<div class="calc-info">?</div>
				<div class="calc-smaller">&gt;</div>
				<div class="calc-ln">.</div>
			</div>
		</div>
		<div class="calc-left1">
			<!-- <div><div>2nd</div></div>
			<div><div>(</div></div>
			<div><div>)</div></div>
			<div><div>%</div></div>
			<div><div>1/x</div></div>
			<div><div>x<sup>2</sup></div></div>
			<div><div>x<sup>3</sup></div></div>
			<div><div>y<sup>x</sup></div></div>
			<div><div>x!</div></div>
			<div><div>&radic;</div></div>
			<div><div class="calc-radxy">
				<sup>x</sup><em>&radic;</em><span>y</span>
			</div></div>
			<div><div>log</div></div>
			<div><div>sin</div></div>
			<div><div>cos</div></div>
			<div><div>tan</div></div>
			<div><div>ln</div></div>
			<div><div>sinh</div></div>
			<div><div>cosh</div></div>
			<div><div>tanh</div></div>
			<div><div>e<sup>x</sup></div></div>
			<div><div>Deg</div></div>
			<div><div>&pi;</div></div>
			<div><div>EE</div></div>
			<div><div>Rand</div></div> -->
		</div>
		<div class="calc-right">
			<!-- <div><div>mc</div></div>
			<div><div>m+</div></div>
			<div><div>m-</div></div>
			<div><div>mr</div></div> -->
			<div class="calc-brown"><div >AC</div></div>
			<div class="calc-brown"><div>+/&#8211;</div></div>
			<div class="calc-brown calc-f19"><div>%</div></div>
			<div class="calc-orange calc-f19"><div>&divide;</div></div>
			<div class=""><div>7</div></div>
			<div class=""><div>8</div></div>
			<div class=""><div>9</div></div>
			
			<div class="calc-orange calc-f21"><div>&times;</div></div>
			<div class=""><div>4</div></div>
			<div class=""><div >5</div></div>
			<div class=""><div>6</div></div>
			<div class="calc-orange calc-f18"><div>&#8211;</div></div>
			<div class=""><div>1</div></div>
			<div class=""><div>2</div></div>
			<div class=""><div>3</div></div>
			<div class="calc-orange calc-f18"><div>+</div></div>
			<!-- <div class="calc-blank"><textarea></textarea></div> -->
			<div class='calc-orange calc-eq calc-f17'><div>
				<div class="calc-down">=</div>
			</div></div>
			<div class=" calc-zero"><div>
				<span>0</span>
			</div></div>
			<div class=" calc-f21"><div>.</div></div>
			
		</div>
		<input type="hidden" id="cal-input" value="">
		<div class="cal-bottom">

			<div class="row">
				<div class="col">
					<div ><button class="btn btn-sm btn-danger w-100 close-cal"> <i class="las la-times"></i> Close</button></div>
				</div>
				<div class="col">
					<div><button class="btn btn-sm btn-success w-100 ok-cal"> <i class="las la-check"></i> OK</button></div>
				</div>
			</div>
			
			
		</div>

	</div>
</div>
@push('js')
	<script type="text/javascript" src="{{asset('assets/js/calculator.js')}}"></script>

@endpush