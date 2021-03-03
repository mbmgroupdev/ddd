@extends('merch.index')
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
                    <a href="#">Setup</a>
                </li>
                <li class="active"> Supplier Info </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                  @include('inc/message')
                  <div class="panel panel-success">
                    <div class="panel-heading">
                      <h6>Supplier Edit <a class="pull-right healine-panel" href="{{ url('merch/setup/supplier') }}" rel="tooltip" data-tooltip="Supplier Size List/Create" data-tooltip-location="top"><i class="fa fa-list"></i></a></h6>
                    </div>
                    <div class="panel-body">
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('merch/setup/supplier_update') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                         <br>
                        <input type="hidden" name="sup_id" value="{{$supplier->sup_id}}">

                            @foreach($unit_id as $ui)
                                <input type="hidden" name="unit_id" value="{{ $ui->as_unit_id }}">
                            @endforeach

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="sup_name" > Supplier Name<span style="color: red">&#42;</span> </label>
                                <div class="col-sm-9">
                                    <input type="text" name="sup_name" id="sup_name" placeholder="Supplier Name"  class="col-xs-12" value="{{$supplier->sup_name}}" data-validation="required length custom" data-validation-length="1-50" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="cnt_id" >Country<span style="color: red">&#42;</span> </label>
                                <div class="col-sm-9">
                                    {{ Form::select('cnt_id', $countryList, $supplier->cnt_id, ['placeholder'=>'Select Country', 'class'=> 'col-xs-12 filter', 'data-validation' => 'required']) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="sup_address"> Address <span style="color: red">&#42;</span> </label>
                                <div class="col-sm-9">
                                    <textarea name="sup_address" id="sup_address" class="col-xs-12" placeholder="Address"  data-validation="required length" data-validation-length="0-128">{{ $supplier->sup_address}}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="sup_type"> Supplier Type <span style="color: red">&#42;</span> </label>
                                <div class="col-sm-9">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" id="sup_type" name="sup_type"  class="ace" value="Local"  data-validation="required" {{ ($supplier->sup_type=="Local")?"checked":null }}/>
                                            <span class="lbl"> Local</span>
                                        </label>
                                        <label>
                                            <input type="radio" id="sup_type" name="sup_type" class="ace" value="Foreign" {{ ($supplier->sup_type=="Foreign")?"checked":null }}/>
                                            <span class="lbl">Foreign</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="addRemove">
                                @foreach($sup_contact AS $contact)
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="scp_details"> Contact Person <span style="color: red">&#42;</span> (<span style="font-size: 9px">Name, Cell No, Email</span>)</label>

                                    <div class="col-sm-9">
                                        <textarea name="scp_details[]" id="scp_details" class="col-xs-9" placeholder="Contact Person"  data-validation="required length" data-validation-length="0-128">{{ $contact->scp_details }}</textarea>

                                        <div class="form-group col-xs-3">
                                            <button type="button" class="btn btn-sm btn-success AddBtn">+</button>
                                            <button type="button" class="btn btn-sm btn-danger RemoveBtn">-</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-info btn-sm " data-toggle="modal" data-target="#select_item"> Item <i class="glyphicon glyphicon-plus"></i> </button>
                                </div>
                                <div class="col-sm-9" id="Item_description">
                                    @foreach($sup_category as $sc)
                                        <button type="button" class="btn btn-xs btn-default">{{ $sc->mcat_name }}</button>
                                        <input type="hidden" name="item_id[]" value="{{$sc->mcat_id}}">
                                        <input type="hidden" name="items[]" id="items[]" placeholder="Food" value="{{ $sc->mcat_name }}" class="col-xs-12"/>
                                    @endforeach
                                </div>
                            </div>

                            
                            <div class="widget-footer text-right">
                                {!! (!empty($buttons)?$buttons:null) !!}
                            </div>
                            <br>
                            <br>
                            @include('merch.common.update-btn-section')
                        </form>
                    </div>
                  </div>
                </div>
                
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="select_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <!-- <button type="button" class="btn btn-danger btn-xs pull-right" data-dismiss="modal">Close</button> -->
                <h2 class="modal-title text-center" id="myModalLabel"> Items</h2>
            </div>
            <form class="form-horizontal" role="form" method="post" action="#" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body"  style="padding:0 15px">


                    {!! (!empty($itemList)?$itemList:null) !!}


                </div>
                <div class="modal-footer">
                    <div class="col-md-8" style="padding-top: 20px;">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm" type="button" id="modal_data" data-dismiss="modal">
                            <i class="ace-icon fa fa-check bigger-110" ></i> Done
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal End -->


<script type="text/javascript">

    $(document).ready(function(){
        // var data = $('.AddBtn').parent().parent().parent().parent().html();
        var data= '<div class="form-group">\
                    <label class="col-sm-3 control-label no-padding-right" for="scp_details"> Contact Person <span style="color: red">&#42;</span> </label>\
                    <div class="col-sm-9">\
                        <textarea name="scp_details[]" id="scp_details" class="col-xs-9" placeholder="Contact Person"  data-validation="required length" data-validation-length="0-128"></textarea>\
                        <div class="form-group col-xs-3">\
                            <button type="button" class="btn btn-sm btn-success AddBtn">+</button>\
                            <button type="button" class="btn btn-sm btn-danger RemoveBtn">-</button>\
                        </div>\
                    </div>\
                </div>';
        $('body').on('click', '.AddBtn', function(){
            $('.addRemove').append(data);
        });

        $('body').on('click', '.RemoveBtn', function(){
            $(this).parent().parent().parent().remove();
        });


        // Modal Check Box
                $('#select_item').on('hidden.bs.modal', function (e) {
            var data= "";
            $('.checkbox-input').each(function(i, v){
                if ($(this).is(":checked"))
                {
                    var id= $(this).val();
                    console.log(id);
                    var item_name= $(this).next().text();
                    //var item_code= $(this).parent().next().text();
                    data+='<div class="col-sm-7"><button type="button" class="btn btn-xs btn-default">'+item_name+'</button><div class="col-sm-7"> <input type="hidden" name="items[]" id="items[]" placeholder="Food" value="'+item_name+'" class="col-xs-12" readonly/> </div></div> <div class="form-group"> <input type="hidden" name="item_id[]" value="'+id+'"> </div>';
                }
            });
            // console.log(data);
            $("#Item_description").html(data);
        });


    });
</script>

<script type="text/javascript">

    $(document).ready(function(){

        $('#dataTables').DataTable();
        var data = $('.AddBtn').parent().parent().parent().parent().html();
        $('body').on('click', '.AddBtn', function(){
            $('.addRemove').append(data);
        });

        $('body').on('click', '.RemoveBtn', function(){
            $(this).parent().parent().parent().remove();
        });


        // Modal Check Box
        $('#select_item').on('hidden.bs.modal', function (e) {
            var data= "";
            $('.checkbox-input').each(function(i, v){
                if ($(this).is(":checked"))
                {
                    var id= $(this).val();
                    console.log(id);
                    var item_name= $(this).next().text();
                    //var item_code= $(this).parent().next().text();
                    data+='<div class="col-sm-3 no-padding"><input type="text" name="items[]" id="items[]" placeholder="Food" value="'+item_name+'" class="col-xs-12" readonly/> <input type="hidden" name="item_id[]" value="'+id+'"> </div>';
                }
            });
            // console.log(data);
            $("#Item_description").html(data);
        });

    });
</script>

<script type="text/javascript">

    $(document).ready(function() {


        $('#country_id').on("change",function() {

            var country_id = $(this).val();
            console.log(country_id);
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
    });



</script>
@endsection
