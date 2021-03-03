@extends('merch.index')
@push('css')
<style type="text/css">
  .sub-table-recon tr:first-child td, .sub-table-recon{border-top: none !important; }

  @media print {
  body {
    font-family: 'SolaimanLipi', sans-serif;
}
.profile-user-info {
    display: table;
    width: 100%;
    margin: 0;
}
.profile-info-row {
    display: block;
    width: 100%;
}
.profile-info-name {
    text-align: left;
    width: 35%;
    padding: 6px 10px 6px 4px;
    font-weight: 400;
    color: #667E99;
    background-color: transparent;
    vertical-align: middle;
    float: left;
}
.profile-info-value {
    padding: 6px 4px 6px 6px;
    width: 45%;
    float:right;

}
.profile-info-name, .profile-info-value {
    display: table-cell;
    border-top: 1px dotted #D5E4F1;
}

}
  
</style>
@endpush
@section('content')
<div class="main-content">
       <div class="main-content-inner">
 
           <div class="page-content">
 
               <div class="row">
                   <div class="col-xs-12">
                       <div id="user-profile-1" class="user-profile row">
                           <div class="col-sm-3 center">
                               <div>
                                <span class="profile-picture">
                                    <a href="{{ asset(!empty($order->stl_img_link)?$order->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" target="_blank">
                                      <img id="avatar" style="width: 180px; height: 200px;" class="img-responsive" alt="profile picture" src="{{ asset(!empty($order->stl_img_link)?$order->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}"/>
                                    </a>
                                </span>
                                   <div class="space-4"></div>
                                   
                               </div>
 
                               <div class="space-6"></div>
                               <div class="profile-contact-info">
                                   <div class="profile-contact-links align-left">
                                       <p style="text-align: center;"><strong>Order Code : </strong> {{ $order->order_code }}</p>
                                       <p style="text-align: center;"><strong>Order Status : </strong> {{ $order->order_status }}</p>
                                       <p style="text-align: center;"><strong>Order Quantity : </strong> {{ $order->order_qty }}</p>
                                       <p style="text-align: center;"><strong>Order Delivery Date : </strong> {{ $order->order_delivery_date }}</p>
                                   </div>
                               </div>
 
                           </div>
                           <div class="space-10"></div>
                           <div class="col-sm-9">
                               
                                   <!-- Reservation Information -->
                                   <div class="panel panel-info">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Reservation Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(!empty($order))
                                           <div class="panel-body">
                                            <div class="row">
                                              <div class="col-sm-6">
                                               <div class="profile-user-info">
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation Id </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_id }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Unit </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->hr_unit_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Buyer Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation Month </div>
                                                       <div class="profile-info-value">
                                                           <span>
                                                               <?php
                                                                 $monthNum = $order->res_month;
                                                                 $monthName = date('F',mktime(0,0,0, $monthNum, 19));
 
                                                                 echo $monthName;
                                                               ?>
                                                           </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation Year </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_year }} </span>
                                                       </div>
                                                   </div>
                                               </div>
                                              </div>
                                              <div class="col-sm-6">
                                                <div class="profile-user-info">
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Product Type </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->prd_type_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation Quantity </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_quantity }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation sah </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_sah }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation Sweing Smv </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_sewing_smv }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Reservation Created </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_created_on }} </span>
                                                       </div>
                                                   </div>
                                                </div>
 
                                              </div>
                                            </div> 
                                           </div>
                                           @else
                                           <div class="panel-body">
                                               <h3>No data found</h3>
                                           </div>
                                           @endif
                                       </div>
                                   </div>
 
                                   <!-- Order Reconciliation -->
                                   {{-- <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Order Reconciliation
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(!empty($order))
                                               <div class="panel-body">
                                                   <div class="profile-user-info">
 
                                                       <div class="widget-body">
                                                           <div class="row">
                                                               <div class="col-sm-12">
                                                                   <table class="table table-bordered">
                                                                       <tbody>
 
                                                                       <tr>
                                                                           <th>Name</th>
                                                                           <td>Cost/Unit</td>
                                                                           <td>Total Value</td>
                                                                       </tr>
                                                                        @if(count($reconData->fabs)> 0)
                                                                        <tr>
                                                                          <th >Febric Price</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($reconData->fabs as $key => $fab)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$fab->price}}</td>
                                                                                <td>{{$fab->price}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                        <tr>
                                                                          <th >Febric YY</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($reconData->fabs as $key => $fab)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$fab->yy}}</td>
                                                                                <td>{{($fab->yy)*($order->order_qty)}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                        <tr>
                                                                          <th >Febric Cost</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($reconData->fabs as $key => $fab)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$fab->cost}}</td>
                                                                                <td>{{($fab->cost)*($order->order_qty)}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                       @endif
                                                                       @if(count($reconData->trims)> 0)
                                                                        <tr>
                                                                          <th >Trims</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($reconData->trims as $key => $trim)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$trim->t_sum}}</td>
                                                                                <td>{{($trim->t_sum)*($order->order_qty)}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                        @endif

                                                                        @if(count($reconData->wash)> 0)
                                                                        <tr>
                                                                          <th >Wash</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($reconData->wash as $key => $wash)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$wash->cost}}</td>
                                                                                <td>{{($wash->cost)*($order->order_qty)}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                        @endif

                                                                        @if(count($other_cost)> 0)
                                                                        <tr>
                                                                          <th >CM</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($other_cost as $oc)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$oc->cm}}</td>
                                                                                <td>{{($oc->cm)*($order->order_qty)}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                        @endif

                                                                        @if(count($other_cost)> 0)
                                                                        <tr>
                                                                          <th >FOB</th>
                                                                          <td colspan="2" style="padding: 0;">
                                                                            <table class="table no-margin sub-table-recon"> 
                                                                            @foreach($other_cost as $oc)
                                                                              <tr>
                                                                                <td style="border-right:1px solid #ddd;width:47.1%">{{$oc->agent_fob}}</td>
                                                                                <td>{{($oc->agent_fob)*($order->order_qty)}}</td>
                                                                              </tr>
                                                                            @endforeach
                                                                            </table>
                                                                          </td>
                                                                        </tr>
                                                                        @endif
 
                                                                       </tbody>
                                                                   </table>
                                                               </div>
                                                           </div>
                                                       </div>
 
                                                   </div>
                                               </div>
                                           @else
                                               <div class="panel-body">
                                                   <h3>No data found</h3>
                                               </div>
                                           @endif
                                       </div>
                                   </div> --}}
 
                                   <!-- Style Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Style Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Type </div>
                                                       <div class="profile-info-value">
                                                        <span>{{ $order->stl_type }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Buyer Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style no </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_no }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Product Type No </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->prd_type_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Garments Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->gmt_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Product Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_product_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Description </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_description }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Session Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->se_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Smv </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_smv }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Added By </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_addedby }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Added On </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_added_on }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Style Satus </div>
                                                       <div class="profile-info-value">
                                                           <span>
                                                               @if($order->stl_status == 0)
                                                                   Created
                                                                   @elseif($order->stl_status == 1)
                                                               Submitted
                                                                   @elseif($order->stl_status == 2)
                                                               Approved
                                                                   @else
                                                               Nothing Found
                                                                   @endif
                                                           </span>
                                                       </div>
                                                   </div>
 
                                               </div>
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif
                                       </div>
                                   </div>
 
                                   <!-- Purchase Order Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Purchase Order Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                   @foreach($order_purchase as $op)
 
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Purchase Order No </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->po_no }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Purchase Order Quantity </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->po_qty }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Purchase Order Ex Fty </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->po_ex_fty }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Purchase Order Delivery Country Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->cnt_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Country Fob </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->country_fob }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Remarks </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->remarks }} </span>
                                                       </div>
                                                   </div>
                                                       <hr>
                                                       @endforeach
 
                                               </div>
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif
                                       </div>
                                   </div>
 
                                   <!-- BOM Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Bom Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           <div class="panel-body table-responsive">
                                               <div class="profile-user-info">
 
                                                   <div class="widget-body">
                                                       <div class="row">
                                                           <div class="col-sm-12">
                                                               <table class="table" width="50%" cellpadding="0" cellspacing="0" border="0">
                                                                   <tbody>
                                                                   @if(!empty($order))
                                                                   <tr>
                                                                       <th>Order No</th>
                                                                       <td>{{ $order->order_code }}</td>
                                                                       <th>Unit</th>
                                                                       <td>{{ $order->hr_unit_name }}</td>
                                                                       <th>Buyer</th>
                                                                       <td>{{ $order->b_name }}</td>
                                                                   </tr>
                                                                   <tr>
                                                                       <th>Brand</th>
                                                                       <td>{{ $order->br_name }}</td>
                                                                       <th>Season</th>
                                                                       <td>{{ $order->se_name }}</td>
                                                                       <th>Style No</th>
                                                                       <td>{{ $order->stl_no }}</td>
                                                                   </tr>
                                                                   <tr>
                                                                       <th>Order Quantity</th>
                                                                       <td>{{ $order->order_qty }}</td>
                                                                       <th>Delivery Date</th>
                                                                       <td>{{ $order->order_delivery_date }}</td>
                                                                       <th>Reference No</th>
                                                                       <td>{{ $order->order_ref_no }}</td>
                                                                   </tr>
                                                                       @else
                                                                       <h3>No Data Found</h3>
                                                                       @endif
                                                                   </tbody>
                                                               </table>
                                                           </div>
                                                       </div>
                                                   </div>
 
                                                   <div class="widget-body">
                                                       <table id="bomItemTable" class="table table-striped table-bordered">
                                                           <thead>
                                                           <tr>
                                                               <th>Main Category</th>
                                                               <th>Item</th>
                                                               <th>Item Code</th>
                                                               <th>Description</th>
                                                               <th>Color</th>
                                                               <th>Size / Width</th>
                                                               <th>Article</th>
                                                               <th>Composition</th>
                                                               <th>Construction</th>
                                                               <th>Supplier</th>
                                                               <th>Consumption</th>
                                                               <th>Extra (%)</th>
                                                               <th>Unit</th>
                                                               <th>Terms</th>
                                                               <th>FOB</th>
                                                               <th>L/C</th>
                                                               <th>Freight</th>
                                                               <th>Unit Price</th>
                                                               <th>Total Price</th>
                                                               <th>Req. Qty</th>
                                                               <th>Total Value</th>
                                                           </tr>
                                                           </thead>
                                                           <tbody id="order-bom">
                                                           @if(count($boms)>0)
                                                           @foreach($boms as $bom)
                                                               <tr>
                                                                   <td>{{ $bom->mcat_name }}</td>
                                                                   <td>{{ $bom->item_name }}</td>
                                                                   <td>{{ $bom->item_code }}</td>
                                                                   <td>{{$bom->item_description}}</td>
                                                                   <td>{{ $bom->clr_code }}</td>
                                                                   <td>{{ $bom->size }}</td>
                                                                   <td>{{ $bom->art_name }}</td>
                                                                   <td>{{ $bom->comp_name }}</td>
                                                                   <td>{{ $bom->construction_name }}</td>
                                                                   <td>{{ $bom->sup_name }}</td>
                                                                   <td class="consumption">{{ $bom->consumption }}</td>
                                                                   <td class="extra">{{ $bom->extra_percent }}</td>
                                                                   <td>{{ $bom->uom }}</td>
                                                                   <td> {{ $bom->bom_term }}</td>
                                                                   <td> {{ $bom->precost_fob }}</td>
                                                                   <td> {{ $bom->precost_lc }}</td>
                                                                   <td> {{ $bom->precost_freight }}</td>
                                                                   <td> {{ $bom->precost_unit_price }}</td>
                                                                   <td>
                                                                       <?php
                                                                       $total_price = ($bom->consumption+($bom->consumption*($bom->extra_percent/100)))*$bom->precost_unit_price;


                                                                       $req_qty=($bom->consumption+($bom->consumption*($bom->extra_percent/100)))* $bom->order_qty;

                                                                       $total_price= number_format($total_price,2);
 
                                                                       echo $total_price;
                                                                       ?>
                                                                   </td>
                                                                   <td> {{ $req_qty }} </td>
                                                                   <td> {{ $req_qty*$bom->precost_unit_price }}</td>
                                                               </tr>
 
                                                           @endforeach
                                                               @else
                                                               <h3>No Data Found</h3>
                                                               @endif
                                                           </tbody>
                                                       </table>
                                                   </div>
 
                                               </div>
                                           </div>
                                       </div>
                                   </div>
 
                                   <!-- Costing Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Costing Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           <div class="panel-body table-responsive">
                                               <div class="profile-user-info">
                                                   <div class="widget-body">
                                                       <table id="bomCostingTable" class="table table-bordered table-condensed">
                                                           <thead>
                                                           <tr>
                                                               <th>Main Category</th>
                                                               <th>Item</th>
                                                               <th>Item Code</th>
                                                               <th>Description</th>
                                                               <th>Color</th>
                                                               <th>Size / Width</th>
                                                               <th>Article</th>
                                                               <th>Composition</th>
                                                               <th>Construction</th>
                                                               <th>Supplier</th>
                                                               <th>Consumption</th>
                                                               <th>Extra (%)</th>
                                                               <th>Unit</th>
                                                               <th>Terms</th>
                                                               <th>FOB</th>
                                                               <th>L/C</th>
                                                               <th>Freight</th>
                                                               <th>Unit Price</th>
                                                               <th>Total Price</th>
                                                               <th>Req. Qty</th>
                                                               <th>Total Value</th>
                                                           </tr>
                                                           </thead>
                                                           <tbody>
                                                           @if(count($boms)>0)
                                                           @foreach($boms as $bom)
                                                           <tr>
                                                               <td>{{ $bom->mcat_name }}</td>
                                                               <td>{{ $bom->item_name }}</td>
                                                               <td>{{ $bom->item_code }}</td>
                                                               <td>{{$bom->item_description}}</td>
                                                               <td>{{ $bom->clr_code }}</td>
                                                               <td>{{ $bom->size }}</td>
                                                               <td>{{ $bom->art_name }}</td>
                                                               <td>{{ $bom->comp_name }}</td>
                                                               <td>{{ $bom->construction_name }}</td>
                                                               <td>{{ $bom->sup_name }}</td>
                                                               <td class="consumption">{{ $bom->consumption }}</td>
                                                               <td class="extra">{{ $bom->extra_percent }}</td>
                                                               <td>{{ $bom->uom }}</td>
                                                               <td> {{ $bom->bom_term }}</td>
                                                               <td> {{ $bom->precost_fob }}</td>
                                                               <td> {{ $bom->precost_lc }}</td>
                                                               <td> {{ $bom->precost_freight }}</td>
                                                               <td> {{ $bom->precost_unit_price }}</td>
                                                               <td>
                                                                  <?php
                                                                       $total_price = ($bom->consumption+($bom->consumption*($bom->extra_percent/100)))*$bom->precost_unit_price;


                                                                       $req_qty=($bom->consumption+($bom->consumption*($bom->extra_percent/100)))* $bom->order_qty;

                                                                       $total_price= number_format($total_price,2);
 
                                                                       echo $total_price;
                                                                       ?>
                                                               </td>
                                                               <td> {{ $req_qty }} </td>
                                                                   <td> {{ $req_qty*$bom->precost_unit_price }}</td>
                                                           </tr>
 
                                                           <tr>
                                                               <th colspan="18" class="text-center"> Total {{ $bom->mcat_name }} Price</th>
                                                               <th>
                                                                   <?php
                                                                   
 
                                                                   echo $total_price;
                                                                   ?>
                                                               </th>
                                                               <th></th>
                                                               <th></th>
                                                           </tr>
                                                           @endforeach
                                                           @endif


                                                           @if(count($special_operation)>0)
                                                           @foreach($special_operation as $spo)
                                                           <tr>
                                                               <td colspan="10" class="text-center">{{ $spo->opr_name }}</td>
                                                               <td class="consumption">1</td>
                                                               <td>0</td>
                                                               <td>Piece</td>
                                                               <td colspan="4"></td>
                                                               <td>{{ $spo->unit_price }}</td>
                                                               <td>{{ $spo->unit_price }}</td>
                                                               <td></td>
                                                               <td></td>
                                                           </tr>
                                                           @endforeach
                                                           @endif


                                                           @if(!empty($other_cost1))
 
 
 
                                                           <tr>
                                                               <td colspan="10" class="text-center">Testing Cost</td>
                                                               <td class="consumption">1</td>
                                                               <td>0</td>
                                                               <td>Piece</td>
                                                               <td colspan="4"></td>
                                                               <td>{{ $other_cost1->testing_cost }}</td>
                                                               <td>{{ $other_cost1->testing_cost }}</td>
                                                               <td></td>
                                                               <td></td>
                                                           </tr>
 
                                                           <tr>
                                                               <td colspan="10" class="text-center">CM</td>
                                                               <td class="consumption">1</td>
                                                               <td>0</td>
                                                               <td>Piece</td>
                                                               <td colspan="4"></td>
                                                               <td>
                                                                   {{ $other_cost1->cm }}
                                                               </td>
                                                               <td>{{ $other_cost1->cm }}</td>
                                                               <td></td>
                                                               <td></td>
                                                           </tr>
 
                                                           <tr>
                                                               <td colspan="10" class="text-right">Comercial Cost</td>
                                                               <td></td>
                                                               <td colspan="6" class="text-left"></td>
                                                               <td>{{ $other_cost1->commercial_cost }}</td>
                                                               <td>{{ $other_cost1->commercial_cost }}</td>
                                                               <td></td>
                                                               <td></td>
                                                           </tr>
 
 
                                                           <tr>
                                                               <th colspan="18" class="text-center">Net FOB </th>
                                                               <th>
                                                                  {{ $other_cost1->net_fob }}
                                                               </th>
                                                               <th></th>
                                                               <th></th>
                                                           </tr>
 
                                                           <tr>
                                                               <td colspan="10" class="text-right">Buyer Commision</td>
                                                               <td>{{ $other_cost1->buyer_comission_percent }}</td>
                                                               <td colspan="6" class="text-left">%</td>
                                                               <td>{{ $other_cost1->buyer_fob-$other_cost1->net_fob }}</td>
                                                               <td>{{ $other_cost1->buyer_fob-$other_cost1->net_fob }}</td>
                                                               <td></td>
                                                               <td></td>
                                                           </tr>
 
                                                           <tr>
                                                               <th colspan="18" class="text-center">Buyer FOB </th>
                                                               <th>
                                                                {{ $other_cost1->buyer_fob }}
                                                               </th>
                                                               <th></th>
                                                               <th></th>
                                                           </tr>
                                                           <tr>
                                                               <td colspan="10" class="text-right">Agent Commision</td>
                                                               <td>{{ $other_cost1->agent_comission_percent }}</td>
                                                               <td colspan="6" class="text-left">%</td>
 
                                                               <td>{{ $other_cost1->agent_fob-$other_cost1->buyer_fob }}</td>
                                                               <td>{{ $other_cost1->agent_fob-$other_cost1->buyer_fob }}</td>
                                                               <td></td>
                                                               <td></td>
                                                           </tr>
 
                                                           <tr>
                                                               <th colspan="18" class="text-center">Agent FOB </th>
                                                               <th>
                                                                   {{ $other_cost1->agent_fob }}
                                                               </th>
                                                               <th></th>
                                                               <th></th>
                                                           </tr>
                                                               @endif
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
 
                                   <!-- Approval Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Approval Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(count($order_approve)>0)
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                    @foreach($order_approve as $op)
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Order Code</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->order_code }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Title</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->title }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Submit By </div>
                                                       <div class="profile-info-value">
                                                           <span>
                                                               @if($op->submit_by == 1)
                                                                   Approved
                                                                   @elseif($op->submit_by == 2)
                                                               Declined
                                                                   @else
                                                               Nothing Found
                                                                   @endif
                                                           </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Submit To</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->submit_to }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Comments </div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->comments }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Status </div>
                                                       <div class="profile-info-value">
                                                           <span>
                                                               @if($op->status == 0)
                                                                   Created
                                                               @elseif($op->status == 1)
                                                                   Submitted
                                                               @elseif($op->status == 2)
                                                                   Approved
                                                               @else
                                                                   Nothing Found
                                                               @endif
                                                           </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Created At </div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->created_at }}</span>
                                                       </div>
                                                   </div>
 
                                                       <hr>
 
                                                   @endforeach
 
                                               </div>
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif
                                       </div>
                                   </div>
 
                                   <!-- TNA Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   TNA Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                   @if(!empty($order))
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Order Code</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $order->order_code }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Confirm Date </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->confirm_date }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Lead Days </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->lead_days }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Tolerance Days </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->tolerance_days }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Tna Template </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->tna_temp_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Tna Template Buyer Name</div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Begin Date </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->begin_date }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Revise Begin Date </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->revise_begin_date }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Created AT </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->created_at }} </span>
                                                       </div>
                                                   </div>
                                                   @else
                                                       <h3>No Data Found</h3>
                                                   @endif
 
                                                   <h5 style="font-size: 13px;">Time and Action Templete</h5>
                                                   <hr>
 
                                                   <div class="col-xs-10" style="width: 300%;margin-left: -12px;">
                                                       <table class="table table-bordered" style="border:1px solid #6EAED1">
                                                           <thead>
                                                           <tr>
                                                               <th>SL</th>
                                                               <th>Activity</th>
                                                               <th>Actual Date</th>
                                                               <th>Remark</th>
                                                           </tr>
                                                           </thead>
                                                           <tbody>
                                                           @if(count($order_tna)>0)
                                                           @foreach($order_tna as $ot)
                                                           <tr>
                                                               <td>{{ $ot->id }}</td>
                                                               <td>{{ $ot->tna_lib_action }}</td>
                                                               <td>{{ $ot->actual_date }}</td>
                                                               <td></td>
                                                           </tr>
                                                               @endforeach
                                                               @else
                                                           <h3>No Data Found</h3>
                                                               @endif
                                                           </tbody>
                                                       </table>
                                                   </div>
 
                                               </div>
                                           </div>
                                       </div>
                                   </div>
 
                                   <!-- Buyer Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Buyer Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Buyer Name</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $order->b_name }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Buyer Short Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_shortname }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Buyer Address  </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_address }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name"> Buyer Country </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_country }} </span>
                                                       </div>
                                                   </div>
 
                                               </div>
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif
                                       </div>
                                   </div>

                                   <!-- Booking Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               
                                                   Booking Information
                                               
                                           </h4>
                                       </div>
 
                                       <div>
                                           @if(!empty($order_booking))
                                           <div class="panel-body">
                                            @foreach($order_booking as $ob)
                                               <div class="profile-user-info">
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Booking Ref No</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $ob->booking_ref_no }}</span>
                                                       </div>
                                                   </div>
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Name</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $ob->b_name }}</span>
                                                       </div>
                                                   </div>
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Supplier Name</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $ob->sup_name }}</span>
                                                       </div>
                                                   </div>
                                               </div>
                                               <br><br><br>
                                            @endforeach
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif
                                       </div>
                                   </div>
                               
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
 
   <script type="text/javascript">
       
   </script>
    @endsection