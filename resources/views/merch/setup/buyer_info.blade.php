@extends('merch.layout')
@section('title', 'Buyer')
@section('main-content')
    @push('css')
    <style type="text/css">
        @media only screen and (max-width: 767px) {
            #tableDiv{padding-right: 10px !important; padding-left: 0px;}
            .dataTables_wrapper .col-sm-12{width: 100%;}
        }
        fieldset.group  {
            margin: 0;
            padding: 0;
            margin-bottom: 1.25em;
            padding: .125em;
            border-bottom: 1px solid lightgray;
            border-right: 1px solid lightgray;
            border-top: 1px solid lightgray;
          }

          fieldset.group legend {
            margin: 0;
            padding: 0;
            font-weight: bold;
            margin-left: 20px;
            color: black;
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
          }


          ul.checkbox  {
            margin: 0;
            padding: 0;
            margin-left: 20px;
            list-style: none;
          }

          ul.checkbox li input {
            margin-right: .25em;
          }

          ul.checkbox li {
            border: 1px transparent solid;
          }

          ul.checkbox li:hover,
          ul.checkbox li.focus  {
            background-color: lightyellow;
            border: 1px gray solid;
          }
          .checkbox label, .radio label {
            padding-left: 0px;
            font-size: 10px;
        }
        #dataTables{
            width: 100% !important; 
        }

        @media only screen and (max-width: 1125px) {
            #buttonDiv{padding-left: 35px;}
            .add_remove_div{padding-left: 0px; padding-right: 0px;}
        }

        @media only screen and (max-width: 767px) {
            #buttonDiv{padding-left: 0px;}
            .dataTables_wrapper .col-sm-12{width: 100%;}
            .modal-dialog{padding-top: 50px;}
            
        }
        @media only screen and (max-width: 480px) {
            
            .modal-dialog{padding-top: 90px;}
            
        }
    </style>
    @endpush
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Merchandising </a>
            </li> 
            <li>
                <a href="#"> Setup </a>
            </li>
            <li class="active"> Buyer </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       
       <div class="col mail-box-detail">
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  Create New Buyer </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                          <div class="col-12">
                            <div class="panel-body pb-0">
                               <form class="form-horizontal" id="buyerForm" role="form" enctype="multipart/form-data">
                                    {{ csrf_field() }} 
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-6">
                                                  <div class="form-group has-float-label has-required">
                                                      <input type="text" class=" form-control" id="march_buyer_name" name="march_buyer_name" placeholder="Enter Supplier Name" value="" autocomplete="off" required />
                                                    <label for="march_buyer_name">Buyer Name</label>
                                                  </div>
                                                  <div class="form-group has-float-label has-required">
                                                      <input type="text" class=" form-control" id="march_buyer_short_name" name="march_buyer_short_name" placeholder="Enter Supplier Name" value="" autocomplete="off" required />
                                                    <label for="march_buyer_short_name">Buyer Short Name</label>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col-10 pr-0">
                                                      <div class="form-group has-required has-float-label">
                                                        <input type="text" id="brand0" name="brand_name[]" placeholder="Enter Brand Name" class="form-control" autocomplete="off" required />
                                                        <label for="brand0" > Brand Name </label>
                                                      </div>
                                                    </div>
                                                    <div class="col-2">
                                                      <button type="button" class="btn btn-xs btn-outline-success AddBtnBrand_bu">+</button>
                                                    </div>
                                                  </div>
                                                  <div id="addBrand"></div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                    {{ Form::select('country', $country, null, ['placeholder'=>'Select Country Name','id'=>'country_id','class'=> 'form-control filter', 'required']) }}
                                                    <label for="country_id">Country</label>
                                                  </div>
                                                  <div class="form-group has-float-label has-required">
                                                    
                                                    <input type="text" id="march_buyer_address" name="march_buyer_address" placeholder="Enter Buyer Address" class="form-control" autocomplete="off" required />
                                                    <label for="march_buyer_address">Buyer Address</label>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col-10 pr-0">
                                                      <div class="form-group has-required has-float-label">
                                                        <input type="text" id="contact0" name="march_buyer_contact[]" placeholder="Enter Contact Person (Name, Cell No, Email)" class="form-control" autocomplete="off" required />
                                                        <label for="contact0" > Contact Person </label>
                                                      </div>
                                                    </div>
                                                    <div class="col-2">
                                                      <button type="button" class="btn btn-xs btn-outline-success AddContactBtn_bu">+</button>
                                                    </div>
                                                  </div>
                                                  <div id="contactPersonData"></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                          <div class="row">
                                            <div class="col-sm-3 col-xs-3">
                                                <button type="button" class="btn btn-success btn-sm" rel="tooltip" data-tooltip="Add Sample" data-tooltip-location="top"  data-toggle="modal" data-target="#sampleTypeModal" > <i class="fa fa-plus"></i> Sample <i class="glyphicon glyphicon-plus"></i> &nbsp; &nbsp; &nbsp; </button>
                                            </div>
                                            <div class="col-sm-9 col-xs-9" id="added_sample_type">
                                            </div>
                                          </div>
                                          <br>
                                          <div class="row">
                                            <div class="col-sm-3 col-xs-3">
                                                <button type="button" class="btn btn-success btn-sm" rel="tooltip" data-tooltip="Add Size Group" data-tooltip-location="top" data-toggle="modal" data-target="#addProductSizeModal" style=""> <i class="fa fa-plus"></i> Size Group <i class="glyphicon glyphicon-plus"></i></button>
                                            </div>
                                            <div class="col-sm-9 col-xs-9" id="added_product_size">
                                            </div>
                                          </div>
                                          <br>
                                          <div class="row">
                                            <div class="col-sm-3 col-xs-3">
                                                <button type="button" class="btn btn-success btn-sm" rel="tooltip" data-tooltip="Add Season" data-tooltip-location="top" data-toggle="modal" data-target="#addSeasonModal"> <i class="fa fa-plus"></i> Season <i class="glyphicon glyphicon-plus"></i> &nbsp; &nbsp;  </button>
                                            </div>
                                            <div class="col-sm-9 col-xs-9" id="added_season">
                                            </div>
                                            
                                          </div>
                                        </div>
                                          
                                    </div>
                                    <!-- Add Sample Type  Modal-->
                                    <div class="modal fade" id="sampleTypeModal" tabindex="-1" role="dialog" aria-labelledby="sampleTypeTitle" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                   <h5 class="modal-title" id="sampleTypeTitle">Add Sample Type</h5>
                                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                   <span aria-hidden="true">×</span>
                                                   </button>
                                                </div>
                                                <div class="modal-body">
                                                    
                                                    <div class="row">
                                                      <div class="col-10">
                                                        <div class="form-group has-required has-float-label">
                                                          <input type="text" id="sample0" name="sample_name[]" placeholder="Enter Sample Type" class="form-control" autocomplete="off" />
                                                          <label for="sample0" > Sample Type </label>
                                                          <div class="msg" style="color: red"></div>
                                                        </div>
                                                      </div>
                                                      <div class="col-2">
                                                        <button type="button" class="btn btn-xs btn-outline-success AddSampleBtn_bu">+</button>
                                                      </div>
                                                    </div>
                                                    <div id="sampleTypeData"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="sampleTypeModalDone">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add product size modal  -->
                                    <div class="modal fade" id="addProductSizeModal" tabindex="-1" role="dialog" aria-labelledby="addProductSizeTitle" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                   <h5 class="modal-title" id="addProductSizeTitle">Add Size Group</h5>
                                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                   <span aria-hidden="true">×</span>
                                                   </button>
                                                </div>
                                                <div class="modal-body">
                                                    
                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                
                                                        {{ Form::select('product_type', $productType, null, ['placeholder'=>'Select Product Type','id'=>'product_type','class'=> 'form-control', 'required']) }}
                                                        <label for="product_type"> Product Type  </label>
                                                    </div>
                                                    
                                                    <div class="form-group has-required has-float-label select-search-group">
                                                        <select name="gender" class="form-control" id="gender" required>
                                                          <option>Select</option>
                                                          <option value="Men's">Men's</option>
                                                          <option value="Ladies">Ladies</option>
                                                          <option value="Boys/Girls">Boys/Girls</option>
                                                          <option value="Girls">Girls</option>
                                                          <option value="Women's">Women's</option>
                                                          <option value="Men's & Ladies">Men's & Ladies</option>
                                                          <option value="Baby Boys/Girls">Baby Boys/Girls</option>
                                                        </select>
                                                        <label for="gender"> Gender  </label>
                                                      </div>
                                                    
                                                    <div class="form-group has-required has-float-label">
                                                        <input type="text" id="sg_name" name="sg_name" placeholder="Enter Size Group Name " class="form-control" autocomplete="off" />
                                                        <label for="sg_name" > Size Group Name </label>
                                                    </div>
                                                    
                                                    <div class="form-group has-required has-float-label">
                                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sizeModal" style="width: 100%">Select Size Group</button>
                                                        <div id="show_selected_sizes" style="padding-top: 10px; margin: 0px; padding-left: 0px; padding-right: 0px;">
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" >
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="addProductSizeModalDone">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Select Size Items Modal -->
                                    <div class="modal fade" id="sizeModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabelTitle" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                          
                                          <div class="modal-header">
                                               <h5 class="modal-title" id="sizeLabelTitle">Add Size Group</h5>
                                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                               <span aria-hidden="true">×</span>
                                               </button>
                                            </div>
                                          <div class="modal-body">
                                            @foreach($sizeModalData AS $modalData)
                                            {!! $modalData !!}
                                            @endforeach
                                          </div>
                                          <div class="modal-footer" >
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="sizeModalDone">Save changes</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <!-- Add Season Modal-->
                                    <div class="modal fade" id="addSeasonModal" tabindex="-1" role="dialog" aria-labelledby="addSeasonModalTitle" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                   <h5 class="modal-title" id="addSeasonModalTitle">Add Season</h5>
                                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                   <span aria-hidden="true">×</span>
                                                   </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group has-required has-float-label">
                                                        <input type="text" id="se_name" name="se_name" placeholder="Enter Season Name" class="form-control" autocomplete="off" />
                                                        <label for="se_name" > Season Name </label>
                                                        
                                                    </div>
                                                    <div class="form-group has-required has-float-label">
                                                        <input type="month" class="form-control" id="se_mm_start" name="se_mm_start" placeholder="Start Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                                        <label for="se_mm_start" > Start Month-Year </label>
                                                        
                                                    </div>
                                                    <div class="form-group has-required has-float-label">
                                                        <input type="month" class="form-control" id="se_mm_end" name="se_mm_end" placeholder="End Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                                        <label for="se_mm_end" > End Month-Year </label>
                                                        
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                   <button type="button" class="btn btn-primary" id="addSeasonModalDone">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <button class="btn btn-outline-success btn-md" type="button" id="buyerAddBtn"><i class="fa fa-save"></i> Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form> 
                            </div>
                          </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card iq-accordion-block accordion-active">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  List Of Buyer </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                        <div class="col-12">
                            <div class="panel-body worker-list">
                              <table id="dataTables" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
                                <thead>
                                    <tr>
                                        <th width="5%">SL.</th>
                                        <th width="15%">Buyer Name</th>
                                        <th width="10%">Short Name</th>
                                        <th width="30%">Address</th>
                                        <th width="20%">Contact Persons</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=0; @endphp
                                    @foreach($buyers as $buyer)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{!! $buyer->b_name !!}</td>
                                            <td>{!! $buyer->b_shortname !!}</td>
                                            <td>{!! $buyer->b_address !!} <br> {!! $buyer->b_country !!}</td>
                                            <td>
                                                @if(isset($getBuyerContact[$buyer->b_id]) && count($getBuyerContact[$buyer->b_id]) > 0)
                                                  {!! implode(', ',$getBuyerContact[$buyer->b_id]) !!}
                                                @endif
                                            </td>
                                            <td>
                                              <div class="btn-group">
                                                {{-- <a type="button" href="{{ url('merch/setup/buyer_info_edit/'.$buyer->b_id) }}" class='btn btn-sm btn-primary' title="Update"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a type="button" href="{{ url('merch/setup/buyer_profile/'.$buyer->b_id) }}" class='btn btn-sm btn-info' title="View"><i class="ace-icon fa fa-eye bigger-120"></i></a> --}}
                                                 <a href="{{ url('merch/setup/buyerdelete/'.$buyer->b_id) }}" type="button" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure you want to delete this Buyer?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                              </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                          </div>
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                   </div>
                </div>
                
            </div>
       </div>
    </div>
    <div class="modal right fade" id="right_modal_lg" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg right-modal-width" role="document" > 
            <div class="modal-content">
                <div class="modal-header">
                    <a class="view " data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
                        <i class="las la-chevron-left"></i>
                    </a>
                    <h5 class="modal-title right-modal-title text-center" id="modal-title-right-extra"> &nbsp; </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="modal-content-result-extra" id="content-result-extra">
              
                    </div>
                </div>
          
            </div>
        </div>
    </div>
    

    @push('js')

    <script>
        jQuery('#buyerAddBtn').click(function(event) {
            $("#app-loader").show();
            var curStep = jQuery(this).closest("#buyerForm"),
              curInputs = curStep.find("input[type='text'],input[type='email'],input[type='password'],input[type='month'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
              isValid = true;
            jQuery(".form-group").removeClass("has-error");
            for (var i = 0; i < curInputs.length; i++) {
               if (!curInputs[i].validity.valid) {
                  isValid = false;
                  jQuery(curInputs[i]).closest(".form-group").addClass("has-error");
               }
            }

            if (isValid){
               $.ajax({
                  type: "POST",
                  url: '{{ url("/merch/setup/ajax_save_buyer") }}',
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  },
                  data: curInputs.serialize(), // serializes the form's elements.
                  success: function(response)
                  {
                    $("#app-loader").hide();
                    console.log(response)
                    $.notify(response.message, response.type);
                     if(response.type === 'success'){
                        setTimeout(function() {
                           window.location.href=response.url;
                        }, 500);
                     }
                  },
                  error: function (reject) {
                    $("#app-loader").hide();
                    if( reject.status === 400) {
                        var data = $.parseJSON(reject.responseText);
                         $.notify(data.message, {
                            type: data.type,
                            allow_dismiss: true,
                            delay: 100,
                            timer: 300
                        });
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
                $("#app-loader").hide();
                $.notify("Some field are required", {
                  type: 'error',
                  allow_dismiss: true,
                  delay: 100,
                  z_index: 1031,
                  timer: 300
                });
            }
            
        });

        $(document).on('click','.generate-drawer', function(){
            var url = $(this).data('url'),
                headline = $(this).data('headline')+' Item Edit';
            $("#modal-title-right-extra").html(headline);
            $('#right_modal_lg').modal('show');
            $("#content-result-extra").html(loaderContent);
            
            $.ajax({
                url: url,
                type: "GET",
                success: function(response){
                    // console.log(response);
                    if(response !== 'error'){
                        setTimeout(function(){
                            $("#content-result-extra").html(response);
                            $('#uom-edit').select2({
                                dropdownParent: $('#right_modal_lg')
                            });
                        }, 1000);
                    }else{
                        console.log(response);
                    }
                }
                
            });

        });

        $('body').on('click', '.RemoveBtn_bu', function(){
            $(this).parent().parent().remove();
        });

        $(document).ready(function(){
            //Data TAble Buyer///
            $('#dataTables').DataTable();

            //Add More Buyer Contact Person
            var co=1;
            $(".AddContactBtn_bu").on('click',function(){
              cohtml = '<div class="row"><div class="col-10 pr-0"><div class="form-group has-float-label">';
              cohtml += '<input type="text" id="contact'+co+'" name="march_buyer_contact[]" placeholder="Enter Contact Person (Name, Cell No, Email)" class="form-control march_buyer_contact"/>';
              cohtml += '<label for="contact'+co+'"> Contact Person </label></div></div><div class="col-2">';
              cohtml += '<button type="button" class="btn btn-xs btn-outline-danger RemoveBtn_bu">-</button></div></div>';
              
              $('#contactPersonData').append(cohtml);
              $('#contact'+co).focus();
              co++;
            });

            //Add More Sample Type
            var sa=1;
            $(".AddSampleBtn_bu").on('click',function(){
              sahtml = '<div class="row"><div class="col-10 pr-0"><div class="form-group has-required has-float-label">';
              sahtml += '<input type="text" id="sample'+sa+'" name="sample_name[]" placeholder="Enter Sample Type" class="form-control sample_name"/>';
              sahtml += '<label for="sample'+sa+'"> Sample Type </label>';
              sahtml += '</div></div><div class="col-2">';
              sahtml += '<button type="button" class="btn btn-xs btn-outline-danger RemoveBtn_bu">-</button></div></div>';
              
              $('#sampleTypeData').append(sahtml);
              $('#sample'+sa).focus();
              sa++;
            });

            //Add more Brand Name
            var ba=1;
            $(".AddBtnBrand_bu").on('click',function(){
              bahtml = '<div class="row"><div class="col-10 pr-0"><div class="form-group has-required has-float-label">';
              bahtml += '<input type="text" id="brand'+ba+'" name="brand_name[]" placeholder="Enter Brand name" class="form-control brand_name"/>';
              bahtml += '<label for="brand'+ba+'"> Brand name </label>';
              bahtml += '</div></div><div class="col-2">';
              bahtml += '<button type="button" class="btn btn-xs btn-outline-danger RemoveBtn_bu">-</button></div></div>';
              
              $('#addBrand').append(bahtml);
              $('#brand'+ba).focus();
              ba++;
            });

            //Select Product Sizes
            var modal = $("#sizeModal");
            $("body").on("click", "#sizeModalDone", function(e) {
                var data="";
                //-------- modal actions ------------------
                modal.find('.modal-body input[type=checkbox]').each(function(i,v) {
                    if ($(this).prop("checked") == true)
                    {
                        data+= '<button type="button" class="btn btn-sm btn-info" style="margin:2px; padding:2px;">'+$(this).next().text()+'</button>';
                        data+= '<input type="hidden" name="seleted_sizes[]" value="'+$(this).next().text()+'"></input>';
                    }
                });
                modal.modal('hide');
                $("#show_selected_sizes").html(data);
            });


            // Sample Type 2 modal
            var modal2 = $("#sampleTypeModal");
            $("body").on("click", "#sampleTypeModalDone", function(e) {
                if(modal2.find('input[name="sample_name[]"]').val()){
                    var data="";
                    var tr_end = 0;
                    //-------- modal actions ------------------
                    data += '<table class="table" style="margin-top: 0px;">';
                    data += '<tbody>';
                    modal2.find('input[name="sample_name[]"]').each(function(i,v) {
                    if ($(this).val()) {
                        if((i/4) % 1 === 0) {
                            data += '<tr>';
                            tr_end = i+3;
                        }
                        data += '<td style="border: 1px solid lightgray;" class="text-center"><strong>'+$(this).val()+'</strong></td>';
                        data+= '<input type="hidden" name="sample_name[]" value="'+$(this).val()+'"></input>';
                        if(tr_end == 4) {
                            data += '</tr>';
                        }
                    }
                    });
                    data += '</tbody>';
                    data += '</table>';
                    $("#added_sample_type").html(data);
                }
                modal2.modal('hide');
            });


            //Product Size Modal
            var modal3 = $("#addProductSizeModal");
            $("body").on("click", "#addProductSizeModalDone", function(e) {
                if(modal3.find('input[name="sg_name"]').val() ){
                    var data="";
                    var tr_end = 0;
                    //-------- modal actions ------------------
                    data += '<table class="table table-bordered">';
                    // gender
                    data += '<tr>';
                    data += '<td style="font-weight:bold">Gender</td>';
                    data += '<td>'+modal3.find('select[name="gender"]').attr('selected', true).val()+'</td>';
                    data += '</tr>';
                    // group name
                    data += '<tr>';
                    data += '<td style="font-weight:bold">Group Name</td>';
                    modal3.find('input[name="sg_name"]').each(function(i,v) {
                    if ($(this).val() != null) {
                            data += '<td>'+$(this).val();
                            data += '</td>';
                        }
                    });
                    data += '</tr>';
                    // product type
                    data += '<tr>';
                    data += '<td style="font-weight:bold">Product Type</td>';
                    data += '<td>'+modal3.find('select[name="product_type"] option:selected').text()+'</td>';
                    data += '</tr>';
                    // size
                    data += '<tr>';
                    data += '<td style="font-weight:bold">Sizes</td>';
                    data += '<td>';
                    modal.find('.modal-body input[type=checkbox]').each(function(i,v) {
                        if ($(this).prop("checked") == true) {
                            if(i == 0) {
                                data += $(this).next().text()+', ';
                            } else {
                                data += $(this).next().text()+', ';
                            }
                        }
                    });
                    data += '</td>';
                    data += '</tr>';

                    data += '</table>';
                    $("#added_product_size").html(data);
                }
                modal3.modal('hide');
            });

            //end of product size modal

            //Add Season Modal
            var modal5 = $("#addSeasonModal");
            $("body").on("click", "#addSeasonModalDone", function(e) {
                if(modal5.find('input[name="se_name"]').val() && modal5.find('input[name="se_mm_start"]').val() && modal5.find('input[name="se_mm_end"]').val()){

                    var data="";
                    var tr_end = 0;
                    //-------- modal actions ------------------
                    data += '<table class="table table-bordered" style="margin-top: 0px;">';
                    data += '<thead>';
                    data += '<tr>';
                    data += '<td colspan="3" class="text-center">Season Name</td>';
                    data += '<td colspan="3" class="text-center">Start</td>';
                    data += '<td colspan="3" class="text-center">End</td>';
                    data += '</tr>';
                    data += '</thead>';
                    data += '<tbody>';
                    data += '<tr>';
                    data += '<td style="border-bottom: 1px solid lightgray;" class="text-center" colspan="3"><strong>'+modal5.find('input[name="se_name"]').val()+'</strong></td>';
                    data += '<td style="border-bottom: 1px solid lightgray;" class="text-center" colspan="3"><strong>'+modal5.find('input[name="se_mm_start"]').val()+'</strong></td>';
                    data += '<td style="border-bottom: 1px solid lightgray;" class="text-center" colspan="3"><strong>'+modal5.find('input[name="se_mm_end"]').val()+'</strong></td>';
                    data += '</tr>';
                    data += '</tbody>';
                    data += '</table>';
                    $("#added_season").html(data);
                }
                modal5.modal('hide');
            });
        });
    </script>
    @endpush
@endsection

