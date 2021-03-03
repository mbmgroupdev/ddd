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
        <li><a href="#"> Setup </a></li>
        <li class="active">Article Construction & Compostion</li>
      </ul><!-- /.breadcrumb --> 
    </div>

    <div class="page-content"> 
            <!-- row -->
      <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
           <div class="panel panel-info">
                <div class="panel-heading">
                  <h6>Article, Construction & Compostion
                  <a class="pull-right healine-panel" href="{{ url('merch/setup/article') }}" rel="tooltip" data-tooltip="Article Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>    
                </div>
                <div class="panel-body">
                  <!-- Display Erro/Success Message -->
                  @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/article_update') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                  
                     
                      <input type="hidden" name="art_id" value="{{ $articleList->id }}">
                      <input type="hidden" name="typeval" value="{{ $type }}">

                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="sz_id" >Supplier<span style="color: red">&#42;</span></label>
                          <div class="col-sm-8">
                            <input type="text" name="supplier" id="supplier" class="col-xs-12 filter" data-validation = "required",data-validation-optional="true",data-validation-length="1-50" value="{{$articleList->sup_name}}" readonly="readonly" />
                          </div>
                      </div>
           
                    @if($type==1) 
                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="art_name" >Article</label>
                        <div class="col-sm-8">
                         
                          <input type="text" id="art_name" name="art_name" placeholder="Enter Article" class="col-xs-12" value="{{$articleList->art_name}}" />
                       
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="composition" >Composition </label>
                          <div class="col-sm-8">
                            <input type="text" id="composition" name="composition" placeholder="Enter Composition" class="col-xs-12" value="{{$articleList->comp_name}}" />
                          </div>
                      </div> 

                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="art_construction" >Construction</label>
                        <div class="col-sm-8">
                          <input type="text" id="art_construction" name="art_construction" placeholder="Enter Construction" class="col-xs-12" value="{{$articleList->construction_name}}"  />
                        </div>
                      </div>  
                       
                    @endif

                    @if($type==2)            
                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="composition" >Composition </label>
                          <div class="col-sm-8">
                            <input type="text" id="composition" name="composition" placeholder="Enter Composition" class="col-xs-12" value="{{$articleList->comp_name}}" />
                          </div>
                      </div>   
                    @endif   

                    @if($type==3)        
                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="art_construction" >Construction</label>
                        <div class="col-sm-8">
                          <input type="text" id="art_construction" name="art_construction" placeholder="Enter Construction" class="col-xs-12" value="{{$articleList->construction_name}}"  />
                        </div>
                      </div>  
                    @endif   
                      @include('merch.common.update-btn-section')
                    </form>
                </div>
            </div>
        </div>    
     
      </div><!--- /. Row Form 1---->
    </div><!-- /.page-content -->
  </div>
</div>
@endsection