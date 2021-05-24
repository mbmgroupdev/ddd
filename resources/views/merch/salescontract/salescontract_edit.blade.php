@extends('merch.layout')
@section('title', 'Edit Sales Contract')
@push('css')
<style>
    input[type=text], input[type=number] {
    padding: 4px !important;
}
</style>
@endpush
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Merchandising</a>
                </li>
                
                <li class="active">Sales Contract Update</li>
                <li class="top-nav-btn">
                  <a class="btn btn-sm btn-primary" href="{{ url('merch/sales_contract/sales_contract_list') }}"><i class="las la-list"></i></a>
              </li>
            </ul><!-- /.breadcrumb -->
        </div>

          <div class="panel">
            <?php $url = explode('/',Request::url()); ?>
            {{-- <div class="page-header">
                <h1>Commercial <small><i class="ace-icon fa fa-angle-double-right"></i> Sales Contract Update  </small></h1>
            </div> --}}

            

            <div class="panel-body">

              @include('inc/message')
                <form class="form-horizontal" role="form" method="post" action=" {{ url('merch/sales_contract/sales_contract_update') }}" enctype="multipart/form-data">
                  @csrf
                    <div class="row">
                                
    
                                  <div class="col-md-4 ">
    
                                      <div class="form-group has-float-label select-search-group has-required">
                                              {{ Form::select('buyer', $buyer, $sales->mr_buyer_b_id, ['placeholder'=>'Select Buyer','id'=> 'buyer','class'=> 'form-control col-xs-10', 'data-validation' => 'required', 'disabled' => true]) }}
                                            <label for="product" >Buyer </label>
                                      </div>

                                      
    
                                      <div class="form-group has-float-label select-search-group has-required">
                                          
    
                                        
                                            {{ Form::select('unit', $unit, $sales->hr_unit_id, ['placeholder'=>'Select','id'=>'unit','class'=> 'form-control col-xs-10', 'data-validation' =>'required', 'disabled' => true]) }}
                                            <label for="unit" >Unit: </label>
                                      </div>
                                      <div class="form-group has-float-label select-search-group has-required">
                                          <label class="col-sm-6 control-label no-padding-right" for="contract_no" >Contract Source:<span style="color: red">&#42;</span></label>
                                          
                                          {{ Form::select('contract_no', array('In House'=>'In House', 'Buyer'=>'Buyer'), $sales->contract_no_by, ['placeholder'=>'Select','id'=>'c_number_by','class'=> 'form-control col-xs-10', 'data-validation' => 'required', 'disabled' => true]) }}
    
                                          <label for="contract_no" >Contract Source: </label>
    
                                      </div>
    
                                      <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-sm" id="add_order_button" data-toggle="modal" data-target="#right_modal_item" style="width:100%;border-radius: 5px;">Add Order</button>
                                        
                                    </div>
    
                                  </div>
                                  <div class="col-md-4">
    
                                      <div class="form-group has-float-label has-required">
                                          <input type="text" name="exlc_contract_no"  value="{{$sales->lc_contract_no}}" placeholder="Enter" id="exlc_contract_no" class=" form-control" readonly="readonly" data-validation ="required" value="{{$sales->lc_contract_no}}" />
                                          <label for="exlc_contract_no" > Export LC / Contract No. </label>
                                      </div>

                                      <div class="form-group has-float-label has-required">
                                            <input type="text" name="contract_qty" id="contract_qty" placeholder="Enter" class=" form-control"  data-validation ="required" value="{{$amend->contract_qty}}" readonly />
                                            <label for="contract_qty" > Contract Qty: </label>
                                      </div>
                                      <div class="form-group has-float-label has-required">
                                            <input type="text" name="contract_value" id="contract_value"  placeholder="Enter" class="contract_value form-control"  data-validation ="required" value="{{$amend->contract_value}}"/>
                                            <strong id="contract_value_suggestion"></strong>
                                            <label for="contract_value" > Contract Value: </label>
                                      </div>
    
                                      <div class="form-group has-float-label has-required">
                                          <input type="text" name="elc_date" id="elc_date" placeholder="Enter" class=" form-control datepicker" value="{{$amend->elc_amend_date}}" readonly=/>
                                          <label for="elc_date" > Contract Issue Date: </label>
                                      </div>
                                  </div>
                                  <div class="col-md-4 ">
    
                                      <div class="form-group ">
                                        <?php  $lc=$sales->lc_contract_type;?>
                                          <label class="form-check-label col-sm-6 control-label no-padding-right pl-0" for="cmpc" >Export Type: </label>
    
                                          <div class="col-sm-6">
                                            <input type="radio" class="form-check-input" id="lctype" name="lctype" value="ELC" <?php if($lc=='ELC'){ echo "checked=checked";}?> > ELC <br/>
                                            <input type="radio" class="form-check-input" id="lctype" name="lctype" value="Contract" <?php if($lc=='Contract'){ echo "checked=checked";}?> > Contract
                                        </div>
                                      </div>
                                  </div>
                          </div>
    
                          <div class="row">
                            <div class="col-md-12 form-horizontal">
                                <div class="tabbable">
                                  <ul class="nav nav-tabs">
                                      <li class="active">
                                          <a data-toggle="tab" href="#merch" aria-expanded="true">Optional</a>
                                      </li>
    
    
                                  </ul>
                                  <div class="tab-content">
                                    <!-- merch -->
                                      <div id="merch" class="tab-pane in active">
    
                                        <div class="row form-group">
    
                                          <div class="col-xs-6 col-sm-4">
                                              <div class="form-group has-float-label">
                                                  <input type="text" name="exp_date" id="exp_date" value="{{$amend->expire_date}}" placeholder="Enter" class=" form-control datepicker"/>
                                                  <label for="exp_date" > Expire Date: </label>
                                              </div>
                                              
                                              <div class="form-group has-float-label">
                                                  <input type="text" name="remark" id="remark" placeholder="Enter" class="form-control" value="{{$sales->remarks}}"/>
                                                  <label for="remark" > Remarks: </label>
                                              </div>
                                          </div>
                                          <div class="col-xs-6 col-sm-4">
                                              <div class="row">
                                                <div class="col-sm-9 ">
                                                  <div class="form-group has-float-label">
                                                    <input type="text" class="form-control" id="initial_value" name="initial_value" value="{{$sales->initial_value}}" placeholder="Enter" style="height: 32px;"  autocomplete="off">
                                                    <label for="initial_value"> Initial Value:  </label>
                                                  </div>   
                                                </div>
                                                <div class="col-sm-3">
                                                  <div class="form-group has-float-label select-search-group">  
                                                    {{ Form::select('currency', array('USD'=>'$ USD', 'EUR'=>'€ EUR','GBP'=>'£ GBP','Tk'=>'৳ Tk'), $sales->currency_type, ['placeholder'=>'Select','class'=> '', 'data-validation' => 'required']) }}
                                                  </div>   
                                              </div>
                                            </div> 
                                              
                                            <div class="form-group has-float-label select-search-group">
                                              {{ Form::select('lc_bank', $bank, $sales->lc_open_bank_id, ['placeholder'=>'Select','class'=> 'form-control']) }}
                                              <label for="lc_bank" >Buyer Bank: </label>
              
                                            </div>
                                              
                                          </div>


                                          <div class="col-xs-6 col-sm-4 col-sm-offset-1">

                                              <div class="form-group has-float-label select-search-group">
                                                {{ Form::select('btb_bank', $bank, $sales->btb_bank_id, ['placeholder'=>'Select','class'=> 'form-control col-xs-12']) }}
                                                <label for="btb_bank" >BTB Bank: </label>
                
                                              </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
    
                          <div class="row">
                            <div class="col-md-12" style="margin-top: 20px;">
                                @if($sales->contract_no_by== "In House")
                                  <table  class="table table-responsive table-bordered" >
                                    <thead>
                                        <tr>
                                          <th width="30%">Order Number</th>
                                          <th width="30%">Qty</th>
                                          <th width="30%">FOB</th>
                                          <th width="30%">Value</th>
                                        <tr>
                                    </thead>
    
                                    <tbody id="order_list_des">
                                      <?php $qty=0; $order_val=0;?>
                                      @foreach($sales_order as $order)
                                        <tr class="edit_rows" id="{{$order->mr_order_entry_order_id}}">
                                          <td width="20%"><input type="hidden" name="order_id[]" value="{{$order->mr_order_entry_order_id}}" readonly/>
                                          <input type="text" name="order_no[]" id="items[]" placeholder="" value="{{$order->order_code}}" readonly/></td>
                                          <td width="20%"><input type="text" name="qty[]" class="qty" placeholder="" value="{{$order->order_qty}}"  readonly/></td>
                                          <td width="20%"><input type="text" name="fob[]"  placeholder="" value="{{$order->agent_fob}}"  readonly/></td>
                                          <td width="20%">
                                          <input type="text" name="order_value[]" class="order_value" value="{{$order_value=$order->agent_fob*$order->order_qty}}"  readonly/></td>
                                        </tr>
    
                                        <?php $qty+=$order->order_qty;
                                              $order_val+=$order_value; ?>
                                      @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th width="20%" align="right"><b>Total :</b></th>
                                        <th colspan="2" >
                                          <input type="text" name="total_qty" readonly="readonly" id="total_qty" value="{{$qty}}">
                                        </th>
    
                                        <th width="20%">
                                          <input type="text" name="total_value" readonly="readonly" id="total_value" value="{{$order_val}}">
                                        </th>
                                    </tfoot>
                                  </table>
    
                                @else
                                    <table  class="table table-responsive table-bordered" >
                                    <thead>
                                        <tr>
                                          <th width="10%">Order Number</th>
                                          <th width="10%">Qty</th>
                                          <th width="10%">FOB</th>
                                          <th width="10%">New FOB</th>
                                          <th width="10%">Value</th>
                                          <th width="10%">New Value</th>
                                          <th width="20%">Order Delivery Date</th>
                                          <th width="20%">PO Delivery Date</th>
                                        <tr>
                                    </thead>
    
                                    <tbody id="order_list_des">
                                      <?php $qty=0; $order_val=0;$new_order_val=0;?>
                                      @foreach($sales_order as $order)
                                        <tr class="edit_rows" id="{{$order->mr_order_entry_order_id}}">
                                          <td width="10%"><input type="hidden" name="order_id[]" value="{{$order->mr_order_entry_order_id}}" readonly/>
                                          <input type="text" style="width:50px" name="order_no[]" id="items[]" placeholder="" value="{{$order->order_code}}" readonly/></td>
                                          <td width="10%"><input type="text" name="qty[]" class="qty" placeholder="" value="{{$order->order_qty}}"  readonly/></td>
                                          <td width="10%"><input style="width:50px" type="text" class="fob" name="fob[]"  placeholder="" value="{{$order->agent_fob}}"  readonly/></td>
                                          <td width="10%"><input style="width:50px" type="text" class="new_fob" name="new_fob[]"  placeholder="" value="{{$order->contract_fob}}"  readonly/></td>
                                          <td width="10%">
                                          <input type="text" name="order_value[]" class="order_value" value="{{$order_value=$order->agent_fob*$order->order_qty}}"  readonly/></td>
    
                                          <td width="10%">
                                              <input type="text" name="new_order_value[]" class="new_order_value" value="{{$order->contract_value}}"  readonly/>
                                          </td>
                                          <td width="20%">
                                            <input style="" type="text" name="order_delivery_date[]" class="order_delivery_date datepicker" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d', strtotime( $order->order_delivery_date)) }}" readonly/></td>
                                          <td width="20%">
                                              <input style="" type="text" name="po_delivery_date[]" class="po_delivery_date datepicker" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d', strtotime( $order->po_delivery_date)) }}" readonly/>
                                          </td>
                                        </tr>
    
                                        <?php $qty+=$order->order_qty;
                                              $order_val+=$order_value;
                                              $new_order_val+=$order->contract_value; ?>
                                      @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th  align="right"><b>Total :</b></th>
                                        <th colspan="3" >
                                          <input type="text" name="total_qty" readonly="readonly" id="total_qty" value="{{$qty}}">
                                        </th>
    
                                        <th colspan="">
                                          <input type="text" name="total_value" readonly="readonly" id="total_value" value="{{$order_val}}">
                                        </th>
                                        <th  colspan="3" width="10%">
                                            <input type="text" name="new_total_value" readonly="readonly" id="new_total_value" value="{{$new_order_val}}">
                                        </th>
                                    </tfoot>
                                  </table>
                                @endif
                            </div>
                          </div>
    
                          <div class="clearfix form-actions">
                              <div class="col-sm-offset-4 col-sm-4">
                                <input type="hidden" name="con_id" value="{{$sales->id}}">
                                  <button class="btn btn-sm btn-success" type="submit">
                                      <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                  </button>
    
                                  &nbsp; &nbsp; &nbsp;
                                  <button class="btn btn-sm" type="reset">
                                      <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                  </button>
                              </div>
                          </div>
            </form>
            </div>

            

            

          </div>
                <!-- PAGE CONTENT ENDS -->
    </div>

</div><!-- /.page-content -->

<!-- Old Modal Select Order -->
{{-- <div class="modal fade" id="select_item" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Order List</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        <div class="row" style="padding: 20px;" id="addListToModal">
           @if($orderLists->isEmpty())
               <span>No Order, Please select Buyer and Unit</span>
           @else
               <table class="table table-responsive table-bordered table-striped">
                 <thead>
                    <th class="">Order No</th>
                    <th class="">Style</th>
                    <th class="">Order Qty</th>
                    <th class="">Order Delivary Date</th>
                </thead>
                <tbody>
          @foreach($orderLists as $orderd)
                    <tr>
                      <td>
                        <input name="selected_item[]" type="checkbox" value="{{$orderd->order_id}}" class="ace col-sm-2 checkbox-input" checked>
                        <span class="lbl">&nbsp;&nbsp; {{$orderd->order_code}}</span>
                        <input type="hidden" class="qty" value="{{$orderd->order_qty}}">
                        <input type="hidden" class="fob" value="{{$orderd->agent_fob}}">
                        <input type="hidden" class="ord_del_date" value="{{$orderd->order_delivery_date}}">
                        <input type="hidden" class="po_del_date" value="{{$orderd->po_delivery_date}}">
                      </td>
                      <td>
                        <span class="lbl">{{$orderd->style_no}}</span>
                      </td>
                      <td>
                        <span class="lbl">{{$orderd->order_qty}}</span>
                      </td>
                      <td>
                         <span class="lbl">{{$orderd->order_delivery_date}}</span>
                      </td>
                  </tr>
          @endforeach
                </tbody>
            </table>
           @endif
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="modal_data" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div> --}}
<!-- Modal End -->

<!-- New Modal -->

<div class="modal right fade" id="right_modal_item" tabindex="-1" role="dialog" aria-labelledby="right_modal_item">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back">
          <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row" style="padding: 20px;" id="addListToModal">
          @if($orderLists->isEmpty())
              <span>No Order, Please select Buyer and Unit</span>
          @else
              <table class="table table-responsive table-bordered table-striped">
                <thead>
                   <th width="30%" class="">Order No</th>
                   <th width="30%" class="">Style</th>
                   <th width="30%" class="">Order Qty</th>
                   <th width="30%" class="">Order Delivary Date</th>
               </thead>
               <tbody>
         @foreach($orderLists as $orderd)
                   <tr>
                     <td>
                       <input name="selected_item[]" type="checkbox" value="{{$orderd->order_id}}" class="ace col-sm-2 checkbox-input" checked>
                       <span class="lbl">&nbsp;&nbsp; {{$orderd->order_code}}</span>
                       <input type="hidden" class="qty" value="{{$orderd->order_qty}}">
                       <input type="hidden" class="fob" value="{{$orderd->agent_fob}}">
                       <input type="hidden" class="ord_del_date" value="{{$orderd->order_delivery_date}}">
                       <input type="hidden" class="po_del_date" value="{{$orderd->po_delivery_date}}">
                     </td>
                     <td>
                       <span class="lbl">{{$orderd->style_no}}</span>
                     </td>
                     <td>
                       <span class="lbl">{{$orderd->order_qty}}</span>
                     </td>
                     <td>
                        <span class="lbl">{{$orderd->order_delivery_date}}</span>
                     </td>
                 </tr>
         @endforeach
               </tbody>
           </table>
          @endif
       </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="modal_data" class="btn btn-primary btn-sm">Done</button>
      </div>
      
    </div>
  </div>
</div>
<!-- End New Modal -->



<script type="text/javascript">
  $(document).ready(function(){

    var order_num=0;

    function makeReadOnly(){
      var selected_option= $("#c_number_by option:selected").text();
      if(selected_option == "In House"){
        $("#contract_value").prop("readonly", "readonly");
      }
      else{
        var value= parseFloat($("#contract_value").val());
        var five_percent= parseFloat((value*5)/100);
        var suggestion_string= "+ 5% ="+(parseFloat(value+five_percent).toFixed(2))+"; - 5% ="+(parseFloat(value-five_percent).toFixed(2));
        $("#contract_value_suggestion").text(suggestion_string);
      }
      var count=0;
      $("#order_list_des").find('.edit_rows').each(function(){
        count++;
      });
      order_num=count;
    }
    //make readonly or show suggestion on load
    makeReadOnly();

     /*
      * Restrict modal open if Contract Source is not selected
      * -----------------------
      */
    $("#add_order_button").on("click", function(e){
        var selected_option= $("#c_number_by option:selected").text();
        if(selected_option == "Select"){
            alert("Please select Contract Source!");
            e.stopPropagation();
        }
    });
 /*
  * SHOW DATA AFTER MODAL HIDE
  * -----------------------
  */
    var sgmodal = $("#select_item");
    $("body").on("click", "#modal_data", function(e) {

        order_num=0; //initialize order number
        var data= '';
        var table_head='';
        var table_foot='';
        var sum_qty = 0;
        var sum_value = 0;
        var contractText   = $("#c_number_by option:selected").html();

        if(contractText == "In House"){
            table_head='<tr>\
                          <th width="20%">Order Number</th>\
                          <th width="20%">Qty</th>\
                          <th width="20%">FOB</th>\
                          <th width="20%">Value</th>\
                        <tr>';

            table_foot='<th width="20%" align="right"><b>Total :</b></th>\
                                <th colspan="2" >\
                                  <input type="text" name="total_qty" readonly="readonly" id="total_qty">\
                                </th>\
                                <th width="20%">\
                                  <input type="text" name="total_value" readonly="readonly" id="total_value">\
                                </th>';
        }
        else{
            table_head= '<tr>\
                          <th width="10%">Order Number</th>\
                          <th width="10%">Qty</th>\
                          <th width="10%">FOB</th>\
                          <th width="10%">New FOB</th>\
                          <th width="10%">Value</th>\
                          <th width="10%">New Value</th>\
                          <th width="20%">Order Delivery Date</th>\
                          <th width="20%">PO Delivery Date</th>\
                        <tr>';

            table_foot='<th width="10%" align="right"><b>Total :</b></th>\
                                <th width="10%" colspan="3" >\
                                  <input type="text" name="total_qty" readonly="readonly" id="total_qty">\
                                </th>\
                                <th width="10%">\
                                  <input type="text" name="total_value" readonly="readonly" id="total_value">\
                                </th>\
                                <th width="10%" colspan="3">\
                                  <input type="text" name="new_total_value" readonly="readonly" id="new_total_value">\
                                </th>';
        }

        var existing_orders= [];

        $("#order_list_des").find(".edit_rows").each(function(){
            existing_orders.push(parseInt($(this).attr('id')));

        });
        console.log(existing_orders);

        $("#table_head").html(table_head);
        $("#table_foot").html(table_foot);

        $('.checkbox-input').each(function(i, v){
                // $("#select_item .modal-body").find('input:checkbox').prop('checked', true);


              if ($(this).is(":checked"))
            {
                //count number of order
                order_num++;
                var id= $(this).val();
               // console.log(id);
                var item_name= $(this).next().text();
                var qty= $(this).next().next('.qty').val();
                var fob= $(this).next().next().next('.fob').val();
                var order_del_date= $(this).next().next().next().next('.ord_del_date').val();
                var po_del_date= $(this).next().next().next().next().next('.po_del_date').val();

                var cvalue=(qty*fob).toFixed(2);

                if(jQuery.inArray(id, existing_orders) !== -1){
                  data+= $("#order_list_des").find("#"+id)[0].outerHTML;
                }
                else{
                  if(contractText == "In House"){
                      data+='<tr class="edit_rows" id="'+id+'">\
                         <td width="20%"><input type="hidden" name="order_id[]" value="'+id+'" readonly/>\
                         <input type="text" name="order_no[]" id="items[]" placeholder="" value="'+item_name+'" readonly/></td>\
                         <td width="20%"><input type="text" name="qty[]" class="qty" placeholder="" value="'+qty+'"  readonly/></td>\
                         <td width="20%"><input type="text" name="fob[]"  placeholder="" value="'+fob+'"  readonly/></td>\
                         <td width="20%">\
                         <input type="text" name="order_value[]" class="order_value" value="'+cvalue+'"  readonly/></td>\
                        </tr>';
                  }
                  else{
                      data+='<tr class="edit_rows" id="'+id+'">\
                         <td width="10%"><input style="width:56px" type="hidden" name="order_id[]" value="'+id+'" readonly/>\
                         <input style="width:50px" type="text" name="order_no[]" id="items[]" placeholder="" value="'+item_name+'" readonly/></td>\
                         <td width="10%"><input style="" type="text" name="qty[]" class="qty" placeholder="" value="'+qty+'"  readonly/></td>\
                         <td width="10%">\
                         <input style="width:50px" type="text" class="fob" name="fob[]"  placeholder="" value="'+fob+'"  readonly/></td>\
                         <td width="10%">\
                         <input style="width:50px" type="text" class="new_fob" name="new_fob[]"  placeholder="" value=""  readonly/></td>\
                         <td width="10%">\
                         <input style="" type="text" name="order_value[]" class="order_value" value="'+cvalue+'"  readonly/></td>\
                         <td width="10%">\
                         <input style="" type="text" name="new_order_value[]" class="new_order_value" value=""  readonly/></td>\
                         <td width="20%">\
                         <input style="" type="text" name="order_delivery_date[]" class="order_delivery_date datepicker" placeholder="yyyy-mm-dd" value="'+order_del_date+'"  readonly/></td>\
                         <td width="20%">\
                         <input style="" type="text" name="po_delivery_date[]" class="po_delivery_date datepicker" placeholder="yyyy-mm-dd" value="'+po_del_date+'"  readonly/></td>\
                        </tr>';
                  }
                }

              // Calculate Total Quantity & Value

                sum_qty += parseInt(qty);
                sum_value += parseFloat(cvalue);
            }
          });

        // Set Values
        $("#order_list_des").html(data);
        $("#total_qty").val(sum_qty);
        $("#total_value").val(sum_value);

        if(contractText == "In House"){
            $("#contract_qty").val(sum_qty);
            $("#contract_value").val(sum_value);
            $("#contract_value_suggestion").text("");
        }
        else{
            $("#contract_qty").val(sum_qty);
            $("#contract_value").val(sum_value);

            var increased_value= sum_value + getPercentage(sum_value, 5);
            var decreased_value= sum_value - getPercentage(sum_value, 5);
            calculateNewFOB(sum_value, "Buyer");

            var suggestion_string= "+ 5% ="+decreased_value+"; - 5% ="+increased_value;
            $("#contract_value_suggestion").text(suggestion_string);
        }




        var expire_date = new Date(getExpireDate());
        // var currentDate = new Date();
        var dateLimit = new Date(Date.parse(new Date(expire_date.getFullYear(), expire_date.getMonth(), expire_date.getDate() + 15)));

        $("#exp_date").val(getFormattedDate(dateLimit));

        sgmodal.modal('hide');

        // Checking for Contract Qty and Contract Value
        var contract_qty= $("#contract_qty").val();
        var contract_value= $("#contract_value").val();
        checkQuantity(contract_qty,sum_qty);
        checkValue(contract_value,sum_value);
    });


    function getFormattedDate(date) {
        var year = date.getFullYear();
        var month = (1 + date.getMonth()).toString();
        month = month.length > 1 ? month : '0' + month;

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        return year + '-' +month + '-' + day;
    }


    //calculate percentage
    function getPercentage(value, percent){
        return ((value*percent)/100);
    }

    $("#contract_qty").on("keyup", function() {
        var contractQty= $("#contract_qty").val();
        var sumQty= $("#total_qty").val();

        //alert(contract_qty);
        if(sumQty!=""){
          checkQuantity(contractQty,sumQty);
        }

    });
    $("#contract_value").on("change", function() {

        var contract_value= $("#contract_value").val();
        var sumValue= $("#total_value").val();
        var contractText   = $("#c_number_by option:selected").html();

        calculateNewFOB(contract_value, contractText);
    });

    //calculate new FOB
    function calculateNewFOB(contract_value, contractText){
        var old_total_value= parseFloat($("#total_value").val()).toFixed(2);
        var new_total_value= contract_value;

        var value_difference= (new_total_value - old_total_value);
        var extra_value=  parseFloat(value_difference / order_num).toFixed(2);

        $("#order_list_des").find(".qty").each(function(){
            var qty= parseFloat($(this).val()).toFixed(2);
            var old_fob= parseFloat($(this).parent().next().find('.fob').val()).toFixed(2);

            var old_value= parseFloat($(this).parent().next().next().next().find('.order_value').val()).toFixed(2);
            var test= old_value+extra_value;
            var new_value= parseFloat(parseFloat(old_value)+parseFloat(extra_value)).toFixed(2);

            var new_fob= parseFloat(new_value / qty).toFixed(2);

          console.log(new_fob);
          //console.log(sumValue);
          //console.log(contractText);



            $(this).parent().next().next().find('.new_fob').val(new_fob);
            $(this).parent().next().next().next().next().find('.new_order_value').val(new_value);
            $("#new_total_value").val(contract_value);
        });

        if(contractText == "Buyer"){
            var increased_value= parseFloat(parseFloat(old_total_value) + parseFloat(getPercentage(old_total_value, 5))).toFixed(2);

            var decreased_value= parseFloat(parseFloat(old_total_value) - parseFloat(getPercentage(old_total_value, 5))).toFixed(2);

            checkValueWhenBuyer(contract_value, increased_value, decreased_value, old_total_value);
        }
        else{
            checkValue(contract_value,old_total_value);
        }

    }

    //expire date calculation
    function getExpireDate(){
        var date_array= [];
        $("#order_list_des").find('.po_delivery_date').each(function(){
            if($(this).val() != ""){
                date_array.push($(this).val());
            }
        });
        //if no po delivery date, then find order delivery date

        if(date_array.length==0){
            date_array= [];
            $("#order_list_des").find('.order_delivery_date').each(function(){
                if($(this).val() != null){
                    date_array.push($(this).val());
                }
            });
        }
        if(date_array.length>0){
            date_array.sort();
            date_array.reverse();
            return date_array[0];
        }
        else
            return null;
    }

    function checkQuantity(contract_qty,sum_qty){
       if(contract_qty>sum_qty){
        alert('Contract quantity is Greater than Order Quantity');
      }

    }

    function checkValue(contract_value,sum_value){
       if(contract_value>sum_value){
        alert('Contract Value is Greater than Order Value');

      }
    }

    //check contract value when source is buyer
    function checkValueWhenBuyer(contract_value,increased_value,decreased_value,old_total_value){

        var msg= 'Contract value will be between '+decreased_value+' and '+increased_value;
        contract_value= parseFloat(contract_value);
        increased_value= parseFloat(increased_value);
        decreased_value= parseFloat(decreased_value);
        old_total_value= parseFloat(old_total_value);

        if(contract_value>increased_value){
            alert(msg);
            $("#contract_value").val(old_total_value);
            calculateNewFOB(old_total_value, "Buyer");
        }
        if(contract_value<decreased_value){
            alert(msg);
            $("#contract_value").val(old_total_value);
            calculateNewFOB(old_total_value, "Buyer");
        }
    }
});

</script>
@endsection
