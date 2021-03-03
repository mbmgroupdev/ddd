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
                  <li>
                    <a href="#"> Materials </a>
                </li>
                <li class="active">Size Update</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content"> 
            <div class="page-header">
                <h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Materials <i class="ace-icon fa fa-angle-double-right"></i> Size Update</small></h1>
            </div>
            
            <div class="row">
                @include('inc/message')
                <div class="col-sm-2"></div>
                <div class="col-sm-6">
                    <h5 class="page-header">Material Size Edit</h5>
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/size_update') }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                        <input type="hidden" name="sz_id" value="{{ $sizeList->sz_id }}">
                        <div class="form-group">
                         <label class="col-sm-3 control-label no-padding-right" for="matitem_id" >Item <span style="color: red">&#42;</span></label>
                            <div class="col-sm-9">
                               {{ Form::select('matitem_id', $itemList, $sizeList->matitem_id, [ 'id'=> 'matitem_id', 'placeholder' => 'Select Item', 'class' => 'col-xs-12 filter', 'data-validation' => 'required']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="sz_name" >Size<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9">
                                <input type="text" id="sz_name" name="sz_name" placeholder="Enter Size Name" class="col-xs-12" value="{{ $sizeList->sz_name }}" data-validation="required length custom" data-validation-length="1-50" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="sz_code" >Size Code<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9">
                                <input type="text" id="sz_code" name="sz_code" placeholder="Enter Code" class="col-xs-12" value="{{ $sizeList->sz_code }}" data-validation="required length custom" data-validation-length="1-50" />
                            </div>
                        </div>
                     
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="sz_description" > Description <span style="color: red">&#42;</span></label>
                            <div class="col-sm-9">
                                <textarea name="sz_description" id="sz_description" class="col-xs-12" placeholder="Enter Size Description"  data-validation="required length" data-validation-length="0-128">{{ $sizeList->sz_description }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="sz_inseam" >Inseam</label>
                            <div class="col-sm-9">
                                <input type="text" id="sz_inseam" name="sz_inseam" placeholder="Enter Inseam" class="col-xs-12" value="{{ $sizeList->sz_inseam }}" data-validation="required length custom" data-validation-length="1-50" data-validation-optional="true" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="sz_lenght" >Length</label>
                            <div class="col-sm-9">
                                <input type="text" id="sz_lenght" name="sz_lenght" placeholder="Enter Length" class="col-xs-12" value="{{ $sizeList->sz_lenght }}" data-validation="required length custom" data-validation-length="1-50" data-validation-optional="true"/>
                            </div>
                        </div>   
                        
                 
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-3 col-md-9"> 
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <button class="btn" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form> 
                   
                </div>     
                <!-- /.col -->
            </div><!--- /. Row  Form 3---->
            
        </div><!-- /.page-content -->
    </div>
</div>
@endsection