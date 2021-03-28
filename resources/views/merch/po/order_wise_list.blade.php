@php  
  $getColor = material_color_by_id();
  $getCountry = country_by_id();
  $getPort = port_by_id();
  $productSize = size_by_id();
@endphp
<div class="panel panel-success">
    <div class="panel-body pb-2">
      {{-- order info --}}
      {{-- @include('merch.common.order_info') --}}
      {{-- size breakdown --}}
      <div class="size-breakdown-section">
        @if(count($getPoList) > 0)
        @php $totalPoQty = 0; $flagS = 0; @endphp
        @foreach($getPoList as $po)
        @php $flag = 0; $totalPoQty += $po->po_qty; @endphp
        <div class="row">
          <div class="col-sm-12 pr-0">
            <div class="iq-card">
                
                <div class="iq-card-body p-2">
                   
                   <div class="size-group">
                      <div class="row">
                        <div class="offset-sm-1 col-sm-10">
                          <ul class="speciality-list m-0 p-0 justify-content-between d-flex">
                            <li class="d-flex mb-2 align-items-center w-25-left">
                               <div class="po-info img-fluid"><a href="#" class="iq-bg-primary"><i class="las la-shopping-basket"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>PO number</h6>
                                  <p class="mb-0">{{ $po->po_no }}</p>
                               </div>
                            </li>
                            <li class="d-flex mb-2 align-items-center w-25-left">
                               <div class="po-info img-fluid"><a href="#" class="iq-bg-warning"><i class="las la-palette"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Color</h6>
                                  <p class="mb-0">{{ $getColor[$po->clr_id]->clr_name??'' }}</p>
                               </div>
                            </li>
                            <li class="d-flex mb-2 align-items-center w-25-left">
                               <div class="po-info img-fluid"><a href="#" class="iq-bg-info"><i class="las la-globe"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Country & Port</h6>
                                  <p class="mb-0">{{ $getCountry[$po->po_delivery_country]->cnt_name??'' }} - {{ $getPort[$po->port_id]->port_name??'' }}</p>
                               </div>
                            </li>
                            <li class="d-flex mb-2 align-items-center w-25-left">
                               <div class="po-info img-fluid"><a href="#" class="iq-bg-success"><i class="las la-calendar"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Ex-fty Date</h6>
                                  <p class="mb-0">{{ custom_date_format($po->po_ex_fty )}}</p>
                               </div>
                            </li>
                            <li class="d-flex mb-2 align-items-center w-25-left">
                               <div class="po-info img-fluid"><a href="#" class="iq-bg-dark"><i class="las la-calendar"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>PO Quantity</h6>
                                  <p class="mb-0">{{ $po->po_qty }}</p>
                               </div>
                            </li>
                            
                         </ul>
                          <table class="table table-striped table-bordered table-head">
                            @if(isset($getPOSize[$po->po_id]))
                            @foreach($getPOSize[$po->po_id]->chunk(5) as $getSize)
                            <thead>
                              @if($flag == 0)
                                @php $flag = 1; @endphp
                                <tr>
                                @foreach($getSize as $size)
                                  <th>Size</th>
                                  <th>Quantity</th>
                                @endforeach
                                </tr>
                              @endif
                            </thead>
                            <tbody>
                              <tr>
                                @foreach($getSize as $k => $qty)
                                <td>{{ $productSize[$k]->mr_product_pallete_name }}</td>
                                <td>
                                  {{ $qty }}
                                </td>
                                @endforeach
                            </tr>
                            </tbody>
                            @endforeach
                            @endif
                          </table>
                        </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
          
        </div>
        @endforeach
        <div class="iq-card">
            <div class="iq-card-heading">
              <h4 class="text-center">Size Breakdown Summery</h4>
            </div>    
            <div class="iq-card-body p-2">
               
               <div class="size-group">
                  <div class="row">
                    <div class="offset-sm-1 col-sm-10">
                      <ul class="speciality-list m-0 p-0 justify-content-between d-flex">
                        <li class="d-flex mb-2 align-items-center w-25-left">
                           <div class="po-info img-fluid"><a href="#" class="iq-bg-primary"><i class="las la-shopping-basket"></i></a></div>
                           <div class="media-support-info ml-3">
                              <h6>Total PO</h6>
                              <p class="mb-0">{{ count($getPoList) }}</p>
                           </div>
                        </li>
                        <li class="d-flex mb-2 align-items-center w-25-left">
                               <div class="po-info img-fluid"><a href="#" class="iq-bg-info"><i class="las la-globe"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Order Qty</h6>
                                  <p class="mb-0">{{ $order->order_qty }}</p>
                               </div>
                            </li>
                        <li class="d-flex mb-2 align-items-center w-25-left">
                           <div class="po-info img-fluid"><a href="#" class="iq-bg-dark"><i class="las la-calendar"></i></a></div>
                           <div class="media-support-info ml-3">
                              <h6>Total PO Quantity</h6>
                              <p class="mb-0">{{ $totalPoQty }}</p>
                           </div>
                        </li>
                        
                     </ul>
                      <table class="table table-striped table-bordered table-head">
                        @if(count($uniqueSizeQty) > 0)
                          @foreach($uniqueSizeQty->chunk(5) as $sizeqty)
                          <thead>
                            @if($flagS == 0)
                              @php $flagS = 1; @endphp
                              <tr>
                              @foreach($sizeqty as $s)
                                <th>Size</th>
                                <th>Quantity</th>
                              @endforeach
                              </tr>
                            @endif
                          </thead>
                          <tbody>
                            <tr>
                              @foreach($sizeqty as $k => $qty)
                              <td>{{ $productSize[$k]->mr_product_pallete_name }}</td>
                              <td>
                                {{ $qty }}
                              </td>
                              @endforeach
                          </tr>
                          </tbody>
                          @endforeach
                          @endif  
                      </table>
                    </div>
                  </div>
               </div>
            </div>
         </div>
        @else
          <h4 class="text-center">No PO Found!</h4>
        @endif
      </div>
    </div> 
</div>