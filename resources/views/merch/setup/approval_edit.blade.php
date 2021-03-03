@extends('merch.index')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
              <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising Update</a>
                </li>
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Approval Hierarchy Update</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6> Approval Hierarchy Update
                                     <a class="pull-right healine-panel" href="{{ url('merch/setup/approval') }}" rel="tooltip" data-tooltip="Approval Hierarchy Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                  <!-- Display Erro/Success Message -->
                                @include('inc/message')
                                <div class="hidden output"></div>
                                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/approval_update')  }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="type"> Approval Type<span style="color: red">&#42;</span> </label>
                                            <div class="col-sm-9">
                                                {{ Form::select('type',array('Style Costing' => 'Style Costing', 'Order Costing' => 'Order Costing','Supplier Approval' => 'Supplier Approval'), $approval->mr_approval_type, ['placeholder'=>'Select Category Name','id'=>'mcat_id','class'=> 'col-xs-12' , 'disabled' => true]) }}
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="type"> Unit<span style="color: red">&#42;</span> </label>
                                            <div class="col-sm-9">

                                                {{ Form::select('unit',$unitList, $approval->unit, ['placeholder'=>'Select Unit','id'=>'unit','class'=> 'col-xs-12', 'data-validation' => 'required']) }}
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="level1" > Level 1<span style="color: red">&#42;</span> </label>
                                            <div class="col-sm-9">
                                                <select name="level1" class='associates col-xs-12 col-sm-12'>
                                                <option value="{{$approval1->associate_id}}">{{$approval1->associate_id}}-{{$approval1->as_name}}</option>
                                                 @foreach($employee as $key => $emp)
                                                 <option value="{{$key}}">{{$key}}-{{$emp}}</option>
                                                 @endforeach
                                            </select>
                                           </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="level2" >  Level 2<span style="color: red">&#42;</span></label>
                                            <div class="col-sm-9">
                                                <select name="level2" class='associates col-xs-12 col-sm-12'>
                                                    <option value="{{$approval2->associate_id}}">{{$approval2->associate_id}}-{{$approval2->as_name}}</option>
                                                     @foreach($employee as $key => $emp)
                                                     <option value="{{$key}}">{{$key}}-{{$emp}}</option>
                                                     @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="level3" >  Level 3<span style="color: red">&#42;</span></label>
                                            <div class="col-sm-9">
                                                <select name="level3" class='associates col-xs-12 col-sm-12'>
                                                    <option value="{{$approval3->associate_id}}">{{$approval3->associate_id}}-{{$approval3->as_name}}</option>
                                                     @foreach($employee as $key => $emp)
                                                     <option value="{{$key}}">{{$key}}-{{$emp}}</option>
                                                     @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="approv_id" value=" {{$approval->id}}">
                                        
                                        @include('merch.common.update-btn-section')
                                        <!-- /.row -->
                                    </form>
                              </div>
                        </div>
                </div>


                <div class="col-sm-6 col-sm-offset-2">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
           <!--   //////table here -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
    $('select.associates').select2({
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    })
                };
          },
          cache: true
        }
    });
});
</script>
@endsection
