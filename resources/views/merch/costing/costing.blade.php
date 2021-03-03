@extends('merch.index')
@section('content')
@push('css')
<style>
	.elminate-item td{
		background: #ffe5e5 !important;
	}
</style>
@endpush
@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li> 
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Costing Compare</a>
				</li>  
				<li class="active">{{$order->order_code}}</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content">   
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="panel panel-success">
            	<div class="panel-heading page-headline-bar">
            		<h6>
            			Costing Compare					
						<div class="text-right pull-right">
			            	
		                	<a href="{{ url('/merch/costing-compare') }}" rel='tooltip' data-tooltip-location='left' data-tooltip='Costing Compare list' type="button" class="btn btn-info btn-sm">
								<i class="glyphicon  glyphicon-list"></i>
							</a>
			            </div>
					</h6>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									
								</div>
								<div class="col-sm-6"></div>
							</div>
							<table class="table" width="50%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<th colspan="2" rowspan="3" style="border:0;">

										<a href="{{asset(!empty($order->style['stl_img_link'])?$order->style['stl_img_link']:'assets/images/avatars/profile-pic.jpg')}}" target="_blank">
											<img class="thumbnail" height="100px" src="{{asset(!empty($order->style['stl_img_link'])?$order->style['stl_img_link']:'assets/images/avatars/profile-pic.jpg')}}" alt=""/>
										</a>

									</th>
									
									
									<th>Order COde</th>
									<td>{{$order->order_code}}</td>
									
								</tr>
								<tr>
									<th>Order Quantity</th>
									<td>{{$order->order_qty}}</td>
									
								</tr>
								<tr>
									<th>Unit</th>
									<td>{{$order->unit['hr_unit_name']}}</td>
									
								</tr>
								<tr>
									<th>Brand</th>
									<td>{{$order->brand['br_name']}}</td>
									<th>Season</th>
									<td>{{$order->season['se_name']}}</td>
									
								</tr>
								<tr>
									<th>Style No</th>
									<td>{{$order->style['stl_no']}}</td>
									<th>Delivery Date</th>
									<td>{{$order->order_delivery_date}}</td>
									
								</tr>
								<tr>
									<th>Reference No</th>
									<td>{{$order->order_ref_no??''}}</td>
									<th>Buyer</th>
									<td>{{$order->buyer['b_name']}}</td>
								</tr>
							</table>
						</div>
						<div class="col-sm-5">
							<canvas id="attendance-chart"></canvas>
						</div>
					</div>
					<div class="row">
						<div class="space-10"> </div>
						<div class="col-sm-12" >
							<div class="widget-box transparent">
								<div class="widget-header widget-header-small header-color-blue2">
									<h4 class="widget-title smaller">
										<i class="ace-icon fa fa-table bigger-100"></i>
										Item Wise Costing 
									</h4>
								</div>
							</div>
							<table id="example" class="display stripe row-border order-column custom-font-table table table-bordered" style="width:100%">
									<thead>
										<tr class="info">
											<th>Main Category</th>
											<th>Item</th>
											<th>Item Code</th>
											<th>Article</th>
											<th>Composition</th>
											<th>Construction</th>
											<th>Supplier</th>
											<th>Consumption</th>
											<th>Extra (%)</th>
											<th>Unit</th> 
											<th>Terms</th> 
											<th>Style Cost</th> 
											<th>Order Cost</th> 
											<th>PO Cost</th> 
											<th>PI Cost</th> 
											<th>Req. Qty</th>  
										</tr>
			                        </thead>  
									<tbody>
									@foreach($order_cost as $item)
										<tr>
											<td>
												{{$item->cat_item->mr_material_category['mcat_name']}}
											</td>
											<td>{{$item->cat_item['item_name']??'' }}</td>
											<td>{{$item->cat_item['item_code']??'' }}</td>
											<td>{{ $item->article['art_name']??'' }}</td>
											<td>{{$item->composition['comp_name']??''}}</td>
											<td>{{$item->construction['construction_name']??''}}</td>
											<td>{{$item->supplier['sup_name']??''}}</td>
											<td>{{$item->consumption}}</td>
											<td>{{$item->extra_percent}}</td>
											<td>{{$item->uom}}</td> 
											<td>{{$item->bom_term}}</td> 
											<td>
												@if(isset($style_cost[$item->mr_cat_item_id]->price))
													{{round($style_cost[$item->mr_cat_item_id]->price,6)}}
												@else
													N/A
												@endif
											</td> 
											<td>
												{{ ($item->precost_unit_price*($item->consumption+($item->consumption*($item->extra_percent / 100)))) }}
											</td> 
											<td>PO Cost</td> 
											<td>
												@if(isset($pi_cost[$item->mr_cat_item_id]))
													{{round($pi_cost[$item->mr_cat_item_id],6)}}
												@endif
											</td> 
											<td>{{$item->precost_req_qty}}</td>  
										</tr>
									@endforeach
									@if(count($style_old)>0)
									@foreach($style_old as $style)
										<tr class="elminate-item" >
											<td>
												{{$style_cost[$style]->cat_item->mr_material_category['mcat_name']}}
											</td>
											<td>{{$style_cost[$style]->cat_item['item_name'] }}</td>
											<td>{{$style_cost[$style]->cat_item['item_code'] }}</td>
											<td>{{ $style_cost[$style]->article['art_name'] }}</td>
											<td>{{$style_cost[$style]->composition['comp_name']}}</td>
											<td>{{$style_cost[$style]->construction['construction_name']}}</td>
											<td>{{$style_cost[$style]->supplier['sup_name']}}</td>
											<td>{{$style_cost[$style]->consumption}}</td>
											<td>{{$style_cost[$style]->extra_percent}}</td>
											<td>{{$style_cost[$style]->uom}}</td> 
											<td>{{$style_cost[$style]->bom_term}}</td> 
											<td>{{$style_cost[$style_cost[$style]->mr_cat_item_id]->price??0}}</td> 
											<td> N/A </td> 
											<td> N/A </td> 
											<td> N/A </td> 
											<td> N/A </td>  
										</tr>
									@endforeach
									@endif 
			                        </tbody>   
			                    </table>
							
	                    </div>						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@push('js')
<script src="{{ asset('assets/js/chartjs.min.js') }}"></script>
<script type="text/javascript">
var data = {
          datasets: [{
            data: [{{ $style_other_cost->agent_fob??0}},{{ $order->order_costing['agent_fob']??0}},{{$total_po_cost}},{{$total_pi_cost??0}}],
            backgroundColor: [
              "#42b73e", "#8A2BE2", "#f24646",  "#f6f94a"
            ]
          }],
          labels: ["Style Cost","Order Cost","PO Cost", "PI Cost"],
          label: "Cost (Tk.)"
    };
$(document).ready(function() {
    var canvas = document.getElementById('attendance-chart');
    var ctx = canvas.getContext("2d");
    ctx.canvas.height= 150;
    var myBarChart  = new Chart(ctx, {
      type: 'bar',
      data: data,
      options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    steps: 10
                },
                afterFit: function(scale) {
                   scale.width = 60  //<-- set value as you wish 
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Cost (Tk.)'
                  }
            }],
            xAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Costing Comparison'
                  }
            }]
        },
        layout: {
            padding: {
                left: 0,
                right: 10,
                top: 0,
                bottom: 0
            }
        },
        legend: {
            display: false,
            position: 'bottom'
        }
    }
    });
});

</script>
@endpush
@endsection