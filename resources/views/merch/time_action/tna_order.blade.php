@extends('merch.layout')
@section('title', 'Order TNA')
@section('main-content')
    @push('css')
        <style>
            .ui-autocomplete {
                position: absolute;
                z-index: 2150000000 !important;
                cursor: default;
                border: 2px solid #ccc;
                padding: 5px 0;
                border-radius: 2px;
            }
            .close-button {
                position: absolute;
                z-index: 100;
                right: 5px;
                border: none;
                padding: 4px 6px;
                color: #fff;
                font-size: 13px;
                top: -10px;
                background: rgb(8 155 171);
                border-radius: 50%;
                font-weight: 500;
            }
            .opr-item{
                border: 1px solid #d1d1d1;
                margin: 3px;
            }


            @media only screen and (max-width: 767px) {

                .modal{margin-top: 45px;}
                .checkbox label input[type=checkbox].ace+.lbl, .radio label input[type=radio].ace+.lbl{margin-left: 10px;}
                input[type=checkbox].ace+.lbl, input[type=radio].ace+.lbl{ margin-left: 10px; }

            }
            @media only screen and (max-width: 480px) {

                .modal{margin-top: 85px;}
                .checkbox label input[type=checkbox].ace+.lbl, .radio label input[type=radio].ace+.lbl{margin-left: 10px;}
                input[type=checkbox].ace+.lbl, input[type=radio].ace+.lbl{margin-left: 10px;}
                .modalDiv .col-xs-8 {width: 100% !important; padding-top: 10px;}
                .modalDiv .col-xs-4 {padding-left: 0px;}

            }

            .slide_upload {
                width: auto;
                height: 100px;
                position: relative;
                cursor: pointer;
                background: #eee;
                border: 1px solid rgb(8 155 171);
                border-radius: 5px;
                overflow: hidden;
            }
            .slide_upload img {
                width: 100%;
                padding: 2px;
                object-fit: cover;
            }
            .slide_upload::before{content: "+";position: absolute;top: 50%;color: rgb(8 155 171);left: 50%;font-size: 52px;margin-left: -17px;margin-top: -37px;}

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
                        <a href="#">Time & Action</a>
                    </li>
                    <li class="active">Order TNA</li>
                </ul><!-- /.breadcrumb -->
            </div>

            @include('inc/message')
            <div class="panel">
                <div class="panel-body">
                    <div class="style_section">
                    <div class="row">
                        <div class="col-sm-5">
                          <!-- -Form 1---------------------->
                          <form class="form-horizontal col-sm-12" role="form" method="post" action="{{ url('merch/time_action/tna_generate_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}


                                <input type="hidden" name="stl_order_type" id="inlineRadio1" value="Development" required="required" readonly>
                                <span style="color: green">* TNA Generate</span>
                                <!-- PAGE CONTENT BEGINS -->
                                <div class="form-horizontal" style="margin-top: 10px">

                                  <div class="form-group">
                                        <div class="form-group has-float-label select-search-group has-required">

                                          {{ Form::select('mbm_order', $order_en, null, ['placeholder'=>'Select ','id'=>'order_id','class'=> 'col-xs-12 form-control', 'data-validation' => 'required']) }}
                                            <label for="mbm_order" >MBM Order </label>
                                        </div>
                                  </div>
                                  <div class="form-group has-float-label has-required">
                                      <input type="text" name="confirm_date" id="confirm_date" class="datepicker col-xs-12 form-control" value="" data-validation="required" autocomplete="off" placeholder="Y-m-d" />
                                      <label for="confirm_date" >Confirm Date </label>
                                      <div id="msg" class="col-sm-9 pull-right" style="color: red"></div>
                                  </div>
                                  <div class="form-group has-float-label has-required">

                                      <input type="text" id="lead_days" name="lead_days" placeholder="Enter Text" class="col-xs-12 form-control" data-validation="required length custom" data-validation-length="1-50"/>
                                      <label for="lead_days" >Lead Days</label>
                                      <div id="msg" class="col-sm-9 pull-right" style="color: red"></div>
                                  </div>
                                  <div class="form-group has-float-label has-required">
                                           <input type="text" id="tolerance_days" name="tolerance_days" placeholder="Enter Text" class="col-xs-12 form-control" data-validation="required length custom" data-validation-length="1-50"/>
                                      <label for="tolerance_days" >Tolerance Days</label>
                                        <div id="msg" class="col-sm-9 pull-right" style="color: red">
                                        </div>
                                  </div>
                                  <div class="form-group has-float-label select-search-group has-required">
                                      <select id="tna_type" class="col-xs-12 form-control" name="tna_templatetype"><option value=" " data-validation="required">Select Order</option></select>
                                      <label for="tna_templatetype" >TNA Type </label>
                                      <div id="msg" class="col-sm-9 pull-right" style="color: red"></div>
                                  </div>
                                  <div class="form-group has-float-label has-required ">

                                        <input type="text" name="ok_to_begin" id="ok_to_begin" class="datepicker col-xs-12 form-control" value="" data-validation="required" autocomplete="off" placeholder="Y-m-d" />
                                      <label  for="ok_to_begin" >OK to Begin </label>
                                      <div id="msg" class="col-sm-9 pull-right" style="color: red"></div>
                                  </div>
                                  <div class="form-group has-float-label has-required">


                                            <input type="text" name="rev_ok_to_begin" id="rev_ok_to_begin" class="datepicker col-xs-12 form-control" value="" data-validation="required" autocomplete="off" placeholder="Y-m-d" />
                                      <label  for="rev_ok_to_begin" >Rev OK to Begin </label>
                                        <div id="msg" class="col-sm-9 pull-right" style="color: red">
                                        </div>
                                  </div>

                                  </div>
                              <div class="clearfix form-actions">
                                  <div >
                                      <a style="padding-left: 10px; padding-right: 10px; border-radius: 5px; color: white" class="btn btn-sm btn-primary generatetna" type="submit">
                                          Generate TNA
                                      </a>
                                      <button style="width: 100px; border-radius: 5px" class="btn btn-sm btn-success" type="submit">
                                          Save
                                      </button>
                                      <button class="btn btn-sm" type="reset">
                                          <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                      </button>
                                  </div>
                              </div>
                          </form>
                              </div>
                            <!-- /.col -->


                        <div class="col-sm-7 tna-generate">
                            <!--Table here--->
                        </div>
                    </div><!--- /. Row Form 1---->

                </div>
              </div>


        {{-- <div class="panel panel-default"></div> --}}
      </div><!-- /.page-content -->
    </div>
</div>
<!--  <script type='text/javascript'>
         $(document).ready(function() {
            //option A
            $("form").submit(function(e){
                alert('submit intercepted');
                e.preventDefault(e);
            });
        });
</script> -->
<script type="text/javascript">

$(document).ready(function(){
  //$("#confirm_date").val(moment().format("YYYY-MM-DD"));
  //template buyer wise
    $('#order_id').on("change", function(){

        $.ajax({
            url : "{{ url('merch/time_action/templates_list') }}",
            type: 'get',
            data: {
              order_id: $("#order_id").val(),

            },
            success: function(data)
            {
                $('#tna_type').html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });

    });

  // Generate TNA

     var basedon = $(".generatetna");
     var action_place=$(".tna-generate");
      basedon.on("click", function(){

        // Action Element list
        $.ajax({
            url : "{{ url('merch/time_action/tna_generate1') }}",
            type: 'get',
            data: {
              order_id: $("#order_id").val(),
              confirm_date:$("#confirm_date").val(),
              lead_days:$("#lead_days").val(),
              tolerance_days:$("#tolerance_days").val(),
              tna_type: $("#tna_type").val(),
              ok_to_begin:$("#ok_to_begin").val(),
              rev_ok_to_begin:$("#rev_ok_to_begin").val()
            },

            success: function(data)
            {
                action_place.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });

    });
///

});
</script>
@endsection
