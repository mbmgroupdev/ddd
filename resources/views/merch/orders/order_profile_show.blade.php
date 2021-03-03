@extends('merch.index')
@push('css')
<style type="text/css">
  .sub-table-recon tr:first-child td, .sub-table-recon{border-top: none !important; }
  
</style>
@endpush
@section('content')
<div class="main-content">
       <div class="main-content-inner">
           <div class="breadcrumbs ace-save-state" id="breadcrumbs">
               <ul class="breadcrumb">
                   <li>
                       <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a>
                   </li>
                   <li>
                       <a href="#">Merchandising</a>
                   </li>
                   <li>
                       <a href="#">Order</a>
                   </li>
                   <li class="active">Order Profile Information</li>
               </ul><!-- /.breadcrumb -->
           </div>
 
           <div class="page-content">
               <div class="page-header">
                   <h1 style="float:left;">Merchandising<small> <i class="ace-icon fa fa-angle-double-right"></i> Order Profile Information</small></h1>

                   <a type="button" href="{{url('merch/orders/order_profile_pdf/'.$id)}}" class="btn btn-xs btn-danger"><span class="fa fa-file-pdf-o" aria-hidden="true"></span> Download</a>
                   <button class="btn btn-xs btn-primary hidden-print" onclick="myFunction('pro_pic','contactInfo','basicInfoTable','styleInfoTable','poInfoTable','bomInfo','costingInfo','approvalInfoTable','tnaDetailTable','tnaTable','buyerInfoTable','bookingInfoTable')"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
               </div>
 
               <div class="row">
                   <div class="col-xs-12">
                       <div id="user-profile-1" class="user-profile row">
                           <div class="col-sm-3 center">
                               <div id="pro_pic">
                                <span class="profile-picture">
                                    {{-- <img id="avatar" style="width: 180px; height: 200px;" class="img-responsive" alt="profile picture" src="{{ $order->stl_img_link }}" /> --}}
                                    <a href="{{ asset(!empty($order->stl_img_link)?$order->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" target="_blank">
                                      <img id="avatar" style="width: 180px; height: 200px;" class="img-responsive" alt="profile picture" src="{{ asset(!empty($order->stl_img_link)?$order->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}"/>
                                    </a>
                                </span>
                                   <div class="space-4"></div>
                                   <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
 
                                       <div class="inline position-relative">
                                           <a href="#" class="user-title-label">
                                               <span class="white">{{ $order->stl_product_name }}</span>
                                           </a>
                                       </div>
                                   </div>
                               </div>
 
                               <div class="space-6"></div>
                               <div class="profile-contact-info" id="contactInfo">
                                   <div class="profile-contact-links align-left">
                                       <p style="text-align: center;"><strong>Order Code : </strong> {{ $order->order_code }}</p>
                                       <p style="text-align: center;"><strong>Order Status : </strong> {{ $order->order_status }}</p>
                                       <p style="text-align: center;"><strong>Order Quantity : </strong> {{ $order->order_qty }}</p>
                                       <p style="text-align: center;"><strong>Order Delivery Date : </strong> {{ $order->order_delivery_date }}</p>
                                   </div>
                               </div>
 
                           </div>
 
                           <div class="col-sm-9">
                               {!!$steps!!}
                               <div id="accordion" class="accordion-style1 panel-group accordion-style2">
                                   <!-- Reservation Information -->
                                   <div class="panel panel-info">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#basicInfo" aria-expanded="true">
                                                   <i class="bigger-110 ace-icon glyphicon glyphicon-minus-sign" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Reservation Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse in" id="basicInfo" aria-expanded="true" style="">
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation Id </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_id }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Unit </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->hr_unit_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation Month </div>
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
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation Year </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_year }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Product Type </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->prd_type_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation Quantity </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_quantity }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation sah </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_sah }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation Sweing Smv </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_sewing_smv }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Reservation Created </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->res_created_on }} </span>
                                                       </div>
                                                   </div>
 

                                               </div>
                                           </div>
                                           @else
                                           <div class="panel-body">
                                               <h3>No data found</h3>
                                           </div>
                                           @endif

                                           <div class="widget-body" id="basicInfoTable">
                                             <div class="row">
                                                 <div class="col-sm-12">
                                                   @if(!empty($order))
                                                     <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                        <tbody>
                                                         
                                                         <tr>
                                                             <th>Reservation Id</th>
                                                             <td>{{ $order->res_id }}</td>
                                                             <th>Unit</th>
                                                             <td>{{ $order->hr_unit_name }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Buyer Name</th>
                                                             <td>{{ $order->b_name }}</td>
                                                             <th>Reservation Month</th>
                                                             <td><?php
                                                                 $monthNum = $order->res_month;
                                                                 $monthName = date('F',mktime(0,0,0, $monthNum, 19));
                                                                 echo $monthName;?>    
                                                             </td>
                                                         </tr>
                                                         <tr>
                                                             <th>Reservation Year</th>
                                                             <td>{{ $order->res_year }}</td>
                                                             <th>Product Type</th>
                                                             <td>{{ $order->prd_type_name }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Reservation Quantity</th>
                                                             <td>{{ $order->res_quantity }}</td>
                                                             <th>Reservation sah</th>
                                                             <td>{{ $order->res_sah  }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Reservation Sweing Smv</th>
                                                             <td>{{ $order->res_sewing_smv }}</td>
                                                             <th>Reservation Created</th>
                                                             <td>{{ $order->res_created_on }}</td>
                                                         </tr>
                                                        </tbody>
                                                     </table>
                                                   @else 
                                                      <table class="hidden">
                                                       <tbody>
                                                         <tr>
                                                           <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                         </tr>
                                                       </tbody>
                                                     </table>
                                                   @endif
                                                 </div>
                                             </div>
                                           </div>
                                       </div>
                                   </div>
 
                                   <!-- Order Reconciliation -->
                                   {{-- <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#orderReInfo">
                                                   <i class="bigger-110 ace-icon glyphicon glyphicon-minus-sign" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Order Reconciliation
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="orderReInfo">
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
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#styleInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Style Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="styleInfo">
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Type </div>
                                                       <div class="profile-info-value">
                                                        <span>{{ $order->stl_type }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style no </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_no }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Product Type No </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->prd_type_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Garments Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->gmt_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Product Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_product_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Description </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_description }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Session Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->se_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Smv </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_smv }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Added By </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_addedby }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Added On </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->stl_added_on }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Style Satus </div>
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

                                           <div class="widget-body" id="styleInfoTable">
                                             <div class="row">
                                                 <div class="col-sm-12">
                                                  @if(!empty($order))
                                                     <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                         <tbody>
                                                         
                                                         <tr>
                                                             <th>Style Type</th>
                                                             <td>{{ $order->stl_type }}</td>
                                                             <th>Style No</th>
                                                             <td>{{ $order->stl_no }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Buyer Name</th>
                                                             <td>{{ $order->b_name }}</td>
                                                             <th>Product Type</th>
                                                             <td>{{ $order->prd_type_name }}</td>
                                                         </tr>
                                                         <tr>
                                                             <th>Garments Name</th>
                                                             <td>{{ $order->gmt_name }}</td>
                                                             <th>Style Product Name</th>
                                                             <td>{{ $order->stl_product_name }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Style Description</th>
                                                             <td>{{ $order->stl_description }}</td>
                                                             <th>Session Name</th>
                                                             <td>{{ $order->se_name  }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Style Smv</th>
                                                             <td>{{ $order->stl_smv }}</td>
                                                             <th>Style Added By</th>
                                                             <td>{{ $order->stl_addedby }}</td>
                                                         </tr>
                                                         <tr>
                                                             <th>Style Added On</th>
                                                             <td>{{ $order->stl_added_on }}</td>
                                                             <th>Style Status</th>
                                                             <td><?php
                                                                 if($order->stl_status == 0)
                                                                     echo 'Created';
                                                                 else if($order->stl_status == 1)
                                                                     echo 'Submitted';
                                                                 else if($order->stl_status == 2)
                                                                     echo 'Approved';
                                                                 else
                                                                     echo 'Nothing Found';
                                                                 
                                                                 ?>
                                                             </td>
                                                         </tr>
                                                         </tbody>
                                                     </table>
                                                  @else
                                                     <table class="hidden">
                                                       <tbody>
                                                         <tr>
                                                           <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                         </tr>
                                                       </tbody>
                                                     </table>
                                                   @endif
                                                 </div>
                                             </div>
                                           </div>
 

                                       </div>
                                   </div>
 
                                   <!-- Purchase Order Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#poInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Purchase Order Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="poInfo">
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                   @foreach($order_purchase as $op)
 
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Purchase Order No </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->po_no }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Purchase Order Quantity </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->po_qty }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Purchase Order Ex Fty </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->po_ex_fty }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Purchase Order Delivery Country Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->cnt_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Country Fob </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->country_fob }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Remarks </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $op->remarks }} </span>
                                                       </div>
                                                   </div>
                                                      
                                                   @endforeach
 
                                               </div>
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif

                                           <div class="widget-body" id="poInfoTable">
                                             <div class="row">
                                                 <div class="col-sm-12">
                                                   @if(!empty($order))
                                                    @foreach($order_purchase as $op)
                                                     <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                         <tbody>
                                                         
                                                         <tr>
                                                             <th>Purchase Order No </th>
                                                             <td>{{ $op->po_no }}</td>
                                                             <th>Purchase Order Quantity</th>
                                                             <td>{{ $op->po_qty }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Purchase Order Ex Fty </th>
                                                             <td>{{ $op->po_ex_fty }}</td>
                                                             <th>Product Type</th>
                                                             <td>{{ $order->prd_type_name }}</td>
                                                         </tr>
                                                         <tr>
                                                             <th>Purchase Order Delivery Country Name</th>
                                                             <td>{{ $op->cnt_name }}</td>
                                                             <th>Country Fob</th>
                                                             <td>{{ $op->country_fob }}</td>
                                                        </tr>
                                                         <tr>
                                                             <th>Remarks</th>
                                                             <td>{{ $op->remarks }}</td>
                                                         </tr>
                                                         </tbody>
                                                     </table>
                                                    @endforeach
                                                    @else
                                                         <table class="hidden">
                                                         <tbody>
                                                           <tr>
                                                             <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                           </tr>
                                                         </tbody>
                                                       </table>
                                                    @endif
                                                 </div>
                                             </div>
                                           </div>

                                       </div>
                                   </div>
 
                                   <!-- BOM Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#bomInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Bom Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="bomInfo">
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
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#costingInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Costing Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="costingInfo">
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
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#approvalInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Approval Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="approvalInfo">
                                           @if(count($order_approve)>0)
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                    @foreach($order_approve as $op)
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Order Code</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->order_code }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Title</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->title }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Submit By </div>
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
                                                       <div class="profile-info-name" style="font-weight: bold;"> Submit To</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->submit_to }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Comments </div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->comments }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Status </div>
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
                                                       <div class="profile-info-name" style="font-weight: bold;"> Created At </div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $op->created_at }}</span>
                                                       </div>
                                                   </div>
 
                                                   @endforeach
 
                                               </div>
                                           </div>
                                               @else
                                           <div class="panel-body">
                                               <h3>No Data Found</h3>
                                           </div>
                                               @endif

                                           <div class="widget-body" id="approvalInfoTable">
                                               <div class="row">
                                                   <div class="col-sm-12">
                                                     @if(count($order_approve)>0)
                                                       <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                           <tbody>
                                                           
                                                           <tr>
                                                               <th>Order Code </th>
                                                               <td>{{ $op->order_code }}</td>
                                                               <th>Title</th>
                                                               <td>{{ $op->title }}</td>
                                                          </tr>
                                                           <tr>
                                                               <th>Submitted By </th>
                                                               <td>
                                                                <?php
                                                                if($op->submit_by == 1)
                                                                    echo 'Approved';
                                                                else if($op->submit_by == 2)
                                                                   echo 'Declined';
                                                                else
                                                                   echo 'Nothing Found';
                                                                ?> 
                                                               </td>
                                                               <th>Submit To</th>
                                                               <td>{{ $op->submit_to }}</td>
                                                           </tr>
                                                           <tr>
                                                               <th>Comments</th>
                                                               <td>{{ $op->comments }}</td>
                                                               <th>Status</th>
                                                               <td>
                                                                 <?php
                                                                 if($op->status == 0)
                                                                     echo 'Created';
                                                                 else if($op->status == 1)
                                                                     echo 'Submitted';
                                                                 else if($op->status == 2)
                                                                     echo 'Approved';
                                                                 else
                                                                     echo 'Nothing Found';
                                                                 ?>
                                                               </td>
                                                          </tr>
                                                           <tr>
                                                               <th>Created At</th>
                                                               <td>{{ $op->created_at }}</td>
                                                           </tr>
                                                           </tbody>
                                                       </table>
                                                     @else
                                                       <table class="hidden">
                                                         <tbody>
                                                           <tr>
                                                             <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                           </tr>
                                                         </tbody>
                                                       </table>
                                                     @endif
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
 
                                   <!-- TNA Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#tnaInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   TNA Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="tnaInfo">
                                           <div class="panel-body">
                                               <div class="profile-user-info">
                                                   @if(!empty($order))

                                                <div id="tnaDetail">
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Order Code</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $order->order_code }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Confirm Date </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->confirm_date }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Lead Days </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->lead_days }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Tolerance Days </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->tolerance_days }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Tna Template </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->tna_temp_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Tna Template Buyer Name</div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_name }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Begin Date </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->begin_date }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Revise Begin Date </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->revise_begin_date }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Created AT </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->created_at }} </span>
                                                       </div>
                                                   </div>
                                                   @else
                                                       <h3>No Data Found</h3>
                                                   @endif
 
                                                   <h5 style="font-size: 13px;">Time and Action Templete</h5>
                                                   <hr>

                                                </div>

                                                <div class="widget-body" id="tnaDetailTable">
                                                   <div class="row">
                                                       <div class="col-sm-12">
                                                         @if(!empty($order))
                                                           <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                              <tbody>
                                                               
                                                               <tr>
                                                                   <th>Order Code</th>
                                                                   <td>{{ $order->order_code }}</td>
                                                                   <th>Confirm Date</th>
                                                                   <td>{{ $order->confirm_date }}</td>
                                                              </tr>
                                                               <tr>
                                                                   <th>Lead Days</th>
                                                                   <td>{{ $order->lead_days }}</td>
                                                                   <th>Tolerance Days</th>
                                                                   <td>{{ $order->tolerance_days }}</td>
                                                               </tr>
                                                               <tr>
                                                                   <th>TNA Template</th>
                                                                   <td>{{ $order->tna_temp_name }}</td>
                                                                   <th>Tna Template Buyer Name</th>
                                                                   <td>{{ $order->b_name }}</td>
                                                              </tr>
                                                               <tr>
                                                                   <th>Begin Date</th>
                                                                   <td>{{ $order->begin_date }}</td>
                                                                   <th>Revise Begin Date</th>
                                                                   <td>{{ $order->revise_begin_date  }}</td>
                                                              </tr>
                                                               <tr>
                                                                   <th>Created At</th>
                                                                   <td>{{ $order->created_at }}</td>
                                                                   
                                                               </tr>
                                                              </tbody>
                                                           </table>
                                                         @else 
                                                            <table class="hidden">
                                                             <tbody>
                                                               <tr>
                                                                 <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                               </tr>
                                                             </tbody>
                                                           </table>
                                                         @endif
                                                       </div>
                                                   </div>
                                                </div>

                                                <div>
                                                   <div class="col-xs-10" id="tnaTable" style="width: 100%;margin-left: -12px;">
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
                                   </div>
 
                                   <!-- Buyer Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#buyerInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Buyer Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="buyerInfo">
                                           @if(!empty($order))
                                           <div class="panel-body">
                                               <div class="profile-user-info">
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Name</div>
                                                       <div class="profile-info-value">
                                                           <span>{{ $order->b_name }}</span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Short Name </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_shortname }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Address  </div>
                                                       <div class="profile-info-value">
                                                           <span> {{ $order->b_address }} </span>
                                                       </div>
                                                   </div>
 
                                                   <div class="profile-info-row">
                                                       <div class="profile-info-name" style="font-weight: bold;"> Buyer Country </div>
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

                                       <div class="widget-body" id="buyerInfoTable">
                                         <div class="row">
                                             <div class="col-sm-12">
                                               @if(!empty($order))
                                                 <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                    <tbody>
                                                     
                                                     <tr>
                                                         <th>Buyer Name</th>
                                                         <td>{{ $order->b_name }}</td>
                                                         <th>Buyer Short Name</th>
                                                         <td>{{ $order->b_shortname }}</td>
                                                    </tr>
                                                     <tr>
                                                         <th>Buyer Address</th>
                                                         <td>{{ $order->b_address }}</td>
                                                         <th>Buyer Country</th>
                                                         <td>{{ $order->b_country }}</td>
                                                     </tr>
                                                     
                                                    </tbody>
                                                 </table>
                                               @else 
                                                  <table class="hidden">
                                                   <tbody>
                                                     <tr>
                                                       <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                     </tr>
                                                   </tbody>
                                                 </table>
                                               @endif
                                             </div>
                                         </div>
                                       </div>
                                   </div>

                                   <!-- Booking Information -->
                                   <div class="panel panel-default">
                                       <div class="panel-heading">
                                           <h4 class="panel-title">
                                               <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#bookingInfo">
                                                   <i class="ace-icon glyphicon glyphicon-plus-sign bigger-110" data-icon-hide="ace-icon glyphicon glyphicon-minus-sign" data-icon-show="ace-icon glyphicon glyphicon-plus-sign"></i>
                                                   Booking Information
                                               </a>
                                           </h4>
                                       </div>
 
                                       <div class="panel-collapse collapse" id="bookingInfo">
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
                                       <div class="widget-body" id="bookingInfoTable">
                                         <div class="row">
                                             <div class="col-sm-12">
                                               @if(!empty($order))
                                                @foreach($order_booking as $ob)
                                                 <table class="hidden" width="70%" cellpadding="0" cellspacing="0" border="0">
                                                    <tbody>
                                                     
                                                     <tr>
                                                         <th>Booking Ref No</th>
                                                         <td>{{ $ob->booking_ref_no }}</td>
                                                         <th>Buyer Name</th>
                                                         <td>{{ $ob->b_name }}</td>
                                                    </tr>
                                                     <tr>
                                                         <th>Supplier Name</th>
                                                         <td>{{ $ob->sup_name }}</td>
                                                         
                                                     </tr>
                                                     
                                                    </tbody>
                                                 </table>
                                                 <br><br>
                                                @endforeach
                                               @else 
                                                  <table class="hidden">
                                                   <tbody>
                                                     <tr>
                                                       <td style="text-align: center; font-weight: bold;">No Data Found</td>
                                                     </tr>
                                                   </tbody>
                                                 </table>
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
           </div>
       </div>
   </div>
 
   <script type="text/javascript">
       function myFunction(pro_pic,contactInfo,basicInfoTable,styleInfoTable,poInfoTable,bomInfo,costingInfo,approvalInfoTable,tnaDetailTable,tnaTable,buyerInfoTable,bookingInfoTable) {
         // $('#accordion .collapse').addClass("in");
         var style_sheet = '' +
        '<style type="text/css">' +
        '@page {'+
        'size: A4 landscape;'+
        '}'+
        'table{ '+
        'width: 100%;'+
        'border-collapse: collapse;'+
      // 'display: block'+
        '}'+
        'table th {' +
        'border:1px solid #808080;' +
        'padding:0.5em;' +
        'margin:0px;'+
        '}' +
        'table td {' +
        'border:1px solid #808080;' +
        'padding:0.4em;' +
        'margin:0px;'+
        '}' +
        'h2 {' +
          'text-align:center; page-break-before: always; border: 1px solid lightgrey; border-radius:8px; padding:0.2em; margin-bottom: 30px;'
        '}' +


        '</style>';
    var mywindow = window.open('', 'PRINT', 'height=900,width=1200');

    mywindow.document.write('<html><head><title style="text-align:center;">' + 'Order Profile'  + '</title>');
    mywindow.document.write('</head><body>');
    mywindow.document.write('<h1 style="text-align:center;">&nbsp ' +document.getElementById(pro_pic).innerHTML+ '</h1><br>');

    mywindow.document.write('<div style="width: 100%; margin-left:20px;"><div style="text-align:center;">');

    mywindow.document.write(document.getElementById(contactInfo).innerHTML+' </div></div>');

    mywindow.document.write('<br><br>');
    mywindow.document.write('<h3 style="text-align:center; border: 1px solid lightgrey; border-radius:8px; padding:0.2em; margin-bottom: 30px;">Basic Information<small></small></h3>');
    
    mywindow.document.write('<div style="width:70%; margin-left:15%; margin-top:20px; border: 1px solid #808080; border-radius:8px; text-align:center;">');

    mywindow.document.write(document.getElementById(basicInfoTable).innerHTML+'</div>');

    // mywindow.document.write('<br><br><br>');
    // mywindow.document.write('<h2 ">Order Information<small></small></h2>');
    // mywindow.document.write(document.getElementById(orderReInfo).innerHTML);
    mywindow.document.write('<br><br>');
    mywindow.document.write('<h2 ">Style Information<small></small></h2>');
    mywindow.document.write('<div style="width:70%; margin-left:15%; margin-top:20px; border: 1px solid #808080; border-radius:8px; text-align:center;">');
    mywindow.document.write(document.getElementById(styleInfoTable).innerHTML+'</div>');
    mywindow.document.write('<br><br>');
    mywindow.document.write('<h3 style="text-align:center; border: 1px solid lightgrey; border-radius:8px; padding:0.2em; margin-bottom: 30px;">Purchase Order Information<small></small></h3>');
    mywindow.document.write('<div style="width:70%; margin-left:15%; margin-top:20px; border: 1px solid #808080; border-radius:8px; text-align:center;">');
    mywindow.document.write(document.getElementById(poInfoTable).innerHTML+'</div>');
    mywindow.document.write('<br><br>');
    mywindow.document.write('<h2 ">BOM Information &nbsp<small></small></h2>');
    mywindow.document.write(document.getElementById(bomInfo).innerHTML);
    mywindow.document.write('<br><br>');
    mywindow.document.write('<h2 ">Costing Information &nbsp<small></small></h2>');
    mywindow.document.write(document.getElementById(costingInfo).innerHTML);
    mywindow.document.write('<br><br>');
    mywindow.document.write('<h2 ">Approval Information &nbsp<small></small></h2>');
    mywindow.document.write('<div style="width:70%; margin-left:15%; margin-top:20px; border: 1px solid #808080; border-radius:8px; text-align:center;">');
    mywindow.document.write(document.getElementById(approvalInfoTable).innerHTML+'</div>');
    mywindow.document.write('<br><br>');
    mywindow.document.write('<h2 ">TNA Information &nbsp<small></small></h2>');
    mywindow.document.write('<div style="width:50%; margin-left:25%; margin-top:20px; border: 1px solid #808080; border-radius:8px; text-align:center;">');
    mywindow.document.write(document.getElementById(tnaDetailTable).innerHTML+'</div>');
    mywindow.document.write('<br><br>');
    mywindow.document.write(document.getElementById(tnaTable).innerHTML);
    mywindow.document.write('<h2 ">Buyer Information &nbsp<small></small></h2>');
    mywindow.document.write('<div style="width:50%; margin-left:25%; margin-top:20px; border: 1px solid #808080; border-radius:8px; text-align:center;">');
    mywindow.document.write(document.getElementById(buyerInfoTable).innerHTML+'</div>');
    mywindow.document.write('<h3 style="text-align:center; border: 1px solid lightgrey; border-radius:8px; padding:0.2em; margin-bottom: 30px;">Booking Information<small></small></h3>');
    mywindow.document.write('<div style="width:50%; margin-left:25%; margin-top:20px;  border-radius:8px; text-align:center;">');
    mywindow.document.write(document.getElementById(bookingInfoTable).innerHTML+'</div>');
    mywindow.document.write('</body>'+style_sheet+'</html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    location.reload();

    
    return true;
       }
   </script>
    @endsection