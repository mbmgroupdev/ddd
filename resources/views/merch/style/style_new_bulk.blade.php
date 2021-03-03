@extends('merch.layout')
@section('title', 'Style Bulk')
@section('main-content')
@push('css')
  <style>
  
</style>
@endpush
  <div class="main-content">
      <div class="main-content-inner">
          <div class="col-sm-12">
              <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                      <li>
                          <i class="ace-icon fa fa-home home-icon"></i>
                          <a href="#">Merchandising</a>
                      </li>
                      <li>
                          <a href="#">Style</a>
                      </li>
                      <li class="active">Create Bulk</li>
                  </ul><!-- /.breadcrumb -->
       
              </div>
              <div class="panel">
                <div class="panel-heading">
                      <h6>Style Selection
                          <div class="pull-right">
                            
                            <a class="btn btn-primary" href="{{ url('merch/style/style_list') }}"><i class="las la-list"></i> Style List</a>
                            
                        </div>
                      </h6>
                </div>
                <div class="panel-body">
                  @include('inc/message')
                  <div class="style_section">
                    <div class="row" style="margin-top: 20px;">
                       <div class="offset-sm-1 form-group col-sm-8">
                          <form action="" class="form-horizontal row" method="get">
                              <label class="col-sm-4 control-label no-padding-right" for="style_no" style="text-align: right;"> Style<span style="color: red">&#42;</span> </label>
                              <div class="col-sm-7">
                                <div class="row">
                                  <div class="col-sm-9">
                                    {{ Form::select('style_no', $stylelist, Request::get('style_no'), ['placeholder'=>'Select Style', 'class'=> 'col-xs-12', 'id'=>"style_no", 'data-validation' => 'required' ]) }}
                                  </div>
                                  <div class="col-sm-3">
                                    <button class="btn btn-success btn-sm pull-right" type="submit" style="border-radius: 5px;">
                                        <i class="ace-icon fa fa-search bigger-110"></i> Search
                                    </button>
                                  </div>
                                </div>
                              </div>
                          </form>
                       </div>
                    </div>
                    @if (!empty(request()->has('style_no')))
                      <!--    {{request('style_no')}} -->
                      <div class="panel panel-success">
                        <div class="panel-heading">
                          <div class="row no-padding no-margin">
                            <div class="col-sm-6 no-padding no-margin">
                              <h6>Create Bulk</h6>
                            </div>
                            <div class="col-sm-6 no-padding no-margin">
                              {{-- <button class="btn btn-sm btn-danger pull-right" style="border-radius: 2px;"><i class="fa fa-print"></i> Print</button> --}}
                            </div>
                          </div>
                            {{-- <h6>Create Bulk</h6>
                            <button class="btn btn-sm">print</button> --}}
                        </div>
                        <div class="panel-body">
                            <!-- Display Erro/Success Message -->
                            @include('inc/message')
                            <div id="style_form" class="col-sm-12" >
                              <!---form--Here-->

                                  <?php
                                    $check = DB::table('mr_style AS s')
                                             ->where('stl_id',request('style_no'))
                                             ->where('stl_type','Bulk')

                                             ->exists();
                                    if($check==null)   {
                                          $style = DB::table('mr_style AS s')
                                            ->select(
                                                "s.*",
                                                "b.b_name",
                                                "b.b_id",
                                                "p.*"
                                            )
                                            ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
                                            ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
                                            ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
                                            ->where('s.stl_id',request('style_no'))
                                            ->first();

                                          $stlsize = DB::table('mr_stl_size_group AS s')
                                                              ->select(
                                                                  "s.*",
                                                                  "p.id",
                                                                  "p.size_grp_name"
                                                              )
                                                              ->leftJoin('mr_product_size_group AS p', 'p.id', '=', 's.mr_product_size_group_id')
                                                              ->where('s.mr_style_stl_id',request('style_no'))
                                                              ->get();

                                          $stlwash = DB::table('mr_stl_wash_type AS sw')
                                                              ->select(
                                                                  "sw.*",
                                                                  "mw.id",
                                                                  "mw.wash_name"
                                                              )
                                                              ->leftJoin('mr_wash_type AS mw', 'mw.id', '=', 'sw.mr_wash_type_id')
                                                              ->where('sw.mr_style_stl_id',request('style_no'))
                                                              ->get();

                                      ?>

                                      {{ Form::open(["url" => "merch/style/bulk_store", "class"=>"form-horizontal", "files"=>true]) }}

                                        <!-- Top -->
                                        <div class="row">
                                          <div class="col-sm-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label no-padding-right" for="se_id" > Image  </label>
                                                <div class="col-sm-8">
                                                  @php
                                                    if($style->stl_img_link == null){
                                                      $imageUrl = "/assets/files/style/empty_style.jpg";
                                                    }else{
                                                      $imageUrl = "/$style->stl_img_link";
                                                    }
                                                  @endphp
                                                <img id="imagepreview" class="thumbnail" src="{{ url($imageUrl) }}" alt="Style image" style="margin-left: 10px;" width="100" />
                                                <input type="hidden" class="setfile" name="style_img" value="{{$style->stl_img_link}}">
                                             </div>

                                            </div>

                                          </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 control-label no-padding-right" for="b_id" > Production Type<span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <label class="radio-inline">
                                                            @if($style->stl_type == 'Bulk')
                                                        <input type="radio" name="stl_order_type" id="inlineRadio1" value="Bulk" data-validation="required" checked> Bulk
                                                                @else
                                                        <input type="radio" name="stl_order_type" id="inlineRadio1" value="Development" data-validation="required" checked> Development
                                                                @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                          <!-- 1st Row -->
                                          <div class="col-sm-4">
                                              <div class="form-group row">
                                                  <label class="col-sm-4 control-label no-padding-right" for="b_id" > Buyer<span style="color: red">&#42;</span> </label>
                                                  <div class="col-sm-8" style='pointer-events: none;'>
                                                      {{ Form::select('b_id', $buyerList, $style->mr_buyer_b_id, ['placeholder'=>'Select Buyer', 'class'=> 'col-xs-12', 'id'=>"b_id", 'data-validation' => 'required']) }}
                                                  </div>
                                                
                                              </div>
                                              <div class="form-group row">
                                                  <label class="col-sm-4 control-label no-padding-right" for="mr_brand_br_id" >Brand<span style="color: red">&#42;</span></label>
                                                  <div class="col-sm-8 col-xs-11" style='pointer-events: none;'>
                                                      {{ Form::select('mr_brand_br_id', $brand, $style->mr_brand_br_id, ['id'=> 'mr_brand_br_id', 'placeholder' => 'Select Brand', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                                  </div>
                                              </div>

                                              <div class="form-group row">
                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="prd_type_id" > Product Type<span style="color: red">&#42;</span> </label>
                                                  <div class="col-sm-8" style='pointer-events: none;'>
                                                      {{ Form::select('prd_type_id', $productTypeList, $style->prd_type_id, ['placeholder'=>'Select Product Type', 'class'=> 'col-xs-12', 'id'=>'prd_type_id', 'data-validation' => 'required', 'disable'=>'disable']) }}
                                                  </div>
                                                 
                                              </div>

                                              <div class="form-group row">
                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="stl_product_name" > Style Reference 2</label>
                                                  <div class="col-sm-8">
                                                    <input type="text" id="stl_product_name" name="stl_product_name" placeholder="Enter Style Reference 2" class="form-control col-xs-12" value="{{$style->stl_product_name}}" readonly="readonly" />
                                                </div>
                                              </div>

                                              <div class="form-group row">
                                                    <label class="col-sm-4 pr-0 control-label no-padding-right" for="stl_smv" > Sewing SMV<span class="color: warning">&#42;</span></label>
                                                  <div class="col-sm-8">
                                                    <input type="text" id="stl_smv" name="stl_smv" placeholder="Enter Value" class="form-control col-xs-12" data-validation="number" data-validation-allowing="float" value="{{$style->stl_smv}}" readonly="readonly" />
                                                  </div>
                                              </div>
                                              <!-- Operation -->
                                              <div class="addmore">
                                                  <div class="form-group row">
                                                      <label class="col-sm-4 pr-0 control-label no-padding-right" for="opr_id" > Operation </label>
                                                      <div class="col-sm-5" style='padding-left:30px;'>

                                                          <div class="checkbox row">
                                                               @foreach($operationList as $id => $name)

                                                                 <?php
                                                                  $tnalib=DB::table('mr_style_operation_n_cost AS so')
                                                                           ->select(
                                                                                    'so.*'
                                                                                )
                                                                          ->leftjoin('mr_operation AS op', 'op.opr_id', '=', 'so.mr_operation_opr_id')
                                                                          ->where('so.mr_style_stl_id', $style->stl_id)
                                                                          ->where('so.mr_operation_opr_id', $id)
                                                                          ->first();

                                                                  ?>
                                                                  @if($tnalib)
                                                              <label class='col-sm-6' style='padding:0px;pointer-events: none;'>
                                                                  <input name="opr_id[]" id="opr_id" type="checkbox" class="ace" value="{{ $id }}" @if($tnalib) checked @endif>
                                                                  <span class="lbl"><?= $name.'  ' ?></span>
                                                              </label>
                                                                @endif
                                                             @endforeach
                                                          </div>

                                                    </div>

                                               </div>
                                              </div>

                                              <!-- Special Machine -->
                                              <div class="addmore">
                                                  <div class="form-group row">
                                                      <label class="col-sm-4 pr-0 control-label no-padding-right" for="sp_machine_id" > Special Machine</label>
                                                      <div class="col-sm-5" style='padding-left:30px;'>

                                                          <div class="checkbox row">
                                                             @foreach($machineList as $id => $name)

                                                              <?php
                                                                  $smachine=DB::table('mr_style_sp_machine AS sm')
                                                                          ->select('sm.*')
                                                                          ->leftjoin('mr_special_machine AS sp', 'sp.spmachine_id', '=', 'sm.spmachine_id')
                                                                          ->where('sm.stl_id', $style->stl_id)
                                                                          ->where('sm.spmachine_id', $id)
                                                                          ->first();

                                                              ?>
                                                               @if($smachine)
                                                              <label style='padding:0px;pointer-events: none;'>
                                                                  <input name="sp_machine_id[]" id="sp_machine_id" type="checkbox" class="ace" value="{{ $id }}"  @if($smachine) checked @endif>
                                                                  <span class="lbl"> {{ $name }}</span>
                                                              </label>
                                                              @endif
                                                             @endforeach
                                                          </div>

                                                      </div>

                                                  </div>
                                              </div>
                                          </div>

                                          <!-- 2nd Row -->
                                          <div class="col-sm-4">
                                              <div class="form-group row">
                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="stl_no" > Style Reference 1</label>
                                                  <div class="col-sm-8">
                                                      <input type="text" id="stl_no" name="stl_no_old" placeholder="Enter Style Reference 1" class="form-control col-xs-12" data-validation="required length custom" data-validation-length="1-30"  value="{{$style->stl_no}}" readonly="readonly" disabled="disabled" />
                                                  </div>
                                              </div>
                                                
                                              <div class="form-group row">
                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="gmt_id" > Garments Type<span class="color: warning">&#42;</span> </label>
                                                  <div class="col-sm-8" style="pointer-events: none;">
                                                    {{ Form::select('gmt_id', $garmentsTypeList, $style->gmt_id, ['placeholder'=>'Select Garments Type', 'id'=>'gmt_id', 'class'=> 'form-control col-xs-12', 'data-validation' => 'required']) }}
                                                  </div>
                                              </div>
                                              <div class="form-group row">
                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="stl_description" > Remarks </label>
                                                  <div class="col-sm-8">
                                                      <textarea name="stl_description" id="stl_description" placeholder="Remarks"  class="form-control" readonly="readonly">{{$style->stl_description}}</textarea>
                                                  </div>
                                              </div>
                                              <div class="form-group row">

                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="mr_sample_style"> Sample Type </label>
                                                  <div class="col-sm-8" style='padding-left:20px;'>
                                                      <div class="control-group" >
                                                          <div class="checkbox row" id="sample-checkbox">
                                                            <!---Sample Update--->
                                                             @foreach($sampleTypeList as $id => $name)

                                                               <?php
                                                                  $sample=DB::table('mr_stl_sample AS sm')
                                                                    ->select('sm.*')
                                                                    ->leftjoin('mr_sample_type AS sp', 'sp.sample_id', '=', 'sm.sample_id')
                                                                    ->where('sm.stl_id', $style->stl_id)
                                                                    ->where('sm.sample_id', $id)
                                                                    ->first();
                                                                ?>
                                                              @if($sample)
                                                              <label class='col-sm-6' style='padding:0px;pointer-events: none;'>
                                                                  <input name="mr_sample_style[]" id="mr_sample_style" type="checkbox" class="ace" value="{{ $id }}" @if($sample) checked @endif>
                                                                  <span class="lbl">{{ $name }}</span>
                                                              </label>
                                                              @endif
                                                              @endforeach
                                                              <!---End Sample Update--->
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <!-- 3rd Row -->
                                          <div class="col-sm-4">
                                              @foreach($stlsize as $k=>$size)
                                                   <div class="form-group row">
                                                      <label class="col-sm-4 pr-0 control-label no-padding-right" for="prdsz_id" >
                                                        <?php if($k == 0){?>
                                                        Size Group<span class="color: warning">&#42;</span>
                                                      <?php } ?>
                                                      </label>
                                                      <div class="col-sm-8" style='pointer-events: none;'>
                                                        {{ Form::select('prdsz_id[]', $sizegroupList, $size->mr_product_size_group_id, ['placeholder'=>'Select Size Group', 'id'=>'prdsz_id', 'class'=> 'col-xs-10 prdsz_id', 'data-validation' => 'required']) }}

                                                             
                                                      </div>
                                                     
                                               </div>
                                              @endforeach

                                              
                                             <div class="form-group row">
                                                  <label class="col-sm-4 pr-0 control-label no-padding-right" for="se_id"> Season <span class="color: warning">&#42;</span> </label>
                                                  <div class="col-sm-8" style='pointer-events: none;'>
                                                    {{ Form::select('se_id', $season, $style->mr_season_se_id, ['placeholder'=>'Select', 'id'=>'se_id', 'class'=> 'col-xs-10 se_id', 'data-validation' => 'required']) }}
                                                  </div>
                                                 
                                             </div>

                                             @foreach($stlwash as $k=>$swash)
                                                 <div class="form-group row">
                                                      <label class="col-sm-4 pr-0 control-label no-padding-right" for="wash_id" >
                                                        <?php if($k == 0){?>
                                                        Wash Type<span class="color: warning">&#42;</span>
                                                      <?php } ?>
                                                      </label>
                                                      <div class="col-sm-8" style='pointer-events: none;'>
                                                        {{ Form::select('wash[]', $wash, $swash->mr_wash_type_id, ['placeholder'=>'Select Wash', 'class'=> 'col-xs-10 wash_id', 'data-validation' => 'required']) }}
                                                        

                                                      </div>
                                                    
                                                 </div>
                                              @endforeach
                                          

                                          </div>
                                        </div>
                                        <!-- Submit Button -->
                                        <div class="col-sm-12">
                                            <div class="clearfix form-actions row">
                                                <div class="offset-sm-4 col-md-4">
                                                    <button class="btn btn-info btn-sm" type="submit" style="border-radius: 5px;">
                                                        <i class="ace-icon fa fa-check bigger-110"></i> Save
                                                    </button>
                                                    &nbsp; &nbsp; &nbsp;
                                                    <button class="btn btn-sm" type="reset" style="border-radius: 5px;">
                                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                    </button>


                                                     <input type="hidden" name="style_id" value="{{$style->stl_id}}">
                                                </div>
                                            </div>
                                        </div>
                                      {{ Form::close() }}

                                <?php }

                                    else echo"<div class='col-sm-6' align='center'><h1>Bulk Already Created</h1></div>";

                                      ?>



                              <!--form----->
                            </div>
                        </div>
                      </div>
                    @endif

                  </div>
                </div>
              </div>
          </div>
      </div>
  </div>
@push('js')


<script type="text/javascript">
  // Updated 08/2018 to prevent changing value with tab

$(window).on('load', function() {


    var readonly_select = $('select');
    $(readonly_select).attr('readonly', true);

});



$(document).ready(function()
{


    // Form Generate Based On Style

     var styleno = $("#style_no");

    //   styleno.on("change", function(){

    //     // Action Element list
    //     $.ajax({
    //         url : "{{ url('merch/style/find_bulk') }}",
    //         type: 'get',
    //         data: {style_id : $(this).val()},
    //         success: function(data)
    //         {
    //              $("#style_form").html(data);
    //         },
    //         error: function()
    //         {
    //             alert('failed...');
    //         }
    //     });

    // });

    //Add More  buyer modal
        var data_b = $("#contactPersonData").html();
        $('body').on('click', '.AddBtn_bu', function(){
            $("#contactPersonData").append(data_b);
        });

        $('body').on('click', '.RemoveBtn_bu', function(){
            $(this).parent().parent().parent().remove();
      });

    // script for season modal
      $("#se_name").keyup(function(){
        var bid = $("#b_id2").val();
        if(bid != ''){
            // Action Element list
            $.ajax({
                url : "{{ url('merch/setup/season_input') }}",
                type: 'get',
                data: {keyword : $(this).val(),b_id: bid},
                 beforeSend: function(){
                //$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
                success: function(data)
                {

                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(data);

                    },
                error: function()
                {
                   // alert('failed...');
                }
            });
          }
          else{ alert("Please Select Buyer")}

        });


         var basedon = $("#b_id");
         var action_element=$("#sample-checkbox");
         var action_season=$("#se_id");
         var action_size=$(".prdsz_id");

    // Sample type Based On Buyer

          basedon.on("change", function(){

            // Sample  list
            $.ajax({
                url : "{{ url('merch/style/sample_season') }}",
                type: 'get',
                dateType: 'JSON',
                data: {b_id : $(this).val()},
                success: function(data)
                {
                    action_element.html(data.samplelist);
                    action_season.html(data.selist);
                    action_size.html(data.sizelist);
                },
                error: function()
                {
                    alert('failed...');
                }
            });

        });

    //Add More size group in form
            var data_s = $("#size-add").html();
            $('body').on('click', '.AddBtn_size_s', function(){
                $("#size-add").append(data_s);
            });

           $('body').on('click', '.RemoveBtn_size_s', function(){
            $(this).parent().parent().parent().remove();
      });

    //Add More size group in modal
            // var data = $('.AddBtn_size').parent().parent().parent().parent().html();
               var data = $('.AddBtn_size').parent().parent().parent().parent().html();
            $('body').on('click', '.AddBtn_size', function(){
                $('.addRemove').append("<div>"+data+"</div>");
            });

            $('body').on('click', '.RemoveBtn_size', function(){
                $(this).parent().parent().parent().parent().remove();
            });
    //Add More wash in form
            var data_w = $("#wash-add").html();
            $('body').on('click', '.AddBtn_wash', function(){
                $("#wash-add").append(data_w);
            });

           $('body').on('click', '.RemoveBtn_wash', function(){
            $(this).parent().parent().parent().remove();
      });

    // Product Type  Add through ajax

    $('#new_product').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);

        $("#newProdTypeFrm").on("submit", function(e) {
            e.preventDefault();
              var prod_name = $("#prd_type_name").val();
             var that = $(this);

            // Product insert url
            $.ajax({
                url : "{{ url('merch/setup/product_type_store') }}",
                type: 'post',
                data: {
                   prd_type_name: prod_name,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Product Successfully saved</div>");

                    // Ajax call for product list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/product') }}",
                        type: 'get',
                        data: {},
                        success: function(data)
                        {
                           button.parent().prev().find("#prd_type_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


    });

    // Season Add through ajax
    $('#new_season').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);

        $("#newSeasonFrm").on("submit", function(e) {
            e.preventDefault();
            var action_place = $("#se_id");
            var buyr = $("#b_id").val();
            var se_name = $("#se_name").val();
            var se_mm_start  = $("#se_mm_start").val();
            var se_mm_end    = $("#se_mm_end").val();

             var that = $(this);

            // Product insert url
            $.ajax({
                url : "{{ url('merch/setup/season_store') }}",
                type: 'post',
                data: {
                    se_name    : se_name,
                    b_id       : buyr,
                    se_mm_start: se_mm_start,
                    se_mm_end  : se_mm_end
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Wash Successfully saved</div>");

                    // Ajax call for product list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/season') }}",
                        type: 'get',
                        data: {b_id : buyr},
                        success: function(data)
                        {
                           button.parent().prev().find("#se_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


    });

    // Wash Type  Add through ajax

    $('#newWashModal').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);

        $("#newWashFrm").on("submit", function(e) {
            e.preventDefault();
             var wash_name = $("#wash_name").val();
             var wash_rate = $("#wash_rate").val();
             var that = $(this);

            // Wash insert url
            $.ajax({
                url : "{{ url('merch/setup/wash_type') }}",
                type: 'post',
                data: {
                    wash_name: wash_name,
                    wash_rate: wash_rate
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Wash Successfully saved</div>");

                    // Ajax call for wash list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/wash') }}",
                        type: 'get',
                        data: {},
                        success: function(data)
                        {
                           button.parent().prev().find(".wash_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


    });

    // Size Group Add through ajax

    $('#new_size_group').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);
        var buyer = $("#b_id").val();
        if(buyer==""){ alert('Please Select The buyer first!!');}


        $("#newSizeFrm").on("submit", function(e) {

            e.preventDefault();
             var buyer = $("#b_id").val();
             var brand = $("#brand").val();
             var product_type = $("#product_type").val();
             var gender = $("#gender").val();
             var sg_name = $("#sg_name").val();

             var that = $(this);

             var psize_array = new Array();
                $('input[name="psize[]"]').each(function(){
                   psize_array.push($(this).val());
                });
             var sino_array = new Array();
                $('input[name="sino[]"]').each(function(){
                   sino_array.push($(this).val());
                });

            // Size Group insert url
            $.ajax({
                url : "{{ url('merch/setup/productsizestore') }}",
                type: 'post',
                data: {
                    buyer  : buyer,
                    brand  : brand,
                    product_type: product_type,
                    gender : gender,
                    sg_name: sg_name,
                    psize  : psize_array,
                    sino   : sino_array

                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Size Group Successfully saved</div>");

                    // Ajax call for Sizegroup list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/sizegroup') }}",
                        type: 'get',
                        data: { buyer: buyer },
                        success: function(data)
                        {
                           button.parent().prev().find(".prdsz_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


    });

    // Buyer Add through ajax

    $('#new_buyer').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);

        $("#newBuyerFrm").on("submit", function(e) {

            e.preventDefault();
             var march_buyer_name = $("#march_buyer_name").val();
             var march_buyer_short_name = $("#march_buyer_short_name").val();
             var country = $("#country").val();
             var march_buyer_address = $("#march_buyer_address").val();

             var that = $(this);

             var march_buyer_contact = new Array();
                $('textarea[name="march_buyer_contact[]"]').each(function(){
                   march_buyer_contact.push($(this).val());
                });

            // Buyer insert url
            $.ajax({
                url : "{{ url('merch/setup/buyerinfostore') }}",
                type: 'post',
                data: {
                    march_buyer_name    : march_buyer_name,
                    march_buyer_short_name  : march_buyer_short_name,
                    country: country,
                    march_buyer_address : march_buyer_address,
                    march_buyer_contact : march_buyer_contact

                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Buyer Successfully saved</div>");

                    // Ajax call for Buyer list if Successfully saved
                    $.ajax({
                        url : "{{ url('merch/style/buyerlist') }}",
                        type: 'get',
                        data: {},
                        success: function(data)
                        {
                           button.parent().prev().find("#b_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


    });

});
</script>

<script>
// Image preview
  var loadFile = function(event) {
    var output = document.getElementById('imagepreview');
    output.src = URL.createObjectURL(event.target.files[0]);
  };
</script>
@endpush
@endsection
