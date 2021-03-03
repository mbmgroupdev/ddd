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
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Garments Type </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 

            <div class="row">
                  <!-- Display Erro/Success Message -->
                <div class="col-sm-6 col-sm-offset-3">
                    @include('inc/message')
                    <div class="panel panel-success">
                        <div class="panel-heading">
                          <h6>Garments Type Edit <a class="pull-right healine-panel" href="{{ url('merch/setup/garments_type') }}" rel="tooltip" data-tooltip="Garments Type List/Create" data-tooltip-location="top"><i class="fa fa-list"></i></a></h6>
                        </div>
                        <div class="panel-body">
                            <!-- PAGE CONTENT BEGINS --> 
                            {{ Form::open(["url"=>"merch/setup/garments_type_update", "class"=>"form-horizontal"]) }}

                                <input type="hidden" name="gmt_id" value="{{ $garment->gmt_id }}">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="prd_type_id"> Style Type<span style="color: red">&#42;</span>  </label>
                                    <div class="col-sm-9"> 
                                        {{ Form::select('prd_type_id', $productList, $garment->prd_id, ['placeholder'=>'Select Style Type', 'id'=>'prd_type_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Style Type field is required']) }}  
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="gmt_name" > Garment Type<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="gmt_name" id="gmt_name" placeholder="Garment Type" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" value="{{ $garment->gmt_name }}" />
                                    </div>
                                </div>  

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="gmt_remarks"> Remarks</label>
                                    <div class="col-sm-9">
                                        <textarea multiple="multiple" name="gmt_remarks" id="gmt_remarks" class="form-control" placeholder="Remarks"  data-validation="length custom" data-validation-length="1-128" data-validation-optional="true">{{ $garment->gmt_remarks }}</textarea>
                                    </div>
                                </div>
         
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                @include('merch.common.update-btn-section')
                                <!-- /.row --> 
                            </form> 
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                    </div>
                </div> 
            </div>
        </div><!-- /.page-content -->
    </div>
</div> 
@endsection