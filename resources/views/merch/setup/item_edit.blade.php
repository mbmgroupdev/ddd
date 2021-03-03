@extends('merch.index')
@push('css')
    <style type="text/css">
         .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;   /* prevent horizontal scrollbar */
            overflow-x: hidden; /* add padding to account for vertical scrollbar */
            z-index:1000 !important;
        }
    </style>
@endpush
@section('content')
<div class="main-content">
    <div class="main-content-inner">
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
                <li class="active"> Item Edit</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
          <!---Form 1---------------------->
            <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6>Update Item
                                     <a class="pull-right healine-panel" href="{{ url('merch/setup/item') }}" rel="tooltip" data-tooltip="Item Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                <!-- Display Erro/Success Message -->
                                @include('inc/message')
                                  <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/item_update') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }} 
                                    <input type="hidden" name="mcat_id" value="{{ $mitem->id }}">
                                    <div class="form-group" style="pointer-events: none;">
                                        <label class="col-sm-3 control-label no-padding-right" for="mcat_name" >  Main Category<span style="color: red">&#42;</span> </label>
                                        <div class="col-sm-9">
                                            {{ Form::select('mcat_name', $cat_list, $mitem->mcat_id, ['placeholder'=>'Select Category Name','id'=>'mcat_name','class'=> 'col-xs-9', 'data-validation' => 'required']) }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="choice">Sub Cat. Opr.<span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-9">
                                            <label class="col-sm-3 no-padding"><input type="radio" name="choice" id="choice" value="rename" >Rename</label>
                                            <label class="col-sm-3"><input type="radio" name="choice" id="choice" value="new_entry" checked="checked">New Entry</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="subcategory_name">Sub Category<span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="subcategory_name" data-type="subcategory_name" id="subcategory_name" class="col-xs-9 autocomplete_sub_cat" data-validation="required" placeholder="Enter Text" value="{{$mitem->msubcat_name}}"  />
                                            <input type="hidden" name="msubcat_id" id="msubcat_id" value="{{$mitem->msubcat_id}}">
                                        </div>
                                    </div>
                                    <div class="addRemove">
                                        <div class="newItem">
                                            <div class="form-group" style="">
                                                <label class="col-sm-3 control-label no-padding-right" for="item_name[]"> Item <span style="color: red">&#42;</span> </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="item_name" name="item_name" placeholder="Enter Text" class="col-xs-9" data-validation="required length custom" data-validation-length="1-100" value="{{$mitem->item_name}}" />

                                                    <div class="form-group col-xs-3">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-3 control-label no-padding-right" for="description">Description</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="description" id="description" class="col-xs-9 description"  placeholder="Enter Text" value="{{$mitem->description}}" />
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label no-padding-right" for="item_code"> Item Code<span style="color: red">&#42;</span> </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="item_code" name="item_code" placeholder="Enter Text" class="col-xs-9" data-validation="required length custom" data-validation-length="1-45" value="{{$mitem->item_code}}" />

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label no-padding-right" for="uom" >UoM  </label>
                                                <div class="col-sm-9">
                                                    {!! Form::select('uom[]',$uom, $uomThis, ['id' => 'uom', 'class' => 'uom col-xs-9','multiple' => 'multiple']) !!}
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label no-padding-right" for="depends"> Depends On<span style="color: red">&#42;</span> </label>
                                                <div class="radio col-sm-9">

                                                    <label>
                                                        <input name="depends" type="radio" value="1" class="ace"<?php ($mitem->dependent_on==1)? printf("checked"):printf(""); ?>>
                                                        <span class="lbl">Color</span>
                                                    </label>
                                                    <label>
                                                        <input name="depends" type="radio" value="2" class="ace" data-validation="required" <?php ($mitem->dependent_on==2)? printf("checked"):printf(""); ?> >
                                                        <span class="lbl">Size</span>
                                                    </label>
                                                    <label>
                                                        <input name="depends" type="radio" value="3" class="ace" <?php ($mitem->dependent_on==3)? printf("checked"):printf(""); ?>>
                                                        <span class="lbl">Size & Color</span>
                                                    </label>
                                                    <label>
                                                        <input name="depends" type="radio" value="0" class="ace" <?php ($mitem->dependent_on==0)? printf("checked"):printf(""); ?>>
                                                        <span class="lbl">None</span>
                                                    </label>
                                                </div>
                                            </div> 

                                        </div>   
                                    </div>   

                                    @include('merch.common.update-btn-section')
                                </form>
                              </div>
                        </div>
                    </div>
            </div><!--- /. Row ---->
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
    //material sub category suggestion...
        $(document).on('focus','.autocomplete_sub_cat',function(){
        var type = $(this).data('type');
        var getId = $(this).attr('id');
        var mcat_id = $('#mcat_name').val();
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

    $(document).ready(function(){
        $('#dataTables').DataTable(); 
        var data = $('.AddBtn').parent().parent().parent().parent().html();
        $('body').on('click', '.AddBtn', function(){
            $('.addRemove').append("<div>"+data+"</div>");
        });

        $('body').on('click', '.RemoveBtn', function(){
            $(this).parent().parent().parent().parent().remove();
        });  
    });
</script>
@endsection
