@extends('merch.layout')
@section('title', 'Style Profile')
@section('main-content')
    @push('css')
        <style type="text/css">
            /*#largePreview{position: absolute;display:none;top: 0;height: auto;z-index: 100;box-shadow: 0 0 10px 5px #428BCA;left: 300px;max-width: 800px;}*/

            .slider-container {
                width: 20%;
                height: 152px !important;
                margin: unset;
            }

            .light-box .slider-container {
                width: 90% !important;
                height: 100% !important;
            }

            .steps-div {
                padding-bottom: 20px;
                border-left: 2px solid #d1d1d1;
            }

            .steps {
                list-style: none;
                display: table;
                width: 100%;
                padding: 0;
                margin: 0;
                position: relative;
            }


            .steps > li {
                display: table-cell;
                text-align: center;
                width: 1%;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #039e08;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #5293c4;
            }

            .steps > li:first-child:before {
                max-width: 51%;
                left: 50%;
            }

            .steps > li:before {
                display: block;
                content: "";
                width: 100%;
                height: 1px;
                font-size: 0;
                overflow: hidden;
                border-top: 4px solid #ced1d6;
                position: relative;
                top: 21px;
                z-index: 1;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #039e08;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #5293c4;
            }

            .steps > li .step {
                border: 5px solid #ced1d6;
                color: #546474;
                font-size: 15px;
                border-radius: 100%;
                position: relative;
                z-index: 2;
                display: inline-block;
                width: 40px;
                height: 40px;
            }


            .steps > li .step, .steps > li.complete .step:before {
                line-height: 30px;
                background-color: #fff;
                text-align: center;
            }

            .steps > li.active .title, .steps > li.complete .title {
                color: #2b3d53;
            }

            .steps > li .title {
                display: block;
                margin-top: 4px;
                max-width: 100%;
                color: #949ea7;
                font-size: 14px;
                z-index: 104;
                text-align: center;
                table-layout: fixed;
                word-wrap: break-word;
            }


            .accordion-style2.panel-group .panel-heading .accordion-toggle {
                background-color: #edf3f7;
                border: 2px solid #6eaed1;
                border-width: 0 0 0 2px;
            }

            .accordion-style1.panel-group .panel-heading .accordion-toggle {
                color: #4c8fbd;
                background-color: #eef4f9;
                position: relative;
                font-weight: 700;
                font-size: 13px;
                line-height: 1;
                padding: 10px;
                display: block;
            }

            .accordion-style1.panel-group .panel-heading .accordion-toggle > .ace-icon:first-child {
                width: 16px;
            }

            .bigger-110 {
                font-size: 110% !important;
            }

            .ace-icon {
                text-align: center;
            }

            .glyphicon {
                position: relative;
                top: 1px;
                display: inline-block;
                font-family: 'Glyphicons Halflings';
                font-weight: 400;
                line-height: 1;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .glyphicon, address {
                font-style: normal;
            }

            .profile-picture {
                border: 1px solid #ccc;
                background-color: #fff;
                padding: 4px;
                display: inline-block;
                max-width: 100%;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                box-shadow: 1px 1px 1px rgb(0 0 0 / 15%);
            }

            .label-xlg.arrowed-in-right, .label-xlg.arrowed-right {
                margin-right: 7px;
            }

            .label.arrowed-in-right, .label.arrowed-right {
                position: relative;
                z-index: 1;
            }

            .label.arrowed, .label.arrowed-in {
                position: relative;
                z-index: 1;
            }

            .badge-info, .badge.badge-info, .label-info, .label.label-info {
                background-color: #3a87ad;
            }

            .label.arrowed, .label.arrowed-in {
                margin-left: 5px;
            }

            .label.arrowed-in-right, .label.arrowed-right {
                margin-right: 5px;
            }

            .label {
                margin: 1px;
            }

            .label-xlg {
                padding: .3em .7em .4em;
                font-size: 14px;
                line-height: 1.3;
                height: 28px;
            }

            .label {
                color: #fff;
                display: inline-block;
            }

            .badge.no-radius, .btn.btn-app.no-radius > .badge.no-radius, .btn.btn-app.radius-4 > .badge.no-radius, .label {
                border-radius: 0;
            }

            .badge, .label {
                font-size: 12px;
            }

            .badge, .label {
                font-weight: 400;
                background-color: #abbac3;
                text-shadow: none;
            }

            .width-80 {
                width: 80% !important;
            }

            .label-info {
                background-color: #5bc0de;
            }

            .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                color: #fff;
                border-radius: .25em;
            }

            .badge, .close, .label {
                line-height: 1;
            }

            .badge, .label {
                font-weight: 700;
                white-space: nowrap;
                text-align: center;
            }

            .label, sub, sup {
                vertical-align: baseline;
            }

            html {
                font-size: 10px;
                -webkit-tap-highlight-color: transparent;
            }

            html {
                font-family: sans-serif;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }


            @media screen and (-webkit-min-device-pixel-ratio: 1.2) and (-webkit-max-device-pixel-ratio: 1.3), not all
                .label-xlg.arrowed-in-right:after, .label-xlg.arrowed-in:before {
                    border-width: 14.5px 7px;
                }

                .label-xlg.arrowed-in:before {
                    left: -7px;
                    border-width: 14px 7px;
                }

                .label.arrowed-in:before {
                    left: -5px;
                    border-width: 10px 5px;
                }

                .label-info.arrowed-in:before {
                    border-color: #3a87ad #3a87ad #3a87ad transparent;
                }

                .label.arrowed-in:before {
                    border-color: #abbac3 #abbac3 #abbac3 transparent;
                }

                .label.arrowed-in:before, .label.arrowed:before {
                    display: inline-block;
                    content: "";
                    position: absolute;
                    top: 0;
                    z-index: -1;
                    border: 1px solid transparent;
                    border-right-color: #abbac3;
                }

                .label-xlg {
                    padding: .3em .7em .4em;
                    font-size: 14px;
                    line-height: 1.3;
                    height: 28px;
                }

                .label {
                    line-height: 1.15;
                    height: 20px;
                }

                .white {
                    color: #fff !important;
                }

                select, input[type=email], span, input[type=url], input[type=search], input[type=tel], input[type=color], input[type=text], input[type=password], input[type=datetime], input[type=datetime-local], input[type=date], input[type=month], input[type=time], input[type=week], input[type=number], textarea {
                    font-size: 11px;
                }

                @media screen and (-webkit-min-device-pixel-ratio: 1.2) and (-webkit-max-device-pixel-ratio: 1.3), not all
                    .label-xlg.arrowed-in-right:after, .label-xlg.arrowed-in:before {
                        border-width: 14.5px 7px;
                    }

                    .label-xlg.arrowed-in-right:after {
                        right: -7px;
                        border-width: 14px 7px;
                    }

                    .label.arrowed-in-right:after {
                        right: -5px;
                        border-width: 10px 5px;
                    }

                    .label-info.arrowed-in-right:after {
                        border-color: #3a87ad transparent #3a87ad #3a87ad;
                    }

                    .label.arrowed-in-right:after {
                        border-color: #abbac3 transparent #abbac3 #abbac3;
                    }

                    .label.arrowed-in-right:after, .label.arrowed-right:after {
                        display: inline-block;
                        content: "";
                        position: absolute;
                        top: 0;
                        z-index: -1;
                        border: 1px solid transparent;
                        border-left-color: #abbac3;
                    }

            .profile-contact-info{
                background: #F8FAFC;
                border: 1px black solid;
                margin-top: 10px;
                padding: 10px;
                line-height: 1.8em;
            }

        </style>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
              rel="stylesheet">
        <link href="{{ asset('assets/css/slider.css') }}" rel="stylesheet" type="text/css">
    @endpush
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Merchandising</a>
                    </li>
                    <li>
                        <a href="#">Style</a>
                    </li>
                    <li>
                        <a href="#">Style List</a>
                    </li>
                    <li class="active">Style Profile</li>
                    <li class="top-nav-btn">
                        <button class="btn btn-xs btn-primary pull-right hidden-print"
                                onclick="printDiv('style_no_div','printMe1', 'printMe2','printMe3','printMe4','printMe5', 'printMe6')"
                                style="border-radius: 5px;"><span class="glyphicon glyphicon-print"
                                                                  aria-hidden="true"></span> Print
                        </button>
                    </li>
                </ul><!-- /.breadcrumb -->
            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body" id="printMe">
                            <table hidden="hidden" id="printMe1" class="table table-bordered">
                                <tr>
                                    <td>
                                        <img
                                            src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}"
                                            alt="" width="120px" height="160px">
                                    </td>
                                    <td>
                                        @if(count($styleImages) > 0)
                                            @foreach($styleImages as $styleImage)
                                                <img
                                                    src="{{ asset(!empty($styleImage->image)?$styleImage->image:'assets/images/avatars/profile-pic.jpg') }}"
                                                    width="120px" height="160px">
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 10px;">

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div>
                                                <div class="slider-container">
                                                    <div class="slider-carousel">
                                                        <div class="slider">
                                                            <div class="slide-panel">
                                                                <img class="slide-img"
                                                                     src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}"
                                                                     alt="">
                                                                <div class="slide-overlay"></div>
                                                            </div>
                                                            @if(count($styleImages) > 0)
                                                                @foreach($styleImages as $styleImage)
                                                                    <div class="slide-panel">
                                                                        <img class="slide-img img-responsive"
                                                                             src="{{ asset(!empty($styleImage->image)?$styleImage->image:'assets/images/avatars/profile-pic.jpg') }}">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>

<!--                                                        <div class="slider-controls">
								          <span class="slider-arrow prev-slide">
								            <i class="material-icons">
								              left
								            </i>
								          </span>
                                                            <span class="slider-arrow next-slide">
								            <i class="material-icons">
								              right
								            </i>
								          </span>
                                                            <div class="slideshow-toggle" style="display: none">
                                                                <i class="material-icons play-slideshow">
                                                                    play
                                                                </i>
                                                                <i class="material-icons pause-slideshow">
                                                                    pause
                                                                </i>
                                                            </div>
                                                            <ul class="slide-selector">
                                                                <li class="slide-selected"></li>
                                                                @if(count($styleImages) > 0)
                                                                    @foreach($styleImages as $styleImage)
                                                                        <li></li>
                                                                    @endforeach
                                                                @endif
                                                            </ul>
                                                        </div>-->
                                                    </div>
                                                </div>

<!--                                                <div class="slider-modal">
                                                    <span class="slider-close">&times;</span>
                                                    <div class="modal-slide light-box"></div>
                                                </div>-->
                                                <div class="space-4"></div>
                                                <div style="margin-top: 85px"
                                                    class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                                    <div class="inline position-relative" id="style_no_div">
                                                        <a href="#" class="user-title-label">
                                                                    <span
                                                                        class="white">{{ (!empty($style->stl_no)?$style->stl_no:null) }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-6"></div>
                                            <div class="profile-contact-info" id="printMe2">
                                                <div class="profile-contact-links align-left">
                                                    <p style="text-align: center;"><strong>Production
                                                            Type:</strong> {{ (!empty($style->stl_type)?($style->stl_type == 'D' ? 'Development' : 'Bulk'):null) }}
                                                    </p>
                                                    <p style="text-align: center;">
                                                        <strong>Operation:</strong> {{ (!empty($operations->name)?$operations->name:null) }}
                                                    </p>
                                                    <p style="text-align: center;">
                                                        <strong>Buyer:</strong> {{ (!empty($style->b_name)?$style->b_name:null) }}
                                                    </p>
                                                    <p style="text-align: center;">
                                                        <strong>SMV/PC:</strong> {{ (!empty($style->stl_smv)?$style->stl_smv:null) }}
                                                    </p>
                                                    <p style="text-align: center;"><strong>Speacial
                                                            Machine:</strong> {{ (!empty($machines->name)?$machines->name:null) }}
                                                    </p>
                                                    <p style="text-align: center;"><strong>Style Reference
                                                            2:</strong> {{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}
                                                    </p>
                                                    <p style="text-align: center;"><strong>Sample
                                                            Type:</strong> {{ (!empty($samples->name)?$samples->name:null) }}
                                                    </p>
                                                    <p style="text-align: center;">
                                                        <strong>Remarks:</strong> {{ (!empty($style->stl_description)?$style->stl_description:null) }}
                                                    </p>
                                                    <p style="text-align: center;"><strong>Total
                                                            Order:</strong> {{ count($orders) }} </p>
                                                    <p style="text-align: center;"><strong>Total Order
                                                            Qty:</strong>
                                                        <?php
                                                        $totalQty = 0;
                                                        if (isset($orders)) {
                                                            foreach ($orders as $Count) {
                                                                $totalQty += $Count->order_qty;
                                                            }
                                                        }
                                                        ?>
                                                        {{ $totalQty }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <!-- style steps -->
                                            {!!$style_steps!!}
                                            <div id="accordion"
                                                 class="accordion-style1 panel-group accordion-style2">
                                                <!-- Basic Information -->
                                                <div class="panel panel-info">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle" data-toggle="collapse"
                                                               data-parent="#accordion" href="#basicInfo"
                                                               aria-expanded="false">
                                                                <i class="bigger-110 las la-plus-circle"
                                                                   data-icon-hide="las la-minus-circle"
                                                                   data-icon-show="las la-plus-circle"></i>
                                                                BOM Information
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse multi-collapse in collapse show"
                                                         id="basicInfo" aria-expanded="true" style="">
                                                        <div class="panel-body table-responsive">
                                                            <div class="profile-user-info">
                                                                <div class="widget-body">
                                                                    <div class="row" id="printMe3">
                                                                        <div class="col-sm-12">
                                                                            <table
                                                                                class="table custom-font-table"
                                                                                width="50%" cellpadding="0"
                                                                                cellspacing="0" border="0">
                                                                                <tr>
                                                                                    <th>Production Type</th>
                                                                                    <td>{{ (!empty($style->stl_type)?($style->stl_type == 'd' ? 'Development' : 'Bulk'):null) }}</td>
                                                                                    <th>Style Reference 1</th>
                                                                                    <td>{{ (!empty($style->stl_no)?$style->stl_no:null) }}</td>
                                                                                    <th>Operation</th>
                                                                                    <td>{{ (!empty($operations->name)?$operations->name:null) }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Buyer</th>
                                                                                    <td>{{ (!empty($style->b_name)?$style->b_name:null) }}</td>
                                                                                    <th>SMV/PC</th>
                                                                                    <td>{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
                                                                                    <th>Speacial Machine</th>
                                                                                    <td>{{ (!empty($machines->name)?$machines->name:null) }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Style Reference 2</th>
                                                                                    <td>{{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}</td>
                                                                                    <th>Sample Type</th>
                                                                                    <td>{{ (!empty($samples->name)?$samples->name:null) }}</td>
                                                                                    <th>Remarks</th>
                                                                                    <td>{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <br>
                                                                <div class="widget-body" id="printMe4">
                                                                    <table id="bomItemTable"
                                                                           class="custom-font-table table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Main Category</th>
                                                                            <th>Item</th>
                                                                            <th>Item Code</th>
                                                                            <th>Description</th>
                                                                            <th width="80">Color</th>
                                                                            <th>Size/Width</th>
                                                                            <th width="80">Supplier</th>
                                                                            <th width="80">Article</th>
                                                                            <th>Composition</th>
                                                                            <th>Construction</th>
                                                                            <th width="80">UoM</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra (%)</th>
                                                                            <th>Extra Qty</th>
                                                                            <th>Total</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php if(count($styleCatMcats) == 0){ ?>
                                                                        <tr>
                                                                            <td colspan="15"><h4
                                                                                    class="text-center">No BOM
                                                                                    found for this style</h4>
                                                                            </td>
                                                                        </tr>
                                                                        <?php }else{ ?>
                                                                        <?php
                                                                        foreach ($styleCatMcats as $styleCatMcat) {?>
                                                                        <tr>
                                                                            <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_code}}</td>
                                                                            <td>{{ $styleCatMcat->item_description}}</td>
                                                                            <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                            <td>{{ $styleCatMcat->size}}</td>
                                                                            <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                            <td>{{ $styleCatMcat->comp_name}}</td>
                                                                            <td>{{ $styleCatMcat->construction_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                            <td>{{ $styleCatMcat->consumption}}</td>
                                                                            <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                            <td><?= $styleCatMcat->extra_percent / 100 ?></td>
                                                                            <td><?= $styleCatMcat->extra_percent != 0 ? $styleCatMcat->consumption + $styleCatMcat->extra_percent / 100 : 0  ?></td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <br>
                                                                    <br>
                                                                </div><!-- /.col -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Advance Information -->
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed"
                                                               data-toggle="collapse" data-parent="#accordion"
                                                               href="#advanceInfo">
                                                                <i class="bigger-110 las la-plus-circle"
                                                                   data-icon-hide="las la-minus-circle"
                                                                   data-icon-show="las la-plus-circle"></i>
                                                                Costing Information
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse multi-collapse"
                                                         id="advanceInfo">
                                                        <div id="costing" class="panel-body table-responsive">
                                                            <div class="profile-user-info">
                                                                <div class="widget-body" id="printMe5">
                                                                    <table id="bomCostingTable"
                                                                           class="custom-font-table table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Main Category</th>
                                                                            <th>Item</th>
                                                                            <th>Item Code</th>
                                                                            <th>Description</th>
                                                                            <th>Color</th>
                                                                            <th>Size / Width</th>
                                                                            <th>Article</th>
                                                                            <th>Composition</th>
                                                                            <th>Construction</th>
                                                                            <th>Supplier</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra (%)</th>
                                                                            <th>Unit</th>
                                                                            <th>Terms</th>
                                                                            <th>FOB</th>
                                                                            <th>L/C</th>
                                                                            <th>Freight</th>
                                                                            <th>Unit Price</th>
                                                                            <th>Total Price</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php if(!empty($other_cost)){?>
                                                                        <?php if($other_cost->agent_fob != 0){?>
                                                                        <?php if(count($styleCatMcatFabs) > 0){?>
                                                                        <tr>

                                                                            <?php
                                                                            $netFab = 0;
                                                                            foreach ($styleCatMcatFabs as $styleCatMcat) {
                                                                            $thisUnit = $styleCatMcat->precost_unit_price;
                                                                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                                                                            $netFab = $netFab + $thisTotal;
                                                                            ?>
                                                                            <td> {{ $styleCatMcat->mcat_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_code}}</td>
                                                                            <td>{{ $styleCatMcat->item_description}}</td>
                                                                            <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                            <td>{{ $styleCatMcat->size}}</td>
                                                                            <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                            <td>{{ $styleCatMcat->comp_name}}</td>
                                                                            <td>{{ $styleCatMcat->construction_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                            <td>{{ $styleCatMcat->consumption}}</td>
                                                                            <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                            <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                            <td>{{ $styleCatMcat->bom_term}}</td>
                                                                            <td>{{ $styleCatMcat->precost_fob}}</td>
                                                                            <td>{{ $styleCatMcat->precost_lc}}</td>
                                                                            <td>{{ $styleCatMcat->precost_freight}}</td>
                                                                            <td>{{ $thisUnit}}</td>
                                                                            <th>{{ $thisTotal}}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <tr>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center"> Total
                                                                                Fabric Price
                                                                            </th>
                                                                            <th>{{ $netFab }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(count($styleCatMcatSwings) > 0){?>
                                                                        <tr>
                                                                            <?php
                                                                            $netSwings = 0;
                                                                            foreach ($styleCatMcatSwings as $styleCatMcat) {
                                                                            $thisUnit = $styleCatMcat->precost_unit_price;
                                                                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                                                                            $netSwings = $netSwings + $thisTotal;
                                                                            ?>
                                                                            <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->item_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->item_code}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->item_description}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->clr_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->size}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->art_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->comp_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->construction_name}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->sup_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->consumption}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->extra_percent}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->uom}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->bom_term}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->precost_fob}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->precost_lc}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->precost_freight}}</td>
                                                                            <th>
                                                                            {{ $thisUnit }} </td>
                                                                            <th>{{ $thisTotal }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <tr>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center"> Total
                                                                                Sweing Accessories Price
                                                                            </th>
                                                                            <th>{{ $netSwings }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(count($styleCatMcatFinishing) > 0){?>
                                                                        <tr>
                                                                            <?php
                                                                            $netFinishing = 0;
                                                                            foreach ($styleCatMcatFinishing as $styleCatMcat) {
                                                                            $thisUnit = $styleCatMcat->precost_unit_price;
                                                                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                                                                            $netFinishing = $netFinishing + $thisTotal;
                                                                            ?>
                                                                            <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_code}}</td>
                                                                            <td>{{ $styleCatMcat->item_description}}</td>
                                                                            <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                            <td>{{ $styleCatMcat->size}}</td>
                                                                            <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                            <td>{{ $styleCatMcat->comp_name}}</td>
                                                                            <td>{{ $styleCatMcat->construction_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                            <td>{{ $styleCatMcat->consumption}}</td>
                                                                            <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                            <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                            <td>{{ $styleCatMcat->bom_term}}</td>
                                                                            <td>{{ $styleCatMcat->precost_fob}}</td>
                                                                            <td>{{ $styleCatMcat->precost_lc}}</td>
                                                                            <td>{{ $styleCatMcat->precost_freight}}</td>
                                                                            <td>{{ $thisUnit }} </td>
                                                                            <th>{{ $thisTotal }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <tr>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center"> Total
                                                                                Finising Price
                                                                            </th>
                                                                            <th>{{ $netFinishing }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(count($special_operations) > 0) {?>
                                                                        <?php foreach ($special_operations as $special_operation) {?>
                                                                        <tr>
                                                                            <td colspan='10'
                                                                                class='text-center'>{{$special_operation->opr_name}}</td>
                                                                            <td>1</td>
                                                                            <td>0</td>
                                                                            <td>
                                                                                {{$special_operation->uom}}
                                                                            </td>
                                                                            <td colspan='4'></td>
                                                                            <td>
                                                                                {{$special_operation->unit_price}}
                                                                            </td>
                                                                            <td>
                                                                                {{$special_operation->unit_price}}
                                                                            </td>
                                                                        </tr>
                                                                        <?php } }?>
                                                                        <tr>
                                                                            <td colspan="10"
                                                                                class="text-center">Testing Cost
                                                                            </td>
                                                                            <td class="consumption">1</td>
                                                                            <td>0</td>
                                                                            <td>Piece</td>
                                                                            <td colspan="4"></td>
                                                                            <td>
                                                                                {{ isset($other_cost->testing_cost)?$other_cost->testing_cost:''}}
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->testing_cost)?$other_cost->testing_cost :''}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10"
                                                                                class="text-center">CM
                                                                            </td>
                                                                            <td class="consumption">1</td>
                                                                            <td>0</td>
                                                                            <td>Piece</td>
                                                                            <td colspan="4"></td>
                                                                            <td>
                                                                                {{ isset($other_cost->cm)?$other_cost->cm:''}}
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->cm)?$other_cost->cm:''}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10" class="text-right">
                                                                                Commertial Cost
                                                                            </td>
                                                                            <td>
                                                                            </td>
                                                                            <td colspan="6"
                                                                                class="text-left"></td>
                                                                            <td>
                                                                                {{ isset($other_cost->commercial_cost)?$other_cost->commercial_cost:''}}
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->commercial_cost)?$other_cost->commercial_cost:''}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Net FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->net_fob)?$other_cost->net_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10" class="text-right">
                                                                                Buyer Commision
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->buyer_comission_percent)?$other_cost->buyer_comission_percent:''}}
                                                                            </td>
                                                                            <td colspan="6" class="text-left">
                                                                                %
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $buyer_comm = number_format(($other_cost->net_fob * ($other_cost->buyer_comission_percent / 100)), 2, '.', '');
                                                                                ?>
                                                                                {{ $buyer_comm }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $buyer_comm }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Buyer FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->buyer_fob)?$other_cost->buyer_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10" class="text-right">
                                                                                Agent Commision
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->agent_comission_percent)?$other_cost->agent_comission_percent:''}}
                                                                            </td>
                                                                            <td colspan="6" class="text-left">
                                                                                %
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $agent_comm = number_format(($other_cost->buyer_fob * ($other_cost->agent_comission_percent / 100)), 2, '.', '');
                                                                                ?>
                                                                                {{ $agent_comm }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $agent_comm }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Agent FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->agent_fob)?$other_cost->agent_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Total FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->agent_fob)?$other_cost->agent_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <?php }else{?>
                                                                        <tr>
                                                                            <td colspan="19"><h4
                                                                                    class="text-center">No
                                                                                    Costing found for this
                                                                                    style</h4></td>
                                                                        </tr>
                                                                        <?php    } }else{ ?>
                                                                        <tr>
                                                                            <td colspan="19"><h4
                                                                                    class="text-center">No
                                                                                    Costing found for this
                                                                                    style</h4></td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <br>
                                                                    <br>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Education History  -->
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed"
                                                               data-toggle="collapse" data-parent="#accordion"
                                                               href="#EducationHistory">
                                                                <i class="bigger-110 las la-plus-circle"
                                                                   data-icon-hide="las la-minus-circle"
                                                                   data-icon-show="las la-plus-circle"></i>
                                                                Order Information
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse multi-collapse"
                                                         id="EducationHistory">
                                                        <div class="panel-body">
                                                            <div class="widget-body" id="printMe6">
                                                                <table id="bomItemTable"
                                                                       class="custom-font-table table table-bordered">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Order Code</th>
                                                                        <th>Order Referance No</th>
                                                                        <th>Order Quantity</th>
                                                                        <th>Order Delivery Date</th>
                                                                        <th width="80">Order Status</th>
                                                                        <th>Buyer Name</th>
                                                                        <th width="80">Brand Name</th>
                                                                        <th width="80">Season Name</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php if(count($orders) == 0){ ?>
                                                                    <tr>
                                                                        <td colspan="8"><h4 class="text-center">
                                                                                No Order found for this
                                                                                style</h4></td>
                                                                    </tr>
                                                                    <?php }else{ ?>
                                                                    <?php foreach ($orders as $order) {
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <a href="{{url('merch/orders/order_profile_show',$order->order_id)}}">{{ $order->order_code}}</a>
                                                                        </td>
                                                                        <th>{{ $order->order_ref_no}}</th>
                                                                        <th>{{ $order->order_qty}}</th>
                                                                        <th>{{ $order->order_delivery_date}}</th>
                                                                        <th width="80">{{ $order->order_status}}</th>
                                                                        <th>{{ $order->b_name}}</th>
                                                                        <th width="80">{{ $order->br_name}}</th>
                                                                        <th width="80">{{ $order->se_name}}</th>
                                                                    </tr>
                                                                    <?php } ?>
                                                                    <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                                <br>
                                                                <br>
                                                            </div><!-- /.col -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- PAGE CONTENT ENDS -->
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div>
                    </div>
                </div><!-- /.page-content -->
            </div>
        </div>
    </div>
    @push('js')
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
        <script src="{{ asset('assets/js/slider.js')}}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                /*
                * NEW BOM ITEM
                * -----------------------
                */
                function getItem(item_id, category_id) {
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_item_data') }}",
                        data: {item_id, category_id},
                        success: function (data) {
                            $("#bomItemTable tbody").append(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });
                }

                var modal = $("#newBomModal");
                $("body").on("click", "#newBomModalDone", function (e) {

                    // ------table actions----------
                    var table_item = [];
                    $("body #bomItemTable > tbody > tr").each(function (i, v) {
                        table_item.push($(this).attr("id"));
                    });

                    //-------- modal actions ------------------
                    modal.find('.modal-body input[type=checkbox]').each(function (i, v) {
                        var item = $(this).val();
                        var category = $(this).prev().val();
                        //--------------------------------------
                        if ($(this).prop("checked") == true) {
                            if (table_item.length == 0) {
                                getItem(item, category);
                            } else if (table_item.includes(item) == false) {
                                getItem(item, category);
                            }
                        } else {
                            $("body #bomItemTable > tbody").find('tr[id="' + item + '"]').remove();
                        }
                    });
                    modal.modal('hide');
                });

                // get item

                /*
                * --------CALCULATE TOTAL---------
                */
                $("body").on("keyup", ".calc", function () {
                    var consumption = $(this).parent().parent().find(".consumption").val();
                    var extra = $(this).parent().parent().find(".extra").val();
                    var qty = parseFloat(((parseFloat(consumption) / 100) * parseFloat(extra))).toFixed(2);
                    var total = (parseFloat(qty) + parseFloat(consumption)).toFixed(2);
                    $(this).parent().parent().find(".qty").val(qty);
                    $(this).parent().parent().find(".total").val(total);
                });


                /*
                * GET ARTICLE, COMPOSITION AND CONSTRUCTION
                * -------------------------------------------
                */
                $("body").on("change", ".supplier", function () {
                    var that = $(this);
                    // load article
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_article_by_supplier') }}",
                        data: {
                            "supplier_id": that.val(),
                            "name": "mr_article_id[]",
                            "selected": "",
                            "option": {
                                "class": "form-control input-sm no-select",
                                "placeholder": "Select"
                            }
                        },
                        success: function (data) {
                            that.parent().next().html(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });

                    // load composition
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_composition_by_supplier') }}",
                        data: {
                            "supplier_id": that.val(),
                            "name": "mr_composition_id[]",
                            "selected": "",
                            "option": {
                                "class": "form-control input-sm no-select",
                                "placeholder": "Select"
                            }
                        },
                        success: function (data) {
                            that.parent().next().next().html(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });

                    // load construction
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_construction_by_supplier') }}",
                        data: {
                            "supplier_id": that.val(),
                            "name": "mr_construction_id[]",
                            "selected": "",
                            "option": {
                                "class": "form-control input-sm no-select",
                                "placeholder": "Select",
                                "data-validation": "required"
                            }
                        },
                        success: function (data) {
                            that.parent().next().next().next().html(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });
                });


                /*
                * NEW ARTICLE
                * -------------------------------------------
                */
                $('.newArticleModal').on('show.bs.modal', function (e) {
                    var modal = $(this);
                    var button = $(e.relatedTarget);
                    var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
                    var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
                    modal.find("#supplier_id").val(supplier_id);
                    modal.find("#supplier_name").val(supplier_name);

                    // new article
                    $("#newArticle").on("submit", function (e) {
                        e.preventDefault();
                        var that = $(this);
                        $.ajax({
                            url: "{{ url('merch/style_bom/new_article') }}",
                            dataType: "json",
                            data: {
                                "supplier_id": that.find("#supplier_id").val(),
                                "article_name": that.find("#article_name").val(),
                                "name": "mr_article_id[]",
                                "selected": "",
                                "option": {
                                    "class": "form-control input-sm no-select article",
                                    "placeholder": "Select"
                                }
                            },
                            success: function (data) {
                                if (data.status) {
                                    button.parent().parent().parent().html(data.result);
                                    modal.find("#supplier_id").val("");
                                    modal.find("#supplier_name").val("");
                                    modal.find("#article_name").val("");
                                    modal.find(".message").html("");
                                    $('.newArticleModal').modal('hide');
                                    that.unbind('submit');
                                } else {
                                    modal.find(".message").html("<div class='alert alert-danger'>" + data.message + "</div>");
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });

                /*
                * NEW COMPOSITION
                * -------------------------------------------
                */
                $('.newCompositionModal').on('show.bs.modal', function (e) {
                    var modal = $(this);
                    var button = $(e.relatedTarget);
                    var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
                    var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
                    modal.find("#supplier_id").val(supplier_id);
                    modal.find("#supplier_name").val(supplier_name);

                    // new article
                    $("#newComposition").on("submit", function (e) {
                        e.preventDefault();
                        var that = $(this);
                        $.ajax({
                            url: "{{ url('merch/style_bom/new_composition') }}",
                            dataType: "json",
                            data: {
                                "supplier_id": that.find("#supplier_id").val(),
                                "composition_name": that.find("#composition_name").val(),
                                "name": "mr_composition_id[]",
                                "selected": "",
                                "option": {
                                    "class": "form-control input-sm no-select article",
                                    "placeholder": "Select"
                                }
                            },
                            success: function (data) {
                                if (data.status) {
                                    button.parent().parent().parent().html(data.result);
                                    modal.find("#supplier_id").val("");
                                    modal.find("#supplier_name").val("");
                                    modal.find("#composition_name").val("");
                                    modal.find(".message").html("");
                                    $('.newCompositionModal').modal('hide');
                                    that.unbind('submit');
                                } else {
                                    modal.find(".message").html("<div class='alert alert-danger'>" + data.message + "</div>");
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });


                /*
                * NEW CONSTRUCTION
                * -------------------------------------------
                */
                $('.newConstructionModal').on('show.bs.modal', function (e) {
                    var modal = $(this);
                    var button = $(e.relatedTarget);
                    var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
                    var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
                    modal.find("#supplier_id").val(supplier_id);
                    modal.find("#supplier_name").val(supplier_name);

                    // new article
                    $("#newConstruction").on("submit", function (e) {
                        e.preventDefault();
                        var that = $(this);
                        $.ajax({
                            url: "{{ url('merch/style_bom/new_construction') }}",
                            dataType: "json",
                            data: {
                                "supplier_id": that.find("#supplier_id").val(),
                                "construction_name": that.find("#construction_name").val(),
                                "name": "mr_construction_id[]",
                                "selected": "",
                                "option": {
                                    "class": "form-control input-sm no-select article",
                                    "placeholder": "Select"
                                }
                            },
                            success: function (data) {
                                if (data.status) {
                                    button.parent().parent().parent().html(data.result);
                                    modal.find("#supplier_id").val("");
                                    modal.find("#supplier_name").val("");
                                    modal.find("#construction_name").val("");
                                    modal.find(".message").html("");
                                    $('.newConstructionModal').modal('hide');
                                    that.unbind('submit');
                                } else {
                                    modal.find(".message").html("<div class='alert alert-danger'>" + data.message + "</div>");
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });

                /*
                * COLOR LIST WITH BACKGROUND COLOR
                * -------------------------------------------
                */
                $("body").on("click", "select.color", function () {
                    $("body select.color option").each(function (i, v) {
                        $(this).css('background-color', $(this).text());
                    });
                    $(this).css('background-color', $(this).find('option:selected').text());
                });

            });
            // function myFunction() {
            // 	$('#accordion .collapse').addClass("in");
            // 	//document.body.style.visibility = 'hidden';
            // 	//var WinPrint = window.open('', '', 'width=900,height=650');
            // 	$('#costing ').removeClass("table-responsive");
            // 		window.print();
            //     location.reload();

            //  }


        </script>
        <!--        <style type="text/css">
                    table {
                        width: 300px;
                    }
                </style>-->
        {{-- <script>
        $(document).ready(function(){
          $(".profile-picture").click(function(){
            $('#largePreview').css("display", "block");
            }, function(){
            $('#largePreview').css("display", "none");
          });
        });
        </script> --}}
    @endpush
@endsection
