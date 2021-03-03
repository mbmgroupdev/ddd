@extends('merch.layout')
@section('title', 'Supplier')
@section('main-content')
    @push('css')
    <style type="text/css">
        @media only screen and (max-width: 767px) {
            #tableDiv{padding-right: 10px !important; padding-left: 0px;}
            .dataTables_wrapper .col-sm-12{width: 100%;}
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
            <li class="active"> Supplier </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       
       <div class="col mail-box-detail">
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block ">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  Create New Supplier </span> </a></div>
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
                                        <input type="hidden" name="unit_id" value="{{ $unit_id }}">
                                        <div class="offset-3 col-6">
                                          <div class="row">
                                            <div class="col-sm-8">
                                              <div class="form-group has-float-label has-required">
                                                  <input type="text" class=" form-control" id="sup_name" name="sup_name" placeholder="Enter Supplier Name" value="" autocomplete="off" required />
                                                <label for="sup_name">Supplier Name</label>
                                              </div>
                                              <div class="form-group has-float-label has-required select-search-group">
                                                {{ Form::select('cnt_id', $countryList, null, ['placeholder'=>'Select Country Name','id'=>'country_id','class'=> 'form-control filter', 'required']) }}
                                                <label for="country_id">Country</label>
                                              </div>
                                              <div class="form-group has-float-label has-required">
                                                <textarea name="sup_address" id="sup_address" rows="1" class="form-control" placeholder="Supplier Address" required></textarea>
                                                <label for="sup_address">Supplier Address</label>
                                              </div>
                                              <div class="row">
                                                <div class="col-10 pr-0">
                                                  <div class="form-group has-required has-float-label">
                                                    <input type="text" id="contact0" name="scp_details[]" placeholder="Enter Contact Person (Name, Cell No, Email)" class="form-control" autocomplete="off" required />
                                                    <label for="contact0" > Contact Person </label>
                                                  </div>
                                                </div>
                                                <div class="col-2">
                                                  <button type="button" class="btn btn-xs btn-outline-success AddBtn_bu">+</button>
                                                </div>
                                              </div>
                                              <div id="addAddress"></div>
                                              <div class="form-group">
                                                <button class="btn btn-outline-success btn-md" type="button" id="supplierAddBtn"><i class="fa fa-save"></i> Save</button>
                                              </div>
                                            </div>
                                            <div class="col-sm-4">
                                              <div class="form-group has-required ">
                                                <label>Supplier Type :</label>
                                                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                                  <input type="radio" id="Local" name="sup_type" class="custom-control-input bg-primary local" value="Local">
                                                  <label class="custom-control-label" for="Local"> Local </label>
                                                </div>
                                                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                                  <input type="radio" id="Foreign" name="sup_type" class="custom-control-input bg-primary foreign" value="Foreign">
                                                  <label class="custom-control-label" for="Foreign"> Foreign</label>
                                                </div>
                                              </div>
                                              <div class="form-group has-required">
                                                <label for="">Item Type :</label>
                                                @foreach($itemList as $key => $item)
                                                <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                  <input type="checkbox" name="items[]" class="custom-control-input bg-primary" value="{{ $item->mcat_id }}" id="item-{{ $item->mcat_id }}">
                                                  <label class="custom-control-label" for="item-{{ $item->mcat_id }}"> {{ $item->mcat_name }}</label>
                                                </div>
                                                @endforeach
                                              </div>
                                              
                                            </div>
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
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title">  List Of Supplier </span> </a></div>
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
                                        <th>SL.</th>
                                        <th width="15%">Supplier Name</th>
                                        <th>Supplier Type</th>
                                        <th width="20%">Item Type</th>
                                        <th width="20%">Address</th>
                                        <th width="20%">Contact Persons</th>
                                        {{-- <th>Status</th> --}}
                                        <th>Action</th>
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

    <script>
        jQuery('#supplierAddBtn').click(function(event) {
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
                  url: '{{ url("/merch/setup/ajax_save_supplier") }}',
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
        $('#country_id').on("change",function() {
            var country_id = $(this).val();
            if (country_id==18){
                $('.local').prop('checked',true);
            }
            else if(country_id==""){
                $('.local').prop('checked',false);
                $('.foreign').prop('checked',false);
            }
            else{
                $('.foreign').prop('checked',true);
            }

        });

        $(document).ready(function(){ 
          var searchable = [1,2,3,4];
          var exportColName = ['Supplier Name','Supplier Type','Item Type','Address','Contact Person'];
          var exportCol = [0,2,3,4,5];
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
                   url: '{!! url("merch/setup/supplier_data") !!}',
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
                      title: 'Supplier list',
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
                      title: 'Supplier list',
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
                      title: 'Supplier list',
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
                      messageTop: customReportHeader('Supplier list', { })
                  } 
              ],
              columns: [  
                   {data: 'DT_RowIndex', name: 'DT_RowIndex'}, 
                   {data: 'sup_name', name: 'sup_name'}, 
                   {data: 'sup_type', name: 'sup_type'}, 
                   {data: 'item_type', name: 'item_type'}, 
                   {data: 'sup_address', name: 'sup_address'}, 
                   {data: 'contact_person', name: 'contact_person'}, 
                   // {data: 'status', name: 'status'}, 
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

        var i=1;
        $(".AddBtn_bu").on('click',function(){
          html = '<div class="row"><div class="col-10 pr-0"><div class="form-group has-float-label">';
          html += '<input type="text" id="contact'+i+'" name="scp_details[]" placeholder="Enter Contact Person (Name, Cell No, Email)" class="form-control scp_details"/>';
          html += '<label for="contact'+i+'"> Contact Person </label></div></div><div class="col-2">';
          html += '<button type="button" class="btn btn-xs btn-outline-danger RemoveBtn_bu">-</button></div></div>';
          
          $('#addAddress').append(html);
          $('#contact'+i).focus();
          i++;
        });

        $('body').on('click', '.RemoveBtn_bu', function(){
            $(this).parent().parent().remove();
        });
    </script>
    @endpush
@endsection
