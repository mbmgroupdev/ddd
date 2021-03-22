@extends('merch.layout')
@section('title', 'Style BOM')

@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li>
                <li class="active">New Reservation </li>
                <li class="top-nav-btn">
                    <a href='{{ url("merch/reservation/reservation_list") }}' rel='tooltip' data-tooltip-location='top' data-tooltip='Reservation List' type="button" class="btn btn-primary btn-sm margin-5  pull-right">
                        <i class="glyphicon glyphicon-th-list"></i>
                        Reservation List
                    </a>
                </li>

            </ul><!-- /.breadcrumb -->
        </div>
        @include('inc/message')
        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body">
                    
                
                    <form class="form-horizontal" role="form" method="post" action="#" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-3"></div>
                            <div class="col-sm-3">
                                
                            </div>
                            <div class="col-sm-3">
                                
                            </div>
                        </div>
 

                        <div class="form-group">
                            <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="hr_unit_id">Unit<span style="color: red">&#42;</span> </label>
                                {{ Form::select('hr_unit_id', $unitList, null, ['id' => 'hr_unit_id', 'placeholder' => 'Select Unit', 'class' => 'filter', 'data-validation' => 'required']) }}
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="b_id" >Buyer Name </label>
                            {{ Form::select('b_id', $buyerList, null, ['id'=> 'b_id', 'placeholder' => 'Select Buyer', 'class' => 'col-sm-12 col-xs-12 filter', 'data-validation'=> 'required']) }}
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="res_month"> Month<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9 col-xs-9">
                                <input type="text" id="res_month" name="res_month" value="{{ old('res_month') }}" data-validation=" required" placeholder="Month" class="col-sm-4 col-xs-4 monthpicker"/>
                                <label class="col-sm-3 col-xs-3 no-padding-right">Year<span style="color: red">&#42;</span></label>
                                <input type="text" id="res_year" name="res_year" data-validation=" required" value="{{ old('res_year') }}" placeholder="Year" class="col-sm-4 col-xs-4 yearpicker" style="margin-right: 0px;" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="prd_type_id" >Product Type<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9 col-xs-9">
                                {{ Form::select('prd_type_id', $prdtypList, null, ['id'=> 'prd_type_id', 'placeholder' => 'Select Product Type', 'class' => 'col-sm-12 col-xs-12 fileter', 'data-validation'=> 'required']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="res_quantity"> Quantity<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9 col-xs-9">
                                <input type="text" id="res_quantity" name="res_quantity" data-validation="required length number" data-validation-length="1-11" placeholder="Quantity" class="col-sm-3 col-xs-3 smvCalculation"/>
                                <label class="col-sm-4 col-xs-4 no-padding-right control-label no-padding-right" for="res_sewing_smv"> Sewing SMV<span style="color: red">&#42;</span> </label>
                                <input type="text" id="res_sewing_smv" name="res_sewing_smv" data-validation="required number" data-validation-allowing="float" value="{{ old('res_sewing_smv') }}" placeholder="Sewing SMV" class="col-sm-4 col-xs-4 smvCalculation" style="margin-right: 0px;" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="res_sah"> SAH <span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9 col-xs-9">
                                <input type="text" id="res_sah" name="res_sah" placeholder="SAH" class="col-sm-12 col-xs-12" value="{{ old('res_sah') }}" data-validation="required number" data-validation-allowing="float" readonly/>
                            </div>
                        </div>

                        @include('merch.common.save-btn-section')
                        <!-- /.row --> 
                    </form> 
                    <!-- PAGE CONTENT ENDS -->
                </div>
                    
            </div>
            
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        //Total quantity can not be greater than Projected quantity
        $('.smvCalculation').on('keyup', function(){
            var res_sewing_smv = parseInt($("#res_sewing_smv").val());
            var res_quantity= parseInt($("#res_quantity").val());
            if(res_sewing_smv == null) res_sewing_smv=0;
            if(res_quantity == null) res_quantity=0;
            var sah= ((res_sewing_smv*res_quantity)/60).toFixed(2);
            $("#res_sah").val(sah);
        });
    });
</script>
@endsection