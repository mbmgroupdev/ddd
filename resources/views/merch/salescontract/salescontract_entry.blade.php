@extends('merch.layout')
@section('title', 'Create Sales Contract')
@push('css')
    <style>
        input[type=text], input[type=number] {
            padding: 10px !important;
        }

        input[type="date" i] {
    padding: 10px !important;
    
}
        @media only screen and (max-width: 767px) {

            .modal{padding-top: 50px;}
        }

        .label_background label::after {

            background: #e9ecef;

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

                    <li class="active">Sales Contract Entry</li>
                    <li class="top-nav-btn">
                        <a class="btn btn-sm btn-primary" href="{{ url('merch/sales_contract/sales_contract_list') }}"><i class="las la-list"></i></a>
                    </li>
                </ul><!-- /.breadcrumb -->
            </div>

            <div class="panel">
                {{-- <div class="page-header">
                    <h1>Commercial <small><i class="ace-icon fa fa-angle-double-right"></i> Sales Contract Entry  </small></h1>
                </div> --}}

                <div class="panel-body">

                    @include('inc/message')


                    <form class="form-horizontal" role="form" method="post" action=" {{ url('merch/sales_contract/sales_contract_store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">


                            <div class="col-md-4">

                                <div class="form-group has-float-label select-search-group has-required">
                                    {{ Form::select('buyer', $buyer, null, ['placeholder'=>'Select Buyer','id'=> 'buyer','class'=> 'form-control col-xs-10', 'data-validation' => 'required']) }}
                                    <label for="product" >Buyer </label>

                                </div>

                                <div class="form-group has-float-label select-search-group has-required">
                                    {{ Form::select('unit', $unit, null, ['placeholder'=>'Select','id'=>'unit','class'=> 'form-control col-xs-10', 'data-validation' =>'required']) }}
                                    <label for="unit" >Unit </label>

                                </div>

                                <div class="form-group has-float-label select-search-group has-required">
                                    {{ Form::select('contract_no', array('In House'=>'In House', 'Buyer'=>'Buyer'), null, ['placeholder'=>'Select','id'=>'c_number_by','class'=> 'form-control col-xs-10', 'data-validation' => 'required']) }}
                                    <label for="contract_no" >Contract Source: </label>

                                </div>


                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm" id="add_order_button" data-toggle="modal" data-target="#right_modal_item" style="width:100%;border-radius: 5px;">Add Order</button>

                                </div>

                            </div>
                            <div class="col-md-4">

                                <div class="form-group has-float-label has-required label_background">

                                    <input type="text" name="exlc_contract_no"  value="" placeholder="Enter Value" id="exlc_contract_no" class="col-xs-12 form-control" autocomplete="off" data-validation ="required" readonly="readonly"/>
                                    <label for="exlc_contract_no" > Export LC / Contract No. </label>
                                </div>


                                <div class="form-group has-float-label has-required">

                                    <input type="text" name="contract_qty"  value="" placeholder="Enter Value" id="contract_qty" class="col-xs-12 form-control" autocomplete="off" data-validation ="required"/>
                                    <label for="contract_qty" > Contract Qty: </label>
                                </div>

                                <div class="form-group has-float-label has-required">

                                    <input type="text" name="contract_value"  value="" placeholder="Enter Value" id="contract_value" class="col-xs-12 form-control" autocomplete="off" data-validation ="required"/>
                                    <label for="contract_value" > Contract Value: </label>
                                   <!-- <strong id="contract_value_suggestion"></strong> -->
                                </div>

                                <div class="form-group has-float-label">

                                    <input type="date" name="elc_date"  value="" placeholder="yyyy-mm-dd" id="elc_date" class=" form-control datepicker"/>
                                    <label for="elc_date" > Contract Issue Date: </label>
                                </div>


                            </div>
                            <div class="col-md-4 ">

                                <div class="form-group ">
                                    <label class="form-check-label col-sm-12 control-label no-padding-right pl-0" for="cmpc" >Export Type: </label>

                                    <div class="col-sm-12">
                                        <input type="radio" class="form-check-input" id="lctype" name="lctype" value="ELC" checked> ELC<br/>
                                        <input type="radio" class="form-check-input" name="lctype" value="Contract"> Contract
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

                                                        <input type="date" name="exp_date"  value="" placeholder="yyyy-mm-dd" id="exp_date" class=" form-control datepicker"/>
                                                        <label for="exp_date" > Expire Date: </label>
                                                    </div>

                                                    <div class="form-group has-float-label">

                                                        <input type="text" name="remark"  value="" placeholder="Enter" id="remark" class="form-control"/>
                                                        <label for="remark" > Remarks: </label>
                                                    </div>

                                                </div>
                                                <div class="col-xs-6 col-sm-4">
                                                    <div class="row">
                                                        <div class="col-sm-9 ">
                                                            <div class="form-group has-float-label">
                                                                <input type="text" class="form-control" id="initial_value" name="initial_value" placeholder="Enter"   autocomplete="off">
                                                                <label for="initial_value"> Initial Value:  </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group has-float-label select-search-group">
                                                                {{ Form::select('currency', array('USD'=>'$ USD', 'EUR'=>'€ EUR','GBP'=>'£ GBP','Tk'=>'৳ Tk'), 'USD', ['placeholder'=>'Select','class'=> '', 'data-validation' => 'required']) }}
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="form-group has-float-label select-search-group">
                                                        {{ Form::select('lc_bank', $bank, null, ['placeholder'=>'Select','class'=> 'form-control']) }}
                                                        <label for="lc_bank" >Buyer Bank: </label>

                                                    </div>

                                                </div>
                                                <div class="col-xs-6 col-sm-4 col-sm-offset-1">

                                                    <div class="form-group has-float-label select-search-group">
                                                        {{ Form::select('btb_bank', $bank, null, ['placeholder'=>'Select','class'=> 'form-control col-xs-10']) }}
                                                        <label for="btb_bank" >BTB Bank: </label>

                                                    </div>




                                                </div>
                                                {{--  <div class="col-xs-4 col-sm-4">
                                                      <div class="form-group">
                                                          <label class="col-sm-4 control-label no-padding-right" for="initial value" >

                                                          </label>
                                                          <div class="col-sm-8">
                                                              <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#select_item">Add Order</button>
                                                          </div>
                                                      </div>
                                                  </div> --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="row">
                          <div class="col-md-12" style="margin-top: 20px;">
                            <table  class="table table-responsive table-bordered" >
                              <thead id="table_head">

                              </thead>

                              <tbody id="order_list_des">
                              </tbody>
                              <tfoot id="table_foot">

                              </tfoot>
                            </table>
                          </div>
                        </div>



                        <div class="form-group">
                            <button class="btn btn-sm btn-success" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                            </button>

                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-sm" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                            </button>
                        </div>

                    </form>

                </div> <!-- /.end panel body -->


            </div><!-- /.end panel -->
        </div><!-- /.main-content inner -->
    </div> <!-- /.main-content -->


    <!--Old Modal Select Order -->
    {{-- <div class="modal fade" id="right_modal_item" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Order List</h4>
          </div>
          <div class="modal-body" style="padding:0 15px">
            <div class="row" style="padding: 20px;" id="addListToModal">
                <span>No Order, Please select Buyer and Unit</span>
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
    <!--New Modal Start -->

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
                    <div class="modal-content-result" id="content-result"></div>

                    <div>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="button" id="modal_data" class="btn btn-primary btn-sm">Done</button>
                    </div>

                </div>

                {{--       <div class="modal-footer">--}}
                {{--        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>--}}
                {{--        <button type="button" id="modal_data" class="btn btn-primary btn-sm">Done</button>--}}
                {{--      </div>--}}

            </div>
        </div>
    </div>
    <!--New Modal End -->




    <script type="text/javascript">

        // #######



        // #########

        $(document).ready(function(){

            var order_num=0;

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
             * Export LC / Contract No. GENERATE
             * -----------------------
             */

            $("#buyer,#c_number_by, #unit").on("change", function(){

                var buyer = $("#buyer").val();
                var unit  = $("#unit").val();

                // Action Element list
                $.ajax({
                    url : "{{ url('merch/sales_contract/getcontractlist')}}",
                    type: 'get',
                    data: {b_id:buyer,unit_id:unit},
                    success: function(data)
                    {
                        // Set Contract Value


                        // Get Buyer First 3 character
                        var buyerinput = $("#buyer option:selected").html();
                        var buyer      = buyerinput.trim().replace(/ /g,'');
                        var buyerChar  = (buyer.substr(0, 3)).toUpperCase();

                        // Get Unit First 3 character
                        var unitText   = $("#unit option:selected").html();
                        var unit       = unitText.trim().replace(/ /g,'');
                        var unitChar   = (unit.substr(0, 3)).toUpperCase();

                        // Get Contract Number First 3 character
                        var contractText   = $("#c_number_by option:selected").html();
                        var contract       = contractText.trim().replace(/ /g,'');
                        var contractChar   = (contract.substr(0, 3)).toUpperCase();

                        var year=new Date().getFullYear().toString().substr(-2);
                        var exlc_contract = buyerChar.toUpperCase()+'/'+unitChar+'/'+data+'/'+year;

                        if(contractText=='In House'){

                            $("#exlc_contract_no").val(exlc_contract);
                            $("#exlc_contract_no").prop('readonly',true);
                        }
                        else{
                            $("#exlc_contract_no").val("");
                            $("#exlc_contract_no").prop('readonly',false);
                        }
                    },
                    error: function()
                    {
                        alert('failed...');

                    }
                });

            });

            /*
             * MODAL ITEM BASED ON BUYER & UNIT
             * -----------------------
             */
            $("#buyer,#unit").on("change",function(){

                $("#content-result").html("<span>No Order Found, Please select Buyer and Unit</span>");
                $("#order_list_des").html("");

                var b_id = $('#buyer').val();
                var unit  = $("#unit").val();
                if(b_id != '' && unit!=''){
                    // Action Element list
                    $.ajax({
                        url : "{{ url('merch/sales_contract/getorderlist') }}",
                        type: 'get',
                        data: {buyer_id:b_id,unit_id:unit},
                        success: function(data)
                        {
                            if(data!=""){
                                $("#content-result").html(data);
                            }

                        },
                        error: function()
                        {
                            $("#content-result").html("<span>No Order, Please select Buyer and Unit</span>");
                        }
                    });
                }
                else{
                    $("#content-result").html("<span>No Order, Please select Buyer and Unit </span>");
                    $("#order_list_des").html("");
                }
            });

            /*
             * SHOW DATA AFTER MODAL HIDE
             * -----------------------
             */
            var sgmodal = $("#right_modal_item");
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
                $("#table_head").html(table_head);
                $("#table_foot").html(table_foot);

                $('.checkbox-input').each(function(i, v){
                    // $("#select_item .modal-body").find('input:checkbox').prop('checked', true);
                    if ($(this).is(":checked"))
                    {
                        //count number of order
                        order_num++;
                        var id= $(this).val();
                        // console.log($(this).parent().parent().find('tr').html());
                        var item_name= $(this).next().text();
                        var qty= $(this).next().next('.qty').val();
                        var fob= $(this).next().next().next('.fob').val();
                        var order_del_date= $(this).next().next().next().next('.ord_del_date').val();
                        var po_del_date= $(this).next().next().next().next().next('.po_del_date').val();

                        var cvalue=(qty*fob).toFixed(2);

                        if(contractText == "In House"){
                            data+='<tr>\
                       <td width="20%"><input type="hidden" name="order_id[]" value="'+id+'" readonly/>\
                       <input type="text" name="order_no[]" id="items[]" placeholder="" value="'+item_name+'" readonly/></td>\
                       <td width="20%"><input type="text" name="qty[]" class="qty" placeholder="" value="'+qty+'"  readonly/></td>\
                       <td width="20%"><input type="text" name="fob[]"  placeholder="" value="'+fob+'"  readonly/>\
                       <input style="" type="hidden" name="order_delivery_date[]" class="order_delivery_date datepicker" placeholder="yyyy-mm-dd" value="'+order_del_date+'"  readonly/></td>\
                       <td width="20%">\
                       <input type="text" name="order_value[]" class="order_value" value="'+cvalue+'"  readonly/></td>\
                      </tr>';
                        }
                        else{
                            data+='<tr>\
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

                    var suggestion_string= "- 5% ="+decreased_value+"; + 5% ="+increased_value;
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
                //alert(contract_qty);
                if(contractText == "Buyer"){
                    calculateNewFOB(contract_value, contractText);
                    // calculateNewValue(contract_value);
                }

                // if(sumValue!=""){
                //   checkValue(contract_value,sumValue);
                // }

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
                //console.log(date_array.length);
                if(date_array.length==0){
                    date_array= [];
                    $("#order_list_des").find('.order_delivery_date').each(function(){
                        console.log($(this).val());
                        if($(this).val() != null){
                            date_array.push($(this).val());
                        }
                    });
                }
                console.log(date_array);
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
