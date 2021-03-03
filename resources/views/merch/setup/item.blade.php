@extends('merch.layout')
@section('title', 'Item')
@section('main-content')
    @push('css')
    <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <style type="text/css">
        @media only screen and (max-width: 767px) {
            #tableDiv{padding-right: 10px !important; padding-left: 0px;}
            .dataTables_wrapper .col-sm-12{width: 100%;}
        }

        div .newItem {
            border: 1px solid lightgrey; border-radius: 5px; padding: 15px; background-color: azure;margin-bottom: 10px; padding-bottom: 0;
        }
        
        .form-group input[type=text]{
            height: 34px !important;
        }

        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;   /* prevent horizontal scrollbar */
            overflow-x: hidden; /* add padding to account for vertical scrollbar */
            z-index:1000 !important;
        }
        .form-control{
            background-color: #fff !important;
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
            <li>
                <a href="#"> Materials </a>
            </li>
            <li class="active"> Item </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       <div class="col-lg-2 pr-0">
           <!-- include library menu here  -->
           @include('merch.setup.materials')
       </div>
       <div class="col-lg-10 mail-box-detail">
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block ">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  Create New Item </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                          <div class="col-12">
                            <div class="panel-body pb-0">
                               <form class="form-horizontal" id="itemForm" role="form" enctype="multipart/form-data">
                                    {{ csrf_field() }} 
                                    <div class="row">
                                        <div class="offset-3 col-6">
                                            <div class="form-group has-float-label has-required select-search-group">
                                              {{ Form::select('mcat_name', $cat_list, null, ['placeholder'=>'Select Category Name','id'=>'mcat_id','class'=> 'form-control', 'required']) }}
                                              <label for="mcat_id">Main Category</label>
                                            </div>

                                            <div class="form-group has-float-label">
                                                <input type="text" class="autocomplete_sub_cat form-control" id="subcategory_name" name="subcategory_name" data-type="subcategory_name" placeholder="Enter Sub Category" value="" autocomplete="off" />
                                              <label for="subcategory_name">Sub Category</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="newItem">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group has-float-label">
                                                            <input type="text" class=" form-control" id="item_code-0" name="item_code" placeholder="Enter Item Code" value="" autocomplete="off" />
                                                          <label for="item_code-0">Item Code</label>
                                                        </div>
                                                        <div class="form-group has-float-label has-required">
                                                            <input type="text" class=" form-control" id="item_name-0" name="item_name" placeholder="Enter Item Name" value="" autocomplete="off" required />
                                                          <label for="item_name-0">Item Name</label>
                                                        </div>
                                                        <div class="form-group has-float-label has-required select-search-group">
                                                          {{ Form::select('uom[]', $uom, null, ['id'=>'uom-0','class'=> 'form-control', 'required', 'multiple']) }}
                                                          <label for="uom-0">UOM</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group has-float-label select-search-group">
                                                          {{ Form::select('buyer', $buyerList, null, ['placeholder'=>'Select Buyer','id'=>'buyer-0','class'=> 'form-control']) }}
                                                          <label for="buyer-0">Buyer</label>
                                                        </div>
                                                        <div class="form-group has-float-label">
                                                            <input type="text" class=" form-control" id="description-0" name="description" placeholder="Enter Description" value="" autocomplete="off" />
                                                          <label for="description-0">Description</label>
                                                        </div>
                                                        
                                                        <div class="">
                                                            <label>Depends On:</label>
                                                            <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                                              <input type="radio" id="color-0" name="depends" class="custom-control-input bg-primary" value="1">
                                                              <label class="custom-control-label" for="color-0"> Color </label>
                                                            </div>
                                                            <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                                              <input type="radio" id="size-0" name="depends" class="custom-control-input bg-primary" value="2">
                                                              <label class="custom-control-label" for="size-0"> Size</label>
                                                            </div>
                                                            <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                                              <input type="radio" id="sizecolor-0" name="depends" class="custom-control-input bg-primary" value="3">
                                                              <label class="custom-control-label" for="sizecolor-0"> Size & Color </label>
                                                            </div>
                                                            <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                                              <input type="radio" id="none-0" name="depends" class="custom-control-input bg-primary" value="0" checked>
                                                              <label class="custom-control-label" for="none-0"> None </label>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div> 
                                            <div class="form-group">
                                              <button class="btn btn-outline-success btn-md" type="button" id="itemBtn"><i class="fa fa-save"></i> Save</button>
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
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  List Of Items </span> </a></div>
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
                                        <th>Sl.</th>
                                        <th width="15%">Main Category</th>
                                        <th width="8%">Sub Category</th>
                                        <th>Item Code</th>
                                        <th width="8%">Item Name</th>
                                        <th>UOM</th>   
                                        <th>Depends </th>                      
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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

    <script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
    <script>
         //material sub category suggestion...
        $('#mcat_id').change(function(){
            $('.autocomplete_sub_cat').val(''); 
            $('.autocomplete_sub_cat').focus(); 
        });
        $(document).on('focus','.autocomplete_sub_cat',function(){
            var type   = $(this).data('type');
            var getId  = $(this).attr('id');
            var mcat_id = $('#mcat_id').val();
            eId = getId.split("_");
            // if(type == 'file')autoTypeNo=0;
            $(this).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url : "{{ url('merch/setup/get_material_sub_cat_name_suggestion') }}",
                        method: 'GET',
                        data: {
                          name_startsWith: request.term,
                          type: type,
                          mcat_id: mcat_id
                        },
                        success: function( data ) {
                            // console.log(data);
                            response( $.map( data, function( item ) {
                                var code = item.split("|");
                                //console.log(code[1]);
                                return {
                                    label: code[0],
                                    value: code[0],
                                    data : item
                                }
                            }));
                        }
                    });
                },
                autoFocus: true,
                minLength: 0,
                select: function( event, ui ) {
                    var names = ui.item.data.split("|");
                    $(this).val(names[1]);
                    // id_arr = $(this).attr('id');
                    // id = id_arr.split("_");
                }
            });
        });

        jQuery('#itemBtn').click(function(event) {
            $("#app-loader").show();
            var curStep = jQuery(this).closest("#itemForm"),
              curInputs = curStep.find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
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
                  url: '{{ url("/merch/setup/item_store_ajax") }}',
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  },
                  data: curInputs.serialize(), // serializes the form's elements.
                  success: function(response)
                  {
                    $("#app-loader").hide();

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
        var t=0;
        var s= '@foreach($uom as $key=>$unit) <option value="{{$key}}">{{$unit}}</option> @endforeach';
        $(document).ready(function(){
            
            var data = $('.AddBtn').parent().parent().parent().parent().html();
            $('body').on('click', '.AddBtn', function(){
                t++;
                var data= '<div class="newItem">\
                                    <div class="form-group" style="margin-bottom:0px;">\
                                        <label class="col-sm-3 control-label no-padding-right" for="item_name[]"> Item <span style="color: red">&#42;</span> </label>\
                                        <div class="col-sm-9">\
                                            <input type="text" id="item_name" name="item_name['+t+']" placeholder="Enter Text" class="col-xs-9 item_name" data-validation="required length custom" data-validation-length="1-45" required="required" />\
                                            <div class="form-group col-xs-3">\
                                                <button type="button" class="btn btn-sm btn-success AddBtn">+</button>\
                                                <button type="button" class="btn btn-sm btn-danger RemoveBtn">-</button>\
                                            </div>\
                                        </div>\
                                    </div> \
                                    <div class="form-group">\
                                        <label class="col-sm-3 control-label no-padding-right" for="description">Description</label>\
                                        <div class="col-sm-9">\
                                            <input type="text" name="description[]" id="description" class="col-xs-12 form-control description" data-validation="required" placeholder="Enter Text"  />\
                                        </div>\
                                    </div>\
                                    <div class="form-group">\
                                        <label class="col-sm-3 control-label no-padding-right" for="uom" >Uom  </label>\
                                        <div class="col-sm-9">\
                                            <select name="uom['+t+'][]" class="uom col-xs-12 select2" multiple="multiple">'+s+'</select>\
                                        </div>\
                                    </div>\
                                    <div class="form-group">\
                                        <label class="col-sm-3 control-label no-padding-right" for="depends['+t+']"> Depends On<span style="color: red">&#42;</span> </label>\
                                        <div class="radio col-sm-9">\
                                            <label>\
                                                <input name="depends['+t+']" type="radio" value="1" class="ace" data-validation="required">\
                                                <span class="lbl">Color</span>\
                                            </label>\
                                            <label>\
                                                <input name="depends['+t+']" type="radio" value="2" class="ace">\
                                                <span class="lbl">Size</span>\
                                            </label>\
                                            <label>\
                                                <input name="depends['+t+']" type="radio" value="3" class="ace">\
                                                <span class="lbl">Size & Color</span>\
                                            </label>\
                                            <label>\
                                                <input name="depends['+t+']" type="radio" value="0" class="ace" checked>\
                                                <span class="lbl">None</span>\
                                            </label>\
                                        </div>\
                                    </div> \
                                </div> ';

                $('.addRemove').append("<div>"+data+"</div>");
                $('.uom').select2();
            });

            $('body').on('click', '.RemoveBtn', function(){
                $(this).parent().parent().parent().parent().remove();
            });  
        });
        $(document).ready(function(){ 
          var searchable = [1,2,3,4];
          var exportColName = ['SL','Main Category','Sub Category','Item Name','Description', 'UOM', 'Depends'];
          var exportCol = [0,1,2,3,4,5,6];
          var dt = $('#dataTables').DataTable({
              order: [], //reset auto order
              processing: true,
              language: {
                  processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
              },
              responsive: true,
              serverSide: true,
              pagingType: "full_numbers", 
              ajax: {
                   url: '{!! url("merch/setup/item_data") !!}',
                   type: "GET",
                   headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   } 
              }, 
              dom: "lBftrip",
              buttons: [   
                  {
                      extend: 'csv', 
                      className: 'btn btn-sm btn-success',
                      title: 'Item list',
                      header: true,
                      footer: false,
                      exportOptions: {
                          columns: exportCol,
                          format: {
                              header: function ( data, columnIdx ) {
                                  return exportColName[columnIdx];
                              }
                          }
                      },
                      "action": allExport,
                      messageTop: ''
                  }, 
                  {
                      extend: 'excel', 
                      className: 'btn btn-sm btn-warning',
                      title: 'Item list',
                      header: true,
                      footer: false,
                      exportOptions: {
                          columns: exportCol,
                          format: {
                              header: function ( data, columnIdx ) {
                                  return exportColName[columnIdx];
                              }
                          }
                      },
                      "action": allExport,
                      messageTop: ''
                  }, 
                  {
                      extend: 'pdf', 
                      className: 'btn btn-sm btn-primary', 
                      title: 'Item list',
                      header: true,
                      footer: false,
                      exportOptions: {
                          columns: exportCol,
                          format: {
                              header: function ( data, columnIdx ) {
                                  return exportColName[columnIdx];
                              }
                          }
                      },
                      "action": allExport,
                      messageTop: ''
                  }, 
                  {
                      extend: 'print', 
                      className: 'btn btn-sm btn-default',
                      title: '',
                      header: true,
                      footer: false,
                      exportOptions: {
                          columns: exportCol,
                          format: {
                              header: function ( data, columnIdx ) {
                                  return exportColName[columnIdx];
                              }
                          }
                      },
                      "action": allExport,
                      messageTop: customReportHeader('Item list', { })
                  } 
              ],
              columns: [  
                   {data: 'DT_RowIndex', name: 'DT_RowIndex'}, 
                   {data: 'main_category', name: 'main_category'}, 
                   {data: 'sub_category', name: 'sub_category'}, 
                   {data: 'item_code', name: 'item_code'}, 
                   {data: 'item_name', name: 'item_name'}, 
                   {data: 'uom', name: 'uom'}, 
                   {data: 'depends', name: 'depends'},
                   {data: 'action', name: 'action', orderable: false, searchable: false}
                ],

                initComplete: function () {   
                var api =  this.api();

                // Apply the search 
                api.columns(searchable).every(function () {
                    var column = this; 
                    var input = document.createElement("input"); 
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 120px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });
             } 
           }); 

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
    </script>
    @endpush
@endsection
