@extends('merch.layout')
@section('title', 'Order PO')
@section('main-content')
@push('css')
<style>
   .panel-heading {
	    border-top: 1px solid rgb(195 225 228);
	}
	.select2 {
	    width: 100% !important;
	}
	.size-qty{
		position: relative;
	}
	.preview-po{
		width: 14%;
    	margin: 0 auto;
	}
	.preview-small {
	    width: 20% !important;
	}
</style>
@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="#">Merchandising</a>
              </li>
              <li>
                  <a href="#">Order</a>
              </li>
              <li class="active">PO</li>
              <li class="top-nav-btn">
                <a class="btn btn-sm btn-primary text-white" href="{{ url('merch/po') }}"><i class="las la-list"></i> PO List</a>
              </li>
          </ul><!-- /.breadcrumb -->

      </div>

    <div class="page-content">
        <div class="panel panel-success">
            <div class="panel-body pb-2">
            	<div class="row">
		            <div class="offset-sm-2 col-sm-8">
		              <form role="form" method="get" action="{{ url('merch/po-order')}}" class="attendanceReport" id="attendanceReportEmp">
		                <div class="panel" style="margin-bottom: 0;">
		                    
		                    <div class="panel-body" style="padding-bottom: 5px;">
		                        <div class="row">
		                            <div class="col-8">
		                                <div class="form-group has-float-label has-required select-search-group">
		                                	@if(isset($order) && $order != null)
		                                	{{ Form::select('order_id', [$order->order_id => $order->order_code.' - '.$order->style->stl_no], $order->order_id, ['placeholder'=>'Select Internal Order No/Style No', 'id'=>'order_id', 'class'=> 'mbm-order-no no-select ','style', 'required'=>'required']) }}
		                                	@else
		                                    {{ Form::select('order_id', [Request::get('order_id') => Request::get('order_id')], Request::get('order_id'), ['placeholder'=>'Select Internal Order No/Style No', 'id'=>'order_id', 'class'=> 'mbm-order-no no-select ','style', 'required'=>'required']) }}
		                                    @endif
		                                    <label  for="order_id"> Internal Order No/Style No </label>
		                                </div>
		                            </div>
		                            
		                            <div class="col-4">
		                                <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-search"></i> Search</button>
		                                
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </form>
		          </div>
		      	</div>
		      	@if(isset($order) && $order != null)
		      	@php
			    	$getColor = material_color_by_id();
			    	$getColor = collect($getColor)->pluck('clr_name', 'clr_id');
			    	$getCountry = country_by_id();
			    	$getCountry = collect($getCountry)->pluck('cnt_name', 'cnt_id');
			    	$getBuyer = buyer_by_id();
			    @endphp
                @include('merch.common.order_info')
                @endif
            </div> 
        </div>
        @if(isset($order) && $order != null)
        <div class="">
        	<div class="panel panel-success">
        		<form class="form-horizontal" role="form" id="poForm">
	        		<div class="panel-heading active" role="tab" id="headingOne">
				        <div class="row">
				        	<div class="col pr-0">
				        		<h5 class="panel-title"> Create PO </h5>
				        	</div>
				        	<div class="col pl-0">
				        		<table class="table m-0">
                                    <tbody>
                                        <tr>
                                            <th align="right" class="no-padding" style="border: 0;">
                                                Order Qty:
                                                <span style="color: maroon;" id="odr_qty_view">{{ $order->order_qty }}</span>
                                            </th>
                                            <th align="center" class="no-padding" style="border: 0;">
                                                Total PO Qty:
                                                <span style="color: maroon;" id="po_qty_total">{{ $totalPoQty }}</span>
                                            </th>

                                        </tr>
                                    </tbody>
                                </table>
				        	</div>
				        	<div class="col">
				        		<a class="btn btn-xs btn-success add-new text-white pull-right" data-type="size breakdown"><i class="las la-list"></i> Size Color Breakdown</a>
				        	</div>
				        	
				        </div>
				    </div>
        			<input type="hidden" name="order_id" value="{{ $order->order_id }}">
        			<input type="hidden" id="total-po-order" value="{{ $totalPoQty }}">
        			<input type="hidden" id="total-order-qty" value="{{ $order->order_qty }}">
		            {{ csrf_field() }} 
		            <div class="panel-body">
		            	<div class="row mb-3">
		            		<div class="col pr-0">
		                		<div class="form-group has-float-label has-required mb-0">
                                	<input type="text" name="po_no" id="po_no" class="form-control " autocomplete="off" onClick="this.select()" value="" placeholder="Enter PO Number" autofocus required>
                                    <label  for="po_no"> PO Number </label>
                                </div>
		                	</div>
		                	<div class="col pr-0">
		                		<div class="form-group has-float-label has-required select-search-group">
                                	<select name="clr_id" id="color" class="form-control" required>
                                        <option value=""> - Select - </option>
                                        @foreach($getColor as $k => $color)
                                        <option value="{{ $k }}">{{ $color }}</option>
                                        @endforeach
                                    </select>
                                    <label  for="color"> Color </label>
                                </div>
		                	</div>
		                	<div class="col pr-0">
		                		<div class="form-group has-float-label has-required select-search-group">
                                	<select name="po_delivery_country" id="country" class="form-control" required>
                                        <option value=""> - Select - </option>
                                        @foreach($getCountry as $k => $country)
                                        <option value="{{ $k }}">{{ $country }}</option>
                                        @endforeach
                                    </select>
                                    <label  for="country"> Country </label>
                                </div>
		                	</div>
		                	<div class="col pr-0">
		                		<div class="form-group has-float-label has-required select-search-group">
                                	<select name="port_id" id="port" class="form-control" required disabled>
                                        <option value=""> - Select - </option>
                                    </select>
                                    <label  for="port"> Port </label>
                                </div>
		                	</div>
		                	<div class="col pr-0">
		                		<div class="form-group has-float-label mb-0">
                                	<input type="text" data-type="remarks" name="remarks" id="remarks" class="form-control" autocomplete="off" value="" placeholder="Enter remarks">
                                    <label  for="remarks"> Remarks </label>
                                </div>
		                	</div>
		                	
		                	{{-- <div class="col pr-0">
		                		<div class="form-group has-float-label has-required mb-0">
                                	<input type="text" name="fob" id="fob" class="form-control " autocomplete="off" onClick="this.select()" value="0" placeholder="Enter FOB" required >
                                    <label  for="fob"> FOB </label>
                                </div>
		                	</div> --}}
		                	<div class="col pr-0">
		                		<div class="form-group has-float-label has-required mb-0">
                                	<input type="date" name="po_ex_fty" id="exfty" class="form-control action-input" value="{{ $order->order_delivery_date??date('Y-m-d')}}" required>
                                    <label  for="exfty"> Ex-fty </label>
                                </div>
		                	</div>
		                	<div class="col">
		                		<div class="form-group has-float-label has-required mb-0">
                                	<input type="text" step="any" min="0" name="po_qty" id="quantity" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" required>
                                    <label  for="quantity">Po Quantity </label>
                                </div>
		                	</div>
		            	</div>
		            	
		                <div class='row'>
                            <div class='col-sm-12 table-wrapper-scroll-y table-custom-scrollbar' id="po-list-section">
                                <table class="table table-bordered table-hover table-fixed table-responsive" id="itemList">
                                	<thead>
                                		@foreach($getSizeGroup as $size)
                                		<th width="10" style="padding: 10px 20px;">{{ $size->mr_product_pallete_name }}</th>
                                		@endforeach
                                	</thead>
                                	<tbody>
                                		@foreach($getSizeGroup as $size)
                                		<td style="padding: 3px;">
                                			<input type="text" step="any" min="0" name="size_group[{{ $size->id }}]" id="size-{{ $size->mr_product_pallete_name }}" class="form-control size-qty action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0">
                                		</td>
                                		@endforeach
                                	</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                        	<div class="col-sm-12">
                        		<div class="row">
                        			<div class="col-sm-6"></div>
                        			<div class="col-sm-2">
                        				<div class="form-group has-float-label mb-0">
		                                	<input type="text" step="any" min="0" name="totalqty" id="totalQty" class="form-control action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" value="0" readonly>
		                                    <label  for="totalQty"> Total Quantity </label>
		                                </div>
                        			</div>
                        			<div class="col-sm-4">
                        				
                        				<button type="button" class="btn btn-outline-success btn-lg text-center pull-right poBtn" onClick="savePO('manual')"><i class="fa fa-save"></i> Save</button>
                        				&nbsp;
                        				<button type="button" class="btn btn-outline-primary btn-lg text-center pull-right poBtn mr-3" onClick="previewPO()"><i class="fa fa-eye"></i> Preview</button>
                        			</div>
                        		</div>
                                
                            </div>
                        </div>
		                
		            </div>
		        </form>
		        
        	</div>
        	<div class="panel">
        		<div class="panel-body">
        			@if($order->mr_buyer_b_id == 41)
	            	<form class="" id="pdfProcess" method="post" enctype="multipart/form-data"> 
	            		{{ csrf_field() }} 
	            		<div class="process w-100">
	            			<div class="row">
	            				<div class="col-sm-6">
	            					<textarea class="form-control" id="process-text" name="pdf_data" rows="3" placeholder="Paste PDF text copy data..."></textarea>
	            				</div>
	            				<div class="col-sm-2">
	            					<input type="file" name="file" id="file">
	            					<input type="hidden" name="b_id" value="{{ $order->mr_buyer_b_id }}">
	            				</div>
	            				<div class="col-sm-4">
	            					<button type="submit" class=" btn btn-primary btn-sm "><i class="las la-recycle"></i> Process</button>
	            					{{-- <a class="process btn btn-primary btn-sm text-white" onClick="process()"><i class="las la-recycle"></i> Process</a> --}}
	            				</div>
	            			</div>
	            		</div>
	            	</form>
	            	<br>
            		@endif
        		</div>
        	</div>
        </div>
    	@include('merch.common.right-modal')
    </div><!-- /.page-content -->
    @endif
  </div>
</div>
@push('js')
<script src="{{ asset('assets/js/po.js')}}"></script>
<script type="text/javascript">
	@if(isset($order) && $order != null)
	var sizeQty = @json($sizeValue);

	@endif
	$(document).on('click', '.add-new', function() {
	    type = $(this).data('type');
	    $(".preview-small").addClass('right-modal-width');
	    $('#right_modal_item').modal('show');
	    $('#modal-title-right').html(' '+type);
	    $("#content-result").html(loaderContent);
	    var url = '';
	    if(type === 'size breakdown'){
	      url = '/merch/po-size-breakdown';
	    }
	    $.ajax({
	        type: "GET",
	        url: "{{ url('/')}}"+url,
	        data:{
	        	order_id: $("#order_id").val()
	        },
	        success: function(response)
	        {
	        	// console.log(response);
	          if(response !== 'error'){
	            $('#content-result').html(response);
	            
	          }else{
	            $('#content-result').html('<h4 class="text-center">Something wrong, please close and try again!</h4>');
	          }
	        },
	        error: function (reject) {
	          console.log(reject);
	        }
	    });
	    
	});

	function savePO(savetype) {
	  if(savetype =='manual' ) $(".app-loader").show();
	  var curStep = $("#poForm"),
	    curInputs = curStep.find("input[type='text'],input[type='hidden'],input[type='number'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
	    isValid = true;
	  $(".form-group").removeClass("has-error");
	  for (var i = 0; i < curInputs.length; i++) {
	     if (!curInputs[i].validity.valid) {
	        isValid = false;
	        $(curInputs[i]).closest(".form-group").addClass("has-error");
	     }
	  }
	  // PO qty > 0
	  if(parseInt($("#quantity").val()) < 1){
	  	isValid = false;
	  	$("#quantity").notify("Qty at least 1 or more ", 'error');
	  	$(".app-loader").hide();
	  	return false;
	  }
	  // total quantity check PO quantity
	  var poQuantity = $("#quantity").val(), totalQuantity = $("#totalQty").val();
	  if(parseInt(poQuantity) !== parseInt(totalQuantity)){
  		isValid = false;
	    $("#totalQty").notify("The total Quantity of PO Quantity does not match!", 'error');
	  	$(".app-loader").hide();
	  	return false;
	  }

	  // order quantity check total PO quantity
	  var orderPoQty = $("#total-po-order").val(), totalOrderQty = $("#total-order-qty").val();
	  if(parseInt(totalOrderQty) < (parseInt(totalQuantity) + parseInt(orderPoQty))){
  		isValid = false;
  		var leftQty = parseInt(totalOrderQty) - parseInt(orderPoQty);
	    $("#quantity").notify("There are "+leftQty+" order qty", 'error');
	  	$(".app-loader").hide();
	  	return false;
	  }

	  var form = $("#poForm");
	  if (isValid){
	     $.ajax({
	        type: "POST",
	        url: '{{ route("po.store") }}',
	        headers: {
	            'X-CSRF-TOKEN': '{{ csrf_token() }}',
	        },
	        data: form.serialize(), // serializes the form's elements.
	        success: function(response)
	        {
        		$.notify(response.message, response.type);
	         	// console.log(response);
	          	if(response.type === 'success'){
	            	// if(savetype =='manual' ){
	             //  		$.notify(response.message, response.type);
	            	// }
	            	$.each(sizeQty, function(i, v) {
				    	$("#size-"+i).val('0');
		            	$("#size-"+i).parent().removeClass('highlight');
		            	sizeQty[i] = '0';     
				    });
				    $("#totalQty").val('0');
				    $("#total-po-order").val(response.poqty);	
				   
				    $("#po_qty_total").html(response.poqty);	
	          	}
	          	$(".app-loader").hide();
	        },
	        error: function (reject) {
	          $(".app-loader").hide();
	          // console.log(reject);
	          if( reject.status === 400) {
	              var data = $.parseJSON(reject.responseText);
	               $.notify(data.message, data.type);
	          }else if(reject.status === 422){
	            var data = $.parseJSON(reject.responseText);
	            var errors = data.errors;
	            // console.log(errors);
	            for (var key in errors) {
	              var value = errors[key];
	              $.notify(value[0], 'error');
	            }
	             
	          }
	        }
	     });
	  }else{
	      $(".app-loader").hide();
	      $.notify("Some field are required", 'error');
	  }
	};
	$("body").on("keyup blur", ".size-qty", function(){
		var total = 0;
		$(".size-qty").each(function(i, v) {
	        if($(this).val() != '' )total += parseFloat( $(this).val() ); 
	    });
	    var sizesid = $(this).attr('id');
	    sizesid = sizesid.split("-");
	    sizeQty[sizesid[1]] = $(this).val();

	    var quantity = $("#quantity").val();
	    var totalQty = 0;
	    // if(quantity < total){
	    // 	$(this).val(0);
	    // 	$(".size-qty").each(function(i, v) {
		   //      if($(this).val() != '' )totalQty += parseFloat( $(this).val() ); 
		   //  });
	    // 	$("#totalQty").val(totalQty); 
	    // 	$(this).notify("Total Quantity greater then size quantity!", {
		   //    type: 'error',
		   //    allow_dismiss: true,
		   //    delay: 10,
		   //    z_index: 1031,
		   //    timer: 3
		   // });
	    // }else{
	    // 	$("#totalQty").val(total); 
	    // }
	    $("#totalQty").val(total); 
	    
	});
	@if(isset($order) && $order != null)
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	$('#pdfProcess').submit(function(e) {
		e.preventDefault();
       	let formData = new FormData(this);
       	$(".app-loader").show();
		$.ajax({
          	type:'POST',
         	url: "{{ url('/merch/po-process-text')}}",
           	data: formData,
           	contentType: false,
           	processData: false,
           	success: (response) => {
           		console.log(response)
             	$(".app-loader").hide();
	          	if(response.type !== 'error'){
	          		var totalQty = 0;
		            $.each(response.value, function( index, value ) {
		            	$("#size-"+index).val(value);
		            	$("#size-"+index).parent().addClass('highlight');
		            	sizeQty[index] = value;
					});
					$(".size-qty").each(function(i, v) {
				        if($(this).val() != '' )totalQty += parseFloat( $(this).val() ); 
				    });
					$("#process-text").val('').focus();
					$("#file").val('');
				    $("#totalQty").val(totalQty); 
				    $("#quantity").val(totalQty); 
		        }else{
		          	$.notify('Something wrong, please close and try again!', 'error');
	          	}
           },
           error: function(response){
              console.log(response);
              
           }
       });
	});
	/*function process(){
		var data = $("#process-text").val();
		var form = $("#pdfProcess");
		if(data !== null && data !== ''){
			$(".app-loader").show();
			$.ajax({
		        type: "POST",
		        headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
              	},
		        url: "{{ url('/merch/po-process-text')}}",
		        data: form.serialize(),
		        success: function(response)
		        {
		        	console.log(response)
		        	$(".app-loader").hide();
		    //       	if(response.type !== 'error'){
		    //       		var totalQty = 0;
			   //          $.each(response.value, function( index, value ) {
			   //          	$("#size-"+index).val(value);
			   //          	$("#size-"+index).parent().addClass('highlight');
			   //          	sizeQty[index] = value;
						// });
						// $(".size-qty").each(function(i, v) {
					 //        if($(this).val() != '' )totalQty += parseFloat( $(this).val() ); 
					 //    });
						// $("#process-text").val('').focus();
					 //    $("#totalQty").val(totalQty); 
			   //      }else{
			   //        	$.notify('Something wrong, please close and try again!', 'error');
		    //       	}
		        },
		        error: function (reject) {
		          console.log(reject);
		        }
		    });
		}else{
			$("#process-text").notify('Empty text process', 'error');
		}
	};*/
	@endif
	function previewPO(){
		$(".right-modal-width").addClass('preview-small');
		$(".preview-small").removeClass('right-modal-width');
		var totalQty = $("#totalQty").val();
		$('#right_modal_item').modal('show');
	    $('#modal-title-right').html('Size Group details');
	    $("#content-result").html(loaderContent);
	    // $.ajax({
	    //     type: "GET",
	    //     url: "{{ url('/')}}"+url,
	    //     success: function(response)
	    //     {
	    //       if(response !== 'error'){
	    //         $('#content-result').html(response);
	    //         $('.filter').select2({
	    //             dropdownParent: $('#right_modal_item')
	    //         });
	    //       }else{
	    //         $('#content-result').html('<h4 class="text-center">Something wrong, please close and try again!</h4>');
	    //       }
	    //     },
	    //     error: function (reject) {
	    //       console.log(reject);
	    //     }
	    // });
	    var data = '<table class="table table-bordered table-hover table-fixed preview-po"><thead><tr><th>Size</th><th>Quantity</th></tr></thead><tbody>';
	    
	    $.each(sizeQty, function( index, value ) {
	    	if(value !== '0'){
		    	data += '<tr><td>'+index+'</td><td>'+value+'</td></tr>';
	    	}
		});
	    data += '<tr><td></td><td>'+totalQty+'</td></tr></tbody></table>';
	    setTimeout(function(){
	    	$("#content-result").html(data);
	    }, 500); 
	}
	// country 
	$(document).on('change', '#country', function(){
	    $('#port').empty().select2({data: [{id: '', text: ' Select Port'}]}).attr('disabled', true);
	    if($(this).val() !== ''){
	    	var countryid = $(this).val();
	        $.ajax({
	            type: "GET",
	            url: "{{ url('/merch/search/ajax-country-port-search') }}",
	            data: {
	                cnt_id: $(this).val()
	            },
	            success: function(response)
	            {
	            	// console.log(response);
	                if(response !== ''){
	                    $('#port').select2({
	                        data: response
	                    }).removeAttr('disabled');
	                }
	            },
	            error: function (reject) {
	              console.log(reject);
	            }
	        });
	    }
	});
</script>
@endpush
@endsection