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
                  
                <li class="active">Wash Type</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content"> 
           
          <!---Form -->
            
          <!-- -Form 1---------------------->
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6> Wash Type Edit
                                     <a class="pull-right healine-panel" href="{{ url('merch/setup/wash_type') }}" rel="tooltip" data-tooltip=" Wash Type Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                               <!-- Display Erro/Success Message -->
                                @include('inc/message')
                                <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/wash_type_update')}}" enctype="multipart/form-data">
                                {{ csrf_field() }} 
                                <input type="hidden" name="id" value="{{ $wash->id }}">

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="wash_rate" >Category Name<span style="color: red">&#42;</span> </label>
                                        <div class="col-sm-7">
                                            <select id="wash_category" name="wash_category" class="col-xs-12">
                                            <option value="">Select Wash Category</option>
                                            
                                            @foreach($category_name as $cn)
                                            <option value="{{$cn->id}}" @if($s_id == $cn->id) selected @endif >{{$cn->category_name}}</option>
                                            @endforeach
                                           
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="wash_name" >Wash Name<span style="color: red">&#42;</span> </label>
                                        <div class="col-sm-7">
                                            <input type="text" name="wash_name" id="wash_name" placeholder="Wash Name"  class="col-xs-12" value="{{ $wash->wash_name }}"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="wash_rate" >Process Time </label>
                                        <div class="col-sm-7">
                                            <input type="text" name="process_time" id="process_time" placeholder="Process Time"  class="col-xs-12" value="{{ $wash->process_time }}"  />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="wash_rate" >Chemical </label>
                                        <div class="col-sm-7">
                                            <input type="text" name="chemical" id="chemical" placeholder="chemical"  class="col-xs-12" value="{{ $wash->chemical }}"/>
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="wash_rate" >Consumption Rate </label>
                                        <div class="col-sm-7">
                                            <input type="text" name="consumption_rate" id="consumption_rate" placeholder="Wash Rate"  class="col-xs-12" value="{{ $wash->consumption_rate }}"  />
                                        </div>
                                    </div>

                                    @include('merch.common.update-btn-section')                        
                                </form>
                              </div>
                        </div>
                </div>
            </div>
            
        </div><!-- /.page-content -->
    </div>
</div>
@endsection