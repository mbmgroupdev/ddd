@extends('merch.layout')
@section('title', 'Order TNA')
@section('main-content')
    @push('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            /*Bootstrap Calendar*/
            .datepicker {
                border-radius: 0;
                padding: 0;
            }
            .datepicker-days table thead, .datepicker-days table tbody, .datepicker-days table tfoot {
                padding: 10px;
                display: list-item;
            }
            .datepicker-days table thead, .datepicker-months table thead, .datepicker-years table thead, .datepicker-decades table thead, .datepicker-centuries table thead {
                background: #3546b3;
                color: #ffffff;
                border-radius: 0;
            }
            .datepicker-days table thead tr:nth-child(2n+0) td, .datepicker-days table thead tr:nth-child(2n+0) th {
                border-radius: 3px;
            }
            .datepicker-days table thead tr:nth-child(3n+0) {
                text-transform: uppercase;
                font-weight: 300 !important;
                font-size: 12px;
                color: rgba(255, 255, 255, 0.7);
            }
            .table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th {
                padding: 11px 13px;
            }
            .datepicker-months table thead td, .datepicker-months table thead th, .datepicker-years table thead td, .datepicker-years table thead th, .datepicker-decades table thead td, .datepicker-decades table thead th, .datepicker-centuries table thead td, .datepicker-centuries table thead th {
                border-radius: 0;
            }
            .datepicker td, .datepicker th {
                border-radius: 50%;
                padding: 0 12px;
            }
            .datepicker-days table thead, .datepicker-months table thead, .datepicker-years table thead, .datepicker-decades table thead, .datepicker-centuries table thead {
                background: #3546b3;
                color: #ffffff;
                border-radius: 0;
            }
            .datepicker table tr td.active, .datepicker table tr td.active:hover, .datepicker table tr td.active.disabled, .datepicker table tr td.active.disabled:hover {
                background-image: none;
            }
            .datepicker .prev, .datepicker .next {
                color: rgba(255, 255, 255, 0.5);
                transition: 0.3s;
                width: 37px;
                height: 37px;
            }
            .datepicker .prev:hover, .datepicker .next:hover {
                background: transparent;
                color: rgba(255, 255, 255, 0.99);
                font-size: 21px;
            }
            .datepicker .datepicker-switch {
                font-size: 24px;
                font-weight: 400;
                transition: 0.3s;
            }
            .datepicker .datepicker-switch:hover {
                color: rgba(255, 255, 255, 0.7);
                background: transparent;
            }
            .datepicker table tr td span {
                border-radius: 2px;
                margin: 3%;
                width: 27%;
            }
            .datepicker table tr td span.active, .datepicker table tr td span.active:hover, .datepicker table tr td span.active.disabled, .datepicker table tr td span.active.disabled:hover {
                background-color: #3546b3;
                background-image: none;
            }
            .dropdown-menu {
                border: 1px solid rgba(0,0,0,.1);
                box-shadow: 0 6px 12px rgba(0,0,0,.175);
            }
            .datepicker-dropdown.datepicker-orient-top:before {
                border-top: 7px solid rgba(0,0,0,.1);
            }

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
                                      <input type="text" name="confirm_date" id="confirm_date" style="padding-left: 10px" class="datepicker col-xs-12 form-control" value="" data-validation="required" autocomplete="off" placeholder="Y-m-d" />
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

                                        <input type="text" name="ok_to_begin" id="ok_to_begin" style="padding-left: 10px" class="datepicker col-xs-12 form-control" value="" data-validation="required" autocomplete="off" placeholder="Y-m-d" />
                                      <label  for="ok_to_begin" >OK to Begin </label>
                                      <div id="msg" class="col-sm-9 pull-right" style="color: red"></div>
                                  </div>
                                  <div class="form-group has-float-label has-required">


                                            <input type="text" name="rev_ok_to_begin" style="padding-left: 10px" id="rev_ok_to_begin" class="datepicker col-xs-12 form-control" value="" data-validation="required" autocomplete="off" placeholder="Y-m-d" />
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">


$(document).ready(function(){

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        clearBtn: true,
        todayHighlight: true,
        orientation: 'bottom'
    });
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
