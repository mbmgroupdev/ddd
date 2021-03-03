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
                  
                <li class="active">Wash Category</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content"> 
          <!---Form -->
            
          <!-- -Form 1---------------------->
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6> Wash Category
                                     <a class="pull-right healine-panel" href="{{ url('merch/setup/wash_category') }}" rel="tooltip" data-tooltip=" Wash Category Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                    <!-- Display Erro/Success Message -->
                                    @include('inc/message')
                                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/wash_category_update')}}" enctype="multipart/form-data">
                                    {{ csrf_field() }} 
                                    <input type="hidden" name="id" value="{{ $wash->id }}">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="wash_category" >Category Name<span style="color: red">&#42;</span> </label>
                                            <div class="col-sm-9">
                                                <input type="text" name="wash_category" id="wash_category" placeholder="Category Name"  class="col-xs-12" value="{{ $wash->category_name }}" data-validation="required length custom" data-validation-length="1-45" />
                                            </div>
                                        </div>

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