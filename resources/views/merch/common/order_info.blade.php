@php
  $getSeason = season_by_id();
  $getUnit = unit_by_id();
  $getBuyer = buyer_by_id();
@endphp
<div class="wrapper center-block">
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading active" role="tab" id="headingOne">
        <h4 class="panel-title">
          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="display: block; font-size: 13px;">
            {{ $order->order_code }} - Order Information
          </a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
          <div class="row">
              <div class="col-sm-12">
                  <table class="table custom-font-table detailTable" width="50%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td width="80">Order No :</td>
                        <th id="order_code">{{ (!empty($order->order_code)?$order->order_code:null) }}</th>
                        <td width="120">Order Quantity :</td>
                        <th>{{ (!empty($order->order_qty)?$order->order_qty:null) }}</th>
                        <td width="80">Buyer :</td>
                        <th>{!! $getBuyer[$order->mr_buyer_b_id]->b_name??'' !!}</th>
                        <td width="120">Reference No :</td>
                        <th>{{ (!empty($order->order_ref_no)?$order->order_ref_no:null) }}</th>
                      </tr>
                      <tr>
                        <td width="80">Unit :</td>
                        <th>{{ $getUnit[$order->unit_id]['hr_unit_name']??'' }}</th>
                        <td width="120">Delivery Date:</td>
                        <th>{{ custom_date_format($order->order_delivery_date) }}</th>
                        <td width="80">Season :</td>
                        <th>{!! $getSeason[$order->style->mr_season_se_id]->se_name !!} - {{ $order->style->stl_year??'' }}</th>
                        <td width="120">Style No :</td>
                        <th>{!! $order->style->stl_no??'' !!}</th>
                      </tr>
                      
                  </table>
              </div>
              
          </div>
        </div>
      </div>
    </div>
      
  </div>
</div>